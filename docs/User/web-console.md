# Web Console

Many of the application's windows show web pages in an embedded panel: a section's storefront in "Location", a product card in "Descriptions", an order's service page, a web module. Each such panel is a full browser, and the **"Web Console"** gives it the familiar developer tools: the page tree, styles, network requests, JavaScript errors, and an execution console.

## How to Open It

The console is not called on its own but from a particular panel: in a window with an embedded page, choose the **"Open web console"** command. The source gets a tab of its own with the page address in the caption; calling it again from the same window returns to the tab already open rather than creating a second one.

The "Development" → "Web Console" menu item simply shows the window with all the tabs opened earlier. If no panel has been connected yet, the window will be empty — that is normal.

## Working with the Window

The **"Keep on top of other windows"** checkbox holds the console above the application's other windows: convenient when a module is edited in one window and its output is checked in another.

Tabs live as long as the window is open; closing the console disconnects the tools, while the web panels themselves keep working.

What the console can do is determined by the version of Chromium built into the application, and therefore differs from the tools of a current desktop browser. When developing the storefront that customers see, it is safer to check the pages in an ordinary browser as well.
