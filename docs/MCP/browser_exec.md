# browser_exec

Acts in a tab of the AI browser like a person: steps in turn — click, fill in a field, choose in a list, press a key, scroll. After the steps it returns the page the same way as [`browser_load`](browser_load.md).

## The Right

Requires no store rights: access is given by the program's "AI Browser" window — "[The AI Browser](browser.md)".

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `tab` | number | the tab's number |
| `steps` | list of objects | the steps in order, from 1 to 30 |
| [`show`] | string | what to return after the steps — as in [`browser_load`](browser_load.md); `text` by default |

A step is an object with the field `do` and the fields for it:

| `do` | What it does | Fields |
|---|---|---|
| `click` | clicks an element or a point of the picture | `n` or `label` — an element; or `x` and `y` — a point |
| `fill` | replaces the content of a field; empty text clears it | `n` or `label`, `text` |
| `select` | chooses an option of a list by its visible text or value | `n` or `label`, `text` |
| `key` | presses a key; with an element — first puts the cursor into it | `key`, [`n` or `label`] |
| `scroll` | scrolls to an element or turns the wheel by `by` pixels: down, and with a minus up | `n` or `label`; or `by` and [`x`, `y`] — where to turn the wheel, the middle by default |

**An element** is named by its number from the last answer about this tab (`n`) or by its label (`label`). The label is looked up at the moment of the step: after going to a new page the old page's number no longer fits, but the label does. First a full match is looked for, then the start of the label, then a part of it, a visible element before an invisible one; the case of letters does not matter. `fill` looks only among fields, `select` — among lists.

**Keys:** `Enter`, `Tab`, `Escape`, `Backspace`, `Delete`, `Space`, `ArrowUp`, `ArrowDown`, `ArrowLeft`, `ArrowRight`, `PageUp`, `PageDown`, `Home`, `End`. Text is typed only through `fill`.

**A point** `x`, `y` — pixels of the `browser_load` picture from its top left corner.

After every step the program waits for the page — "[The Steps](browser.md#steps)".

A letter in one call:

```json
{"tab": 2, "steps": [
  {"do": "click", "label": "Compose"},
  {"do": "fill", "label": "To", "text": "client@example.com"},
  {"do": "key", "key": "Tab"},
  {"do": "fill", "label": "Subject", "text": "Invoice"},
  {"do": "click", "label": "Send"}
]}
```

## The Answer

As in [`browser_load`](browser_load.md), and in the data block also:

| Field | What it is |
|---|---|
| `done` | how many steps are done |
| `steps` | how many steps were given |
| `error` | why the steps stopped — only if they stopped |

A step with an error stops the rest. The page is returned as it has become by that moment, and the answer's text starts with the line `Stopped after <done> of <given> steps: <reason>`.

| Reason in `error` | When |
|---|---|
| `element <n> is not on the page any more` | the page has changed and the number is out of date: a fresh answer or a label is needed |
| `nothing labelled "<label>" on the page` | there is no element with such a label on the page; for `fill` and `select`, `among the fields` or `among the lists` stands before `on the page` |
| `… is a password field: the User types passwords` | a password field |
| `… is a <kind>, not a field` | `fill` not in a field |
| `… is a <kind>, not a list` | `select` not in a list |
| `… has no option "<text>"; it has: …` | the list has no such option; the ones it has are listed |
| `The browser refused the step` | the browser did not accept the click or the input |
| `The time ran out after <k> steps` | the steps did not fit into a minute |
| `The User closed the tab` | the tab was closed during the call |
| `The page stopped answering…` | the page stopped answering: usually it is waiting for the person's answer in its dialog |

## Refusals

The steps are checked before the first one: with an error in any of them, none is performed.

| Answer | When |
|---|---|
| `tab is required: the number of a tab, as browser_load lists it.` | there is no tab number |
| `steps is a list of 1 to 30 steps, each an object with do.` | there are no steps, they are not a list, or there are more than 30 |
| `step <i>: do is click, fill, select, key or scroll.` | an unknown action |
| `step <i>: click takes n or label - an element - or x and y - a point of the picture.` | the click has no target or has two |
| `step <i>: fill takes n or label - the field - and text…` | the fill has no field or no text |
| `step <i>: select takes n or label - the list - and text - the option.` | the choice has no list or no option |
| `step <i>: key is one of Enter,Tab,…` | the key is not in the list |
| `step <i>: scroll takes n or label - an element to bring into sight - or by - pixels, not 0.` | the scroll has no target or has two |
| `step <i>: label is the text an element reads or is labelled by, instead of n.` | `label` is empty, not a string, or given together with `n` |
| `step <i>: n is the number of an element, from 1.` | the element's number is not a whole number from 1 |
| `step <i>: x and y go together: a point of the picture, from 0.` | only one coordinate is given, or a negative one |
| `step <i>: by is a whole number of pixels.` | `by` is not a whole number |
| `show is text, picture, both or html.` | an unknown value of `show` |

The refusals of access — on "[The AI Browser](browser.md)" page.
