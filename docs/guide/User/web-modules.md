# Web Modules

The "Web Modules" section contains store extensions that run in the built-in Chromium
browser. A web module receives context from the application (for example, the current
product or order) and displays relevant information, allowing you to expand the
store's functionality.

There are two types of modules:

* **Built-in** — closely integrated with the application and work only within it (panels in
  the "Prices", "Orders", "Customers", and "Scheduler" sections);
* **External** — work both in the application and in any browser on a computer, tablet,
  or phone.

## Web Module Catalog

In the "Web Modules" section, on the left, is the **"Module Catalog"**. Available functions: **"Launch
Module"**, **"Reopen Module"**, **"Refresh Module List"**, **"Open
Web Console"**, **"Close Module"**, and **"Open Module in Browser"**. The last one
opens an external module in the system browser — its address can be saved and opened
on another device.

An external module may require authentication (employee login and password), in which case it
can be safely opened from anywhere.

## Configuration

Modules are added and configured in the "Development" → "Modules and Options" section:
for external modules, you set the URL, key, authentication requirements, and the list of users
with access; for built-in modules — the location (products, orders, customers,
scheduler). For more details, see "[Modules and Options](module-options.md)". The general role
of the built-in web module panel is described in "[Basic Operating Principles](basics.md)".