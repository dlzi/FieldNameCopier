# FieldNameCopier

![ProcessWire 3.0.0+](https://img.shields.io/badge/ProcessWire-3.0.0+-orange.svg)
![License MIT](https://img.shields.io/badge/License-MIT-blue.svg)

A tiny ProcessWire admin utility. Hold a modifier key and click any Inputfield toggle to copy the field's machine name to the clipboard — without collapsing the field.

Useful when building templates, writing selectors, or debugging: no more hovering to read the name in the title attribute.

---

## Requirements

- ProcessWire 3.0.0+
- Modern browser (Chrome, Firefox, Edge, Safari — all current versions)

---

## Installation

1. Copy the `FieldNameCopier` folder to `/site/modules/`.
2. In the admin go to **Modules → Refresh**, then install **Field Name Copier**.

---

## Usage

Hold the configured modifier key (default: **Ctrl**) and click the header/toggle of any Inputfield. The field name is copied to your clipboard. The field does not collapse.

A small toast notification confirms the copy.

---

## Configuration

Go to **Modules → Field Name Copier → Configure**.

| Option | Default | Description |
|---|---|---|
| Modifier key | Ctrl | Key to hold while clicking: Ctrl, Alt, Shift, or Meta (⌘/Win) |
| Show toast | ✓ | Show a brief on-screen confirmation after copying |

> ⚠️ **Notes on Modifier Keys:**
> * **Alt:** On some Linux desktop environments, Alt+click is reserved for system window dragging.
> * **Meta (⌘ / Win):** The Meta key is intercepted by the Windows operating system for native shortcuts and will not work reliably on PC. Enable this option **only** if you are developing exclusively on macOS.
> * **Best Practice:** Ctrl or Shift are the safest, most reliable cross-platform choices.

---

## How it works

During the module's `ready()` state (once the API is fully booted and the current page is known), the module checks if the request is for a non-AJAX admin page. If so, it hooks into `Page::render` and appends a small inline script right before `</body>`. This keeps the front-end entirely free of unnecessary hooks. 

To guarantee instant script readiness and avoid potential timing issues, the configuration data is encoded into JSON and inlined directly into the JavaScript wrapper execution payload rather than utilizing ProcessWire's native `$config->js()` array.

The script listens on the document in the **capture phase**, allowing it to intercept the click and halt event propagation before ProcessWire's core jQuery accordion handlers can trigger a collapse. It walks up the DOM from the clicked `.InputfieldHeader` to find the parent `.Inputfield` wrapper. It then uses a regular expression to parse the field name from the wrapper's `id` attribute, falling back to the native `name` attribute of the first internal form element (input, select, or textarea) if an ID match isn't found.

⚠️ Works regardless of debug mode — the field name is always present in the DOM on admin pages.

---

## Changelog

**1.0.1**
- Initial release
