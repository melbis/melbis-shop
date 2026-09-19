# engine_static_remove

Deletes a statics file together with its bundle description. There is no undo. The built bundles are not rebuilt.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the path of the file to delete |

## The Answer

```json
{"path": "./../templates/default/statics/melbis/extra.css", "bundled": 1}
```

| Field | What it is |
|---|---|
| `path` | the deleted file's path |
| `bundled` | `1` if the file had a bundle description, otherwise `0`. The built bundles still hold the deleted file — see "[Statics](engine_static.md)" |

## Refusals

| Answer | When |
|---|---|
| `… is not a static file: statics lie in…` | the path lies outside the statics: the statics are `templates/<group>/statics/`, the files next to a module's templates except `.htm`, and the files in the site's root except php |
| `File not found: <name>` | there is no such file |
| `path is required…` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
