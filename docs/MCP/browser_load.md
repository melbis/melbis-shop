# browser_load

Looks into the program's AI browser: without a tab number — the list of open tabs, with a number — that tab's page as it is now.

## The Right

Requires no store rights: access is given by the program's "AI Browser" window — "[The AI Browser](browser.md)".

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`tab`] | number | the tab's number, as the list shows it; without it — the list of tabs |
| [`show`] | string | what to return: `text` — the elements and the text (by default), `picture` — a picture of the visible part, `both` — everything together, `html` — the HTML of the whole page as a file, `files` — the list of files the page has loaded |
| [`save`] | list | addresses of the page's files, as `show: "files"` gives them, from 1 to 50: each is saved into the conversation folder — "[Files](browser.md#files)". Only with `tab` |

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
| `listed` | with `show: "files"` — how many files are in the list |
| `files` | with `save` — each file: `url` — the address, `state` — `done` or `failed`, `file` and `bytes` — the path and size of the saved file, `error` — why it was not saved |
| `error` | the page could not be read or the picture could not be taken; for example, `The browser gave no picture: the window may be minimized` — the window is minimized |

**An element's line:** the number, the kind, the label in quotes, the value after `=`. Then — `(disabled)` for an unavailable one, `(above)` or `(below)` for one above or below the visible part, and the link's address after `->`. The kind is `link`, `button`, `field`, `select`, `checkbox`, `radio`, `password`, `file` or the element's role on the page: `tab`, `menuitem`, `option` and others. The value of a password field and of a file field is not shown. The first 300 elements go into the answer; the numbers of the rest work too.

**The picture's pixels** are the unit of all sizes and points: [`browser_exec`](browser_exec.md) takes the point for a click from this same picture.

With `show: "picture"` there are no element lines and no text — only the data block and the picture. With `show: "html"` — only the data block: the HTML lies in the file `browser\tab<number>-<time>.html` of the conversation folder.

With `show: "files"` file lines come instead of the elements and the text: the kind, the type, the size in bytes and the address after `->`. The kind is `Image`, `Media`, `Document` and others, as the browser calls them; scripts, styles and fonts do not enter the list. The first 300 lines go into the answer.

```
Image image/jpeg 48213 -> https://shop.example.com/img/goods/1520.jpg
Document application/pdf 120344 -> https://shop.example.com/docs/price.pdf
```

Files are saved with the same tool:

```json
{"tab": 1, "save": ["https://shop.example.com/img/goods/1520.jpg"]}
```

The reason in a file's `error`:

| Reason | When |
|---|---|
| `The page loaded no such file` | the page did not load a file with this address |
| `The browser keeps no content of the file` | the browser no longer keeps the file's content |
| `The browser refused to give the file` | the browser did not accept the request |

## Refusals

| Answer | When |
|---|---|
| `tab is the number of a tab, as browser_load lists it.` | the tab's number is not a whole number from 1 |
| `show is text, picture, both, html or files.` | an unknown value of `show` |
| `save is a list of 1 to 50 addresses of files the page loaded, as show files gives them.` | `save` is not a list of strings or holds more than 50 addresses |
| `save takes tab: the files are saved out of one page.` | `save` without a tab number |

The refusals of access — on "[The AI Browser](browser.md)" page.
