# engine_template_remove

Deletes a template group with everything in it: the templates, the statics, the images and the descriptions of the bundles. There is no undo.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `template` | string | the name of the group to delete — one folder name |

The default group — the one named in `MELBIS_TEMPLATE` of the configuration — cannot be deleted: the storefront is built by it.

## The Answer

```json
{"template": "mobile"}
```

## Refusals

| Answer | When |
|---|---|
| `template is the name of a template group…` | the name has `/`, `\` or `..` in it, or the name is empty |
| `Template group not found: <group>` | there is no group with this name |
| `ACCESS_DENIED: <group> is the default template group of the store…` | this is the store's default group |

The common refusals — "[Answers and Refusals](answers.md)".
