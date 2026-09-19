# engine_dev_cache_clear

Clears the storefront's cache: one kind entirely or the cache of one module.

## The Right

"Direct access → Development → Clear cache" (`AGENT_ENGINE_DEV_CACHE_CLEAR`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `type` | string | what to clear: `cache`, `trick`, `smart`, `static`, `unit_cache`, `unit_trick` or `unit_smart` — see "[Configuration and Cache](engine_dev.md)" |
| [`unit`] | string | the module for `unit_*`: `melbis_cataloge`. Both `melbis_cataloge.php` and `units/melbis_cataloge.php` will do |

`unit` is required for `unit_cache`, `unit_trick` and `unit_smart` and is not accepted with the rest: those clear the whole store.

## The Answer

```json
{"type": "unit_cache", "unit": "melbis_cataloge"}
```

| Field | What it is |
|---|---|
| `type` | what has been cleared |
| `unit` | the module; `null` if the whole kind has been cleared |

## Refusals

| Answer | When |
|---|---|
| `type is one of: cache\|trick\|smart\|static\|unit_cache\|unit_trick\|unit_smart` | `type` is not named or is not from this list |
| `unit is required with unit_cache: …` | no module is named for `unit_*` |
| `unit goes only with unit_cache, unit_trick or unit_smart - … clears the whole site.` | a module is named together with the clearing of a whole kind |
| `No such module: <module>.php` | the store has no such module |

The common refusals — "[Answers and Refusals](answers.md)".
