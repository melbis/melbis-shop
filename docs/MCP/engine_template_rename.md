# engine_template_rename

Renames a template group together with everything that lies in it.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `template` | string | what the group is called now |
| `new_template` | string | what it will be called |

Both values are folder names, not paths.

The default group — the one named in `MELBIS_TEMPLATE` of the configuration — cannot be renamed: the storefront is built by it.

## The Answer

```json
{"template": "mobile", "new_template": "mobile_old"}
```

## Refusals

| Answer | When |
|---|---|
| `template is the name of a template group…`, `new_template is the name of a template group…` | the name has `/`, `\` or `..` in it, or the name is empty |
| `Template group not found: <group>` | there is no group with this name |
| `Template group already exists: <group>` | a group with the new name already exists |
| `ACCESS_DENIED: <group> is the default template group of the store…` | this is the store's default group |

The common refusals — "[Answers and Refusals](answers.md)".
