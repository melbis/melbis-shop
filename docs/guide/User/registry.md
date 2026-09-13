# Settings Registry

The "Settings Registry" section (the "Development" tab) is the central place where
a developer defines the properties and options of store elements. It is important for the owner to know that
many configurable lists (**Type**, **Status**, **Style**, **Content**, etc.),
found throughout the store, are extended here. The window consists of
four tabs.

* **Basic Settings** — keys and their values by section: element types and options
  (products, sections, brands, attributes, filters, promo blocks…),
  "Callable Modules" (order calculation and processing), "Editor Settings"
  (classes, tags and HTML editor templates, image editor profiles and masks — the profiles
  are maintained by the AI assistant as well, see "[AI Tools](ai-tools.md)"),
  "File Groups", "Alternative Catalogs", and more. System keys are marked with `*`.
* **User Settings** — key store variables that staff
  change in the "[Settings](settings.md)" section; their names,
  input templates, values, and display permissions by groups and users are defined here.
* **Additional Options** — fields of the store's own that it adds to its elements:
  catalog sections, attributes, brands, suppliers and their warehouses,
  parameters and parameter values, users and user groups, promo blocks, filters
  and tax areas.
* **Individual Filters** — named SQL queries for products, orders, and
  customers; they appear in the "Advanced Query" of the corresponding section (see
  "[Basic Operating Principles](basics.md)"). The second tab inside it is
  **"Value Lists"**: the directories for the parameters of those queries.

## Additional Options

The tab declares the fields an element's ordinary card does not have. The
**"Placement"** list chooses what the options belong to: **catalog sections**,
**attributes**, **brands**, **suppliers**, **supplier warehouses**,
**parameters**, **parameter values**, **users**, **user groups**, **promo
blocks**, **filters**, **tax areas**. The sets are independent — the options of
sections are not visible on brands and the other way round.

On the left is the **"Section list"** tree: options are grouped by folders,
moved around and renamed. The group flag is switched on with a button on the
panel; a group has no settings of its own, it only holds options together.

For a leaf option the **"Option settings"** open on the right:

* **Key** — the name the storefront's modules read the option by;
* **Option description** — an explanation for whoever is going to fill it in;
* **Mask** — an input mask for when the value is typed by hand: an article
  number, a code, a date. Next to it are **"Mask check"** and **"Check result"**
  — a trial value goes into the first field, and the second shows at once what
  the store will get.

If a choice from a ready list is needed instead of free input, the values are
listed in the **"Fixed key set"** group: each with its own key and name.

The values themselves are filled in not here but in the sections the options
belong to — "Catalog" → "Additional Options", for example, sets them across the
whole tree of sections at once. The registry only declares which options exist
and of what kind; the storefront reads their values and changes the behaviour of
a section — how many products per page, which block to show and the like.

## Individual Filters

The tab declares the named SQL queries an employee switches on in the section's
"Advanced Query" alongside the ordinary conditions. The **"Placement"** list says
where the filter will show up: **products**, **orders** or **customers**.

The query is written against the **server's database** and must return a single
column — the identifiers of the rows to be selected. The table prefix is written
as `{DBNICK}_`, and the application substitutes the store's name. Customers with
no orders, for example:

```sql
SELECT c.id
  FROM {DBNICK}_client c
  LEFT JOIN {DBNICK}_orders_version ov
    ON ov.client_id = c.id
 WHERE ov.id IS NULL
```

**Parameters.** A colon with a name — `:DAYS_AGO` — makes a place for an
employee to put a value of their own: when the filter runs, the application asks
for them in a separate window. The names are arbitrary, and one and the same
name may be repeated in the query.

## Value Lists

Typing a numeric identifier by hand is inconvenient, so a parameter may offer a
choice instead. For that, a **value list** with an **Alias** of its own is
created on the second tab, and the parameter names that alias after the at sign:

```sql
WHERE ps.id = :STOCK@QUERY_STOCK
```

The query of a list is written against the **application's local database** —
the one the directories arrive into when the window opens — and must return
exactly two columns, **ID** and **NAME**. The employee sees the `NAME`, and the
`ID` is what goes into the filter. A name may be assembled from several, so that
identical values can be told apart:

```sql
SELECT ps.id AS ID,
       '[' || p.name || '] ' || ps.name AS NAME
  FROM cut_provider_stock ps
  JOIN cut_provider p
    ON ps.provider_id = p.id
 ORDER BY p.pos, ps.pos
```

The lists are shared: one alias may be called by any number of filters in any
sections. An error in the query or an unknown alias breaks nothing — the
parameter simply stays a field for manual input.

The table names of the local database differ from the server ones: the
directories arrive as copies with a `cut_` prefix, and the working copies of
products and attributes with a `u_` prefix. The structure can be looked at in
the "Workbench".

Details on writing modules and SQL queries are in the developer's guide.
