# engine_html_save

Saves an existing module template.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the template's path |
| [`content`] | string | the template's new body |
| [`content_source`] | string | a file on this computer — an absolute path or a path from the store folder — instead of `content`: the body goes byte for byte, without passing through the correspondence |
| [`build`] | yes/no | raise the store's build number |

Either `content` or `content_source` is needed: a save without a body refuses, otherwise it would empty the template.

**`build`** is needed only if the same save is to give visitors fresh css and js: the template itself the browser does not keep.

What else the save does — rewrites the file whole, resets the module's cache, writes a version — is described in "[Templates](engine_html.md)".

## The Answer

```json
{"path": "./../templates/default/units/melbis_cataloge/main.htm", "build": false}
```

| Field | What it is |
|---|---|
| `path` | the path of the saved template |
| `build` | whether the build number was raised |

## Refusals

| Answer | When |
|---|---|
| `… is not a template…` | the path is not a template: templates lie in `templates/<group>/units/<module>/` |
| `No such file: … SAVE changes a file, it does not create one…` | the template is not on the server — it is created by `engine_html_add` |
| `Say content, or content_source with a path on this machine…` | neither `content` nor `content_source` is named |
| `path is required` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
