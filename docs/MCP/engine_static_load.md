# engine_static_load

Loads a statics file together with its bundle description. Reads the server's logs as well.

## The Right

"Direct access → Development → Read data" (`AGENT_ENGINE_DEV_READ`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the file's path: `templates/default/statics/melbis/main.css`, `core/log/melbis/back.log` |

## The Answer

Two blocks: the data in JSON, then the file's body as text — as is, without escaping.

```json
{"path": "./../templates/default/statics/melbis/main.css",
 "bundle": {"param_info": "melbis.css: 1", "unit_info": ""}}
```

| Field | What it is |
|---|---|
| `path` | the file's path |
| `bundle` | the bundle description in the shape `engine_static_save` takes it: `param_info` — the bundles with their priorities, `unit_info` — the file description. `null` if the file has neither bundles nor a description |

From a log larger than a megabyte only the last megabyte comes, and the body begins with the line `=== TRUNCATED: showing the last 1 MB of … MB, the older part is not loaded ===`. The whole log is downloaded by `engine_whole_load`.

An empty file comes without a text block.

## Refusals

| Answer | When |
|---|---|
| `File not found: … Check the path against engine_map_tree.` | there is no file at this path |
| `path is required…` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
