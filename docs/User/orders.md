# Orders

The "Orders" section is one of the core sections of the program: creating and editing
orders, generating summaries and accompanying documents. Order calculation is performed
by the same code as on the storefront, so the amounts in the program and on the website
are identical in their construction.

## Order List Window

Loading orders, searching, advanced queries with profiles, local filters,
table designer, and the built-in web module are common mechanics described in
"[Basic Operating Principles](basics.md)". Specifics of the order list:

* The top menu contains quick output fields — **"Reg. Fields"** (customer registration
  fields), **"Options"** (order options), and the **"Product Name"** checkbox. They
  display important data (e.g., discount card number, delivery city,
  ordered product) directly in the order list, with sorting available on them.
* Below the list are related sections for the selected order: **"Customer"**
  (registration fields), **"Products and Their Options"**, **"Order Options"**, and
  **"Order Versions"**. Each table has its own "Table Designer" (right-click).

## Order Versions

The program stores the **order change history**. The **"Get All Order Versions"**
button shows which employee edited the order and when; the active version is highlighted
in red. Any version can be opened, and a new order can be created based on it
(**"Create New Order Based on Order Version"**). An important guarantee: even if
saving the order is blocked by business logic, the system will still add a version —
it simply will not make it the active one.

Versions reduce risk when multiple managers work simultaneously; additionally,
the option to automatically request new versions before editing helps as well.
Access rights for orders are configured separately (see
"[Order Options](order-options.md)").

## Creating Orders

* **"Create New Order"** — a regular order;
* **"Create Return Order"** — based on another order; products in it have
  negative quantities;
* **"Create New Order Version"** and **"…Based on Order Version"** — see above;
* **"Delete Selected Orders"**.

## Order Card

Double-clicking an order opens the **"Edit Order"** window with three
tabs: **"Customer"**, **"Products and Order Options"**, and **"Web Module"**.

### Customer

The customer can be new or existing. To search, enter a keyword and
press Enter — select the desired customer from the **"Found Customers"** list and click
**"Select"**. Buttons: **"New"**, **"Clear Fields"**, **"Find Orders"**
(load all orders for this customer). Some registration fields are
editable, some have an input mask or a dropdown list — this is configured
in "[Customer Options](customer-options.md)". A built-in
web module is also available nearby (e.g., calling the customer via a mini-PBX — the **"Execute"** button).

### Products and Order Options

* **"Add Product"** (selection from catalog or by search), **"Remove Products"**,
  navigation through the list, **"Update Product Information from Store"**.
* The position columns include prices (Price, Price 2/3, **selling price**), **In Stock**,
  **Qty**, **Profit**, **Discount** (amount and ratio), **Total**, as well as
  **Note** and **Auto Note** (filled in by the calculation). The **"#"** flag
  indicates that the product has not yet been calculated.
* **"Product Option"** — associated attributes of the position (configured in
  "[Order Options](order-options.md)"); **"Profit Calculation"** selects how
  to display profit (from supplier price, from price, etc.) — for display purposes only.
* Below are the **"Order Options"**: general options for the entire order.

### Calculation

**"Calculate"** sends the order contents to the server for preliminary
processing — using the same business module as on the storefront. This calculates selling
prices, discounts, gifts, reservations, and additional amounts (e.g., cash on delivery),
resulting in an **identical calculation scheme** both in the program and on the website. Calculation is
mandatory before saving (otherwise — "Calculation must be performed before saving!").

The called modules (order calculation, order addition, version addition) are set by
the developer in the "Development" section → "Settings Registry" → "Called Modules"
(see "[Settings Registry](registry.md)"). The **"Transfer Parameters"** field at the bottom
allows additional actions to be performed during calculation or saving
(e.g., sending an SMS to the customer).

## Reports

The **"Reports"** panel and the **"Show Report"** button generate accompanying
documents for the order — an order form, sales receipt and warranty card, waybill,
as well as summaries (e.g., a purchase summary by supplier). For more details, see
"[Reports](reports.md)".