# AI Help

Help for the developer right in the editor: the word under the cursor, the selected fragment, the open file. It sees **exactly what you show it** — it knows nothing about your database, your storefront, or the neighboring modules. In return it answers instantly, changes nothing, and requires neither rights nor a license.

The platform's second assistant is built the other way around: the **AI Assistant** connects to the store and sees all of it, works in many steps, and changes data and files itself. It is configured by the owner and described in the user guide — "[AI Assistant](../User/ai-assistant.md)".

The rule is simple: **fits in a single file — AI Help; needs the store itself — the AI Assistant**.

## What It Is

Built into the editor. It works with any model compatible with the OpenAI API: the distribution includes examples for Groq, Google, OpenAI, and OpenRouter, but the list is open — any compatible provider can be connected by specifying an address and key.

It is useful for quick function reference, analyzing someone else's code, finding errors in a file, and generating snippets on the fly.

> The request goes **directly from the program** to the provider: the store server is not involved and knows nothing about the keys. The key is stored in the Workbench settings, with each developer having their own.

## Configuring Agents

**"Editor Settings → AI Help Agents"**. A table of agents, with a system prompt field below it for the selected row.

| Column | What it defines |
|---|---|
| Quick Reference | agent appears in the `F1` menu |
| File Analysis | agent appears in the `Shift + F1` menu |
| Name | arbitrary name under which the agent is visible in the selection menu |
| Model | model identifier at the provider |
| Address | OpenAI-compatible endpoint (`.../chat/completions`) |
| Key | provider API key |
| Timeout | how long to wait for a response, in seconds |
| Temperature | response variance, from `0` to `1` |

**System prompt** — a base instruction for the agent, set individually for each table row. This is what makes agents differ from one another when using the same model: the ones supplied for quick reference say "explain the function in more detail and provide examples," while the analysis agents say "find errors, especially logical ones."

**The prompt of every supplied agent opens with a description of the platform** — that what the model has in front of it is not a framework project but a Melbis module: a php file, a manifest next to it, `.htm` templates, calls into the engine through `MELBIS()`, `{MELBIS:...}` tags instead of Smarty, a multi-level cache. The same place carries a direct ban on suggesting Laravel and Symfony habits or inventing method names, and the documentation address — [melbis.com/help/en](https://melbis.com/help/en/) — so that a model that is unsure names a section instead of making up an API.

This is not a reference: one paragraph cannot teach a model the platform, nor does it need to. The job of that paragraph is to set a frame in which the model stops answering "as if this were ordinary PHP." Over time the need for it will fade: the Melbis documentation is open, and new models already know it themselves.

You can write your own prompt however you like — but if you remove that description, the answers become noticeably more generic.

A single agent can be marked in both columns at once, or not marked anywhere — in that case it will only be available for a request with clarification, where the list is complete.

### Levels in Names

Junior, Middle, and Senior in the names of the supplied agents are a distribution convention, not a platform mechanism: the platform only reads the model, address, and key.

* **Junior** (`llama-3.1-8b-instant`, `gemini-2.5-flash-lite`, `gpt-4o-mini`) —
  the fastest models. For routine tasks, quick syntax checks, and short references.
* **Middle** (`llama-3.3-70b-versatile`, `gemini-2.5-flash`, `gpt-4o`) —
  balanced, for code generation and everyday tasks.
* **Senior** (`gemini-2.5-pro`, `claude-opus-4.7`) — heavy models. They respond
  noticeably slower, so they are reserved for deep tasks: architecture analysis,
  complex refactoring, finding non-obvious errors.

A heavier model is not always better: on simple questions a fast model responds almost instantly, while waiting for a heavy one can take tens of seconds.

### Provider Keys

* **Groq** — [console.groq.com](https://console.groq.com) → API Keys
* **Google** — [aistudio.google.com](https://aistudio.google.com) → Get API key
* **OpenAI** — [platform.openai.com](https://platform.openai.com) → API Keys
* **OpenRouter** — [openrouter.ai](https://openrouter.ai) → Keys

## Calling from the Editor

Three keyboard shortcuts, one for each mode. If there are multiple suitable agents, a selection menu appears at the cursor; if exactly one is marked, the request is sent immediately.

### `F1` — Quick Reference

Sends the **word under the cursor**, or the selected fragment if there is a selection. The response arrives formatted and is displayed in the AI Help panel. This is the primary mode: you encounter an unfamiliar function — place the cursor on it, press `F1`.

Agents are taken from those marked in the "Quick Reference" column.

### `Ctrl + F1` — Request with Clarification

Opens a dialog. The selected code goes into the context field, with a separate field for your own question. The agent is also selected here, from the **full list**, not just the marked ones — allowing you to use a heavier model for a complex one-off question.

This mode is for everything that doesn't fit into a single word: "why is there a memory leak here," "rewrite this more compactly," "explain what this query does."

### `Shift + F1` — File Analysis

Sends the entire file **with line numbers added**. The prompt also appends a requirement to return strictly one JSON object of a defined form:

```
{result:[{
    line_start:  starting line number of the fragment,
    line_end:    last line number,
    type:        ERROR | WARNING | NOTICE,
    description: description,
    replace:     ready replacement code
    }]}
```

The program parses the response and displays a list of findings. Selecting an item highlights the corresponding lines in the editor, and the apply button replaces the fragment with the suggested code.

It is precisely because of the strict response format that stronger models are used in this mode: a weaker model readily drifts into explanations around the JSON, and parsing fails. If the response cannot be parsed, the environment will report a format error.

Agents are taken from those marked in the "File Analysis" column.

## Follow-up Question

After a response in `F1` and `Ctrl + F1` modes, a follow-up button is available. It carries the previous request **along with the received response** into the context of the new question — this turns a short reference into a dialog, and the agent remembers what was discussed. Each follow-up accumulates context, so long chains are better started fresh rather than extended indefinitely.

## Practical Notes

**Temperature.** For code work it is lowered: the supplied analysis agents are set to `0.2`, and the reference agents to `1`. The lower the value, the more predictable and terse the response; for finding errors, this is exactly what you need.

**Timeout** is chosen to match the model, not set high "just in case": fast agents in the distribution have `30` seconds, heavy ones have `200`. Too small a timeout on a heavy model will produce a connection error at exactly the moment when the response is almost ready.

**Keys and data.** The request sends the code you submitted: a word, a selected fragment, or the entire file. You should not send the contents of files containing access keys, passwords, and visitors' personal data — this is a regular external service, not part of the platform.
