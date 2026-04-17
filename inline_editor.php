<?php

$inlineEditorRoot = __DIR__;

function inline_editor_json_response(array $payload, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function inline_editor_data_config(string $target): ?array {
    $configs = [
        'news.php' => [
            'file' => 'data/news.php',
            'label' => 'News',
            'fields' => [
                ['name' => 'date', 'label' => 'Date'],
                ['name' => 'date_display', 'label' => 'Display Date'],
                ['name' => 'html', 'label' => 'HTML', 'type' => 'textarea'],
            ],
        ],
        'publications.php' => [
            'file' => 'data/publications.php',
            'label' => 'Publications',
            'fields' => [
                ['name' => 'id', 'label' => 'ID'],
                ['name' => 'type', 'label' => 'Type'],
                ['name' => 'year', 'label' => 'Year'],
                ['name' => 'selected', 'label' => 'Selected', 'type' => 'checkbox'],
                ['name' => 'title', 'label' => 'Title', 'type' => 'textarea'],
                ['name' => 'authors', 'label' => 'Authors', 'type' => 'textarea'],
                ['name' => 'venue', 'label' => 'Venue', 'type' => 'textarea'],
                ['name' => 'link', 'label' => 'PDF Link'],
                ['name' => 'extra', 'label' => 'Extra'],
                ['name' => 'area', 'label' => 'Area (comma-separated)'],
            ],
        ],
        'Activities.php' => [
            'file' => 'data/activities.php',
            'label' => 'Activities',
            'fields' => [
                ['name' => 'date', 'label' => 'Date'],
                ['name' => 'html', 'label' => 'HTML', 'type' => 'textarea'],
            ],
        ],
    ];

    return $configs[$target] ?? null;
}

function inline_editor_mode(string $target): string {
    if (inline_editor_data_config($target) !== null) {
        return 'data';
    }

    $disabled = [
        'index.php',
        'publications.php',
        'downloads.php',
        'render.php',
    ];

    return in_array($target, $disabled, true) ? 'disabled' : 'static';
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

function inline_editor_export_php_value($value, int $level = 0): string {
    if (is_array($value)) {
        $indent = str_repeat('  ', $level);
        $childIndent = str_repeat('  ', $level + 1);
        $isSequential = array_keys($value) === range(0, count($value) - 1);
        $lines = [];

        foreach ($value as $key => $item) {
            $rendered = inline_editor_export_php_value($item, $level + 1);
            if ($isSequential) {
                $lines[] = $childIndent . $rendered;
            } else {
                $lines[] = $childIndent . var_export((string) $key, true) . ' => ' . $rendered;
            }
        }

        if ($lines === []) {
            return '[]';
        }

        return "[\n" . implode(",\n", $lines) . "\n" . $indent . ']';
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if ($value === null) {
        return 'null';
    }

    return var_export((string) $value, true);
}

function inline_editor_write_data_file(string $absolutePath, array $items): bool {
    $relative = ltrim(str_replace('\\', '/', str_replace(__DIR__, '', $absolutePath)), '/');
    $contents = "<?php\n";
    $contents .= "// {$relative}\n";
    $contents .= "return " . inline_editor_export_php_value($items) . ";\n";

    return file_put_contents($absolutePath, $contents) !== false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['inline_editor_action'] ?? '');

    if ($action === 'save') {
        $target = basename((string) ($_POST['target'] ?? ''));
        $html = (string) ($_POST['html'] ?? '');

        if ($target === '' || !preg_match('/^[A-Za-z0-9._-]+\.php$/', $target)) {
            inline_editor_json_response(['ok' => false, 'message' => 'Invalid target file.'], 400);
        }

        if (inline_editor_mode($target) !== 'static') {
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

    if ($action === 'save_data') {
        $target = basename((string) ($_POST['target'] ?? ''));
        $config = inline_editor_data_config($target);
        if ($config === null) {
            inline_editor_json_response(['ok' => false, 'message' => 'This page is not configured for data editing.'], 400);
        }

        $items = json_decode((string) ($_POST['items'] ?? ''), true);
        if (!is_array($items)) {
            inline_editor_json_response(['ok' => false, 'message' => 'Invalid data payload.'], 400);
        }

        $allowedFields = array_column($config['fields'], 'name');
        $normalized = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $row = [];
            foreach ($config['fields'] as $fieldConfig) {
                $field = $fieldConfig['name'];
                $type = $fieldConfig['type'] ?? 'text';
                $value = $item[$field] ?? '';

                if ($type === 'checkbox') {
                    $row[$field] = !empty($value);
                    continue;
                }

                if ($field === 'area') {
                    if (is_array($value)) {
                        $row[$field] = implode(', ', array_map('strval', $value));
                    } else {
                        $row[$field] = (string) $value;
                    }
                    continue;
                }

                $row[$field] = (string) $value;
            }

            $allEmpty = true;
            foreach ($row as $value) {
                if (is_bool($value)) {
                    if ($value) {
                        $allEmpty = false;
                        break;
                    }
                    continue;
                }

                if (trim((string) $value) !== '') {
                    $allEmpty = false;
                    break;
                }
            }

            if (!$allEmpty) {
                $normalized[] = $row;
            }
        }

        $dataPath = realpath($inlineEditorRoot . DIRECTORY_SEPARATOR . $config['file']);
        if ($dataPath === false) {
            inline_editor_json_response(['ok' => false, 'message' => 'Data file was not found.'], 404);
        }

        if (!inline_editor_write_data_file($dataPath, $normalized)) {
            inline_editor_json_response(['ok' => false, 'message' => 'Unable to save data file.'], 500);
        }

        inline_editor_json_response(['ok' => true, 'message' => $config['label'] . ' saved successfully.']);
    }
}

if (PHP_SAPI === 'cli') {
    return;
}

function inline_editor_is_local_request(): bool {
    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $serverName = strtolower((string) ($_SERVER['SERVER_NAME'] ?? ''));
    $remoteAddr = (string) ($_SERVER['REMOTE_ADDR'] ?? '');

    $localHosts = [
        'localhost',
        '127.0.0.1',
        '::1',
    ];

    foreach ([$host, $serverName] as $value) {
        if ($value === '') {
            continue;
        }

        $normalized = preg_replace('/:\d+$/', '', $value);
        if (in_array($normalized, $localHosts, true)) {
            return true;
        }
    }

    return in_array($remoteAddr, ['127.0.0.1', '::1'], true);
}

if (!inline_editor_is_local_request()) {
    return;
}

function inline_editor_detect_target(): string {
    $self = realpath(__FILE__) ?: __FILE__;
    $root = realpath(__DIR__) ?: __DIR__;
    $excluded = [
        'config.php',
        'inline_editor.php',
    ];

    foreach (get_included_files() as $file) {
        $real = realpath($file) ?: $file;
        if ($real === $self) {
            continue;
        }

        if (dirname($real) === $root && preg_match('/\.php$/i', $real)) {
            $base = basename($real);
            if (!in_array($base, $excluded, true)) {
                return $base;
            }
        }
    }

    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    foreach (array_reverse($trace) as $frame) {
        $file = $frame['file'] ?? '';
        if ($file === '') {
            continue;
        }

        $real = realpath($file) ?: $file;
        if ($real === $self) {
            continue;
        }

        if (dirname($real) === $root && preg_match('/\.php$/i', $real)) {
            return basename($real);
        }
    }

    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    if ($requestPath !== '' && $requestPath !== '/') {
        return basename($requestPath);
    }

    $scriptFile = basename($_SERVER['SCRIPT_FILENAME'] ?? '');
    if ($scriptFile !== '') {
        return $scriptFile;
    }

    $scriptPath = parse_url($_SERVER['SCRIPT_NAME'] ?? '', PHP_URL_PATH) ?: '';
    return basename($scriptPath);
}

$inlineEditorTarget = inline_editor_detect_target();
$inlineEditorMode = inline_editor_mode($inlineEditorTarget);
$inlineEditorEndpoint = ($BASE_URL ?? '') . '/inline_editor.php';
$inlineEditorDataConfig = inline_editor_data_config($inlineEditorTarget);
$inlineEditorInitialData = $inlineEditorDataConfig !== null
    ? require __DIR__ . '/' . $inlineEditorDataConfig['file']
    : [];
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

  .inline-editor-toolbar.is-hidden {
    display: none;
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

  .inline-editor-modal {
    position: fixed;
    inset: 0;
    z-index: 9998;
    display: none;
    align-items: stretch;
    justify-content: flex-end;
    background: rgba(0, 0, 0, 0.35);
  }

  .inline-editor-modal.is-open {
    display: flex;
  }

  .inline-editor-panel {
    width: min(720px, 92vw);
    height: 100%;
    overflow: auto;
    background: #fff;
    box-shadow: -8px 0 24px rgba(0, 0, 0, 0.22);
    padding: 22px;
    box-sizing: border-box;
    font: 14px/1.45 Arial, sans-serif;
    color: #222;
  }

  .inline-editor-panel h3 {
    margin: 0 0 14px;
  }

  .inline-editor-list {
    display: grid;
    gap: 14px;
  }

  .inline-editor-card {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 14px;
    background: #fafafa;
  }

  .inline-editor-field {
    display: grid;
    gap: 6px;
    margin-bottom: 10px;
  }

  .inline-editor-field input,
  .inline-editor-field textarea {
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 8px 10px;
    font: inherit;
  }

  .inline-editor-field textarea {
    min-height: 110px;
    resize: vertical;
  }

  .inline-editor-panel-actions,
  .inline-editor-card-actions {
    display: flex;
    gap: 10px;
    margin-top: 12px;
  }

  .inline-editor-panel button,
  .inline-editor-card button {
    border: 0;
    border-radius: 6px;
    padding: 8px 12px;
    cursor: pointer;
    background: #c00000;
    color: #fff;
    font: inherit;
  }

  .inline-editor-panel button.inline-editor-secondary,
  .inline-editor-card button.inline-editor-secondary {
    background: #666;
  }

  .inline-editor-panel-status {
    margin-top: 10px;
    color: #444;
  }

  @media (max-width: 768px) {
    .inline-editor-toolbar {
      right: 12px;
      left: 12px;
      bottom: 12px;
      flex-wrap: wrap;
    }

    .inline-editor-status {
      max-width: none;
      width: 100%;
    }
  }
</style>
<script>
  (() => {
    const endpoint = <?= json_encode($inlineEditorEndpoint) ?>;
    const target = <?= json_encode($inlineEditorTarget) ?>;
    const mode = <?= json_encode($inlineEditorMode) ?>;
    const dataConfig = <?= json_encode($inlineEditorDataConfig) ?>;
    const initialData = <?= json_encode($inlineEditorInitialData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    const main = document.querySelector('.main');
    if (!main) return;
    const navList = document.querySelector('.header__item--nav ul');
    let launcher = null;

    if (navList) {
      const launcherItem = document.createElement('li');
      launcherItem.className = 'inline-editor-nav-item';
      launcherItem.innerHTML = '<a href="#" data-inline-editor-launcher>Edit</a>';
      navList.appendChild(launcherItem);
      launcher = launcherItem.querySelector('[data-inline-editor-launcher]');
    }

    const toolbar = document.createElement('div');
    toolbar.className = 'inline-editor-toolbar is-hidden';
    toolbar.innerHTML = `
      <button type="button" data-action="toggle">${mode === 'static' ? 'Edit Page' : mode === 'data' ? 'Edit Data' : 'Editing Unavailable'}</button>
      <button type="button" data-action="save" style="display:none;">Save</button>
      <button type="button" data-action="cancel" style="display:none;background:#555;">Cancel</button>
      <button type="button" data-action="hide" class="inline-editor-secondary" style="background:#555;">Close</button>
      <span class="inline-editor-status">Local inline editor</span>
    `;
    document.body.appendChild(toolbar);

    const toggleButton = toolbar.querySelector('[data-action="toggle"]');
    const saveButton = toolbar.querySelector('[data-action="save"]');
    const cancelButton = toolbar.querySelector('[data-action="cancel"]');
    const hideButton = toolbar.querySelector('[data-action="hide"]');
    const status = toolbar.querySelector('.inline-editor-status');
    let originalHtml = main.innerHTML;

    const openToolbar = () => {
      toolbar.classList.remove('is-hidden');
      if (launcher) {
        launcher.classList.add('navon');
      }
    };

    const closeToolbar = () => {
      toolbar.classList.add('is-hidden');
      if (launcher) {
        launcher.classList.remove('navon');
      }
    };

    if (launcher) {
      launcher.addEventListener('click', (event) => {
        event.preventDefault();
        openToolbar();
      });
    }

    if (mode === 'disabled') {
      toggleButton.disabled = true;
      status.textContent = 'Editing is not enabled for this page yet.';
      hideButton.addEventListener('click', closeToolbar);
      return;
    }

    if (mode === 'static') {
      const setEditing = (enabled) => {
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

      const exitStaticEditing = ({ restore } = { restore: true }) => {
        if (restore) {
          main.innerHTML = originalHtml;
        }
        setEditing(false);
      };

      toggleButton.addEventListener('click', () => {
        originalHtml = main.innerHTML;
        openToolbar();
        setEditing(true);
      });

      cancelButton.addEventListener('click', () => {
        exitStaticEditing();
      });

      hideButton.addEventListener('click', () => {
        if (main.contentEditable === 'true') {
          exitStaticEditing();
        }
        closeToolbar();
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

      return;
    }

    if (mode === 'data' && dataConfig) {
      const cloneItems = () => JSON.parse(JSON.stringify(initialData || []));
      let items = cloneItems();

      const modal = document.createElement('div');
      modal.className = 'inline-editor-modal';
      modal.innerHTML = `
        <div class="inline-editor-panel">
          <h3>Edit ${dataConfig.label}</h3>
          <div class="inline-editor-list"></div>
          <div class="inline-editor-panel-actions">
            <button type="button" data-action="add">Add Item</button>
            <button type="button" data-action="save-data">Save ${dataConfig.label}</button>
            <button type="button" data-action="close" class="inline-editor-secondary">Close</button>
          </div>
          <div class="inline-editor-panel-status">Edit the data items below and save.</div>
        </div>
      `;
      document.body.appendChild(modal);

      const list = modal.querySelector('.inline-editor-list');
      const panelStatus = modal.querySelector('.inline-editor-panel-status');

      const closeDataEditor = () => {
        modal.classList.remove('is-open');
        items = cloneItems();
        renderList();
        panelStatus.textContent = 'Edit the data items below and save.';
        status.textContent = `Local inline editor for ${dataConfig.label.toLowerCase()}`;
        closeToolbar();
      };

      const renderList = () => {
        list.innerHTML = '';

        items.forEach((item, index) => {
          const card = document.createElement('div');
          card.className = 'inline-editor-card';

          const fieldsHtml = dataConfig.fields.map((field) => {
            const value = item[field.name] || '';
            if (field.type === 'checkbox') {
              return `
                <label class="inline-editor-field">
                  <span>${field.label}</span>
                  <input type="checkbox" data-index="${index}" data-field="${field.name}" ${value ? 'checked' : ''}>
                </label>
              `;
            }

            if (field.type === 'textarea') {
              return `
                <label class="inline-editor-field">
                  <span>${field.label}</span>
                  <textarea data-index="${index}" data-field="${field.name}">${String(value).replace(/</g, '&lt;')}</textarea>
                </label>
              `;
            }

            return `
              <label class="inline-editor-field">
                <span>${field.label}</span>
                <input type="text" data-index="${index}" data-field="${field.name}" value="${String(value).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;')}">
              </label>
            `;
          }).join('');

          card.innerHTML = `
            ${fieldsHtml}
            <div class="inline-editor-card-actions">
              <button type="button" data-action="remove" data-index="${index}" class="inline-editor-secondary">Remove</button>
            </div>
          `;
          list.appendChild(card);
        });

        if (items.length === 0) {
          const empty = document.createElement('div');
          empty.className = 'inline-editor-card';
          empty.textContent = 'No items yet. Click "Add Item" to create one.';
          list.appendChild(empty);
        }
      };

      modal.addEventListener('input', (event) => {
        const targetEl = event.target;
        if (!(targetEl instanceof HTMLInputElement || targetEl instanceof HTMLTextAreaElement)) return;
        const index = Number(targetEl.dataset.index);
        const field = targetEl.dataset.field;
        if (!Number.isInteger(index) || !field || !items[index]) return;
        if (targetEl instanceof HTMLInputElement && targetEl.type === 'checkbox') {
          items[index][field] = targetEl.checked;
          return;
        }
        items[index][field] = targetEl.value;
      });

      modal.addEventListener('click', async (event) => {
        const button = event.target.closest('button');
        if (!button) return;

        const action = button.dataset.action;
        if (action === 'add') {
          const next = {};
          dataConfig.fields.forEach((field) => {
            next[field.name] = field.type === 'checkbox' ? false : '';
          });
          items.unshift(next);
          renderList();
          return;
        }

        if (action === 'remove') {
          const index = Number(button.dataset.index);
          items.splice(index, 1);
          renderList();
          return;
        }

        if (action === 'close') {
          closeDataEditor();
          return;
        }

        if (action === 'save-data') {
          panelStatus.textContent = 'Saving...';
          try {
            const form = new URLSearchParams();
            form.set('inline_editor_action', 'save_data');
            form.set('target', target);
            form.set('items', JSON.stringify(items));

            const response = await fetch(endpoint, {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
              body: form.toString()
            });

            const result = await response.json();
            if (!response.ok || !result.ok) {
              throw new Error(result.message || 'Save failed.');
            }

            panelStatus.textContent = result.message || 'Saved.';
            setTimeout(() => window.location.reload(), 350);
          } catch (error) {
            panelStatus.textContent = error.message || 'Save failed.';
          }
        }
      });

      toggleButton.addEventListener('click', () => {
        items = cloneItems();
        renderList();
        openToolbar();
        modal.classList.add('is-open');
        status.textContent = `Editing ${dataConfig.label.toLowerCase()} data`;
      });

      hideButton.addEventListener('click', () => {
        if (modal.classList.contains('is-open')) {
          closeDataEditor();
          return;
        }
        closeToolbar();
      });

      status.textContent = `Local inline editor for ${dataConfig.label.toLowerCase()}`;
      return;
    }
  })();
</script>
