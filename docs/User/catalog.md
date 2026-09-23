# Catalog

The "Catalog" section (the "Structure" tab) is for managing store sections: their
ordering, properties and options, as well as employee access rights to sections.
Quick catalog functions are also available in "Prices", "Descriptions", and "Location", but full
management is here.

The window is organized around the **"Main Catalog"** on the left and a set of tabs on the right:
**"Settings"**, **"Alternative Catalogs"**, **"Access Rights"**, and
**"Additional Options"**.

The catalog is edited **in lock mode** — see "[Three Data Working Modes](basics.md)".

## Managing Sections

Standard functions: **"Add Section"** / **"Add Subsection"**,
**"Edit Section"**, **"Delete Section"**, moving and dragging.
The root section "Root" cannot be deleted.

## Section Settings

For the selected section, the following are configured: text fields **Introduction** and **Description**
(HTML editor), **Section Type**, **Link (URL)**, **Product Sorting** and
**Sort Order** (ascending / descending), **Style**,
**Pseudo-static Code** and **Title** (SEO), **Individual Options**, checkboxes
**"Invisible"** and **"Visible in XML"**, as well as **"Associated Files"** (see
"[File Management](files.md)").

Many fields have a **"Set the same value for subsections"** button — it
copies the current value to all nested sections.

The value lists for the **Section Type**, **Sorting**, and **Style** fields are extended in
the "Settings Registry" (see "[Settings Registry](registry.md)"). The basic section types are
"Default", "Products", "Documents", and "Link"; they cannot be deleted. A new section
gets the "Default" type.

## Alternative Catalogs

The main catalog often contains service sections (cart, warehouse, etc.)
that do not need to be displayed on the storefront in their entirety. **"Alternative Catalogs"**
allow you to assemble separate menus from sections of the main catalog: select a
**"Catalog Name"**, drag the desired section from the main catalog (subsections can be included), and if necessary assign a shorter **"Section Name in This Catalog"** (a copy is edited, not the section itself). Alternative catalog types are configured in the "Settings Registry".

## Access Rights

For each section, permissions are set for groups and users across four independent
dimensions: **Descriptions**, **Prices**, **Location** (the latter also includes
management of the section itself — editing, moving, adding, and deleting) and
**Browser**.

The first three grant the right to **edit** a section's data, the fourth only to
**see** its products in the "[Browser](browse.md)". It is an additive one: a
section is visible in the Browser to whoever holds any of the other three as
well, so it is ticked on its own where a person is meant to look but not edit —
in sections with documents and regulations, for example.

Users with management rights are granted access to all sections automatically;
the **"Apply to Subsections"** button propagates permissions down the tree.

Global access to the "Prices" / "Descriptions" / "Location" / "Browser" sections
is configured in "[Users](users.md)", while here it is set granularly per catalog
section.