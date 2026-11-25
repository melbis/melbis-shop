# Order Options

The "Order Options" section (the "Development" tab) — defining options for orders and
items in an order, as well as access rights to orders. Three tabs.

* **Item options** — accompanying attributes for each order line item. An option has
  a **Type** and the **"Require calculation"**, **"Fixed values only"**,
  **"Allow editing option value amount"**, **"Do not display"** flags; values have an amount and currency,
  **"Default"**, **"Only for already created order"**, incompatibility with
  other values, and edit permissions.
* **Order options** — options for the entire order. An option value can **modify
  the order amount**: a **Number**, **Operation** (Plus / Multiply / Minus /
  Divide), and **Target** (item total or total order amount) are specified. There are
  restrictions on changing values and edit permissions.
* **Order access rights** — permissions for groups and users to view and
  edit orders.

These options appear in the order card (see "[Orders](orders.md)"). The **"Do not display"**
flag (order options have it too) makes an option a service one: it stays in the order, is saved
and passed to embedded web modules, but the order windows do not show its note, and an option
without values is not shown at all. The note of such an option is a handy place for data written
by a module.