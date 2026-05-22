<?php namespace ProcessWire;

/**
 * FieldNameCopier
 *
 * Modifier+click on any Inputfield toggle in the admin copies the field name
 * to the clipboard instead of collapsing the field.
 *
 * @author  Daniel Zilli
 * @version 1.0.1
 * @license MIT
 */
class FieldNameCopier extends WireData implements Module, ConfigurableModule {

	public static function getModuleInfo(): array {
		return [
			'title'    => 'Field Name Copier',
			'version'  => 101,
			'summary'  => 'Modifier+click on an Inputfield toggle copies the field name to clipboard.',
			'author'   => 'Daniel Zilli',
			'autoload' => 'template=admin',
			'singular' => true,
			'icon'     => 'clipboard',
			'requires' => 'ProcessWire>=3.0.0',
		];
	}

	// ── Defaults ────────────────────────────────────────────────────────────

	public static function getDefaultData(): array {
		return [
			'modifier_key' => 'ctrlKey',
			'show_toast'   => 1,
		];
	}

	public function __construct() {
		parent::__construct();
		$this->setArray(self::getDefaultData());
	}

	// ── Ready ───────────────────────────────────────────────────────────────

	public function ready(): void {
		// Only attach the hook if we are actually rendering the admin interface
		if ($this->wire('page')->template->name !== 'admin') return;
		if ($this->wire('config')->ajax) return;

		$this->addHookAfter('Page::render', $this, 'injectAssets');
	}

	public function injectAssets(HookEvent $event): void {
		// Inline config directly into the script — avoids $config->js() timing issue
		$cfg = json_encode([
			'modifierKey' => $this->modifier_key,
			'showToast'   => (bool) $this->show_toast,
		], JSON_UNESCAPED_UNICODE);

		$script = '<script>(function(){var cfg=' . $cfg . ';' . $this->getJs() . '}());</script>';

		$event->return = str_replace('</body>', $script . '</body>', $event->return);
	}

	// ── JS payload (no outer IIFE — wrapper is in injectAssets) ─────────────

	protected function getJs(): string {
		return <<<'JS'

	  function getFieldName(header) {
	    const wrapper = header.closest('.Inputfield');
	    if (!wrapper) return null;

	    const match = (wrapper.id || '').match(/Inputfield_([a-zA-Z0-9_]+)/);
	    if (match) return match[1];

	    const input = wrapper.querySelector('input[name], select[name], textarea[name]');
	    return input ? input.name : null;
	  }

	  function showToast(msg) {
	    const t = document.createElement('div');
	    t.textContent = msg;
	    t.style.cssText = [
	      'position:fixed','bottom:24px','right:24px',
	      'background:#2d2d2d','color:#fff','padding:8px 14px',
	      'border-radius:6px','font:13px/1.4 monospace',
	      'z-index:99999','opacity:0','transition:opacity .2s',
	      'pointer-events:none'
	    ].join(';');
	    document.body.appendChild(t);
	    
	    requestAnimationFrame(() => {
	      t.style.opacity = '1';
	      setTimeout(() => {
	        t.style.opacity = '0';
	        setTimeout(() => t.remove(), 220);
	      }, 1600);
	    });
	  }

	  function copyName(name) {
	    const done = () => { if(cfg.showToast) showToast('Copied: ' + name); };
	    if (navigator.clipboard && navigator.clipboard.writeText) {
	      navigator.clipboard.writeText(name).then(done).catch(done);
	    } else {
	      const ta = document.createElement('textarea');
	      ta.value = name;
	      ta.style.cssText = 'position:fixed;opacity:0';
	      document.body.appendChild(ta);
	      ta.select();
	      document.execCommand('copy');
	      ta.remove();
	      done();
	    }
	  }

	  document.addEventListener('click', function(e) {
	    if (!e[cfg.modifierKey]) return;
	    const header = e.target.closest('.InputfieldHeader');
	    if (!header) return;
	    const name = getFieldName(header);
	    if (!name) return;
	    
	    e.preventDefault();
	    e.stopImmediatePropagation();
	    copyName(name);
	  }, true);

	JS;
	}

	// ── Config page ─────────────────────────────────────────────────────────

	public static function getModuleConfigInputfields(array $data): InputfieldWrapper {
		$data    = array_merge(self::getDefaultData(), $data);
		$modules = wire('modules');
		$wrapper = new InputfieldWrapper();

		/** @var InputfieldSelect $f */
		$f = $modules->get('InputfieldSelect');
		$f->attr('name', 'modifier_key');
		$f->label       = __('Modifier key');
		$f->description = __('Hold this key while clicking an Inputfield toggle to copy the field name. Note: Meta (Win key) is intercepted by Windows and will not work on PC — use it only on macOS.');
		$f->addOption('ctrlKey',  'Ctrl');
		$f->addOption('altKey',   'Alt');
		$f->addOption('shiftKey', 'Shift');
		$f->addOption('metaKey',  'Meta (⌘ / Win — macOS only)');
		$f->attr('value', $data['modifier_key']);
		$wrapper->add($f);

		/** @var InputfieldCheckbox $c */
		$c = $modules->get('InputfieldCheckbox');
		$c->attr('name', 'show_toast');
		$c->label       = __('Show toast notification');
		$c->description = __('Display a brief confirmation toast after copying.');
		$c->attr('value', 1);
		if (!empty($data['show_toast'])) $c->attr('checked', 'checked');
		$wrapper->add($c);

		return $wrapper;
	}
}