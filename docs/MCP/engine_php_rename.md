# engine_php_rename

Renames a PHP file. For a module the manifest and the module's folders in all the template groups are renamed together with the file. A library has no folders: instead of them the engine changes its name in the `includes` of the manifests of every module that includes it.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | what the file is called now |
| `new_path` | string | what it will be called |

Both paths are of one kind: a module stays in `units/`, a root script in the root.

## The Answer

```json
{"path": "./../units/zz_demo.php", "new_path": "./../units/zz_demo_new.php"}
```

## Refusals

| Answer | When |
|---|---|
| `Unit not found: <name>` | there is no module with this name |
| `Unit already exists: <name>` | a module with the new name already exists |
| `File not found: <path>`, `File already exists: <path>` | a root script: the old one is not there or the new one already is |
| `That is the templates tree - engine_html_*, engine_static_* and engine_image_* speak there.` | a path in `templates/` |
| `Both paths must be of one kind…` | one path is in `units/`, the other is not |
| `A module path ends with .php: …`, `units/ keeps a flat list of modules…` | the module's path is written wrong |
| `path is required…`, `new_path is required…` | one of the paths is not named |

The common refusals — "[Answers and Refusals](answers.md)".
