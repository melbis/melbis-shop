# engine_php_load

Loads a PHP file — a module, a library or a root script — together with the module's manifest.

## The Right

"Direct access → Development → Read data" (`AGENT_ENGINE_DEV_READ`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the file's path: `units/melbis_cataloge.php`, `index.php` |

## The Answer

Two blocks: the data in JSON, then the file's body as text — as it is, with no escaping.

```json
{
  "path": "./../units/melbis_cataloge.php",
  "manifest": {"unit_info": "Cataloge menu", "param_info": "", "table_info": "topic=1",
               "includes": "melbis_inc_web_topic.php=1", "cache_on": 0, "cache_time": 2,
               "lazy_load": 0, "ajax_load": 0,
               "trick_on": 0, "trick_load_idx": 0, "trick_load_max": 0,
               "trick_comp_max": 0, "trick_time_max": 0,
               "smart_on": 0, "smart_load_idx": 0, "smart_load_max": 0, "smart_comp_max": 0}
}
```

| Field | What it is |
|---|---|
| `path` | the file's path |
| `manifest` | the module's manifest: the text keys as strings, the flags and the limits as numbers — see "[PHP Files](engine_php.md)". For a root script `null` |

An empty file comes without the text block.

## Refusals

| Answer | When |
|---|---|
| `File not found: … Check the path against engine_map_tree.` | there is no file at this path |
| `path is required…` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
