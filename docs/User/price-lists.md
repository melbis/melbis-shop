# Price Lists

The "Price Lists" section provides semi-automatic batch uploading of supplier
price lists: multiple products are added and updated in a single pass.

The ideal option is automatic updating via a supplier's XML price list, which
is implemented by a developer as a separate module. This section is also used
when a price list arrives as a regular file (for example, an Excel export saved
as a text file) and needs to be processed manually.

## Profiles

Processing the same supplier's data is a recurring task, so it is convenient
to save the markup settings as a **profile** (usually named after the supplier)
using the **"Create"**, **"Edit"**, and **"Delete"** buttons. Next time, the
update will be nearly instant.

## File Upload and Basic Settings

Select the data file (**"Data from file" → "Select"**); if necessary, enable
the **"Use UTF-8 encoding"** checkbox. After analysis, the **"Column List"**
will appear on the left, and the contents of the selected column on the right.

In the **"Settings"** block:

* **First row** — which row to start from (to skip the header);
* **Update mode** — how to match price list products with the database: by "ID", "Store code",
  "Supplier code", "Manufacturer code", "Name", or **"Add only"**;
* **Supplier** — which supplier the upload is being performed for;
* **"Add new products to section:"** — where new products will be placed (use the
  **"Select"** button to choose a section).

## Column Markup

For each column in the list, the following is defined:

* **Data type** — what the column represents: "Supplier code", "Name", "Supplier price",
  "Status (key)", "Quantity", "Discount group (ID)", "Product parameter (first … fifth)",
  and others, or **"Not used"**;
* **Values** — how to process the data: **"As is"**, **"Fixed value"**
  (substitute a single value from the "Fixed value" field), or **"Value substitution"**;
* **Use for products** — **"Added and updated"** or **"Added only"**
  (it makes sense not to update the key identifier field).

Three tabs are available on the right: **"Column contents"** (sample values),
**"Value substitution"**, and **"Parameter options"**.

## Value Substitution

For key fields (for example, "Status (key)"), the supplier's values must be mapped
to the store's keys. The **"Generate list of possible values"** button scans the
entire price list and collects all encountered values; for each one, in the
**"Original value → Replace with value"** pair, specify the store key (for example,
`kGoodsExist` for in-stock status — keys are found in "Development" → "Settings Registry" →
"Basic settings"). The **"Restore original list"** and **"Clear entire list"**
buttons reset the substitution table.

## Parameter Options

If a column is marked as a **"Product parameter"**, the **"Parameter options"** tab
allows you to specify: the parameter name (**"Add/edit parameter"**), where to
insert the value — **"in the Value field"** or **"in the Amount field"** — and the **Currency**.

## Processing and Submission

The **"Start processing and submission"** button generates the **"Preliminary data
processing result"**, where you can verify that everything was recognized correctly:
the **"Result"** column shows either "Found" or "New product". If there is an error,
click **"Go back"** and correct the markup; if everything is correct, click
**"Submit"**. After uploading, the result can be verified in the "[Prices](prices.md)" section.