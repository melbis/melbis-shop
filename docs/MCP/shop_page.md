# shop_page

Opens a storefront page of this store the way a visitor gets it, and saves the HTML on this computer. With the field `post` it sends a form.

## The Right

Not required; with `debug` — "Direct access → Development → Get configuration" (`AGENT_ENGINE_DEV_CONFIG`): the debugger's code is read from the store's configuration.

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`url`] | string | the page's address from the site's root, with the query string: `/catalog/`, `/?mod=melbis_basket`. `/` by default. A full address is accepted only if it is an address of this store |
| [`find`] | string | show the places of the page where this text occurs, instead of its beginning |
| [`debug`] | yes/no | add the debugger's report |
| [`post`] | object | the fields of a form — the call becomes a form submission |
| [`cookies`] | object | the visitor's cookies, name → value: the ones that came in the previous answer, to carry on the same visitor. Without them — a new visitor |
| [`headers`] | object | the request headers, name → value: `Sec-CH-UA-Mobile`, `Accept-Language`, `Referer` and others. `User-Agent` goes after the `MelbisShop-MCP` mark and the access code by which the proxy lets the request through |

The value of a field of `post` is a string, a number or a yes/no; a list of values goes as `name[]=…`, one per value, an empty list does not go at all. What a submission changes and how to lead one visitor through several calls — "[The Storefront](shop.md)".

## The Answer

```json
{"url": "https://shop.example.com/", "debug": false, "post": null,
 "cookies": {"MELBIS_SHOP": "k3fq9…"}, "status": 200,
 "headers": {"Server": "nginx", "Content-Type": "text/html; charset=UTF-8",
             "Set-Cookie": "MELBIS_SHOP=k3fq9…; path=/; secure; HttpOnly; SameSite=Lax"},
 "bytes": 9742, "time": 63, "title": "Home page",
 "file": "D:\\Melbis\\shop.example.com\\mcp\\claude-code\\prices-sept\\pages\\index.html",
 "report": null, "found": null}
```

Then as text — the first 2000 characters of the page, if neither `find` nor `debug` is named.

| Field | What it is |
|---|---|
| `url` | the page's address, without the debugger's code |
| `debug` | whether the report was asked for |
| `post` | the form's body in the shape in which it went: `func=Plus&id=12`; `null` on a read |
| `cookies` | the cookies after the request: the ones passed in the call and the ones written by the site. The ones the site deleted are not here |
| `status` | the HTTP answer's code. An error page is not a refusal: it is saved and comes the same way |
| `headers` | the site's answer headers, name → value; repeated ones, usually `Set-Cookie`, go separated by a line break. Windows follows a redirect by itself, so these are the headers of the final page |
| `bytes` | the page's size |
| `time` | how long the request took, ms |
| `title` | the text of `<title>` |
| `file` | where the page is saved |
| `report` | the debugger's report; `null` without `debug` |
| `found` | the found places; `null` without `find` |

**The page's file.** The name is taken from the address: letters, digits, `-` and `_` stay, the rest becomes `_`. `/` is saved as `index.html`, `/catalog/?page=2` — as `catalog_page_2.html`. Every call with the same address overwrites the file.

### The Report

```json
"report": {"file": "D:\\Melbis\\shop.example.com\\mcp\\claude-code\\prices-sept\\pages\\index.report.json",
           "compile": 0.0099, "sql_count": 6, "sql_time": 0.0057, "cache": true,
           "template": "default", "lang": "ru",
           "units": {"columns": ["unit", "cache_on", "cache_allow", "queries", "query_time"],
                     "rows": [["melbis_base_page", false, false, 1, 0.0008],
                              ["melbis_base_head", false, false, null, null]]}}
```

| Field | What it is |
|---|---|
| `file` | the whole report — the JSON that the debugger's panel downloads with the "Download Reports" button |
| `compile` | the page's generation time, s |
| `sql_count`, `sql_time` | the number of the page's queries and their total time, s |
| `cache` | whether caching is on in the store |
| `template`, `lang` | the page's template group and language |
| `units` | the page's modules: `cache_on` — the module has the cache on; `cache_allow` — the module's cache works: it is on in the store, and in the module, and the module tracks tables; `queries` and `query_time` — its queries and their time, `null` for a module without queries |

What the report's numbers mean and how to parse it — "[Debugging Tools](../Dev/debug.md)". If the report's file could not be written, `report` comes `null`, and the answer's text starts with `The report could not be saved:` and the reason.

### What Was Found

```json
"found": [{"at": 873, "text": "…/statics/bundle.melbis.css?25\">…"}]
```

Up to five places. `at` is the position of the match in the page's file in bytes, counting from 1; `text` is the match and about a hundred bytes before and after. The case is not taken into account for Latin letters. An empty list — the text is not on the page.

## Refusals

| Answer | When |
|---|---|
| `Only pages of this store can be fetched: …` | `url` holds a full address of another site |
| `ACCESS_DENIED…` and `The code of the debugger is read through engine_dev_config…` | `debug` without the right "Get configuration" |
| `The debugger has no code in this store…` | the debugger's password is not set in the store: "Development → Installation", the "Debugger password" field |
| `The field [<name>] of post takes a value or a list of values…` | an object or a list inside a list in the field `post` |
| `cookies takes an object…` | `cookies` is not an object |
| `The cookie [<name>] cannot be sent…` | the cookie's value is an object or a list; the name holds `=`, `;`, a comma or a space; the value holds `;` or a line break |
| `headers takes an object…` | `headers` is not an object |
| `The header [<name>] cannot be sent…` | the header's value is an object, a list or text with a line break; the name holds a colon or a space |
| `The header [<name>] is the request's own…` | `headers` holds `Cookie`, `Host`, `Content-Type` or `Content-Length`: cookies go through `cookies`, the rest is set by the request itself |
| `Cannot connect to …`, `HttpSendRequest failed, code …` | the site does not answer this computer |

In the language your program runs in the captions may differ.

The common refusals — "[Answers and Refusals](answers.md)".
