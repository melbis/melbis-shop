# Descriptions

The "Descriptions" section is for creating product descriptions: photos, descriptions, reviews,
attributes, and other non-commercial data (plus retail price). This is
the content half of working with a product; the commercial half is handled in "[Prices](prices.md)".
Access control is described in "[Users](users.md)" and "[Catalog](catalog.md)".

## Personal Workspace and Replication

Many employees fill in descriptions simultaneously, so each person works in
their own **personal workspace** — a local copy of the loaded products. Clicking
**"Save"** causes the server to reconcile your edits with the shared data; this
procedure — **replication** — is performed automatically. Simultaneous edits
to the same product enter the **"conflicting"** display mode: they must be
reviewed before saving (see "[Basic Operating Principles](basics.md)").

## Data Exchange with User

For external staff (interns, freelancers) who are not yet granted direct
saving to the store, there is a **"Data Exchange with User"** mode: a manager
loads products and passes them to the employee; the employee fills in the descriptions and, when finished,
clicks **"Suspend"** (instead of saving to the store); the manager retrieves
the data via the same exchange, reviews it, and saves it. The **"Save Without Transfer"**
button saves edits locally without sending them to the shared store.

## Window Layout

Two main tabs: **"Personal Workspace"** (working with products) and
**"Attributes"** (your version of the attribute values reference).

On the left — **"Catalog"**, on the right — **"Products"**. Loading, catalog and
display modes (All / Unchanged / Modified / Conflicting), local filter,
group assignments, table designer — these are common mechanics, see
"[Basic Operating Principles](basics.md)". Section-specific features:

* **"Add Product (Ins)"** and **"Add Product Related to Current
  (Ctrl-Ins)"**;
* **"Quick Edit"** — an editing window with quick switching between
  products (convenient for attributes);
* **"Remove Product from Editable"** — removes from the local list (the product
  is not deleted on the server);
* **"Disconnect from Server Product"** — after saving, the loaded product
  will become a new one (it will be duplicated);
* **"Copy to Products"**, **"Create New Clan"**;
* bottom quick-preview panel: **"Images, Attributes"**,
  **"Description"**, **"Binding"**, and the built-in web module.

## Product Card

Opens via **"Edit Product"** (modal) or **"Quick Edit"** (with switching between products). Main data groups:

* **Basic Data** — relation (ID of the related product, e.g. a
  Storefront ↔ Warehouse pair), codes (in store, supplier's, manufacturer's), unit
  of measure, name, brand; text fields **Introduction**, **Description**,
  **Review** (open in the HTML editor, see "[HTML Editor](html-editor.md)");
  configurable **Type**, **Status**, **Style**; "Individual Options";
  "Associated Files" (see "[File Management](files.md)") and "Copy Description
  from Product";
* **Attributes** — an attribute tree with values alongside; on the right — a list
  of possible values (or a number), "Select All" / "Clear All", and quick
  value addition;
* **Product Relations** — binding other products with "Relation Type",
  "Parameters", and "Notes" settings;
* **Clan** — grouping variants of the same product (e.g., clothing sizes) under
  a shared identifier; the "Primary Product in Clan" option, Title and Description fields;
* **SEO Fields** — fields for search engine optimization.

Configurable lists (Type, Status, Style, Relation Type) are extended in the "Settings Registry"
(see "[Settings Registry](registry.md)").

## "Attributes" Tab

This contains your **local version** of the attribute values reference. The
attribute catalog on the left works the same way as in the "[Attributes](attributes.md)"
section (edits are immediately reflected in the shared reference). The values on the right are a copy of the main
reference: existing values cannot be edited, but you can **add a new one** (it
is displayed underlined and is stored locally only for the time being). Once you assign
the new value to a product and perform **"Save"**, the server will add it to the shared
reference — and it will become available to other employees.