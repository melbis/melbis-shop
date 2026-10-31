# Memory

The `memory` family is the agent's memory of the store: the notes that lie in the store's database rather than on the computer or in the correspondence. The next session — on another computer, in another agent application — meets the same notes. What the memory looks like for people — "[AI Assistant](../User/ai-assistant.md)". Working data needed later or by other assistants is kept by the "[Repository](repo.md)".

## Levels

| Level, `level` | Who reads | Who writes |
|---|---|---|
| `all` — for everyone | every user of the store | the administrator |
| `group` — for a group | the users of that group | the administrator |
| `own` — the agent's own | the user the agent signed in under | the agent, with the `memory_save` tool |

The agent's own notes belong to the user the agent signed in under: another user has a memory of its own. The notes for groups and for everyone the administrator writes — in the program or by a row in the `agent_memory` table; `memory_save` and `memory_remove` do not reach them.

**Whom not to show a note to**, the administrator writes in the `for_skip` field: logins and group keys separated by a space, a comma or a semicolon. The server does not give such a note to these logins: it is neither in the list nor among the critical ones that must be read.

## Kinds

Every note has a kind, `kind_key`, from the store's registry — the `key_value` rows with the code `AGENT_MEMORY_KIND_KEY`:

| Kind | What it is |
|---|---|
| `kCritical` | what must be observed in every session |
| `kDirect` | a directive from the person |
| `kSkill` | how to work with this person |
| `kDefault` | everything else |

The owner can add kinds of their own to the registry, and they count as `kDefault`. A kind that the registry does not have is not accepted on saving.

**The critical notes are read first.** `session_connect` reports how many there are; until all of them are loaded through `memory_load`, the server refuses every command except `memory_list` and `memory_load`.

**Precedence**, when notes disagree: the level first, the kind within it.

1. `kCritical`, `kDirect`, `kSkill` for everyone;
2. `kCritical`, `kDirect`, `kSkill` for a group;
3. the agent's own `kCritical`, `kDirect`, `kSkill`;
4. all the other notes.

## The Note

| Field | What it is |
|---|---|
| `id` | the number of the note |
| `name` | a short name |
| `category` | free words the notes are grouped by |
| `info` | one line of description — the list shows it |
| `kind_key` | the kind |
| `body` | the text in simple HTML: `<p>`, `<ul>` and `<li>`, `<b>`, `<code>` |

The list goes by the levels — for everyone, for the groups, the agent's own — and by the category within them, and a new note stands last.

## In the Program

All of the memory is visible and editable in "Development → AI Components", on the "AI Memory" tab; whoever can open this window sees the notes of all users. The window takes the `agent_memory` table into work as soon as it opens, and while it is open `memory_save` and `memory_remove` refuse.

In the language your program runs in the captions may differ.

## Rules

The family has no rules file of its own and no threshold: the rules of the memory come together with the rules of the session in the answer of `session_connect`.
