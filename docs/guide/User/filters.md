# Filters

The "Filters" section is for managing product filters in the storefront sections.
Filters are usually built based on product attributes.

On the left is the **"Catalog"**, on the right is the **"Filter"** list for the selected section. To
set a filter, first select a section. Available functions: **"Add filter"**, **"Remove
filter"**, and list reordering (the order defines the sequence of filters in the
storefront). Filters can be copied by dragging them to another section. The section
is edited **in lock mode** — see "[Three Data Working Modes](basics.md)".

## Filter fields

* **Key** — a service field (used by storefront developers to mark special filters);
* **Attribute** — the key attribute on which the filter is based;
* **New name** — the filter name in the storefront (if not set, the name of the attribute itself is used);
* **Type** — a configurable property: for example, "Logical OR", "Logical AND",
  "Smart filter", "Price filter", "Brand and series";
* **Products** — the scope: **"Section only"** or **"Section and
  subsections"**.

The list of values for the **Type** field is extended by the developer in the "Settings Registry" (see
"[Settings Registry](registry.md)"); the processing and generation of filter values are
the responsibility of the storefront developer.