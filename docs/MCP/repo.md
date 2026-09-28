# Repository

The `repo` family is the store's repository: sections in which the employees' AI assistants keep topics and data for each other. The [memory](memory.md) holds what is important and lasting and is read in every session; the repository holds working data that is read when there is a reason: unfinished work, intermediate results, sites taken apart, ready scripts.

## The Rules

The family has rules of its own — `repo.md` in the `Engine\MCP` folder. The first call of any `repo_*` tool in a session refuses with them; after they are confirmed through [`session_rules_accept`](session_rules_accept.md), all the tools of the family work. What goes into the memory, what into the repository and what into the conversation folder is in the session rules: they come with the answer of `session_connect`.

## Sections

Sections are set up by the administrator in the program; the agent only reads them. Sections make up a tree, and a folder only groups sections — there are no topics in it.

| Field | What it is |
|---|---|
| `id` | the section's number — the other commands refer to the section by it |
| `skey` | the key the administrator gave |
| `name` | the name; `path` in the answer is the same together with the folders above it |
| `descr` | what the section is for: by it the assistant decides what to put here |
| `kind_key` | a label from the registry — the `key_value` rows with the code `AGENT_REPO_KIND_KEY` |

| Label | What it is |
|---|---|
| `kPersonal` | a personal section: the working notes of one person's assistant |
| `kShared` | a shared section: what the assistants of all employees need |
| `kDefault` | no label |

A label restricts nothing: who sees a section is decided by its rights. The administrator sets up a personal section for every employee separately, with rights for that employee alone. The assistant finds its section by the description and the rights, and a memory note can set a scheme — for example, `kPersonal` with an `skey` equal to the user's number.

## Section Rights

A row of a section's rights gives a person or a group — main or additional — a set of flags, and every flag lets through the action of the same name:

| Flag | Action |
|---|---|
| `topic_list` | `topic_list` — the list of topics |
| `topic_create` | `topic_create` — a new topic |
| `topic_update` | `topic_update` — editing any topic of the section |
| `topic_remove` | `topic_remove` — removing any topic with all its records |
| `store_read` | `store_list` and `store_read` — the history and the records |
| `store_write` | `store_write` — a new record |

The rows of one section add up. A section without a single flag is not visible to the agent. Whoever may save the "AI Components" window (`PUT_AGENT_OPTION`) has all the sections open with all the flags — the administrator included.

The tools themselves are opened by the rights of the "Repository" branch — "[Sign-in, Rights, License](access.md)"; the section's flag then decides which action passes inside.

## Topics and Records

A topic is created by the assistant. A topic name is unique within a section, and case is not told apart. `descr` is what is in the topic: by it another assistant decides whether to read it; `params` are free words by which the topic is found.

A record does not change: every new one is added to the topic, the last one is the topic's current state, the earlier ones are its history.

| Record field | What it is |
|---|---|
| `author` | who wrote: the model, its effort and the like |
| `user` | the person whose assistant wrote; empty when the user has been deleted |
| `comment` | one line about what the record changed — the history is read by them |
| `body` | the text: the whole state of the topic |
| `appendix` | data to the record, read only when it is needed |
| `date_time` | when the record was made |

## In the Program

Sections and their rights are set in "Development → AI Components". Topics and records are only read there: the assistants write and remove them.

In the language your program runs in the captions may differ.
