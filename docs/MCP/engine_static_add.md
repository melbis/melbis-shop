# engine_static_add

Creates an empty statics file. The body and the bundle description are written by `engine_static_save`.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the new file's path: `templates/default/statics/melbis/extra.css` |

The folder must already be there. A folder in `statics/` is created by `engine_static_dir_add`; a file can be created in a module's folder in a template group as well.

## The Answer

```json
{"path": "./../templates/default/statics/melbis/extra.css"}
```

## Refusals

| Answer | When |
|---|---|
| `… is not a static file: statics lie in…` | the path lies outside the statics: the statics are `templates/<group>/statics/`, the files next to a module's templates except `.htm`, and the files in the site's root except php |
| `File already exists: <name>` | a file with this name is already there |
| `Folder not found: <folder>` | there is no such folder — it is created by `engine_static_dir_add` |
| `path is required…` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
