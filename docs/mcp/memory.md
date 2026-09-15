# Your memory of this Store

The notes live **in a table of the Store**, not on this machine and not in this
conversation. So the next session, on another computer, meets the very same ones.

A note stands on one of three levels:

| Level | Who reads it | Who writes it |
|---|---|---|
| for everyone | every login of the Store | the administrator |
| for a group | the logins of that group, main or additional | the administrator |
| your own | the login you signed in under | you, with `memory_save` |

Your own notes belong to **the login you signed in under** — that is, to the User at
the keyboard. Another member of staff has a memory of their own, and that is by
design: you remember exactly what you learned with this person and inherit no one
else's conclusions. The same thing will sometimes have to be learned twice — in
exchange the behaviour is predictable. What every agent of the Store, or of one
group, has to know, the administrator writes on the levels above. In the Program
all of it lies open: whoever may open the window of AI settings sees and edits
every note of every login.

Four MCP-tools: `memory_list`, `memory_load`, `memory_save`, `memory_remove`.

## How a session starts

**`memory_list`** — first of all, before you go anywhere near the code. It gives
every note with its number, category, kind and short description, the size and the
time, and marks the notes for everyone and for a group; the texts are asked for
separately. `find` narrows the list to the notes whose name, category, description
or body holds that word.

Then **`memory_load`**, with a list of names at once rather than one at a time:

```json
{ "names": ["cache-rules", "goods-import", "owner-prefers"] }
```

**Every `kCritical` note is loaded before any work**, whatever its level.
`session_connect` says how many there are, and no work starts without them.
A critical note may name other notes to read with it: load those too. The rest are
read when a job needs them, and their `info` line decides which. Without the right
to read memory, `session_connect` says so, and the work goes on without it.

A name brings every note of that name you can see — your own, a group's, everyone's
— each marked with its level. A name with no note comes back in `missed`.

## Kinds

Every note has a kind, `kind_key`, from the registry of the Store:

| Kind | What it is | When it is read |
|---|---|---|
| `kCritical` | what must hold in every session | before any work |
| `kDirect` | an order of the User | when a job needs it |
| `kSkill` | how to work with this person | when a job needs it |
| `kDefault` | everything else | when a job needs it |

The owner may add kinds of their own, and those count as `kDefault`. A kind the
registry does not know is refused, and the refusal lists the ones it does.

**When notes disagree, the order is strict**: the level first, the kind inside it.

1. `kCritical`, `kDirect`, `kSkill` for everyone;
2. `kCritical`, `kDirect`, `kSkill` for a group;
3. your own `kCritical`, `kDirect`, `kSkill`;
4. every other note, whatever its level.

Two cases the order does not settle:

- **A note against what the User says now.** Against an own `kDirect` or `kSkill`,
  do what is said now and rewrite the note: the person changed their mind. Against a
  `kCritical`, or a `kDirect` or `kSkill` for a group or for everyone, do not do it:
  name the note and stop. A note of their own the User may have rewritten; the
  administrator's is not theirs to overrule.
- **Two notes on one step of the order** — of the two groups a login stands in, say.
  Name both and ask which holds. A wrong note of your own is removed; a wrong one of
  the administrator's is theirs to change.

The kind is chosen when you write. `kCritical` is for the few things that must never
be missed: keep them short, and let one of them name the others worth reading at the
start. Who the User is — the owner, a developer or staff — is `kCritical`: every
session starts from it. An order the User gave is `kDirect`, what you learned about
how they want to be worked with is `kSkill`, and everything else is `kDefault`.

## What to write down

What you **learned about this Store and could not have read out of the code**:

- a decision and the reason for it: why it was done this way and not another;
- a convention the owner insists on;
- a trap that cost you an hour.

What **not** to write down: how the code is built, the list of modules, the
structure of the tables, the names of files. All of that is in the map and in the
files, and such a note goes stale before it is ever of use. Memory is for what is
written down nowhere in the Store.

## How to write it

```json
{ "category": "Cache",
  "name": "cache-rules",
  "info": "Why the cache is off on kasdim_goods_cataloge",
  "body": "…" }
```

- **`id`** — the note to change, as `memory_list` numbers it. Without it a new note is
  written, and it stands last.
- **`category`** — free words the notes are grouped by; the list is ordered by them.
- **`name`** — a title: short, stable, kebab-case. A new note cannot go without one.
- **`info`** — one line: this is what `memory_list` shows, and what you will later
  decide by whether to read the body at all. Write it so that the decision can be
  made from that one line.
- **`kind_key`** — the kind, see "Kinds" above. Left out, a new note is `kDefault`
  and an old one keeps its kind.
- **`body`** — the text itself, in plain HTML: `<p>`, `<ul>` and `<li>`, `<b>`,
  `<code>`. Never markdown, and no styles or scripts. `info` stays one plain line.

Write the note in the language you speak with the User: when they ask what you
remember, these are the lines they get to see.

**A field you leave out keeps its old value**: rewriting the text keeps the kind, and
naming a kind keeps the text.

**The Program edits memory too**, on a tab of the window of AI settings, and that
window takes the table into work the moment it opens, whichever tab is in front.
While it is open, `memory_save` and `memory_remove` write nothing and say the table
is busy: tell the User who holds it — `engine_db_locks` — and come back to the note
later.

To forget, `memory_remove` with the id of the note, and only when it turned out to be
**wrong**. A merely old note does no harm: it carries its date, and you will see for
yourself that it speaks of the past.

## Notes for a group and for everyone

They are the administrator's: a rule the whole Store keeps, a convention of one
department. `memory_save` and `memory_remove` never reach them. When the
administrator asks you for one, it is a row of `agent_memory` written with
`engine_db_execute`, announced as a change of data like any other: a `lock` step
first, since the Program edits this table too; `id` from a `generate` step and `pos`
the same number; `user_id` empty; `group_id` the group, or empty too for everyone;
the category, the name, the description, the kind, the text and `edit_time` as for
any note.

Read one as the word of the Store, not as a finding of a session: it stands above
your own notes in the order above, and only the administrator changes it.

## When you are asked what you remember

The Program shows memory on a tab of the window of AI settings. When they ask you,
show `memory_list`, and whatever they need in full.

If they ask you to forget something, forget it — `memory_remove`, without arguing
and without "but the note is useful". A note for a group or for everyone is not
yours to forget: say that the administrator removes it.

## Memory and what is expendable

What you put into `mcp\melbis\` — downloaded images, pages, query dumps — is
**expendable**. It can be wiped at any moment, and it goes stale on its own. So can
your own folder beside it: the whole local folder of the Store goes when the User
changes computers.

Memory is the opposite: what goes here is what has to outlive this session and this
machine. If you catch yourself thinking "I will need to remember this", it belongs
in `memory_save` and not in a file beside it.

## The Host's own memory

The Host you work inside may keep a memory of its own — files on this machine. The
line is simple: **everything about the Store and its people goes here, into the
Store's table** — the facts, the traps, the owner's rules, the agreements about how
to work. What is left for the Host's memory is the machine itself: paths, local
tools. The one path kept here instead is the folder the User named for the things
they will open later — reports, tables, exports — and the note names the machine it
belongs to, because the same Store is worked with from more than one.

Do not keep one note in both places: the copy will quietly drift away from the
original.

And when you report "saved", name the Store and the note — "into the Store's
memory, `owner-work-rules`". A bare "saved to memory" leaves the owner guessing
where to look.
