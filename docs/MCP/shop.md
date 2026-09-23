# The Storefront

The `shop` family works with the store from outside: it opens a storefront page the way a visitor gets it, runs the agent's own module on the storefront and copies the store's code onto this computer.

| Tool | What it does | Where it goes |
|---|---|---|
| [`shop_page`](shop_page.md) | opens a page or sends a form | the page's address on the site |
| [`shop_run`](shop_run.md) | runs the agent's module | `agent.php` in the site's root |
| [`shop_download`](shop_download.md) | copies the modules, the profiles or the database | the engine, like the `engine_*` tools |

`shop_page` and `shop_run` require no rights: they go to the site by the visitor's road, not to the engine. One exception: `shop_page` with `debug=true` asks the engine for the debugger's code and therefore requires the right "Get configuration". `shop_download` requires the right "Export → Store".

## The Rules

The family has rules of its own — `shop.md` in the `Engine\MCP` folder. The first call of any `shop_*` tool in a session refuses with them; after they are confirmed through [`session_rules_accept`](session_rules_accept.md) all the tools of the family work.

## The Page

`shop_page` requests a page the way a visitor's browser does, and saves the HTML in `pages\` of the conversation folder. Every read is a new visit: without cookies and without a session.

**The page may come from the cache.** Saving a module's file resets the cache of that module only — what clears itself is described in "[Configuration and Cache](engine_dev.md)". If an edit is not visible on the page, the reason is sometimes the cache of another module, the one that prints the changed place.

**This is markup, not a picture.** From the HTML one sees what has changed in the page's code; how it looks is seen only by a person in a browser.

**The debugger's report.** With `debug` the page is requested in the debugger's machine-readable view ("[Debugging Tools](../Dev/debug.md)"): the whole report is saved next to the page, and the main numbers and the page's modules come in the answer. The debugger's code the server takes from the store's configuration, so such a call requires the right "Direct access → Development → Get configuration". The code gets neither into the answer nor into the file names. If there is no code, the owner sets it: "Development → Installation", the "Debugger password" field. In the language your program runs in the captions may differ.

## The Form

With the field `post` the call becomes a form submission. That is how everything alive on the storefront works: the cart, a request, a review, an order. Unlike a read, a submission changes the store — rows of the cart appear, requests, letters to the manager, and in a working store a real order too. The storefront has no test mode.

**The cookies of the submissions are kept** while the MCP server runs, so submissions one after another are one visitor. A read of a page does not see these cookies: putting a product into the cart and looking at the cart are two submissions. In the demo store the cart answers to the fields `func` and `id`:

```json
{"url": "/?mod=melbis_basket", "post": {"func": "Plus", "id": 12}}
{"url": "/?mod=melbis_basket", "post": {"func": "Goods"}}
```

**The fields of a form** are named the way the page's markup writes them. A list of values goes as `name[]=…`, one per value — that is how a browser sends checkboxes and a multiple choice, and only so does PHP read the field as an array. The form's token, if there is one, does not take itself: the page is read first.

## The Agent's Module

`shop_run` runs a module through the entry point `agent.php` in the site's root and returns the storefront's answer. That is how the work is done for which there is no AI tool: a report across several tables, a check of the whole catalog, a one-off edit with a logic of its own.

The entry point runs a module only if:

1. the session key fits — it is given out by `session_connect`;
2. the module's name starts with `agent_`;
3. the module is in `units/`;
4. the module is an entry point: `ajax_load: 1` in the manifest, in the program — "Entry
   point module".

**The session key** is one per login: parallel sessions of the same user share it. The store's server keeps the key in memory for a day, every `session_connect` extends the term, and a restart of the server wipes the key.

**The parameters** go the same way as the fields of a form — a list comes as an array — and the module gets them in one serialized argument: `post: serial` is declared in the manifest, the values lie in `$mVars['post']` — see "[Modular Scripts](../Dev/unit.md)". The service `login`, `secret` and `mod` the entry point removes before running the module.

**The answer** passes through the parser like any page: templates, tags, cache. The whole of it is saved in `runs\<module>.txt` of the conversation folder. If the module has fallen, the message, the file and the line the engine writes into the storefront's log `core/log/melbis/front.log`.

**There are no rights for running.** A ready `agent_*` module can be run by anyone who has signed in to the store; to create or change a module — a login with the right "Direct access → Development → Modify data".

## shop_run and tool_run

Both run the store's PHP code, but these are different doors.

| | `shop_run` | `tool_run` |
|---|---|---|
| Where it goes | the storefront, `agent.php` in the site's root | the engine, `core/mcp.php` |
| What it runs | an `agent_*` entry point module | a command of an AI tool |
| How | through the parser: templates, cache | calls the command's function in the tool's module |
| What it returns | the module's answer as text | the command's JSON: `result`, `message`, `detail`, `tables`, `files` |
| Who allows it | the session key alone | the registry of AI tools and the command granted |
| What for | code of one's own: to try something out, a one-off job | the work the owner has provided for |

`tool_run` is the working call, `shop_run` is the workshop. A draft from the workshop often becomes an AI tool: "[AI Tools](tool.md)".

## A Copy of the Store

`shop_download` is the only tool of the family that takes data from the engine. It collects the archive the same way as "[Copying](../User/installation.md)" in the program, and unpacks it on this computer:

| `action` | What is in the copy |
|---|---|
| `modules` | `units/`, `templates/` and the files of the site's root |
| `profiles` | `profiles/` — the profiles of the program's users: the saved settings of the windows and the conditions of the queries |
| `database` | `dump.sql` — a dump of the store's whole database |

`config.json` never gets into a copy. There is no kind of copy with the element files from `files/`: they are downloaded by [`engine_files_load`](engine_files_load.md).

A copy is a snapshot at the moment of the call. An edit in it does not go to the server, and later edits of the store do not get into it: the store's files are changed by the `engine_*` tools.
