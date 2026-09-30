# Suppliers

The "Suppliers" section is a directory of the store's product suppliers, their locations and
additional options.

## Window Layout

The window is divided into three parts: on the left — **"Supplier Groups"**, in
the middle — the **"Suppliers"** of the selected group, below them — the
**"Supplier Locations"**. The right-hand column holds the additional options on
two tabs: **"Supplier Options"** and **"Location Options"**. Every list has the
standard functions: adding, deleting and moving items in the list (up and down),
as well as a "Table Designer" for column configuration. The section is edited
**in lock mode** — changes are applied using the "Save" / "Save and Exit"
buttons (see "[Three Data Working Modes](basics.md)").

## Supplier Fields

* **Key** — a service code (used mainly by developers);
* **Name** — the supplier's name;
* **Type**, **Status** — configurable properties (see below);
* **Parameters** — a free-form text field;
* **Manager** — the responsible manager;
* **Phone Numbers**, **Email Address** — supplier contact details;
* **Warehouse Address**, **Service Center Address**, **Service Center Phone** — warehouse and service
  center address;
* **Note** — a free-form note.

## Supplier Locations

A location is a supplier's place where goods are shipped from or handed over:
a warehouse, a service center, a pickup point, a shop. The **Type** tells which of them it is.

The list of the selected supplier's locations: **Key**, **Name** and a
configurable **Type**. The order of the rows is set with the move buttons and is
saved together with the section.

## Additional Options

A supplier and each of its locations have a set of options of their own. The
option tree and the sets of fixed values are defined in the "Settings Registry"
(the "suppliers" and "supplier locations" placements), and in the right-hand
column a value is assigned to the selected supplier or location: from a list,
as free text or by an input mask (see "[Settings Registry](registry.md)").

## Configuring Type and Status

The lists of values for the supplier's **Type** and **Status** fields, as well as
a location's **Type**, are extended by a developer in the "Development" →
"Settings Registry" section (see "[Settings Registry](registry.md)").

## Where It Is Used

A supplier and its locations are assigned to a product in the
"[Prices](prices.md)" section — the quantity of a product is kept by location.
The location a product was sold from is remembered in the row of an
"[Order](orders.md)". Supplier grouping is also used when loading
"[Price Lists](price-lists.md)".
