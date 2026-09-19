# Attributes

The "Attributes" section is a reference directory of descriptive product attributes and their
possible values (for example, "Color" and its values: red, green, blue).

The directory is edited **in lock mode** — see "[Three Data Working Modes](basics.md)".

## Window Layout

On the left is a tree-structured **attribute catalog**, on the right are the **values** of the selected
attribute. Tabs are available at the top: **"Attribute Values"**, **"Access Rights"**,
and **"Additional Options"**.

The catalog is managed using standard functions: **"Add Section"** /
**"Add Subsection"**, **"Edit"**, **"Delete"**, move,
**"Set/Reset Group Flag"**, and drag-and-drop.

## Attribute Fields

* **Key** — a service code (for developers);
* **Name**, **Description**;
* **Type** and **Content** — configurable properties (see below);
* **SEO code** and display options for the attribute in store sections.

The **Content** field has two key values (which cannot be deleted):

* **"Value Set"** — the attribute is selected from a list of values (shown on the right);
* **"Number"** — the value is set as a number for each product individually.

The value lists for the **Type** and **Content** fields are extended by a developer in
"Settings Registry" → "Basic Settings" (see "[Settings Registry](registry.md)").
For example, the "Key Attribute" type can be used by the storefront to find
similar products.

## Attribute Values

On the right side is a list of values: **"Add"**, **"Edit"**,
**"Delete"**, move, **"Save Order"**, and **"Find Unused
Values"** (to collect values not assigned to any product, for deletion or
transfer). Values can be dragged from one attribute to another.

Value fields: **Key**, **Attribute Value** (content), **Description**,
**Type**, **Parameters**, **SEO code**, and **Associated Files** (see
"[File Management](files.md)").

## Access Rights

On the **"Access Rights"** tab, two permissions are configured for groups and users:
**"Edit Attribute Properties"** and **"Edit Attribute Values"**.
Users with management rights gain access to all attributes
automatically; the **"Apply to Subsections"** button propagates the permission
down the tree.

Working with attributes of specific products is done in the
"[Descriptions](descriptions.md)" section.