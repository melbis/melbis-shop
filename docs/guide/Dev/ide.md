# Workbench

The **"Workbench"** section is a built-in IDE for managing the code, design, and
database of your online store.

The tool includes the following tabs:

## 1. Scripts, Templates
The main workspace for working with the file structure directly on the server. Files in the tree are divided into 4 logical groups:

*   **Static:** Auxiliary scripts and styles (e.g., JavaScript and CSS).
*   **Images:** Managing graphics without using third-party FTP clients. Files are uploaded to the root `images` directory.
*   **Root Scripts:** Entry points and store routers (e.g., `index.php`, `.htaccess`). They handle `GET`/`POST` requests and invoke the necessary modules.
*   **Module Scripts:** Scripts that perform isolated tasks (menu generation, displaying a product block, etc.). They support nesting — one script can call others.

**Template Groups (Themes)**
Inside groups (except root scripts), you can create subdirectories — template groups (the `default` group is used by default). This allows you to switch designs while using the same PHP code for different visual representations (desktop, mobile version) or different languages.

**Working in the Code Editor:**

*   **Bold font** in the file tree indicates that a file has been modified.
*   `Ctrl + Enter` — opens the list of code templates (Code Templates) for PHP.
*   *Configuring templates:* Editor Settings -> Editor -> Syntax Highlighting -> Configuration -> Code Templates.

**Module Parameters**

To the right of the code editor is the parameter panel for the current module. This is where everything that is not defined by code is configured:

| Tab | What is configured | More details |
|---|---|---|
| Parameters | connected libraries, entry point flag, lazy loading, basic cache and its pause | "Module Scripts", "Caching", "Lazy Loading" |
| Tables | DB tables that the module's cache depends on | "Caching" |
| Trick | protection against peak loads | "Caching" |
| Smart | early cache refresh | "Caching" |

Above the editor are two fields: the module description and the declaration of input parameters. The
IDE uses these to build autocompletion suggestions when editing templates (see "Module
Scripts").

## 2. Store Online
A built-in Chromium-based browser for instant visual testing of changes.

*   **Quick Preview (`Ctrl + E`):** Pressing this shortcut in the code editor automatically saves all modified files and immediately refreshes the page in the built-in browser.

## 3. Server DB and Local DB
A tool for executing, debugging, and testing SQL queries against the store's database.

**Variable auto-replacement feature:**
Allows you to copy SQL queries directly from PHP scripts without manually removing variables.

*Example workflow:*
Your code contains a query:
```sql
SELECT * FROM {DBNICK}_topic WHERE id = :ID
```
1. Paste this query into the DB editor.
2. Double-click on the keys (in this case, `{DBNICK}` and `:ID`).
3. The keys will automatically be added to the right-hand auto-replacement block.
4. Enter the actual values next to them and run the query.

## 4. DB Statistics
Summary information about the state of tables (sizes, indexes) and other system analytics needed by the developer to monitor database performance.

## Keyboard Shortcuts

**Code Editor**

| Keys | Action |
|---|---|
| `Ctrl+Enter` | Insert code template (Code Templates) |
| `Ctrl+Space` | Autocomplete (suggestion) |
| `Ctrl+/` | Comment / uncomment selection |
| `Ctrl+R` | Load content from server |
| `Ctrl+Z` | Undo edit |
| `Ctrl+Shift+Y` | Redo edit |
| `Ctrl+E` | Save all files and open site preview |

**SQL Editor (Server DB and Local DB)**

| Keys | Action |
|---|---|
| `Ctrl+Enter` | Execute query |
| `Ctrl+R` | Refresh table list |

**AI Help**

| Keys | Action |
|---|---|
| `F1` | Quick help |
| `Shift+F1` | File analysis |
| `Ctrl+F1` | Free query |