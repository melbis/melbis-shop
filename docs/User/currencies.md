# Currencies

The "Currencies" section is a reference list of currencies used in the store.

The reference list is straightforward: adding, removing, and reordering currencies
("Table Designer" configures the columns). The section is edited **in lock mode** —
changes are applied using the "Save" / "Save and Exit" buttons (see
"[Three Data Working Modes](basics.md)").

## Currency Fields

* **Key** — a service code (used primarily by developers);
* **Name** — for example, USD or EUR;
* **Rate** — the currency exchange rate;
* **Relation** — how the rate is applied to the base currency: **"Multiply by rate"**
  or **"Divide by rate"**;
* **Supplier** — a currency can be linked to a specific supplier.

## Where It Is Used

A currency is selected for various product prices in the "[Prices](prices.md)" section, as well as
in discount conditions (see "[Discounts](discounts.md)").