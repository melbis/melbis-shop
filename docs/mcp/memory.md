# Your memory of this Store

The notes live **in a table of the Store**, not on this machine and not in this
conversation. So the next session, on another computer, meets the very same ones,
and a colleague who connects to the same Store meets the ones marked shared.

A note belongs to **the login you signed in under** — that is, to the User at the
keyboard. Another member of staff has a memory of their own, and that is by design:
you remember exactly what you learned with this person and inherit no one else's
conclusions. The same thing will sometimes have to be learned twice — in exchange
the behaviour is predictable.

Four MCP-tools: `memory_list`, `memory_load`, `memory_save`, `memory_remove`.

## How a session starts

**`memory_list`** — first of all, before you go anywhere near the code. It gives
names with their short descriptions, the size and the time; the texts are asked for
separately. `find` narrows the list to the notes whose name, description or body
holds that word.

Then **`memory_load`**, with a list of names at once rather than one at a time:

```json
{ "names": ["cache-rules", "goods-import", "owner-prefers"] }
```

Names that are not there, or that you are not shown, come back in `missed`. A name
is unique within one person, so load someone else's shared note together with the
`login` of its author — the list shows it on every line.

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
{ "name": "cache-rules",
  "info": "Why the cache is off on kasdim_goods_cataloge",
  "body": "…" }
```

- **`name`** — the key. Short, stable, kebab-case. A second save under the same name
  replaces the first.
- **`info`** — one line: this is what `memory_list` shows, and what you will later
  decide by whether to read the body at all. Write it so that the decision can be
  made from that one line.
- **`body`** — the text itself.
- **`shared: true`** — opens the note to the other users of this Store. **Ask about
  every note on its own and wait for a plain yes.** One agreement opens one note: a
  yes given earlier is not a standing permission, "share whatever is useful" is not
  a permission at all, and a note that already went out shared does not license the
  next one. There is no right behind this and nothing will stop you — which makes
  the restraint yours: deciding for someone what of your findings their colleagues
  get to see is not your call.

Write the note in the language you speak with the User: when they ask what you
remember, these are the lines they get to see.

**A field you leave out keeps its old value.** Opening a note to others by sending
just `shared` will not wipe its text; rewriting the text will not close it again.

To forget, `memory_remove`, and only when a note turned out to be **wrong**. A
merely old note does no harm: it carries its date, and you will see for yourself
that it speaks of the past.

## Other people's notes

A note belongs to whoever wrote it, and one supervising right steps across that
line — `AGENT_MEMORY_FULL`. Usually the developer has it and no one else does.

**Reading.** An ordinary request always gives you your own notes and everything
marked shared — that is enough for the work, and rights have nothing to do with it.
Wider than that only on request, and only with the supervising right: `login` shows
everything one named person keeps, `all: true` every note in the Store, whoever
wrote it. The right on its own widens nothing: ask for no more and you get the
ordinary answer. And the other way round — ask wider without the right and nothing
is refused, the answer simply stays the ordinary one, except that a named `login`
still narrows it, to that person's shared notes.

**Writing and forgetting.** Your own, always. Someone else's, with the supervising
right, and whether it is shared or personal makes no difference.

When you read a shared note, remember whose it is: this is not documentation of the
Store but the conclusion of another session, drawn with another person and possibly
half a year ago. Check it before you build on it.

And when you delete a shared one — **say out loud that you are deleting it**, yours
or not. It was written for everybody, someone may be leaning on it, and there is
nothing to restore it from: memory keeps no history.

## When you are asked what you remember

Memory has no window of its own in the Program: the only way a person can find out
what you remember about their Store is through you. If they ask, show
`memory_list`, and whatever they need in full. For an owner with the supervising
right, put the whole picture together: `all: true` shows everything every agent
remembers about this Store — every line but your own carries the name of its author.

If they ask you to forget something, forget it — `memory_remove`, without arguing
and without "but the note is useful". They may remove someone else's too, if the
right is there; a shared one with the warning described above.

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
