# shop_module

Opens a web module of this store the way the program opens it in its windows: in the name of the session's user and with the window's fields. For checking and developing built-in and external modules — "[Web Modules](../Dev/web.md)".

## The Right

Not required: the right to the module is checked by the module itself by key, as in the program's window; with `debug` — "Direct access → Development → Get configuration" (`AGENT_ENGINE_DEV_CONFIG`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `url` | string | the address by which the program opens the module: `/?mod=melbis_web_sample`. A full address is accepted only if it is an address of this store |
| [`post`] | object | the window's fields — `order_id`, `store_id`, `topic_id` and others — and `func`, to call a function of the module |
| [`cookies`] | object | the cookies of the previous answer, name → value, to stay in the same module session. Without them — a new session |
| [`headers`] | object | the request headers — as in [`shop_page`](shop_page.md); without an `Accept-Language` of its own the program's language goes |
| [`find`] | string | show the places of the answer where this text occurs, instead of its beginning |
| [`debug`] | yes/no | add the debugger's report |

Every call is a POST. The server itself adds the session user's `login`, `stamp` and `sign` to the fields, like the program with the "User identification required" checkbox; they do not pass through the agent: if the module prints its variables, the signature in its answer and in the file is replaced with `<sign>`. Which fields each window sends — "[Web Modules](../Dev/web.md)". The value of a field of `post` is a string, a number or a yes/no; a list of values goes as `name[]=…`, one per value.

**Cookies — as in [`shop_page`](shop_page.md):** they come in the answer and go with the call, the server does not keep them. The module's session lasts while the agent passes the cookies on. Without them every call opens a new session, and the signature sign-in goes with every call.

**Language — as in the program's browser:** the server puts the program's language into `Accept-Language` unless `headers` sets its own, and the module learns it the same way as in a program window — "[Web Modules](../Dev/web.md)", the "The User's Language" section.

## The Answer

```json
{"url": "https://shop.example.com/?mod=melbis_web_sample", "debug": false,
 "post": "order_id=1&func=GetGoods", "cookies": {"MELBIS_SHOP": "p81sd…"}, "status": 200,
 "headers": {"Content-Type": "text/html; charset=UTF-8"},
 "bytes": 1840, "time": 71, "title": "",
 "file": "D:\\Melbis\\shop.example.com\\mcp\\claude-code\\prices-sept\\modules\\mod_melbis_web_sample.html",
 "report": null, "found": null}
```

Then as text — the first 2000 characters of the module's answer, if neither `find` nor `debug` is given.

The fields are as in [`shop_page`](shop_page.md); `post` is the fields the agent passed, without `login`, `stamp` and `sign`, or `null`. The module's answer is saved into `modules\` of the conversation folder, and the file name is taken from the address.

## Refusals

| Answer | When |
|---|---|
| `url is required…` | `url` is not given |
| `Only pages of this store can be fetched: …` | `url` holds a full address of another site |
| `The field [<name>] of post takes a value or a list of values…` | a field of `post` holds an object or a list inside a list |
| `cookies takes an object…`, `The cookie [<name>] cannot be sent…`, `headers takes an object…`, `The header [<name>] …` | the cookies or the headers were passed wrongly — as in [`shop_page`](shop_page.md) |
| `ACCESS_DENIED…` and `The code of the debugger is read through engine_dev_config…` | `debug` without the right "Get configuration" |
| `Cannot connect to …`, `HttpSendRequest failed, code …` | the site does not answer this computer |

A refusal of the module itself — for example, `Access denied` from a function without the right to the module — comes as an ordinary answer in the text. The common refusals — "[Answers and Refusals](answers.md)".
