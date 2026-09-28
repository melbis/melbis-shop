# repo_list

Gives back the repository sections open to the agent — with what may be done in each of them and the number of topics in it.

## The Right

"Repository → Get list" (`AGENT_REPO_LIST`).

## Parameters

None.

## The Answer

```json
{"repos": {"columns": ["id", "skey", "path", "descr", "kind_key", "topics", "may"],
           "rows": [[2, "", "Personal -> Administrator",
                     "Working notes of the administrator’s assistant: unfinished work, …",
                     "kPersonal", 3,
                     ["topic_list", "topic_create", "topic_update", "topic_remove",
                      "store_read", "store_write"]]]}}
```

| Column | What it is |
|---|---|
| `id` | the section's number — for `repo_read` and `repo_write` |
| `skey` | the section's key |
| `path` | the section's name together with the folders above it, through ` -> ` |
| `descr` | what the section is for |
| `kind_key` | the label: `kPersonal`, `kShared`, `kDefault` or the owner's own label |
| `topics` | how many topics the section has |
| `may` | the section's flags the agent has — the actions that will pass |

Sections go in the order of the tree, and there are no folders in the list. A section without a single flag is not shown — "[Repository](repo.md)". If no section is open, `rows` is empty, and the second block is `No section of the repository is open to you in this store.`

## Refusals

Only the common ones — "[Answers and Refusals](answers.md)".
