# Program Overview and Main Menu

Melbis Shop is a desktop Windows application that combines two roles:
a **back office** for day-to-day staff operations (products, prices, orders,
customers) and a **development environment** for the storefront and web modules. Data is stored on
a server, but the application downloads the required portion to the local computer and
works with it in batch mode — hence the speed of a desktop application instead of
web interface delays. The general mechanics of this workflow are covered in a separate section, "[Basic Operating Principles](basics.md)".

There is a third role as well: the application is the control panel for the
**AI Assistant**. The assistant works with the store under the login of whoever
opened it, so its reach is set by ordinary user rights; on top of that the owner
sets up **AI Tools** — ready-made store commands the assistant runs instead of
manual work — and hands them out to employees one command at a time. This is
covered in "[AI Tools](ai-tools.md)".

## Desktop

When the application starts, the **main menu** appears at the top of the screen, and the
**"Dispatcher"** window opens automatically on the left — it shows who is currently
in the store and lists active locks. Sections open as windows on top of
the desktop; unnecessary ones can be closed.

The main menu consists of **five tabs**. The first three — "Business", "Products", and
"Structure" — are primarily user-facing sections. The
"Development" and "System" tabs are intended primarily for administrators and
developers.

## "Business" Tab

* **Scheduler** — a lightweight CRM: task assignments for staff and progress tracking.
* **Suppliers** — a directory of product suppliers.
* **Currencies** — a directory of store currencies.
* **Discounts** — a directory of product discounts and their conditions.
* **Prices** — the commercial component of a product: prices, availability, discounts, parameters (logistics workstation).
* **Price Lists** — batch import of supplier price lists.
* **Orders** — creating and editing orders, summaries, accompanying documents.
* **Customers** — viewing and managing store customers.
* **Web Modules** — external store extensions (also accessible from the "Products" and "Structure" tabs).

## "Products" Tab

* **Brands** — a directory of product brands.
* **Browser** — a quick look through the catalog's products: loading, searching, reports and web modules without the right to edit.
* **Descriptions** — composing product descriptions (non-commercial data and retail price).
* **Location** — moving products between catalog sections.
* **Prices** — the commercial side of a product: prices, availability, discounts, parameters (the logistician's workplace).
* **Price Lists** — batch loading of suppliers' price lists.
* **Recovery** — restoring deleted products or permanently removing them.
* **Reviews** — viewing and managing visitor reviews for products.

## "Structure" Tab

* **Catalog** — managing store sections: settings, alternative catalogs, additional options, access rights.
* **Attributes** — descriptive product attributes and their value lists.
* **Parameters** — internal commercial properties of a product (used in the "Prices" section).
* **Filters** — product filters for storefront sections (typically based on attributes).
* **Promo Blocks** — marketing, navigation, text, and banner blocks.
* **Multilanguage** — languages, translation categories, and translations of store elements.
* **Settings** — user-level store settings.

## "Development" Tab

* **Settings Registry** — types and options of store elements, user settings, individual filters.
* **Order Options** — order and order item options, order access rights.
* **Customer Options** — registration fields and customer groups.
* **Report Editor** — creating reports and managing access rights to them.
* **Modules and Options** — configuration of external and built-in web modules.
* **AI Tools** — the catalog of the store's tools, their commands, and each employee's permissions for every command (see "[AI Tools](ai-tools.md)").
* **Server** — store server management.
* **Installation** — initial setup of base and system functions, the keys of external services, copying and restoring the store.
* **Workbench** — built-in IDE (see the developer guide, "[Workbench](../Dev/ide.md)").
* **Web Console** — web development console.

## "System" Tab

* **Stores** — switching and adding stores, local database placement.
* **Users** — groups, staff members, and their access rights.
* **Connection** — authorization, internet connection settings, licensing; the "AI Assistant" tab lives here too: your role in the conversation with it and a check of its connection.
* **Locks** — viewing and releasing locks.
* **Profiles** — retrieve and save profiles on the server.
* **Reload** — rebuilding the local database.
* **Activity Log** — message exchange between the application and the server.
* **Language** — application interface language.
* **Sizes** — top menu size and interface font size.
* **About** — application information.

## Appearance Settings and Profiles {#profiles}

**Sizes.** The menu size (large, medium, small) and interface font size
are switched in "System" → "Sizes". A font change takes effect after restarting the application.

**Language.** The interface language is switched in "System" → "Language" and
takes effect after a restart.

**Profiles.** Saved advanced query conditions and window settings
are stored on the server: "System" → "Profiles" allows you to retrieve them from the server,
upload them to the server, or clear them. For more details about profiles, see the
"[Basic Operating Principles](basics.md)" section.

> The order of this guide's sections repeats the tabs of the main menu. The
> Workbench is described separately, in the developer guide — see
> "[Workbench](../Dev/ide.md)".