# engine_image_add

Uploads a file into the images folder of a template group: an image, a font, an icon. An upload onto an occupied path replaces the file.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the whole path of the file on the server, the folder and the name: `templates/default/images/logo.png` |
| [`content_source`] | string | a file on this computer — an absolute path or a path from the store folder. It goes byte for byte |
| [`content`] | string | the file's body as text — for text formats such as svg — instead of `content_source` |
| [`overwrite`] | yes/no | replace the file that is already at this path |

Either `content_source` or `content` is needed. The folder must already be there — it is created by `engine_image_dir_add`.

**`overwrite`** is needed to replace an existing file. Image files have no versions, and a replaced file cannot be brought back, so an upload onto an occupied path without `overwrite=true` refuses.

The size of a request is limited by the store's server: in a standard installation it is 64 MB. A bigger request the server itself turns back, and the refusal `The server of the store refused the request as too large (HTTP 413)…` comes.

## The Answer

```json
{"path": "./../templates/default/images/logo.png", "bytes": 5430, "replaced": false}
```

| Field | What it is |
|---|---|
| `path` | the file's path on the server |
| `bytes` | how much was uploaded |
| `replaced` | whether a file that was already there was replaced |

## Refusals

| Answer | When |
|---|---|
| `… already exists, and uploading would overwrite it for good…` | there is already a file at this path, and `overwrite=true` is not named |
| `Give content_source - a path on this machine - or content…` | neither `content_source` nor `content` is named |
| `No such file: <path>` | the file named in `content_source` is not on this computer |
| `No such folder in the images tree: … engine_map_tree lists the folders under dirs.` | the folder is not among the images folders |
| `… is not a folder of the images tree.` | the path lies outside the group's images folder |
| `The last part of path must be a plain file name.` | the file's name contains `..` |
| `path is required…` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
