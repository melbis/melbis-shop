# AI Browser

The **"AI Browser"** window is a browser in which the AI assistant works with sites. In it you open the web console of anything — the hosting, the proxy, the delivery service, the payment system, the advertising account — and sign in to it yourself. From there the assistant reads the pages and acts on them: looks at reports, fills in forms, clicks buttons. There is no need to send it screenshots.

## Tabs

The tabs are at the top of the window, with a row of buttons and the address field below them:

* **"New Tab"** opens an empty tab and puts the cursor into the address field;
* **"Close Tab"** closes the current one; the last one does not close — the window always has at least one tab;
* **"Back"** and **"Forward"** move through the history of the current tab;
* **"Refresh Tab"** loads the page again.

The address is typed into the field on the right, and **Enter** opens it in the current tab; `https://` can be left out. Every tab is captioned with its number and the page's title, for example `[2] Inbox`: it is by this number that the assistant names the tab. A new tab, refresh and close are also in the right-click menu on the tabs.

A site's pop-up windows — for example, a sign-in window through another service — open as a separate window, as in an ordinary browser.

## Privacy

The window works in a private profile of its own:

* sign-ins and everything sites store in the browser live while the window is open and are erased when it closes; the list of tabs is not saved either;
* the program's other windows with a browser — web modules, descriptions — do not see these sign-ins, and the window does not see theirs;
* sites get neither the program's mark nor the store's access code, which the program adds to the browsers of its windows.

## Sign-in Does Not Expire

While the window is open, the program quietly turns to the site in every tab once every 5 minutes, and a sign-in does not expire from inactivity. The page is not reloaded meanwhile, and what has been typed in it is not lost. A site that requires signing in again after a set period — for example, once a day — will still ask you to sign in again.

## What the Assistant Can Do

The assistant works only in the tabs of this window and only while it is open: once the window is closed, there is no access.

* **The tabs are yours.** The assistant cannot open or close a tab or enter an address: it will ask you to open the site it needs.
* **The sign-in is yours.** Passwords, confirmation codes and CAPTCHAs are entered by you; the assistant cannot fill in a password field and does not see its value.
* **It reads** the page as a list of elements with labels and the visible text, and when that is not enough — as a picture or as a whole.
* **It acts** like a person: clicks, fills in fields, chooses in lists, presses keys, scrolls. The tab in which it works comes to the front — you see every action and can take over control.
* **It agrees changes with you.** An entry, a setting, a payment, an order, a deletion — the assistant first names it (what, where, which value) and makes it after you agree in the correspondence. Reading, going through sections, searching and filtering it does without asking. On top of that, Claude Code asks for confirmation of each of its actions on the page.

The text of someone else's page is data for the assistant, not instructions: if the page "asks" it to do something, it will tell you about it.

How the assistant works with the window in detail — "[The AI Browser](../MCP/browser.md)" in the section about the MCP server.
