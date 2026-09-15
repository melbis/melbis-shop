# The session: connecting, rights, licence

The Melbis MCP server is **transport**. It mirrors the commands of the engine one
for one and does not decide for you what to do: what is allowed is decided by the
rights registry on the server, what is needed you decide together with the User. It
makes exactly one decision of its own — to refuse by the list of rights without
going to the server, and that is described below.

Every command goes to a single endpoint of the Store, `core/mcp.php`. It exists for
you alone: json in both directions, a pool of steps, values with nothing cut off.
Some commands it carries out itself, others it hands on to other files of the
engine, but none of that is visible to you and you have no need to know it.

## An MCP-tool's name is a command, and a command stands on a right

Every MCP-tool has an engine command behind it: **`AGENT_` plus the name in upper
case**.

```
engine_php_save   →  AGENT_ENGINE_PHP_SAVE
memory_load       →  AGENT_MEMORY_LOAD
tool_run          →  AGENT_TOOL_RUN
```

The rights are rows of the `oper` table on the server, and **one row can stand
behind many commands.** The development branch is granted by kind of work, not tool
by tool:

| The right | What it opens |
|---|---|
| reading of the development branch | the map of the files and of the modules, the search, the versions, and the load of a module, a view, a css, an image |
| writing of the development branch | every add, save, rename and remove of all of those, and the template sets |
| the configuration | the parameters of `config.json` |
| the clearing of the cache | one level, or one module |

Memory, the AI-tools of the Store, the files of the elements and the database keep
a row per command: reading the tables, reading data, changing data, the locks,
adding and removing a file, and the four commands of memory are each their own.

That the development branch is one right for reading and one for writing is not a
simplification of the registry but the truth about the work: a login that may save a
module can already do with the Store whatever the Store itself can do, and dividing
that into a dozen ticks would only look like a fence.

**Those rights belong to the person, not to you.** You sign in under the login of
the User (see "Connecting"), and it is that person the owner ticks in the AI
assistant tree. So with one member of staff you can do more than with another in the
very same Store, and that is not a fault.

A refusal made here therefore always names **the exact right that is missing** — and
names it by the path along which the owner will find it in the Program:

```
ACCESS_DENIED: this user may not run AGENT_ENGINE_PHP_SAVE.
The owner grants it in the program, to the user you signed in as
- in the user rights, <the path, in the words of this Store>.
The session has to be opened again afterwards.
```

The command in that line is one of many the named right covers, so the tick the owner
makes opens the rest of them too. Say what you were refused and where the tick is;
listing what else it will open is for them to ask.

**The name of the command is your word, the path is the human one.** The owner will
never see the string `AGENT_ENGINE_PHP_SAVE` anywhere: the rights tree is labelled
in words. The path comes from the Store itself, in the Store's own language, along
with the connection — which is why this document does not spell it out: the Store
may well not be a Russian one. The same holds whenever you name a window, a tab or
a button of the Program yourself: the captions live in `Lang/<locale>/` of the
Distribution, one folder per interface language — take the wording from there
instead of translating one of your own (see "Sources in the Distribution" in
[index.md](index.md)).

**But before you pass a refusal on, look at the AI-tools of this Store.** A narrow
right is often not forgetfulness but a decision: the owner did not fail to grant
`AGENT_ENGINE_DB_EXECUTE`, they granted a Command that does the same job by the
rules of this Store. Granting a Command is as deliberate an act as withholding a
right, so making use of it is not getting around the refusal. The order is: refusal
→ [tools.md](tools.md) → found and granted, do it → not found, then name the
missing right. If there is nothing there either, this person was never given access
at all, and the conversation is with the owner in any case.

And the other way round: even when you do hold the direct right, do a job that an
AI-tool covers with that AI-tool — [tools.md](tools.md) explains why.

Give the owner the path: what they need is not "denied" and not the name of a
command, but where to go and what to tick there. After a right is granted **the
session has to be opened again**: the list arrives with the connection and does not
change in the middle of the work.

The refusal comes back at once, with no request to the Store: the server received
the list of rights when it connected and checks against it itself. **Your Host shows
every MCP-tool, whatever the rights** — it reads the list before anybody signs in and
keeps it — so the answer of `session_connect` names the ones granted to you, and any
other one is met by this at-once refusal.

A right taken away in the middle of a session is a different matter. The list this
server holds was fixed at the connection, so the call goes through and it is the
Store that turns it down — with a bare `ACCESS_DENIED`, no command, no right, no
path. It means the login no longer stands where it stood when the session opened.
Open the session again: the list arrives anew, and a tool gone from it is a right
gone. Name to the owner the tool you were refused; the path is theirs to be told
only when the refusal carries one.

Four MCP-tools spend no right and are always there: `session_init`,
`session_connect`, `shop_page`, `shop_run`. One exception inside them:
`shop_page` with `debug=true` reads the code of the debugger out of the
configuration, so that call — and only it — asks for the right over the
configuration.

## Looking around first

**`session_init`** answers before anybody signs in: which Stores this installation
knows, which of them this session signs in to, whether the Program runs and which
Store it holds, and where the documentation starts. It writes nothing, asks the
Store nothing and costs nothing — call it again whenever the User may have switched
Store in the Program.

## Connecting

Call **`session_connect`**. It takes no arguments: which Store you sign in to is
settled before you are asked. From then on it is known to the server for the whole
session — no other tool asks for it again.

Which Store that is has two answers, and `session_init` names the one in force.
Normally it is the **active** Store, the one the Program opened last: another is
needed, the User switches to it in the Program, and after that `session_init` shows
the new picture and `session_connect` signs in to it. But this server can also be
started with a Store named in its own configuration — then that Store is the only
one it will ever open, switching in the Program changes nothing, and the Host has to
be pointed elsewhere instead. Either way there is nothing here for you to choose and
nothing for you to switch.

You enter the Store under the login of **the User**: you work with their rights, and
the Store signs everything you change with their name.

The server obtains the login and password pair itself, from the **running Program**:
it answers directly, out of its own memory. If the Program is closed, the password
comes from the Windows registry, where it lies encrypted — **only if password keeping
is ticked** — and the login from `Shop.ini` in the local folder of the Store. There is
no third way: **a password does not travel through this chat**;
you can neither be told it nor see it.

The answer tells you everything about the session at once:

- **the name of the Store, its folder and the login**, in one line at the top. Say
  all three to the User in your first message, before any work: from inside a
  session two Stores look alike, and the wrong one is noticed only once the work is
  done. When the Store was named in the configuration of this server, the line below
  says so;
- **which login you came in under** — that is the User, and both the engine rights
  and the rights to the Store's AI-tools are counted for them ([tools.md](tools.md));
- a reminder to settle **who you are working with** — the owner, a developer or a
  member of staff (see "Who you are working with" in [index.md](index.md)) — and,
  when the scheduler is theirs, how often they want it looked into. Nobody sets it
  for you: a `kCritical` note may say it already, and if none does, ask outright, do
  not guess, and keep the answer in memory as a `kCritical` note;
- **your own folder** inside the local folder of the Store;
- **the build of the Program**, but only when it differs from the one your last
  session in this Store was stamped with — then what you remember of this
  documentation, of the map of the Program's windows and of the descriptions of the
  tables belongs to the older build: say it to the User, read the pages anew and ask
  the tools of the map for a fresh copy with `reload`. The stamp lies in your own
  folder and moves with the answer, so this is said once;
- how many notes you hold in the memory of this Store — zero means a first session —
  and how many of them are `kCritical`: those are read before any work
  ([memory.md](memory.md)). Without the right to read memory, the answer says that
  instead;
- whether the Store is running without a licence (see below);
- **how many AI-tools this Store has** — the list itself is `tool_list`, see
  [tools.md](tools.md);
- **what waits for the User in the scheduler** — the open tasks they execute, counted
  by state, when the scheduler is theirs by right; without that right
  the line is not there. Tell the User what it says. You look into the scheduler with
  its AI-tool, when it is granted to you; when it is not, say so;
- **which MCP-tools are granted to you**, in the last line.

The map of the project is **not read** when you connect. Every part of it is asked
for by its own tool when it is needed — see [map.md](map.md).

`NO_SETTINGS` means one thing: there is nowhere to take the password from. That is
cured by the person, not by you, and their choice is simple — either start the
Program and open the Store in it, or tick password keeping and save the connection.
They can try the pair itself in the same window, with the check button on the
assistant tab of the connection form — `BAgentCheck` on `TSAgent` of `FConnectParam`,
and the caption the User sees is in `../Lang/<locale>/FConnectParam.xml`. It walks the
whole way with the login and password typed there — licence, password, rights — and
says in words what is wrong. What it does not say is where this server will later find
that pair, so the choice above still has to be carried out.

## Licence

Every request to the Store is signed with a daily token. **The Program fetches the
tokens, this server only reads them**: it takes the one for today out of
`tokens/<login>-<date>.lic` in the local folder of the Store. The Program writes those
files while it runs, for the days the licence service handed it — so on a Store opened
in the Program today the file is simply there and nothing is required of you. On one
it has not been opened on, there may be no file for today, and nothing here can fetch
it: see below.

A licence is issued for a **domain + login pair**. The login is a person's login
now, so the token is **shared** between you and the Program: the very file it takes
for that member of staff. There is no such thing as a separate "AI" licence and
none needs to be arranged — if the person works in the Program, their pair is
already paid for.

A licence refusal reaches you in one of two shapes. `LICENCE_NONE` — this server found
no token file for today, and the words after it name the folder it looked in. Or a
code straight from the engine, which turned a sent token down: `LICENCE_ERROR` — the
token does not verify for this login and day; `LICENCE_DOMAIN` — the address the
request carries is not the domain the Store answers on; `LICENCE_DATE` — the clock of
this machine has drifted more than ten minutes from GMT. Neither shape is a message of
the licence service: this server never asks it. Why the service refused a token to the
Program — the licence unpaid, no such user there, a password drifted apart from the
one recorded — is seen in the Program, and it is the owner who looks.

**Nothing here can fetch a licence** — that errand belongs to the Program alone. On a
licensed Store, no file for today means the connection **does not open at all**:
`session_connect` answers `LICENCE_NONE`, names the `tokens` folder it looked in, and
asks for the owner to open this Store in the Program and connect again. Until that is
done every other MCP-tool answers only `Not connected. Call "session_connect" first.`
— every one but `session_init`, which answers as always and is what shows whether the
Program now holds this Store: the retry is `session_init`, then `session_connect`.
If the day rolls over inside a session already open, the session stays open and the
licence text comes back on every command instead.

## Demo mode

A freshly installed Store lives with an `admin.here` file in its root: the engine
checks **neither the licence nor the login and password** — everyone who connects
becomes an administrator. The server recognises this itself and says so in the
answer to the connection.

**Warn the User in your first message:** the Store is running without a licence,
and **anyone who knows the address** can connect to it with full rights. Advise
finishing the installation in the Program (that removes `admin.here`) and licensing
the Store before it goes live.

After that work as usual, but keep in mind that rights are notional in demo mode.
The real ones come into force once it is over, and they will be counted for the
member of staff's login — leaving the owner to walk the AI assistant tree in the
user's rights and tick what this person can be trusted with.
