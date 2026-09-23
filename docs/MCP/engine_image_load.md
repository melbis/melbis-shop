# engine_image_load

Downloads a file from the images folder onto this computer. An image it shows in the answer, svg it gives out as text.

## The Right

"Direct access → Development → Read data" (`AGENT_ENGINE_DEV_READ`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the file's path: `templates/default/images/favicon.svg` |
| [`output`] | string | where to write it on this computer — an absolute path or a path from the store folder. Without it — the conversation folder and the same path as on the server |

The file is taken from the server every time and overwrites the local copy.

## The Answer

```json
{"path": "./../templates/default/images/favicon.svg",
 "file": "D:\\Melbis\\shop.example.com\\mcp\\claude-code\\prices-sept\\templates\\default\\images\\favicon.svg",
 "bytes": 846, "mime": "image/svg+xml"}
```

| Field | What it is |
|---|---|
| `path` | which file was taken |
| `file` | where it was written |
| `bytes` | the size of the file |
| `mime` | the type of the file as the server determined it |

After the data comes a second block — by the type of the file:

| Type | The second block |
|---|---|
| png, jpeg, gif, webp | the image |
| svg | the text of the file |
| a font, `.ico` and the rest | none: the file is only written onto the disk |

The agent application may shrink the image before handing it to the model; the file on the disk is an exact copy of the server's one.

## Refusals

| Answer | When |
|---|---|
| `No such file in the images tree: …` | the file is not among the image files in the map; if the map has similar names, they are listed after `Similar:` |
| `… is a folder, not a file.` | the path leads to a folder |
| `… is not a file of the images tree…` | the file lies outside the images folder |
| `File not found: … The project map may be stale - repeat engine_map_tree with reload.` | the map knows the file, but on the server it is gone already |
| `path is required…` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
