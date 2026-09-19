# engine_html_rename

Renames a module's template. The template stays in its folder: only the file name changes.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | what the template is called now |
| `new_path` | string | what it will be called — in the same group and module folder |

## The Answer

```json
{"path": "./../templates/default/units/melbis_cataloge/extra.htm",
 "new_path": "./../templates/default/units/melbis_cataloge/extra_new.htm"}
```

## Refusals

| Answer | When |
|---|---|
| `A template is renamed inside its module folder: only the file name changes.` | `new_path` leads into another group or into another module's folder |
| `path goes as templates/<group>/units/<module>/<template>.htm`, `new_path goes as …` | the path is wrong |
| `Template not found: <name>` | there is no template with such a name in the group |
| `Template already exists: <name>` | a template with the new name already exists in the group |

The common refusals — "[Answers and Refusals](answers.md)".
