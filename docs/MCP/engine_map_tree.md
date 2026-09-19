# engine_map_tree

Everything the store is made of: the modules, the folders, the files and the server's logs — in one answer, in four tables, or as a file on this computer. Getting to know an unfamiliar store starts with it.

## The Right

"Direct access → Development → Read data" (`AGENT_ENGINE_DEV_READ`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`path`] | string | keep only the entries whose path contains this string, `templates/default/images` for example |
| [`output`] | string | a path on this computer — absolute or from the store folder: the map's rows are written there instead of into the answer |
| [`reload`] | yes/no | read the tree from the store anew |

## The Answer

```json
{
  "units": {"columns": ["path", "library", "cache", "entry", "lazy", "tables", "includes", "size"],
            "rows": [["./../units/melbis_cataloge.php", false, 1, 0, 0, "store=1|topic=1", "", 1142]]},
  "dirs":  {"columns": ["path", "branch"],
            "rows": [["./../templates/default/statics/base", "static"]]},
  "files": {"columns": ["path", "branch", "type", "size"],
            "rows": [["./../templates/default/statics/base/app.css", "static", "css", 5120]]},
  "logs":  {"columns": ["path", "time", "size"],
            "rows": [["./../core/log/melbis/back.log", "2026-09-15 00:42:15", 27090]]}
}
```

### units — the modules

| Column | What it is |
|---|---|
| `path` | the module's file |
| `library` | a library module: other modules include it, and it has no templates of its own. The engine counts as a library a module whose second word of the name is `inc` or `include`, or whose name has no underscore |
| `cache` | whether the module's base cache is on: `1` or `0` |
| `entry` | the module is an entry point: it can be run directly from a root script |
| `lazy` | lazy loading |
| `tables` | the tables the module's cache depends on: `store=1\|log=0` |
| `includes` | the included libraries: `melbis_inc_auth.php=1` |
| `size` | the size of the module's file in bytes |

### dirs and files — the folders and the files

| Column | What it is |
|---|---|
| `path` | the path of the folder or the file |
| `branch` | the part of the store — see "[The Engine](engine.md)" |
| `type` | files only: the extension in lower case — `css`, `htm`, `woff2`, and `htaccess` for `.htaccess`; empty if there is no extension |
| `size` | files only: the size in bytes |

### logs — the server's logs

| Column | What it is |
|---|---|
| `path` | the log file in `core/log/` |
| `time` | the time of the last record |
| `size` | the size in bytes |

### What Gets into the Tree

* the modules and libraries `units/*.php`, and among the folders — the groups of
  ordinary modules, gathered by the start of the name (`units/<vendor>_<group>`).
  There are no such folders on the disk, `units/` keeps a flat list of files;
* the files of the site's root, except `config.json` and `favicon.ico`;
* the statics, the images and the templates of every group together with their
  folders. The module's folder `templates/<group>/units/<module>` is listed for
  every ordinary module in every group, even if it is not there on the disk;
* the logs of `core/log/`, except the compressed `.gz` archives.

### With `output`

The map's rows are written into a file, one per line: an object with the columns of its own table and the table's name in `table`. `path` narrows the file down too. In the answer — the file's path and the number of rows of every table:

```json
{"file": "D:\\Melbis\\shop.example.com\\mcp\\claude-code\\map.jsonl",
 "rows": {"units": 57, "dirs": 61, "files": 108, "logs": 3}}
```

A line of the file:

```
{"table":"units","path":"./../units/agent_claude_sample.php","library":false,"cache":0,"entry":1,"lazy":0,"tables":"","includes":"","size":982}
```

The file is overwritten at every call with the same `output`.

### The Text

Comes only if the tables are empty: either no entry matched `path`, or the store's tree is empty — that should not happen, and it is a reason to turn to the owner.

## Refusals

| Answer | When |
|---|---|
| `Cannot write <file>: …`, `Cannot create folder …` | the file from `output` could not be written |

The common refusals — "[Answers and Refusals](answers.md)".
