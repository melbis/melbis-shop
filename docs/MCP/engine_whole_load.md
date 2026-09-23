# engine_whole_load

Downloads one whole file of the store onto this computer, byte for byte and past the answer. It is needed for the server's logs — an ordinary load gives out only the last megabyte of a log — and for everything that is too large for the answer — the size is shown in advance by `size` in the map — or has to come without a single change.

## The Right

"Direct access → Development → Read data" (`AGENT_ENGINE_DEV_READ`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | a file of the store from any part: a module, statics, an image, a log |
| `output` | string | where to write it on this computer: an absolute path or a path from the store folder |

## How It Works

The store's server packs the file into an archive, `MelbisMCP.exe` unpacks it and writes it to `output` as is — the line endings do not change. The file's contents do not get into the answer, so the size is not limited by anything.

## The Answer

```json
{"path": "./../core/log/melbis/back.log",
 "file": "D:\\Melbis\\shop.example.com\\mcp\\claude-code\\prices-sept\\back.log",
 "bytes": 27090}
```

| Field | What it is |
|---|---|
| `path` | which file was taken |
| `file` | where it was written |
| `bytes` | the size of what was written |

## Refusals

| Answer | When |
|---|---|
| `File not found: …` | there is no file at this path — the path is checked against `engine_map_tree` |
| `ACCESS_DENIED: the store config is not a file for the agent` | the path leads to `config.json`; the parameters of the configuration are read by `engine_dev_config` |
| `ACCESS_DENIED: parameter path points outside the store files` | the path goes outside the store |
| `path and output are both required…` | `path` or `output` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
