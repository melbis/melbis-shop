# engine_image_dir_add

Creates a folder inside the images folder of a template group.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the whole path of the new folder: `templates/default/images/banners` |

The parent folder must already be there: it is the group's `images/` or a folder inside it.

## The Answer

```json
{"path": "./../templates/default/images/banners"}
```

## Refusals

| Answer | When |
|---|---|
| `… already exists.` | the folder is already there |
| `No such folder in the images tree: … engine_map_tree lists the folders under dirs.` | the parent folder is not among the images folders |
| `… is not a folder of the images tree.` | the parent path lies outside the group's images folder |
| `The last part of path must be a plain name.` | the folder's name contains `..` |
| `path is required…` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
