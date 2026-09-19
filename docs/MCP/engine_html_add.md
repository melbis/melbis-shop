# engine_html_add

Creates a module's template in a template group. If the module's folder does not yet exist in this group, it is created. The template's body is written by `engine_html_save`.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the path of the new template: `templates/<group>/units/<module>/<template>.htm` |

The template appears only in the group named. Added to the main group, it works in the rest as well: where there is no file with such a name, the engine takes the template from the main group. A copy of its own is needed by a group with a different layout.

## The Answer

```json
{"path": "./../templates/default/units/melbis_cataloge/extra.htm"}
```

## Refusals

| Answer | When |
|---|---|
| `path goes as templates/<group>/units/<module>/<template>.htm` | the path is wrong: not inside `templates/<group>/units/<module>/` or without `.htm` |
| `Template already exists: <name>` | a template with such a name already exists in this group |

The common refusals — "[Answers and Refusals](answers.md)".
