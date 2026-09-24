# HTML Editor

The HTML editor is used for product descriptions and any text fields with HTML
(brand descriptions, attributes, sections, promo blocks). Text fields with two
buttons at the end of the line open the editor: the first opens the visual mode
directly, the second opens the code mode. The **"Enable/disable manual HTML code editing mode"** button
switches between modes without closing the editor.

## Formatting with Classes

The key idea is to format text with **classes** rather than manually selecting fonts,
colors, and sizes. A class combines a set of styles in a separate file, so the store
maintains a consistent brand style, and the formatting can be changed without touching the
description texts. Classes are prepared by a developer.

## Elements and Their Properties

An HTML document consists of nested elements (tags). The element bar at the bottom
shows which tags surround the selected text; each element has an **ID**,
**Class**, **Title** (a tooltip, the `title` attribute), and **Style**, as well as the
**"Styles"**, **"Events"**, and **"Console"** tabs.

Element functions include: **"Add element and class"** (with a submenu of ready-made
classes), **"Find element start/end"**, copying, cutting, and pasting
an element (before/after), **"Delete element"** and **"Delete element along with
its content"**, **"Enable/disable element highlighting"**.

## Tables

**"Insert table"**, **"Show tables"** (to see invisible ones), inserting rows and
columns, merging and splitting cells.

## Formatting, Images, Links

Standard functions: undo/redo, clear, clipboard with two paste options —
**"Paste with formatting"** and **"Paste without formatting"** (without
cluttering with extra tags), bold/italic/underline, alignment, indentation, and
lists. **"Insert image"** and **"Image management"** open the object's files
(see "[File Management](files.md)"); **"Assign link"** and
**"Assign link to file"** create links.

Scheduler notes and the agent's memory have no files of their own, so an image from
the clipboard is written right into the text: pasting (**Ctrl+V**) or the
**"Insert image"** button compresses it to WebP and puts it inside the HTML code.
The editor asks for confirmation before inserting.

## Templates

The **"Template"** panel and the **"Insert template"** button add ready-made
formatting blocks (for example, a table, video, page header, or panel).

## Editor Settings

Sets of classes, tags, templates, and connected CSS style files are configured
by a developer in the "Development" section → "Settings Registry" → "Basic Settings"
→ "Editor Settings" → "HTML Editor" (see "[Settings Registry](registry.md)").