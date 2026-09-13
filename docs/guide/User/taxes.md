# Taxes

The "Taxes" section is the reference the store's taxes are reckoned by. It
answers three questions: **where** a tax applies (areas), **what** is taken (tax
groups and their rates) and **how** a rate is applied in a particular place
(rules). A product gets its taxes by being assigned a **tax group** in the
"[Prices](prices.md)" section, and which rates apply and at what size is decided
by the customer's area.

## How the Window Is Built

Three tabs: **"Areas"**, **"Rates"** and **"Rules"**. The section is edited **in
locking mode** — the changes are applied with the "Save" / "Save and exit"
buttons (see "[Three Data Working Modes](basics.md)").

## Areas

On the left is the **"Areas"** tree, on the right the additional options of the
selected area.

An area is any node of a territorial division: a country, a state, a region, a
city, a district, down to a quarter. The depth of the tree is not limited — the
division goes as deep as the taxes require: one node is enough for a single
country, the United States get their states, and, where needed, counties and
cities.

The properties of an area (the **"Edit area"** button):

* **Key** — a service code (used mostly by developers);
* **Name** — the name of the area;
* **Code** — the code the outer world knows the area by: the ISO code of a
  country, a region code, a postal code;
* **Type** — the kind of node (country, state, city…), configured in the
  registry;
* **Structure** — a configurable property of the division, configured in the
  registry;
* **Parameters** — a free text field.

**"Set/clear the group flag"** turns a node into a folder — it serves for
grouping (for example, "Europe") and carries no rules.

The additional options of areas are defined in the "Settings Registry" (the
"tax areas" placement) and assigned in the right-hand column — from a list, as
free text or by an input mask (see "[Settings Registry](registry.md)").

## Rates

On the left are the **"Tax groups"**, on the right the **"Tax rates"** of the
selected group.

A **tax group** is the tax profile of a product, and it is the group that gets
assigned to a product in the "Prices". There are usually few groups: "Standard
products", "Reduced", "Excisable", "No tax". The fields of a group: **Key** and
**Name**.

A **rate** is one tax inside a group:

* **Type** — the kind of calculation, configured in the registry;
* **Name** — what the tax is called in the paperwork ("VAT 20%", "Sales tax",
  "Excise");
* **Percent** — the rate as a percentage of the price;
* **Amount** and **Currency** — a fixed amount per unit of the product (excise
  duties, levies); the percentage and the amount may apply at the same time.

A group may hold several rates — a federal tax and a provincial one, for
example. The order of the rates is set with the move buttons and decides the
order of the rows in the rules and in the calculation.

The values of a rate are its **defaults**: they apply wherever the rule of an
area has not set its own (see below).

## Rules

On the left is the tree of areas, on the right the **"Rate rules"** of the
selected area. Folders have no rules — the table is shown only for real areas.

A rule binds a rate to an area and says how it is applied:

* **Group**, **Rate** — which rate applies; it is chosen from a list where both
  columns are visible (the "Group" column is there for reference, read-only);
* **Type** — the way it is adjusted: how the tax attaches to the price — already
  included in it or charged on top; the list is configured in the registry;
* **Own rate** — a flag: the rule replaces the values of the rate with its own;
* **Own percent**, **Own amount**, **Currency** — the values of this area; they
  apply when the "Own rate" flag is set, and without the flag the values from
  the "Rates" tab apply.

How the rules work:

* **Without a rule there is no tax.** A rate applies in an area only if a rule
  is set for it — and the same rule decides whether the tax is in the price or
  on top of it.
* **The nearest rule wins.** The rule of an area applies to every area nested
  inside it until a rule of its own turns up further down the tree. A rule on a
  country covers all of its regions; a rule on a region overrides the country.
* **An own percent of 0 is an honest zero.** A rule with an "own rate" and a
  percentage of zero is an exemption (export, zero rate), not a "not set".
* **One rate may carry several rules** — its own in different areas.

## Examples of Setting It Up

**One store, one country, VAT in the price.** A "Standard products" group with a
"VAT 20%" rate (Percent = 20). One area — the country of the store. One rule:
the VAT rate, type "in the price". Every product gets the group in the "Prices".

**Different categories of products.** The groups "Standard" (20%), "Reduced"
(7%), "No VAT" (a rate of 0%). Products are spread across the groups through
group assignments in the "Prices". The rules are the same, one per rate in the
country.

**Selling into several countries.** An area per country. For each one, a rule on
the VAT rate with an "own rate": Germany 19, Poland 23, countries outside the
union — 0 (export). At home the default value applies, without the flag.

**The United States: sales tax by state.** Inside the "USA" area — the states,
and, where needed, counties and cities inside the states. A "Sales tax" rate
with a percentage of 0 by default; a rule per state with a percentage of its
own, type "on top of the price". A city with its own percentage overrides the
state automatically.

**Excise.** A second rate in the "Excisable" group, with a fixed **Amount** per
unit of the product and a currency. The rule is added in those areas where the
excise applies.

## Assigning It to a Product

In the "[Prices](prices.md)" section a product has a **"Tax group"** column
(switched on with the "Table Designer") and a row in the group assignments — the
group can be set or cleared on a whole set of products at once.

The tax amounts are then reckoned from this data by the store's storefront when
an order is placed.

## Configuring the Types

The value lists are configured by the developer in the "Development" section →
"[Settings Registry](registry.md)": the **Type** and the **Structure** of an
area, the **Type** of a rate's calculation, the **Type** of a rule's adjustment,
and also the tree of additional options of areas (the "tax areas" placement).
