# Core Operating Principles

Almost all sections of the program follow the same rules: how data is loaded,
filtered, edited, and saved to the server. These mechanisms are described here
once, and the manual sections reference them without repeating the information.

## Three Data Working Modes

Program sections work with data in one of three modes. Understanding the mode
explains why some windows have lock buttons while others have portion loading
and batch saving.

### 1. Editing with Locking

This is how reference books and settings windows work — for example, Currencies, Discounts, Suppliers,
Brands, Parameters, Attributes, Catalog, Filters, Promo Blocks, as well as the
"Development" sections (Settings Registry, Order and Customer Options, Modules and Options,
Report Editor) and "Users". Such a window is recognized by the **"Pause"** and
**"Read Only"** buttons at the bottom.

When opened, the window **locks the section's data on the server** for you: while
the lock is yours, the window has a **red frame**, and other employees can open
the section only for reading.

If the section is already locked by another employee, the **"Tables Are Locked"**
window appears with the name of whoever holds it:

* **Retry** — try again; the button repeats the attempt by itself, with a
  countdown (the interval is "auto-retry on a lock" in
  "[Connection](connection.md)");
* **Open Read-only** — open the section for viewing;
* **Unlock** — go to the "[Locks](dispatcher.md#locks)" window;
* **Cancel** — do not open the section.

Without that window, the section opens read-only if you have no right to save
it or it is not the operation's "Allowed Time" at the moment (see
"[Users](users.md)").

The window's buttons:

* **Save** — send changes to the server without closing the window; the lock
  stays yours;
* **Save and Exit** — send changes, release the lock and close the window;
* **Read Only** — release your lock and stay in the window for viewing, so that
  another employee can work with the data;
* **Reopen** — the same button in read-only mode: load the section again and try
  to take the lock;
* **Pause** — close the window, keeping the lock yours and the changes on this
  computer's disk; the next time you open it, work continues from the same place.
  If your lock was released in the meantime, the section loads from the server
  again, and the unsaved changes are lost;
* **Cancel** — discard unsaved changes, release the lock and close the window;
  the next time you open it, the data loads from the server.

If you close the window with the cross while the lock is yours, the program asks
what to do: "Pause", "Save and Exit" or "Cancel".

A lock is never released by time on its own. A "stuck" lock — for example, one
left on "Pause" on another computer — is released in the
"[Locks](dispatcher.md#locks)" window.

### 2. Working in Portions

This is how sections with large volumes work — Prices, Price Lists, Orders, Customers,
Reviews, Location. The entire table is not locked: you load only the required
**portion** of data (by section, search, or advanced query), edit it
locally, and send it to the server as a **batch** using the "Save" button. Different employees
work with different portions simultaneously. The mechanics of loading, display modes, and
saving are described in the sections below.

### 3. Combined (Replication)

This is how the **"Descriptions"** section works: data is taken into your **personal workspace**
(like a portion, but with a lock against simultaneous editing), and when saved it is
**replicated** to the server and synchronized with the shared data. Simultaneous edits
to the same product are marked as "conflicting". For more details, see "[Descriptions](descriptions.md)".

## Batch Work and Local Database

The program is a desktop application. It downloads only the required portion of
data from the server into a **local database**, and you work with it on your own computer. Edits
accumulate locally and are sent to the server as a **single batch** using the
"Save" button. This makes work fast, minimizes server load,
and prevents the storefront cache from being reset on every minor change.

Until the data is saved, changes exist only on your end. The state of a row
shows in its font style:

* **bold** — the row has been changed and not yet saved;
* **strikethrough** — the row is marked for deletion;
* **underline** — a new row that the server does not have yet, or a product that
  another employee has changed in the meantime;
* **italic** — the row is hidden or inactive: the product is not shown on the
  storefront, the element belongs to another group, the field takes no part in the load.

The text and background colors come from the row's type, status or state — every
value has its own style in the "[Settings Registry](registry.md#styles)". The font
styles add up, so all the marks are visible at once.

The local database should be placed on a fast drive and, as a rule, not on
the system drive `C:`. For more details about choosing the folder, see
"[Connecting to a Store](connection.md#stores)".

## Reloading the Local Database {#recreate}

Over time unnecessary data accumulates in the local database and the work slows
down. **"Reload"** recreates it from scratch: the program closes every window,
disconnects from the database and creates it anew, and the data is pulled down
from the server again as it is needed.

> Data not yet sent to the server is lost in the process. Save your changes
> first.

The dispatcher reminds you about a reload by itself when the local database
becomes outdated.

## Catalog and Product Loading

In product sections ("Prices", "Descriptions", "Location"), the catalog
is on the left and the product list is on the right. The catalog display mode switch determines
what to show when a section is selected:

| Mode | What is displayed |
|---|---|
| Section products | only products from the selected section |
| Section and subsections | products from the section together with all nested ones |
| All products | all loaded products; when a product is selected, its section is highlighted in red in the catalog |

Products can be loaded in three ways:

| Method | How |
|---|---|
| By section | select a section and click "Get products of section and subsections"; the quick option is a double-click on an empty section |
| Search | enter a word in the "Search" field and press Enter; the search runs over the store code, the supplier code, the manufacturer code and the name at once |
| Advanced query | set conditions in a separate window (see below) |

The **"Remove loaded products"** button clears the list locally — products on
the server are not deleted.

## Advanced Query and Profiles

The **Advanced Query** opens a condition selection window. It allows you to set: clearing
already loaded data, limiting by root section, logical condition mode
(**AND** — all conditions are met, **OR** — at least one) and a list of fields by
which conditions are built. At the end of the list there are two special conditions:

* **Direct SQL query** — arbitrary selection using SQL (for developers).
* **Individual filters** — ready-made named queries prepared in advance
  by a developer in the "Development" → "Settings Registry" →
  "Individual Filters" section (see "[Settings Registry](registry.md)").

A frequently repeated query is convenient to save as a **profile**: click "New" → enter a name →
click "Save". The profile appears in the advanced query list, and next time
data is loaded in one step. Profiles are stored on the server — manage
them in the "System" → "Profiles" section (retrieve, load, clear).

The same advanced query and profile mechanism works in the "Orders",
"Customers", and "Reviews" sections.

## Display Modes and Saving

The display mode switch limits the list of already loaded rows:

* **All products** — everything that has been loaded;
* **Unchanged** — only rows without edits;
* **Modified** — only rows with unsaved edits;
* **Conflicting** — only in the "Descriptions" section: rows modified by another
  employee simultaneously with you — these need to be reviewed before saving.

The **"Save"** button sends the entire batch of changes to the server. When clearing
the list with unsaved edits, the program warns about possible data loss.

## Local Filter

The **Local Filter** temporarily narrows the already loaded list without accessing the
server: select a field and value and click "Apply". Clicking the filter button
again restores the full list. This is useful when you need to temporarily find
a subset among loaded data without re-requesting it.

## Group Assignments

**Group Assignments** is an auxiliary window for performing operations on a group of rows
at once. Select the required rows, set a field value, and click "Apply". The window
remains open until you close it or switch to another one. This is
the primary tool for working with large datasets.

## Table Designer

Right-clicking on a table opens the **"Table Designer"** — here you select
visible and editable columns and their widths, separately for each table. In
product tables, the designer allows you to display and edit one of the product's
attributes directly in the list.

There are two view switches in the same place:

* **"Fit column widths to the window"** — the columns stretch across the width
  of the table; switch it off if there are many columns and it is more
  convenient to scroll them horizontally with widths set by hand;
* **"When a column is resized, move all the ones to its right"** — dragging a
  column's border shifts the whole right-hand part of the table instead of
  squeezing the neighbouring column.

Both switches are remembered along with the rest of the window's settings.

## Table Editing, Moving, and Order

* **Quick edit.** The "Enable/disable quick edit mode" button allows you to change
  values directly in a cell; without it, a cell opens for editing with a double-click.
* **Group selection.** Drag the mouse over rows while holding the left button;
  to add more rows to the selection, hold `Ctrl`.
* **Moving and copying.** Dragging moves a product to another section;
  dragging while holding `Ctrl` copies it. A copy is not a clone: it is the same
  product that now exists in multiple sections simultaneously.
* **Order.** Display the "Order" field and sort by it; the
  "Save order" button renumbers products within the current section.

## Built-in Web Module

Many sections contain a **built-in web module** panel — this is a Chromium-based browser
embedded in the program. It receives context (which section you are in, which product or order is open)
and displays information related to it.
Which module to display is selected within the panel itself, and modules are configured
in the "Development" → "Modules and Options" section. For more details, see "[Web Modules](web-modules.md)".

## Access Rights

Employee access is set at two levels: **globally** — in the "System"
→ "Users" section, and **at the catalog section level** — in "Catalog" → "Access
Rights". Commercial fields (the "Prices" section) and content (the "Descriptions" section) are two
independent access planes, to which "Location" and "Browser" are added. The
first three grant the right to edit, the fourth only to look at a section's
products in the "[Browser](browse.md)". For more details, see
"[Users and Access Rights](users.md)" and "[Catalog](catalog.md)".