# Connecting to a Store

The application serves any number of stores: each one gets a folder of its own
with a local database and its own parameters for talking to the server. This
section brings both settings together — choosing a store and connecting to it.

## The Store Registry {#stores}

The **"Stores"** window is the list of stores this copy of the application works
with. The table has two columns: the **name of the store** and the **folder of
the local database**. The name is for you alone, to tell the stores apart in the
list.

The folder is required for every store: it holds the local base — the data
pulled down from the server and the edits not yet sent. Its requirements are
simple:

* a fast disk, preferably an SSD — the speed of the work depends on it directly;
* as a rule, not the system disk `C:` — otherwise some of the features (the HTML
  editor, web modules) will require running the application as administrator;
* write access. If the chosen folder is write-protected, the application warns
  you right away when you choose it, not in the middle of the work.

**"Add"** creates a new row; the folder is chosen with the button in the right
part of the cell. The folder may be one that does not exist yet — it will be
created when the work begins.

**"Delete"** removes a store from the list. The folder with the local base stays
on the disk: the store comes back to the list if the same folder is chosen
again.

**"Start work"** switches the application to the selected store. Before the
switch every open window is closed — and if some window will not close (because
it holds unsaved data, for example), the switch will not begin.

The list of stores is kept beside the application, in the `ShopList.ini` file —
convenient to carry over when the application is installed on another computer.

## Connection {#connect}

The **"Connection"** window sets up the link to the selected store's server.

### Connection Parameters

The store's **site URL** — it can be entered without `http://`, the application
adds that itself. The **login** and the **password** are an employee's account
in the store, and the same account decides the rights to everything you do in
the application.

The **"Store passwords in the system registry"** checkbox spares you typing the
password at every start. An unticked box means "do not store": the passwords
saved earlier are erased on saving.

The **"Proxy server (if used)"** group holds the address, the port, the login
and the password of the proxy, when the way out to the internet goes through
one.

### Connection Settings

Here you set the portions the application exchanges with the server in:

* **rows to receive** and **rows to send** — how many rows go out and come in
  per request;
* **file size** — the portion files are transferred in;
* **rows for merging products** — the portion used when merging product data;
* **auto-retry on a lock** — after how many seconds to try again if the table
  needed is busy with another employee;
* **waiting for the server's answer** — how long to wait before offering to
  break the operation off;
* **forced sending/receiving of files** — transfer the files anew instead of
  relying on their being there already.

Picking the values by hand is normally unnecessary: the **"Auto-configure by
connection type"** group sets a sensible set with the **"Mobile"**, **"Wired"**
and **"Fibre"** buttons. Start with the one that matches your channel, and
change individual fields only if the exchange runs into errors.

### AI Assistant

The tab describes the work with the assistant. **Your role** — "Owner",
"Developer" or "Employee" — sets the manner of the conversation, and the
**"Agent"** says which tool you use.

The assistant signs into the store under your login, so it can do exactly what
you can: it has no account of its own. The **"Check connection"** button runs a
real check and names the reason if something is wrong — no licence issued, the
store did not accept the login or the password, the user has no rights from the
"AI Assistant" branch, or the assistant did not answer at all (usually an
antivirus is blocking it).

More about the assistant itself — "[AI Assistant](ai-assistant.md)".
