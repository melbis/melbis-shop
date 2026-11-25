# Customer Options

The "Customer Options" section (the "Development" tab) — customer configuration. Three
tabs.

* **Registration fields** — a tree of customer registration fields and their
  **Possible values** (Key, Value, Code, Type). Fields can have an input template
  or a list of values; they are used in the "[Customers](customers.md)" and
  "[Orders](orders.md)" sections. A field with the **"Do not display"** flag is kept both in
  the customer card and in orders, but these sections do not show it — for
  service data written by a module.
* **Customer groups** — groups with the fields **Price category** (Price #1 with discount,
  strictly Price #1 / #2 / #3, or Supplier price) and **Group discount, %**.
* **Automatic rules** — customer transfer rules: when the total order amount exceeds
  a specified value (in the indicated currency) — set an individual discount or move
  the customer to another group.