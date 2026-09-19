# engine_static_save

Saves an existing statics file: the body and, if it is passed, the bundle description.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the file's path |
| [`content`] | string | the file's new body |
| [`content_source`] | string | a file on this computer — an absolute path or a path from the store folder — instead of `content`: the body goes byte for byte, without passing through the correspondence |
| [`bundle`] | object | the bundle description: `param_info` and `unit_info`, the way `engine_static_load` gives them back |
| [`build`] | yes/no | raise the store's build number |

Either `content` or `content_source` is needed: a save without a body refuses, otherwise it would empty the file.

**`bundle`** writes the bundle description whole, from both fields, and rebuilds the group's bundles at once. Without `bundle` the file keeps its old description; both fields empty — the file leaves all the bundles. In `param_info` the bundles are listed separated by commas, the priority after a colon: `melbis.css: 10, print.css: 2`. The bundle's name is written with the extension; Latin letters, digits, a dot and an underscore are allowed in it, everything else is cut out. In detail — "[Static Build](../Dev/tpl_bundle.md)".

**`build`** raises the build number the page hangs onto its css and js: without it the visitors' browsers go on taking the old files from their cache.

What else saving does — rebuilds the group's bundles, writes the version, resets the module's cache — is described in "[Statics](engine_static.md)".

## The Answer

```json
{"path": "./../templates/default/statics/melbis/main.css", "bundle": false, "build": false,
 "warning": ""}
```

| Field | What it is |
|---|---|
| `path` | the saved file's path |
| `bundle` | whether the bundle description was passed |
| `build` | whether the build number was raised |
| `warning` | the keys that the build found in none of the bundle's `.psv` files, in one line: `Warning! Unknown: PRIMARY_HOWER (buttons.css);`. It lists the misses in all the group's bundles, whatever file is being saved. Empty if there are no misses or there was no build |

## Refusals

| Answer | When |
|---|---|
| `… is not a static file: statics lie in…` | the path lies outside the statics: the statics are `templates/<group>/statics/`, the files next to a module's templates except `.htm`, and the files in the site's root except php |
| `No such file: … SAVE changes a file, it does not create one…` | there is no such file on the server — it is created by `engine_static_add` |
| `Say content, or content_source with a path on this machine…` | neither `content` nor `content_source` is named |
| `Builds live in a template: … belongs to none of them.` | `bundle` was passed for a file outside a template group |
| `<file>.psv gives no array of keys back` | the bundle's `.psv` gave no array of keys back; the file is already saved |
| `PHP Runtime Exception` or `PHP Shutdown Exception`, then `File: …/<file>.psv : <line>` and the text of the error | an error in the code of the bundle's `.psv`; the file is already saved |
| `Can't include file to bundle: <path>` | a bundle description has stayed from a file that is not on the disk; the file is already saved |
| `path is required` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
