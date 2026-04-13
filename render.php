<?php


class PHPStaticGeneratorImproved {
    private $sourceDir;
    private $outputDir;
    private $baseUrl;
    private $excludedStaticDirs = ['papers'];
    private $papersRawBaseUrl = 'https://raw.githubusercontent.com/wwang-ncsu/NeTWIS/main/papers/';
    private $papersViewerPath = 'pdf-viewer.html?file=';
    private $phpExtensions = ['.php', '.html', '.htm'];
    private $staticExtensions = ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', 
                                '.svg', '.ico', '.woff', '.woff2', '.ttf', '.eot',
                                '.pdf', '.txt', '.xml', '.json', '.webp'];
    private $ignorePatterns = ['.git', '__pycache__', '.DS_Store', 'thumbs.db', 
                              'generate_static.php', 'generate_static_improved.php', 
                              'composer.json', 'composer.lock'];
    private $processedUrls = [];
    private $definedFunctions = [];
    
    public function __construct($sourceDir, $outputDir, $baseUrl = '') {
        $this->sourceDir = realpath($sourceDir);
        $this->outputDir = $outputDir;
        $this->baseUrl = rtrim($baseUrl, '/');
        
        if (!$this->sourceDir) {
            throw new Exception("empty dir: $sourceDir");
        }
    }
    
    private function isExcludedRelativePath($relativePath) {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        $firstSegment = explode('/', $relativePath)[0] ?? '';
        if ($firstSegment !== '' && str_starts_with($firstSegment, 'docs')) {
            return true;
        }
        
        $excludedDirs = array_unique(array_filter([
            'docs',
            basename($this->outputDir),
            ...$this->excludedStaticDirs
        ]));
        
        foreach ($excludedDirs as $excludedDir) {
            $prefix = rtrim($excludedDir, '/') . '/';
            if ($relativePath === $excludedDir || str_starts_with($relativePath, $prefix)) {
                return true;
            }
        }
        
        return false;
    }
    
    
    private function removeDirectory($dir) {
        if (!is_dir($dir)) {
            return true;
        }
        
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        
        return rmdir($dir);
    }
    
   
    private function prepareOutputDirectory() {
        if (is_dir($this->outputDir)) {
            echo "cleaning: {$this->outputDir}\n";
            if (!$this->removeDirectory($this->outputDir)) {
                throw new Exception("error: {$this->outputDir}");
            }
        }
        
        if (!mkdir($this->outputDir, 0755, true)) {
            throw new Exception("error: {$this->outputDir}");
        }
        
        echo "ready: {$this->outputDir}\n\n";
    }
    
   
    private function getAllPhpFiles() {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->sourceDir)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filename = $file->getFilename();
                $extension = '.' . pathinfo($filename, PATHINFO_EXTENSION);
                
               
                if (in_array($filename, $this->ignorePatterns)) {
                    continue;
                }
                
                $relativePath = str_replace($this->sourceDir, '', $file->getPathname());
                $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
                
                if ($this->isExcludedRelativePath($relativePath)) {
                    continue;
                }
                
                
                if (in_array($extension, $this->phpExtensions)) {
                    $files[] = $relativePath;
                }
            }
        }
        
        return $files;
    }
    
    
    private function getAllStaticFiles() {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->sourceDir)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filename = $file->getFilename();
                $extension = '.' . pathinfo($filename, PATHINFO_EXTENSION);
                
                
                if (in_array($filename, $this->ignorePatterns)) {
                    continue;
                }
                
                $relativePath = str_replace($this->sourceDir, '', $file->getPathname());
                $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
                
                if ($this->isExcludedRelativePath($relativePath)) {
                    continue;
                }
                
                if (in_array($extension, $this->staticExtensions)) {
                    $files[] = $relativePath;
                }
            }
        }
        
        return $files;
    }
    
    
    private function processPhpFile($relativePath) {
        echo "process: $relativePath\n";
        
        $sourceFile = $this->sourceDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        
        
        $content = $this->executePhpInSeparateProcess($sourceFile, $relativePath);
        
        if ($content === false) {
            echo "process $relativePath failure\n";
            return;
        }
        
        
        $content = $this->processHtmlContent($content, $relativePath);
        
        
        $this->saveHtmlFile($content, $relativePath);
    }
    
    
    private function executePhpInSeparateProcess($sourceFile, $relativePath) {
        
        $tempDir = sys_get_temp_dir();
        $tempScript = $tempDir . DIRECTORY_SEPARATOR . 'php_static_' . md5($sourceFile) . '.php';
        
        
        $scriptContent = $this->generateExecutionScript($sourceFile, $relativePath);
        
        
        if (file_put_contents($tempScript, $scriptContent) === false) {
            echo "can not process script\n";
            return false;
        }
        
        
        $command = sprintf('php %s 2>&1', escapeshellarg($tempScript));
        $output = shell_exec($command);
        
        
        @unlink($tempScript);
        
        
        if ($output === null) {
            echo "failure\n";
            return false;
        }
        
        
        if ($this->hasPhpErrors($output)) {
            echo "php ex wrong:\n" . $output . "\n";
            return "<!-- php ex wrong:\n" . htmlspecialchars($output) . "\n-->";
        }
        
        return $output;
    }
    
    
    private function generateExecutionScript($sourceFile, $relativePath) {
        $sourceDir = $this->sourceDir;
        $fileDir = dirname($sourceFile);
        
        $script = '<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);


$_SERVER = [
    "REQUEST_METHOD" => "GET",
    "HTTP_HOST" => "localhost",
    "REQUEST_URI" => "/" . ' . var_export($relativePath, true) . ',
    "SCRIPT_NAME" => "/" . ' . var_export($relativePath, true) . ',
    "SERVER_NAME" => "localhost",
    "SERVER_PORT" => "80",
    "HTTPS" => "",
    "DOCUMENT_ROOT" => ' . var_export($sourceDir, true) . ',
    "SCRIPT_FILENAME" => ' . var_export($sourceFile, true) . '
];


$originalDir = getcwd();
chdir(' . var_export($fileDir, true) . ');


ob_start();

try {
    
    include ' . var_export($sourceFile, true) . ';
    
    
    $content = ob_get_contents();
    ob_end_clean();
    
    echo $content;
    
} catch (ParseError $e) {
    ob_end_clean();
    echo "<!-- Parse Error: " . htmlspecialchars($e->getMessage()) . " -->";
} catch (Error $e) {
    ob_end_clean();
    echo "<!-- PHP Error: " . htmlspecialchars($e->getMessage()) . " -->";
} catch (Exception $e) {
    ob_end_clean();
    echo "<!-- Exception: " . htmlspecialchars($e->getMessage()) . " -->";
} finally {
    
    chdir($originalDir);
}
?>';
        
        return $script;
    }
    
    
    private function hasPhpErrors($output) {
        $errorPatterns = [
            '/Fatal error:/i',
            '/Parse error:/i',
            '/Cannot redeclare/i',
            '/Call to undefined function/i',
            '/Uncaught Error:/i'
        ];
        
        foreach ($errorPatterns as $pattern) {
            if (preg_match($pattern, $output)) {
                return true;
            }
        }
        
        return false;
    }
    
    
    private function processHtmlContent($content, $currentPath) {
        $content = preg_replace_callback(
            '/(href\s*=\s*["\'])papers\/([^"\']+\.pdf)(["\'])/i',
            function($matches) {
                $pdfUrl = $this->papersRawBaseUrl . $matches[2];
                $viewerUrl = $this->papersViewerPath . rawurlencode($pdfUrl);
                return $matches[1] . $viewerUrl . $matches[3];
            },
            $content
        );
        
        
        $content = preg_replace('/href\s*=\s*["\']http:\/\/localhost(?::\d+)?([^"\']*)["\']/', 'href="$1"', $content);
        $content = preg_replace('/src\s*=\s*["\']http:\/\/localhost(?::\d+)?([^"\']*)["\']/', 'src="$1"', $content);
        $content = preg_replace('/action\s*=\s*["\']http:\/\/localhost(?::\d+)?([^"\']*)["\']/', 'action="$1"', $content);
        
        
        $content = preg_replace('/href\s*=\s*["\']\/+([^"\']*)["\']/', 'href="$1"', $content);
        $content = preg_replace('/src\s*=\s*["\']\/+([^"\']*)["\']/', 'src="$1"', $content);
        $content = preg_replace('/action\s*=\s*["\']\/+([^"\']*)["\']/', 'action="$1"', $content);
        
        
        $content = preg_replace_callback('/href\s*=\s*["\']([^"\']*\.php)(?:\?[^"\']*)?["\']/', function($matches) {
            $url = $matches[1];
            
            $htmlUrl = preg_replace('/\.php$/', '.html', $url);
            return 'href="' . $htmlUrl . '"';
        }, $content);
        
        
        $content = preg_replace_callback('/action\s*=\s*["\']([^"\']*\.php)["\']/', function($matches) {
            echo "action {$matches[1]}\n";
            return 'action="#" data-original-action="' . $matches[1] . '"';
        }, $content);
        
       
        $content = preg_replace('/href\s*=\s*["\']["\']/', 'href="#"', $content);
        $content = preg_replace('/src\s*=\s*["\']["\']/', '', $content);
        
        
        $metaTag = "\n<!-- " . date('Y-m-d H:i:s') . " -->\n";
        if (preg_match('/<head[^>]*>/', $content)) {
            $content = preg_replace('/(<head[^>]*>)/', '$1' . $metaTag, $content);
        } else {
            $content = $metaTag . $content;
        }
        
        return $content;
    }
    
    
    private function saveHtmlFile($content, $relativePath) {
        
        $outputPath = preg_replace('/\.php$/', '.html', $relativePath);
        
        $fullOutputPath = $this->outputDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $outputPath);
        
       
        $outputDir = dirname($fullOutputPath);
        if (!is_dir($outputDir)) {
            if (!mkdir($outputDir, 0755, true)) {
                echo "can not create dir: $outputDir\n";
                return;
            }
        }
        
       
        if (file_put_contents($fullOutputPath, $content) !== false) {
            echo "saved: $outputPath\n";
            $this->processedUrls[] = $outputPath;
        } else {
            echo "save failure: $outputPath\n";
        }
    }
    
   
    private function copyStaticFiles() {
        echo "\ncopy html files...\n";
        
        $staticFiles = $this->getAllStaticFiles();
        
        foreach ($staticFiles as $relativePath) {
            $sourceFile = $this->sourceDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            $outputFile = $this->outputDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            
            
            $outputDir = dirname($outputFile);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }
            
            
            if (copy($sourceFile, $outputFile)) {
                echo "copied: $relativePath\n";
            } else {
                echo "copied failure: $relativePath\n";
            }
        }
    }
    
    
    
    public function generate() {
        echo "generate HTML files...\n";
        echo "input: {$this->sourceDir}\n";
        echo "output: {$this->outputDir}\n\n";
        
        
        $this->prepareOutputDirectory();
        
        $phpFiles = $this->getAllPhpFiles();
        echo "find " . count($phpFiles) . "  PHP/HTML files\n\n";
        
        foreach ($phpFiles as $file) {
            $this->processPhpFile($file);
        }
        
        $this->copyStaticFiles();
        
        return true;
    }
}

function main() {
    global $argv;
    
    if (count($argv) < 3) {
        echo "usage: php render.php <input> <output>\n";
        echo "example: php render.php . ../html_site\n";
        exit(1);
    }
    
    $sourceDir = $argv[1];
    $outputDir = $argv[2];
    
    try {
        $generator = new PHPStaticGeneratorImproved($sourceDir, $outputDir);
        $generator->generate();
        
        echo "\nsuccess！\n";
        
    } catch (Exception $e) {
        echo "error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

if (php_sapi_name() === 'cli') {
    main();
} else {
    echo "error\n";
}
?>
