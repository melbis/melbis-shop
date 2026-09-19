# engine_search

Where a string occurs in the store's files: a tag, the name of a function, a class in the markup, a piece of text from the storefront. Faster than reading module after module.

## The Right

"Direct access → Development → Read data" (`AGENT_ENGINE_DEV_READ`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `keyword` | string | what to look for |
| [`sensitive`] | yes/no | take the case into account; no by default |

## Where It Searches

| Searches | Does not search |
|---|---|
| the modules and libraries `units/*.php` | the modules' `.json` manifests |
| all the template files `templates/*/units/<module>/` | `core/` — the engine and the logs |
| `.php` and `.htaccess` in the site's root | images and fonts |
| the `css`, `js` and `psv` statics of every template group, including the built `bundle.*` | |

An empty answer means "not in these places".

## The Answer

```json
{
  "keyword": "melbis_cataloge",
  "sensitive": false,
  "files": [
    {"path": "./../templates/default/units/melbis_base_header/main.htm",
     "lines": ["{MELBIS:melbis_cataloge()}"]}
  ]
}
```

| Field | What it is |
|---|---|
| `keyword`, `sensitive` | what was looked for and how |
| `files` | the files where a match was found: `path` — the path, `lines` — the found lines whole, without the spaces at the edges |

### The Text

Comes only if nothing was found: `Nothing found for "<word>".` Where the search does not look is said above.

## Refusals

| Answer | When |
|---|---|
| `keyword is required` | an empty `keyword` |

The common refusals — "[Answers and Refusals](answers.md)".
