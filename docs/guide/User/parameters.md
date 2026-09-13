# Parameters

The "Parameters" section is a reference guide for product parameters. Unlike attributes,
parameters are internal commercial properties used in the
"[Prices](prices.md)" section (for example, warranty, minimum profit, delivery cost).

## Window Layout

On the left is the **"Parameters"** list, in the middle the **"Parameter
Values"** of the selected parameter. Both parts have standard functions for
adding, removing, and moving items. The right-hand column holds the additional
options on two tabs: **"Parameter Options"** and **"Value Options"**. The section
is edited **in lock mode** — changes are applied using the "Save" / "Save and
Exit" buttons (see "[Three Data Working Modes](basics.md)").

## Parameter Fields

* **Key** — a service code (for developers);
* **Name**;
* **Type** — a configurable property with the default value "Default";
* **"Fixed values only"** — allow only values from the list on the right;
* **"Allow editing of value amount"** — whether the value amount can be edited in place.

## Parameter Values

The fields of a value:

* **Key** — a service code;
* **Group** — an arbitrary label for showing the values of one parameter
  together; the field is free, but it suggests the groups already used in this
  parameter;
* **Name**;
* **Type** — a configurable property; a new value is born with the type
  "Default";
* **Amount** (number and currency).

## Additional Options

A parameter and each of its values have a set of options of their own. The option
tree is defined in the "Settings Registry" (the "parameters" and "parameter
values" placements), and on the tabs of the right-hand column an option value is
assigned to the selected parameter or value — from a list, as free text or by an
input mask (see "[Settings Registry](registry.md)").

## Where It Is Configured and Used

The lists of values for the **Type** fields are extended by the developer in the
"Settings Registry" (see "[Settings Registry](registry.md)"). Assigning
parameters to products is described in the "[Prices](prices.md)" section.
