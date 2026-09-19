# engine_php_remove

Deletes a PHP file. For a module the manifest and the module's folders in all the template groups are deleted together with the file. There is no undo.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the path of the file to delete |

## The Answer

```json
{"path": "./../units/zz_demo.php"}
```

## Refusals

| Answer | When |
|---|---|
| `Unit not found: <name>` | there is no module with this name |
| `File not found: <path>` | there is no root script with this path |
| `That is the templates tree - engine_html_*, engine_static_* and engine_image_* speak there.` | a path in `templates/` |
| `A module path ends with .php: …`, `units/ keeps a flat list of modules…` | the module's path is written wrong |
| `path is required…` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
