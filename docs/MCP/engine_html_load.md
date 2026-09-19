# engine_html_load

Loads a module's template.

## The Right

"Direct access → Development → Read data" (`AGENT_ENGINE_DEV_READ`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the template's path: `templates/default/units/melbis_cataloge/main.htm` |

## The Answer

Two blocks: the data in JSON, and then the template's body as text — as it is, without escaping.

```json
{"path": "./../templates/default/units/melbis_cataloge/main.htm"}
```

An empty template comes without the text block.

## Refusals

| Answer | When |
|---|---|
| `File not found: … Check the path against engine_map_tree.` | there is no template at this path |
| `path is required…` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
