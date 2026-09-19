# Configuration and Cache

The `engine_dev_*` section reads the store's parameters from `config.json` and clears the storefront's cache.

## Configuration

`engine_dev_config` gives out the parameters of `config.json`: the connection to the database, the localization, the parameters of the platform, of debugging and of backup. What each of them means — "[Configuration](../Dev/config.md)".

**The secrets are hidden.** The platform counts as a secret every key whose name does not begin with `MELBIS_` — the keys of payment systems, the tokens of external services. Their values come as `<hidden>`, and so do the database password and the debugger code. The names of the secrets are visible: the code addresses them by name.

**There is no writing of the configuration among the tools.** The engine rewrites `config.json` as a whole and gives the secrets out hidden, so reading, editing and writing would wipe them. The parameters and the secrets are changed by the owner in the program: "Development → Installation". The `config.json` file itself the file tools do not open. In the language your program runs in the captions may differ.

The configuration is read once per session; `reload` reads it anew.

## Cache

What kinds of cache there are is described in "[Caching](../Dev/cache.md)". `engine_dev_cache_clear` clears four of them — entirely for the whole store or for one module:

| `type` | What it clears |
|---|---|
| `cache` | the basic cache of all the modules |
| `trick` | the Trick cache of all the modules |
| `smart` | the accumulated statistics of the Smart cache of all the modules |
| `static` | the static query cache in the server's memory |
| `unit_cache` | the basic cache of one module in all the template groups |
| `unit_trick` | the Trick cache of one module in all the template groups |
| `unit_smart` | the statistics of the Smart cache of one module |

## What Clears Itself

**Saving a module, its template or its statics** — any file from the module's folder — resets the basic and the Trick cache of that module and its Smart cache statistics in all the template groups. Saving a library does the same for every module that includes it. A root script, the statics from `statics/` and the images do not touch the modules' cache.

**A change of the tables** the modules' cache follows by the change timestamp of those tables the module reads from. The program marks its own saves itself, and a write through `engine_db_execute` — by its `change` step.

That is why clearing by hand is rarely needed: after an edit the cache did not notice, or for a full tidy-up.
