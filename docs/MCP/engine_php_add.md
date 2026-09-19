# engine_php_add

Creates a PHP file: a module in `units/` or a root script.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | `units/<name>.php` — a module; any other path — a root script next to `index.php` |

## What Is Created

* **A module** — the file `units/<name>.php`, the manifest `units/<name>.json` and, in
  every template group, the module's folder `templates/<group>/units/<name>/` with a
  `main.htm` file. For a library — the second word of the name is `inc` or `include`, or
  the name has no underscore — no folders are created in the template groups.
* **A root script** — one file.

A folder in a template group left over from a deleted module with the same name the creation wipes silently. A name that has already lived in the store is worth checking against `engine_map_tree`.

The file is created empty: the body and the manifest are written by `engine_php_save`.

## The Answer

```json
{"path": "./../units/zz_demo.php", "module": true,
 "folders": ["./../templates/default/units/zz_demo"]}
```

| Field | What it is |
|---|---|
| `path` | the path of the created file |
| `module` | a module was created (`true`) or a root script (`false`) |
| `folders` | the module's folders in the template groups that appeared together with it; empty for a library and for a root script |

## Refusals

| Answer | When |
|---|---|
| `A module path ends with .php: …` | a path in `units/` without `.php` |
| `units/ keeps a flat list of modules: units/<name>.php` | a path with a subfolder inside `units/` |
| `That is the templates tree - engine_html_*, engine_static_* and engine_image_* speak there.` | a path in `templates/` |
| `Unit already exists: <name>` | a module or a library with this name already exists |
| `File already exists: <path>` | a root script with this path already exists |
| `path is required…` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
