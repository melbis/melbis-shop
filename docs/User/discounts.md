# Discounts

The "Discounts" section is a reference book of product discounts and their conditions.

## Window Layout

The window is divided into two parts: on the left — **"Discount Groups"**, on the right — **"Discounts and Conditions"** of the selected group. Groups are added, removed, and reordered using standard buttons. The section is edited **in lock mode** — changes are applied using the "Save" / "Save and Exit" buttons (see "[Three Data Working Modes](basics.md)").

## Discount Condition

Each row in "Discounts and Conditions" describes a single condition:

* **Type** — the condition type (configurable, see below): for example, "From order amount" or "Discount for loyalty card type";
* **Amount or parameter** — the threshold value of the condition (meaning depends on the type);
* **Currency** — the currency of the amount, if the condition is amount-based;
* **Discount(%)** — the discount amount;
* **Validity period** (Start / End) — the period during which the condition is active.

## Configuring Condition Types

The list of values for the **Type** field is extended by the developer in the "Development" section → "Settings Registry" → "Basic Settings" → the "Type" field for discounts (see "[Settings Registry](registry.md)"). The strategy itself — how to apply multiple matching discounts (accumulate or take the maximum) — is implemented by the developer in the store modules according to your requirements.

## Assigning Discounts to Products

Finished **discount groups** are assigned to products in the "[Prices](prices.md)" section: there, the product list displays a "Discount Group" column, and the dropdown list shows the group name and the range of parameters for its conditions.