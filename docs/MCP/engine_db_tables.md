# engine_db_tables

Gives out the list of the database's tables or the makeup of one table: the columns with their types, the keys, the storage engine and whether there is a generator.

## The Right

"Direct access → Database → Get data structure" (`AGENT_ENGINE_DB_TABLES`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`name`] | string | the table: the real name `ms_store`, the bare `store` or the manifest form `_store`. Without it — the list of the tables |
| [`reload`] | yes/no | read the structure anew rather than from the session's memory |

The structure of the database is read once per session.

## The Answer

Without `name` — the real names of all the database's tables, the views and the tables with someone else's prefix among them:

```json
{"tables": ["ms_advert", "ms_advert_goods", "ms_store"]}
```

With `name`:

```json
{"name": "ms_topic", "engine": "MyISAM", "gen": true,
 "columns": {"columns": ["name", "type", "key"],
             "rows": [["id", "int unsigned", "PRI"], ["skey", "char(32)", ""],
                      ["tindex", "int unsigned", "MUL"]]}}
```

| Field | What it is |
|---|---|
| `name` | the real name of the table |
| `engine` | the storage engine: `MyISAM`, `InnoDB`, `MEMORY`; for a view — `VIEW` |
| `gen` | whether the table has a generator. If it has, the id of a new row is taken with the `generate` step; if it has not, the DBMS itself issues it through `AUTO_INCREMENT` |
| `columns` | the columns: `name`, `type` and `key` — `PRI` for the primary key, `UNI` for a unique index, `MUL` for an ordinary one, empty without an index |

What each field means is described in the program's `Engine\Tables\` folder.

## Refusals

| Answer | When |
|---|---|
| `Table "…" not found. Real names carry the DB nick …` | there is no such table; if the database has similar names, they are listed after `Similar:` |

The common refusals — "[Answers and Refusals](answers.md)".
