# Why does my editor not syntax highlight `.page` and `.part` files?

By default, VSCode does not recognize `.page` and `.part` files as Handlebars templates, so they won't be syntax-highlighted correctly. You can fix this by manually associating them with Handlebars (`.hbs`) or HTML as a fallback.

## How to Fix in VSCode

To enable syntax highlighting for `.page` and `.part` files in VSCode:

1. Open **VSCode**.
2. Press **Ctrl + Shift + P** (or **Cmd + Shift + P** on Mac) to open the Command Palette.
3. Search for **"Preferences: Open User Settings (JSON)"** and select it.
4. Add the following lines:

```json
{
  "files.associations": {
    "*.part": "handlebars",
    "*.page": "handlebars"
  }
}
