# The storefront: a page and your own module

Two MCP-tools, and both go **not to the engine but to the storefront itself** — by
the same road a visitor takes to the Store. There is no right in the registry behind
them, with one exception named under `debug=true` below.

## `shop_page` — see what came out

An ordinary page request. The HTML lands in `mcp\melbis\pages\`, and the answer
carries the status, the size, the title and either the head of the page or the
fragments that matched `find` (up to five of them, with a hundred characters of
surroundings on either side). `path` is the address part; a whole URL is accepted only
when it is this Store, anything else is refused.

Two warnings:

- **The page may come out of the cache.** A save drops the cache of the module the
  file belongs to, its views included — but nothing else. So if you changed a
  template that another module renders, or a css, and see no change, clear the cache
  before you go looking for a mistake in the markup (see "The cache" in
  [files.md](files.md)).
- **This is markup, not a picture.** How it **looks** is still for a person to judge.
  Do not say "it looks good now"; say what changed in the markup and ask them to have
  a look.

`debug=true` adds the report of the parser: the timings, the number of queries, the
state of the cache per module. The code of the debugger is taken from the
configuration of the Store, so there is nothing to switch on; the whole report is
saved beside the page as `<page>.report.json` and the headline numbers come into the
answer. If the Store has no such code set, the answer says so — that is a field the
owner fills in the Program.

Two things follow from where the code comes from. **This one call asks for the right
over the configuration**, and a refusal of it says so — the plain page asks for
nothing. And **the code never comes back to you**: the configuration answers it as
`<hidden>` like every other secret, the request carries it without showing it, and
the answer names the address without it. It is a password to the report of the live
storefront, so if the owner ever puts it into the chat themselves, do not repeat it
in your answers.

### `post` — send a form the way a visitor sends it

With a `post` field the same call becomes a POST: that is how everything alive on the
storefront works — the basket, a request, a review, placing an order. The fields come
from the markup of the block that does it; in the demo store the basket answers to
`func` and `id`:

```
shop_page(path='/?mod=melbis_basket', post={'func': 'Plus', 'id': 12})
shop_page(path='/?mod=melbis_basket', post={'func': 'Goods'})
```

**Only by agreement with a person.** Reading a page changes nothing in the Store; a
POST does: it puts rows in a basket, creates a request, emails the manager, and in a
store that takes orders it places a real one. The storefront has no test mode, and it
is the owner who will be clearing up afterwards. So: say in advance **what** you will
send and **where**, wait for agreement, and afterwards name what stayed in the Store.

Three points of mechanics:

- **The session is kept between POSTs** of one connection — which is why "put it in
  the basket" and then "show me the basket" see the same basket. A plain page read,
  on the contrary, is always a fresh visit with no cookies.
- **The token of a form**, if the page has one, a POST does not obtain by itself:
  read the page first and take it from there.
- **A field with several values** is a list: `{"kind": ["kNew", "kUsed"]}` goes out as
  `kind[]=kNew&kind[]=kUsed`, exactly as a browser sends it from checkboxes and a
  multiple select, and only that way does PHP read it as an array. A form goes no
  deeper than a flat list: an object, or a list inside a list, comes back as a refusal
  naming the field — the tool will not quietly send emptiness.

In the answer the line `post=` shows **the body of the request as it went**. If a
field there does not look the way you meant it, this is where to sort it out, not in
the module of the Store.

## `shop_run` — run your own module

Runs a module **on the storefront** and hands back what it printed. The module goes
in `mod`, its fields in `params`. This is the way to do work no AI-tool covers: a report over several tables, a check across the whole
catalogue, a one-off fix with logic of its own.

Three conditions:

1. **The name starts with `agent_`** — the entry point runs nothing else.
2. **The module exists** — create it with `engine_php_add`, write it with
   `engine_php_save`.
3. **It is saved as an entry point** — `ajax_load=1` in the manifest.

The `params` are handed over as one serialized argument, so the module declares
`post: serial` in its manifest and reads them as `$mVars[post]`. The names survive,
and so do a comma and any alphabet inside a value. The same values are in `$_POST`;
the service ones — `login`, `secret` and `mod` — are stripped by the entry point
before the module sees them.

The output is saved in `mcp\melbis\runs\` and comes back whole unless it is very
large.

## Not to be confused with `tool_run`

Both "run php of this Store", and they stand next to each other in the list.

| | `shop_run` | `tool_run` |
|---|---|---|
| the door | the storefront, the root `agent.php` | the engine, `core/mcp.php` |
| what it runs | an entry-point module | a function of a procedural unit |
| how | through the parser: templates, cache | by including the library and calling the function |
| what it returns | what the module **printed** | what the function **returned** |
| who decides whether it may | nobody, only the session key | the registry and the grant of the command |
| what for | you are trying out your own code | you are doing work the owner provided for |

Put simply: **`tool_run` is the working call, `shop_run` is your own workshop.** And
if the work had to be done here because there was no ready door for it, tell the
owner straight away, with the cost of what just happened: "If there is no AI-tool for
the job" in [tools.md](tools.md). A draft from the workshop is often the beginning of
an AI-tool.

## Taking the whole Store

**`shop_download`** is the third name on `shop_`, and the only one here that goes not
to the storefront but to the engine: it collects the code of the Store exactly as the
Program does when the owner backs the Store up (`FServerSetup`, tab `TSBackup`, button
`BBackup`), and unpacks it on **this** machine —
wherever `folder` names, by default into `mcp\melbis\shop`. What to take goes in
`action`:

- `modules` (the default) — `units/`, `templates/` and the root scripts;
- `profiles` — the picture profiles;
- `database` — a dump of the database; for a large Store that is a large file, and it
  travels whole. **Not a way to back the data up before an edit**: on a working Store
  it is the first thing to fall over, and a snapshot of the rows you are touching is
  what that job wants ([data.md](data.md)).

`config.json` never gets into the archive — not for the agent, and not into the
owner's backup.

The right to it is separate, as for any MCP-tool: if it was not granted, `shop_download`
will not even appear in your list (see [session.md](session.md)). Which reads the
other way round as well: if the tool is there, the owner opened that door
deliberately.

It is taken when there is **a lot** to read: comparing a dozen modules, searching
across all the code, checking against a backup. One file is still cheaper to read
with `engine_php_load` — an unpacked copy lives a life of its own and knows nothing of
later edits, so change things through `*_save` and not in it.
