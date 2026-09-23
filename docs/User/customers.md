# Customers

The "Customers" section is for viewing and managing store customers. If the store
is configured properly, you will rarely need to visit this section: when an order is placed, a customer
is created automatically.

## Window Layout

On the left is the **"Customers"** list, on the right are the **"Registration Data"** of the selected
customer (registration fields and their values). Loading, advanced query with
profiles, and local filter are common mechanics, see "[Basic Principles
of Operation](basics.md)".

The list displays the following fields: **Group**, **Login**, **Password**, **Discount %**, **Order Total**,
**Registration Date**, and **Identification Date**; the **"Reg. Field:"** panel
adds the selected registration field to the list. The set of columns can be configured
using the "Table Designer".

## Functions

* **"Add Customer"** — create a customer manually (when an order is placed, the customer
  is created automatically);
* **"Delete Selected Customers"**;
* **"Update Registration Fields"** — if the set of registration fields has changed;
* **"Advanced Customer Query"** — selection by conditions with saving to
  a profile;
* **"Show Report"** — see "[Reports](reports.md)".

## Registration Data

On the right, you can edit any registration fields of the customer, including those closed
during order placement. Fields may have an input mask (e.g., a phone number) or
a dropdown list — this is configured in "[Customer Options](customer-options.md)".
A modified customer is shown in bold; the **"Save"** button submits the data
to the server.