# engine_db_locks

Shows which tables are taken into work: by whom, by what operation and since when.

## The Right

"Direct access → Database → Get lock list" (`AGENT_ENGINE_DB_LOCKS`).

## Parameters

None.

## The Answer

```json
{"locks": {"columns": ["table", "user", "command", "operation", "since"],
           "rows": [["store", "Administrator", "MELBIS_SYSTEM", "", "2026-09-17 10:12:40"]]}}
```

| Column | What it is |
|---|---|
| `table` | the table without the prefix |
| `user` | who holds it; `MELBIS SYSTEM` — the storefront |
| `command` | the operation's command; `MELBIS_SYSTEM` — a lock outside an operation |
| `operation` | the path of the operation in the program's tree of operations; empty outside an operation |
| `since` | since when |

The list takes in every lock of one's own login and every lock of the shared tables; the personal tables of other users (`u_*`) do not get into it.

How to read a row:

| `user` | `command` | What it is |
|---|---|---|
| `MELBIS SYSTEM` | `MELBIS_SYSTEM` | the storefront has taken the table for the time of a write; a table that the `clear_dependent` step is cleaning up looks the same |
| a person | `MELBIS_SYSTEM` | a lock outside an operation: a manual one from the program, a pool's `lock` step or the adding of an element file — or a hanging one |
| a person | a command | an ordinary operation: the whole group of its tables is taken |

**One's own locks cannot be told apart by the name.** The agent signs in under the login of the person at the computer, so its lock and the lock of an open program window stand under one name. They differ in the operation and the time.

## Refusals

Only the common ones — "[Answers and Refusals](answers.md)".
