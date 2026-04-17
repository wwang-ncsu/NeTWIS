<?php

$inlineEditorRoot = __DIR__;

function inline_editor_json_response(array $payload, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function inline_editor_supported_target(string $target): bool {
    $unsupported = [
        'index.php',
        'Activities.php',
        'news.php',
        'publications.php',
        'downloads.php',
        'render.php',
    ];

    return !in_array($target, $unsupported, true);
}

function inline_editor_replace_main_content(string $source, string $replacement): ?string {
    $firstClose = strpos($source, '?>');
    if ($firstClose === false) {
        return null;
    }

    $footerPattern = '/<\?php\s+require(?:_once)?\s+__DIR__\s*\.\s*[\'"]\/partials\/footer\.php[\'"];\s*\?>/s';
    if (!preg_match($footerPattern, $source, $footerMatch, PREG_OFFSET_CAPTURE, $firstClose + 2)) {
        return null;
    }

    $footerOffset = $footerMatch[0][1];
    $prefix = substr($source, 0, $firstClose + 2);
    $suffix = substr($source, $footerOffset);

    return $prefix . "\n\n" . rtrim($replacement) . "\n\n" . $suffix;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['inline_editor_action'] ?? '') === 'save') {
    $target = basename((string) ($_POST['target'] ?? ''));
    $html = (string) ($_POST['html'] ?? '');

    if ($target === '' || !preg_match('/^[A-Za-z0-9._-]+\.php$/', $target)) {
        inline_editor_json_response(['ok' => false, 'message' => 'Invalid target file.'], 400);
    }

    if (!inline_editor_supported_target($target)) {
        inline_editor_json_response(['ok' => false, 'message' => 'This page is not enabled for inline saving yet.'], 400);
    }

    $targetPath = realpath($inlineEditorRoot . DIRECTORY_SEPARATOR . $target);
    if ($targetPath === false || dirname($targetPath) !== $inlineEditorRoot) {
        inline_editor_json_response(['ok' => false, 'message' => 'Target file was not found.'], 404);
    }

    $source = file_get_contents($targetPath);
    if ($source === false) {
        inline_editor_json_response(['ok' => false, 'message' => 'Unable to read target file.'], 500);
    }

    if (preg_match('/<\?(?:php|=)/', $html)) {
        inline_editor_json_response(['ok' => false, 'message' => 'Inline PHP is not allowed in editor content.'], 400);
    }

    $updated = inline_editor_replace_main_content($source, $html);
    if ($updated === null) {
        inline_editor_json_response(['ok' => false, 'message' => 'Could not locate the editable region in this page.'], 500);
    }

    if (file_put_contents($targetPath, $updated) === false) {
        inline_editor_json_response(['ok' => false, 'message' => 'Unable to save target file.'], 500);
    }

    inline_editor_json_response(['ok' => true, 'message' => 'Page saved successfully.']);
}

if (PHP_SAPI === 'cli') {
    return;
}

$inlineEditorTarget = basename(parse_url($_SERVER['SCRIPT_NAME'] ?? '', PHP_URL_PATH) ?: '');
$inlineEditorEnabled = inline_editor_supported_target($inlineEditorTarget);
$inlineEditorEndpoint = htmlspecialchars(($BASE_URL ?? '') . '/inline_editor.php', ENT_QUOTES, 'UTF-8');
$inlineEditorTargetAttr = htmlspecialchars($inlineEditorTarget, ENT_QUOTES, 'UTF-8');
?>
<style>
  .inline-editor-toolbar {
    position: fixed;
    right: 20px;
    bottom: 20px;
    z-index: 9999;
    display: flex;
    gap: 8px;
    align-items: center;
    padding: 10px 12px;
    border-radius: 10px;
    background: rgba(20, 20, 20, 0.92);
    color: #fff;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
    font: 13px/1.2 Arial, sans-serif;
  }

  .inline-editor-toolbar button {
    border: 0;
    border-radius: 6px;
    padding: 7px 10px;
    cursor: pointer;
    background: #c00000;
    color: #fff;
    font: inherit;
  }

  .inline-editor-toolbar button[disabled] {
    cursor: not-allowed;
    opacity: 0.55;
  }

  .inline-editor-status {
    max-width: 220px;
    color: #f0f0f0;
  }

  .inline-editor-active .main {
    outline: 2px dashed #c00000;
    outline-offset: 8px;
  }
</style>
<script>
  (() => {
    const endpoint = <?= json_encode(($BASE_URL ?? '') . '/inline_editor.php') ?>;
    const target = <?= json_encode($inlineEditorTarget) ?>;
    const supported = <?= $inlineEditorEnabled ? 'true' : 'false' ?>;
    const main = document.querySelector('.main');
    if (!main) return;

    const toolbar = document.createElement('div');
    toolbar.className = 'inline-editor-toolbar';
    toolbar.innerHTML = `
      <button type="button" data-action="toggle">${supported ? 'Edit Page' : 'Editing Unavailable'}</button>
      <button type="button" data-action="save" style="display:none;">Save</button>
      <button type="button" data-action="cancel" style="display:none;background:#555;">Cancel</button>
      <span class="inline-editor-status">Local inline editor</span>
    `;
    document.body.appendChild(toolbar);

    const toggleButton = toolbar.querySelector('[data-action="toggle"]');
    const saveButton = toolbar.querySelector('[data-action="save"]');
    const cancelButton = toolbar.querySelector('[data-action="cancel"]');
    const status = toolbar.querySelector('.inline-editor-status');
    let originalHtml = main.innerHTML;
    let editing = false;

    if (!supported) {
      toggleButton.disabled = true;
      status.textContent = 'This page still uses dynamic PHP blocks. Save is disabled.';
      return;
    }

    const setEditing = (enabled) => {
      editing = enabled;
      document.body.classList.toggle('inline-editor-active', enabled);
      main.contentEditable = enabled ? 'true' : 'false';
      saveButton.style.display = enabled ? '' : 'none';
      cancelButton.style.display = enabled ? '' : 'none';
      toggleButton.textContent = enabled ? 'Editing...' : 'Edit Page';
      toggleButton.disabled = enabled;
      status.textContent = enabled
        ? 'Edit the main content area, then click Save.'
        : 'Local inline editor';
    };

    toggleButton.addEventListener('click', () => {
      originalHtml = main.innerHTML;
      setEditing(true);
    });

    cancelButton.addEventListener('click', () => {
      main.innerHTML = originalHtml;
      setEditing(false);
    });

    saveButton.addEventListener('click', async () => {
      saveButton.disabled = true;
      cancelButton.disabled = true;
      status.textContent = 'Saving...';

      try {
        const form = new URLSearchParams();
        form.set('inline_editor_action', 'save');
        form.set('target', target);
        form.set('html', main.innerHTML);

        const response = await fetch(endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
          body: form.toString()
        });

        const result = await response.json();
        if (!response.ok || !result.ok) {
          throw new Error(result.message || 'Save failed.');
        }

        originalHtml = main.innerHTML;
        status.textContent = result.message || 'Saved.';
        setEditing(false);
      } catch (error) {
        status.textContent = error.message || 'Save failed.';
      } finally {
        saveButton.disabled = false;
        cancelButton.disabled = false;
      }
    });
  })();
</script>
