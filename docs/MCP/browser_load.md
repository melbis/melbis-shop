# browser_load

Looks into the program's AI browser: without a tab number — the list of open tabs, with a number — that tab's page as it is now.

## The Right

Requires no store rights: access is given by the program's "AI Browser" window — "[The AI Browser](browser.md)".

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`tab`] | number | the tab's number, as the list shows it; without it — the list of tabs |
| [`show`] | string | what to return: `text` — the elements and the text (by default), `picture` — a picture of the visible part, `both` — everything together, `html` — the HTML of the whole page as a file |

## The Answer

Without `tab` — the list of tabs:

```json
{"tabs": [{"tab": 1, "title": "Google", "url": "https://www.google.com/"}]}
```

With `tab` — the data block, then the element lines and the page's text:

```json
{"tab": 1, "title": "Google", "url": "https://www.google.com/", "width": 1528, "height": 754, "scroll": 0, "full": 754, "elements": 20, "cut": false}
```

```
[4] link "Sign in" -> https://accounts.google.com/ServiceLogin?hl=en
[6] field "Search" = ""
[11] button "Google Search"
[16] link "Business" (below) -> https://www.google.com/services/

Gmail Images
Sign in
…
```

| Field | What it is |
|---|---|
| `tab` | the tab's number |
| `title`, `url` | the page's title and address |
| `width`, `height` | the size of the visible part in the picture's pixels |
| `scroll` | how far the page is scrolled |
| `full` | the height of the whole page |
| `elements` | how many elements are numbered |
| `cut` | the text below is cut at 12,000 bytes |
| `file`, `bytes` | with `show: "html"` — the file in the conversation folder and its size |
| `error` | the page could not be read or the picture could not be taken; for example, `The browser gave no picture: the window may be minimized` — the window is minimized |

**An element's line:** the number, the kind, the label in quotes, the value after `=`. Then — `(disabled)` for an unavailable one, `(above)` or `(below)` for one above or below the visible part, and the link's address after `->`. The kind is `link`, `button`, `field`, `select`, `checkbox`, `radio`, `password`, `file` or the element's role on the page: `tab`, `menuitem`, `option` and others. The value of a password field and of a file field is not shown. The first 300 elements go into the answer; the numbers of the rest work too.

**The picture's pixels** are the unit of all sizes and points: [`browser_exec`](browser_exec.md) takes the point for a click from this same picture.

With `show: "picture"` there are no element lines and no text — only the data block and the picture. With `show: "html"` — only the data block: the HTML lies in the file `browser\tab<number>-<time>.html` of the conversation folder.

## Refusals

| Answer | When |
|---|---|
| `tab is the number of a tab, as browser_load lists it.` | the tab's number is not a whole number from 1 |
| `show is text, picture, both or html.` | an unknown value of `show` |

The refusals of access — on "[The AI Browser](browser.md)" page.
