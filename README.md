# FieldNameCopier

![ProcessWire 3.0.0+](https://img.shields.io/badge/ProcessWire-3.0.0+-orange.svg)
![License MIT](https://img.shields.io/badge/License-MIT-blue.svg)

> Hold a modifier key and click any Inputfield toggle to copy the field’s machine name to the clipboard — without collapsing the field.

Useful when building templates, writing selectors, or debugging.

## Features
- Copies the field machine name on modifier + click
- Field stays open (no collapse)
- Optional toast confirmation
- Works on any admin Inputfield

## Requirements
- ProcessWire 3.0.0+
- Modern browser (current Chrome, Firefox, Edge, Safari)

## Installation
1. Copy the `FieldNameCopier` folder to `/site/modules/`.
2. In the admin go to **Modules → Refresh**, then install **Field Name Copier**.

## Usage
Hold the configured modifier key (default: **Ctrl**) and click the header/toggle of any Inputfield.  
The field name is copied to the clipboard. The field does not collapse.  
A small toast confirms the copy (if enabled).

## Configuration
**Modules → Field Name Copier → Configure**

| Option         | Default | Description                                      |
|----------------|---------|--------------------------------------------------|
| Modifier key   | Ctrl    | Ctrl, Alt, Shift, or Meta (⌘/Win)                |
| Show toast     | ✓       | Brief on-screen confirmation after copying       |

> **Notes on modifier keys**  
> - **Alt**: On some Linux desktops Alt+click is reserved for window dragging.  
> - **Meta (⌘/Win)**: Unreliable on Windows; use only if you develop exclusively on macOS.  
> - **Recommended**: Ctrl or Shift for cross-platform reliability.

## Behavior
- Active only on non-AJAX admin pages.
- Intercepts the click before ProcessWire’s accordion handlers collapse the field.
- Reads the machine name from the Inputfield wrapper ID (falls back to the first form element’s `name` attribute).
- Works regardless of debug mode.

## Changelog
**1.0.1** – Initial release

## License
MIT License.
