# engine_php_save

Saves an existing PHP file: the code and, if it is passed, the module's manifest.

## The Right

"Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `path` | string | the file's path |
| [`content`] | string | the file's new body |
| [`content_source`] | string | a file on this computer — an absolute path or a path from the store folder — instead of `content`: the body goes byte for byte, without passing through the correspondence |
| [`manifest`] | object | the module's manifest with the keys that `engine_php_load` gives back |
| [`build`] | yes/no | raise the store's build number |

Either `content` or `content_source` is needed: a save with no body refuses, otherwise it would empty the file.

**`manifest`** rewrites the module's `.json` whole. A key that is not in the object gets an empty value or a zero, so the manifest is passed in full — the easiest way is to take it from `engine_php_load` and change what is needed. The list of tables `table_info` the engine gathers anew after the save by the module's queries; only the tables taken off (`log=0`) stay. In the `includes` list it is the other way round: the entries taken off (`=0`) are not saved. Without `manifest` only the code changes, the `.json` is not touched.

**`build`** raises the build number that a page hangs on its css and js — then the visitors' browsers will take the fresh statics. The module itself does not need this: its output the browser does not keep.

What the save does besides — rewrites the file whole, clears the cache, writes a version — is described in "[PHP Files](engine_php.md)".

## The Answer

```json
{"path": "./../units/melbis_cataloge.php", "manifest": false, "build": false, "warning": ""}
```

| Field | What it is |
|---|---|
| `path` | the path of the saved file |
| `manifest` | whether the `.json` was rewritten |
| `build` | whether the build number was raised |
| `warning` | the remarks of the module's check in one line after `Warning!`: `Undeclared` — a call of a function from a library that is not in `includes`; `Unused` — a library is included but not used; `One alias only` — the library publishes more than one short name; `Alias differs` — `use` calls the library by a name other than the one it publishes; `Namespace missing` — the library publishes a short name, and the file has no namespace. Empty if there are no remarks; a root script is not checked |

## Refusals

| Answer | When |
|---|---|
| `… is not a php file of the project…` | the path is not a php file of the project: modules lie in `units/`, root scripts in the site's root |
| `No such file: … SAVE changes a file, it does not create one…` | the file is not on the server — it is created by `engine_php_add` |
| `Say content, or content_source with a path on this machine…` | neither `content` nor `content_source` is named |
| `path is required` | `path` is not named |

The common refusals — "[Answers and Refusals](answers.md)".
