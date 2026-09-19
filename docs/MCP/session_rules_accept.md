# session_rules_accept

Accepts the rules of a tool that has refused with them. Until the rules are accepted, the tool is not carried out in this session.

## The Right

Not required. An open session is needed.

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `tool` | string | the tool that refused with rules |
| `code` | string | the code from that refusal |

## How the Rules Are Built

The rules of a tool the server looks for in the `Engine\MCP` folder of the distribution by the name of the tool, from the long one to the short one: for `engine_db_execute` — `engine_db_execute.md`, then `engine_db.md`, then `engine.md`. The first file found is the one that holds, so a single set of rules can cover a whole family. No file — the tool has no rules, and it works without a check.

In the text of the rules the server substitutes marks: `{INSTALL_DIR}` — the program's folder, `{STORE_DIR}` — the store folder of the open session, `{AGENT}` — the name of the agent application. The files of rules end with a `See also` section: pages of the guide and recipes. The `Engine\MCP\Recipes\` folder holds no rules: the recipes the agent reads itself, when the rules refer to them.

The first call of a tool that has rules is not carried out. The answer is a refusal:

```
RULES_REQUIRED: the rules of engine_db_execute are not accepted in this session.
Read them below, call session_rules_accept with tool engine_db_execute and code
7f3a0c, then repeat the refused call. Keep the code in this conversation only -
never write it into a file, a note or a summary.
--- engine.md ---
<the text of the rules>
```

The code is made of the text of the rules and a random number that the server creates at every sign-in. So a code written down somewhere is useless in the next session, and an edit of the file of rules changes the code at once: the agent gets the new text at the very next call, without connecting anew.

## The Answer

```
accepted: engine.md - repeat the refused call.
```

The rules of the file are accepted until the end of the session — for every tool this file covers.

## Refusals

| Answer | When | What to do |
|---|---|---|
| `RULES_REQUIRED:` and the text of the rules | the code is wrong or from a past session | read the rules and accept them with the code from this refusal |
| `NO_RULES: <tool> has no rules - call it as it is.` | the tool has no rules | call the tool as it is |
| `Not connected. Call "session_connect" first.` | the session is not open | sign in |
