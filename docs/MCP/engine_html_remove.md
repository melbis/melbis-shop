# engine_html_remove

Deletes a module's template from a template group. There is no undo.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the path of the template to delete |

## The Answer

```json
{"path": "./../templates/default/units/melbis_cataloge/extra.htm"}
```

## Refusals

| Answer | When |
|---|---|
| `Template not found: <template>` | there is no such template |
| `path goes as templates/<group>/units/<module>/<template>.htm` | the path is wrong |

The common refusals — "[Answers and Refusals](answers.md)".
