# shop_download

Copies the store's modules, profiles or database onto this computer: the engine collects an archive, the MCP server unpacks it into a folder.

## The Right

"Export → Store" (`AGENT_SHOP_DOWNLOAD`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`action`] | string | what to copy: `modules` — by default, `profiles` or `database` |
| [`output`] | string | where to unpack — an absolute path or a path from the store folder. `shop` in the conversation folder by default |

| `action` | What is in the copy |
|---|---|
| `modules` | `units/`, `templates/` and the files of the site's root, except `config.json` |
| `profiles` | `profiles/` — the profiles of the program's users: the saved settings of the windows and the conditions of the queries |
| `database` | `dump.sql` — a dump of the store's whole database |

## The Answer

```json
{"action": "modules", "folder": "D:\\Melbis\\shop.example.com\\mcp\\claude-code\\prices-sept\\shop", "files": 240}
```

| Field | What it is |
|---|---|
| `action` | what has been copied |
| `folder` | where it has been unpacked |
| `files` | how many files have been written |

**The folder is not cleared.** The copy's files overwrite the ones of the same name, and the files that are already not in the store stay from the previous copy. A clean copy — into an empty folder.

**The database travels whole.** `database` collects a dump of the whole database in one request and passes the archive in one answer: with a big store this is long and heavy for the store's server. The size of the database is shown beforehand by the `select` step in [`engine_db_select`](engine_db_select.md):

```sql
SELECT TABLE_NAME, TABLE_ROWS, ROUND((DATA_LENGTH+INDEX_LENGTH)/1048576) AS mb
FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()
ORDER BY DATA_LENGTH+INDEX_LENGTH DESC
```

## Refusals

| Answer | When |
|---|---|
| `Unknown action [<action>]: modules, profiles or database` | an unknown value of `action` |

The common refusals — "[Answers and Refusals](answers.md)".
