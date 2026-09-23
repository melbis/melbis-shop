# Prices

The "Prices" section is the logistician's workspace: this is where the **commercial
component** of a product is managed (prices, availability, discounts, parameters).

The program has a fundamental separation of access to products: the **"Prices"**
section is for commercial fields, the **"[Descriptions](descriptions.md)"** section is for content
(photos, descriptions, reviews, attributes), and **"[Location](location.md)"** is for
moving products through the catalog. Access to them is granted separately (see
"[Users and Access Rights](users.md)" and "[Catalog](catalog.md)").

## Window Layout

On the left — **"Sections"** (the store catalog), on the right — **"Products"**. Product
loading, catalog and display modes, search, advanced queries with profiles,
local filter, group assignments, table designer, quick edit,
moving and copying, saving order — these are common to all product sections
and are described in "[Basic Operating Principles](basics.md)".

Additionally: the search can be restricted to the current section using the **"in section"** checkbox, and
the **"Request related products"** button loads products related to
the selected one (see "[Descriptions](descriptions.md)").

## Commercial Product Fields

Through the "Table Designer", commercial fields are displayed in the list, including:

* **Price**, **Price 2**, **Price 3** and their currencies — retail prices (e.g., wholesale,
  retail, promotional);
* **Supplier: Price**, **RRP** (recommended retail price) and the supplier's **Name**;
* **Discount Group** — assigned from the "[Discounts](discounts.md)" reference book (the
  dropdown list shows the group name and discount range);
* **Tax Group** — the product's tax profile, assigned from the
  "[Taxes](taxes.md)" reference book;
* **Warehouse: Name**, **Warehouse: Qty**, **Warehouse: Parameters** — the
  quantity of the product by the supplier's warehouses; a product with several
  warehouses is shown as several rows, just as with parameters. The warehouse is
  chosen from the warehouses of the product's supplier;
* **Qty** (availability), **Min. Qty**, **Order Step**;
* **Type**, **State**, **Status** — configurable properties (values are set in
  the "Settings Registry"); **Status** typically reflects availability ("In stock",
  etc.);
* **Brand**, **Rating** (Cold … Hot), **Reviews** (count and average
  score);
* **Display** (Visible / Hidden), **XML** (whether to include in the export);
* **Profit** (amount and ratio) — informational fields.

Non-commercial fields — photos, descriptions, attributes — are edited in
"[Descriptions](descriptions.md)".

## Calculation Templates (Formulas)

The **"New Template"** button opens the **"New Calculation Template"** form with
**Name** and **Calculation Formula** fields. Formulas work like in spreadsheets:
operators `*  +  -  /  **` (exponentiation) and parentheses, as well as predefined
product variables in square brackets — `[price]`, `[pprice]` (supplier price),
`[rprice]` (RRP), `[how]` (quantity) and others. The full list is revealed by the
**"Show more detailed information about writing formulas"** checkbox.

Examples:

```
[price]*1,1
([price] - [pprice]) / [price] * 100
```

A template is applied to the selected field via the dropdown menu in its column — make sure
the cursor remains on the required field. If desired, a template can be saved under a name
for reuse; **"Delete Template"** removes an unwanted one.

## Auto-Calculation

Unlike a one-time template application, **Auto-Calculation** fields recalculate the price
**continuously**. The **Base** field sets the calculation basis (e.g., "Supplier Price"
or "Supplier RRP"), **Ratio** sets the percentage of the base, and **Price 2** and
**Price 3** define auto-discounts for the corresponding prices. When the base price changes,
dependent prices are recalculated automatically.

## Product Parameters

The **"Product Parameters"** button opens the list of parameters assigned to the product.
A parameter has a value (text or numeric) and a currency; a single product can
have multiple parameters, including identical ones with different values. A parameter
can be quickly edited directly in the list (the **"Parameter:"** panel) and assigned to a group
of products via "Group Assignments". The parameters themselves are defined in the
"[Parameters](parameters.md)" section.

## Product Warehouses

The **"Product Warehouses"** button opens the **"Product Warehouses"** window —
the list of the selected product's warehouses: **Warehouse**, **Qty** and free
**Parameters**. The warehouse is chosen from the warehouses of the product's
supplier; rows marked for deletion are shown struck through until the save. The same data
is available right in the product list through the **Warehouse: Name / Qty /
Parameters** columns.

In the "Group Assignments" a warehouse has a row of its own: modify/add, add,
modify or delete a warehouse with its quantity and parameters. When a warehouse
is assigned, the product also gets the supplier of that warehouse. The
warehouses themselves are maintained in the "[Suppliers](suppliers.md)" section.

## Reports

The **"Reports"** panel and the **"Show Report"** button generate documents based on
loaded products (e.g., procurement summaries). For more details, see "[Reports](reports.md)".