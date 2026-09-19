# engine_static_dir_add

Creates a folder in a template group's statics.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the whole path of the new folder: `templates/default/statics/banners` |

The parent folder must already be there: it is the group's `statics/` or a folder inside it.

## The Answer

```json
{"path": "./../templates/default/statics/banners"}
```

## Refusals

| Answer | When |
|---|---|
| `… already exists.` | the folder is already there |
| `No such folder in the statics tree: … engine_map_tree lists the folders under dirs.` | the parent folder is not among the statics folders |
| `… is not a folder of the statics tree.` | the parent path lies outside the group's statics folder |
| `The last part of path must be a plain name.` | the folder's name holds `..` |
| `path is required…` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
