# Browser

The "Browser" section is a quick look through the catalog: load products, find
the ones you need, view them with an embedded web module, build a report. Product
data cannot be edited here — that is what "[Descriptions](descriptions.md)",
"[Prices](prices.md)" and "[Location](location.md)" are for.

Hence two uses. The first is a workplace for whoever needs to **look**: the
section opens under a right of its own, and the person does not have to have
access to editing at all. The second is quick access to a product's web modules
for those who do have that access: loading a set here is simpler and faster than
in the heavy sections.

## How the Window Is Built

On the left are the **"Sections"** (the store's catalog), in the middle the
**"Products"**, to the right or below the **"Embedded web module"**. Between the
tree and the products a button opens the **local filter**.

Loading products, the advanced query with its profiles, the local filter, the
table designer, reports and web modules work here exactly as in the other product
sections and are described in "[Core Operating Principles](basics.md)". Below is
only what belongs to the Browser alone.

## What Shows Up in the Sections

The tree shows the sections the employee has any right at all on:
**"Browser"**, "Descriptions", "Prices" or "Location" — any one of them opens the
section for viewing. The "Browser" right is granted where a person is meant to
see products but not edit them; it is given in the "[Catalog](catalog.md)" on the
"Access rights" tab, section by section.

A section with no right on it at all will not appear in the Browser — neither in
the tree, nor in the results of a query.

## Show

The switch above the tree decides what the product list shows:

* **products of the section** — of the selected one only;
* **of the section and its subsections** — the whole branch;
* **all products** — everything loaded into the window, whatever is selected in
  the tree.

After an advanced query and after a search the window switches to "all products"
by itself: what was found lies in different sections, and showing it one section
at a time would be inconvenient.

## Loading and Searching

* **"Get the products of the section and its subsections"** — load the whole
  branch.
* **Search** — the line above the list: type a part of a code or of a name and
  press Enter. The search runs over the store code, the supplier code, the
  manufacturer code and the name at once; the **"in the section"** checkbox
  limits it to the current branch.
* **Advanced query** — conditions on any field that can be loaded; a saved
  **profile** is chosen from the list beside it and runs as soon as it is
  chosen, and the button next to it repeats it once more. The Browser and the
  product selection window share their profiles.
* **"Remove the loaded products"** — clear the list without closing the window.

What is found is **added** to what is already loaded rather than replacing it —
if that gets in the way, tick the clearing before loading in the advanced query,
or clear the list with the button.

## Catalog Sections

The buttons above the tree let you fix the catalog itself in passing: add a
subsection, rename it, delete it, move it up and down; a section can be dragged
into another with the mouse. All of this requires the **"Location"** right on the
section — without it the server will reject the operation.

## Reports

The list of reports and the run button are on the panel to the right. A report is
built from the **selected** rows of the list; rows are selected with the mouse
using Ctrl and Shift. The reports themselves are prepared in the "[Report
Editor](report-editor.md)", and who gets which one is decided there as well.

## Embedded Web Module

The panel shows the selected product through the eyes of the storefront, or of
any other web module granted to the employee. The module is chosen from the list
above the panel, and the button beside it turns the panel vertical or horizontal.
More on this in "[Web Modules](web-modules.md)".
