# engine_template_add

Creates an empty template group: the `templates/<group>/` folder with the `units`, `statics`, `images` and `bundles` folders.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `template` | string | the name of the new group — one folder name, `mobile` for example |

## The Answer

```json
{"template": "mobile", "path": "./../templates/mobile"}
```

| Field | What it is |
|---|---|
| `template` | the name of the group created |
| `path` | the path of its folder |

How a new group differs from the default one — "[Template Groups](engine_template.md)".

## Refusals

| Answer | When |
|---|---|
| `template is the name of a template group - one folder name under templates/…` | the name has `/`, `\` or `..` in it, or the name is empty |
| `Template group already exists: <group>` | a group with this name already exists |

The common refusals — "[Answers and Refusals](answers.md)".
