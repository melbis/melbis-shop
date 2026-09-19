# PHP Files

The `engine_php_*` section works with the store's PHP files of two kinds:

* **root scripts** — `index.php` and the other `.php` files in the site's root;
* **modules and libraries** — `units/<name>.php`.

The modules' templates — the `.htm` files in the template groups — are the `engine_html_*` section.

## What a Module Is Made Of

| Part | Where it lies | What changes it |
|---|---|---|
| code | `units/<module>.php` | `engine_php_load`, `engine_php_save` |
| manifest | `units/<module>.json` | the `manifest` parameter of `engine_php_save` |
| templates | `templates/<group>/units/<module>/*.htm` | `engine_html_*` |

`units/` is a flat list: a module lies right in it, with no subfolders.

**A library** is a module that other modules include; it has no templates of its own. The engine counts a module as a library when the second word of its name is `inc` or `include` (`melbis_inc_auth`), or when there is no underscore in the name. For such a module `engine_php_add` creates no folders in the template groups.

## The Manifest

`engine_php_load` gives the module's manifest back as an object with keys, and `engine_php_save` takes an object with the same keys. How modules and the cache work — "[Modular Scripts](../Dev/unit.md)" and "[Caching](../Dev/cache.md)".

| Key | What it sets |
|---|---|
| `unit_info` | the module's description for the hints in the program |
| `param_info` | the module's input parameters: `id: int, key: str`. For a library — the short names it publishes, the ones the engine checks `use` against |
| `table_info` | the tables the cache depends on: `store=1\|log=0`. The list the engine keeps by the module's queries, the developer only takes a table off — `log=0` |
| `includes` | the libraries included: `melbis_inc_logic.php=1` |
| `cache_on`, `cache_time` | the basic cache: whether it is on, the update pause in minutes |
| `lazy_load` | lazy loading |
| `ajax_load` | the module is an entry point: it can be run straight from a root script |
| `trick_on`, `trick_load_idx`, `trick_load_max`, `trick_comp_max`, `trick_time_max` | the Trick cache: whether it is on, the interval and the limit of the server's load, the limit of the page build time in seconds, the greatest age of the cache in minutes |
| `smart_on`, `smart_load_idx`, `smart_load_max`, `smart_comp_max` | the Smart cache: whether it is on, the interval and the load threshold, the page build time threshold in seconds |

In the `.json` file itself four fields are named otherwise: `unit_info` is `description`, `param_info` is `parameters`, `table_info` is `tables`, `ajax_load` is `entry_point`. The tools speak by the names from the table.

## What Saving Does

* **Rewrites the file whole.** This is not an edit of a part, it is a new version of the
  entire file.
* **Creates nothing.** There is no file — the save refuses; a new file is created by
  `engine_php_add`.
* **Clears the cache.** Saving a module takes its basic and Trick cache off in all the
  template groups, saving a library — the cache of every module that includes it. Together
  with the cache the Smart statistics of the same modules are taken off as well. A root
  script clears nothing.
* **Writes a version** of the file, if that is turned on in the program's Workbench
  settings on this computer.
