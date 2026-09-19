# The Engine

The `engine` family is forty-four MCP tools that work with the store's files and data through its engine: they read the project's map, load and save modules, templates, statics and images, run queries against the database, attach files to elements.

## Who It Is for and Why

Access to the engine is a developer's access. The right to change in the "Direct access → Development" branch opens the whole store: a login that is allowed to save a module can do to the store everything the store itself can (see "[Sign-in, Rights, License](access.md)").

The ordinary work — create a product, set a task, change a price — is more convenient and safer to do through the store's AI tools. The owner writes them to their own rules, and the agent takes them before the engine: the list is given by `tool_list`.

## The Rules

The family has one set of rules for all forty-four tools — `engine.md` in the `Engine\MCP` folder. The first call of any engine tool in a session refuses with them; after they are confirmed through [`session_rules_accept`](session_rules_accept.md) all the tools of the family work. This is a deliberate threshold: the engine is for those who are ready to work with it, and the ordinary work goes through the AI tools.

## Sections

| Section | Tools | What they do |
|---|---|---|
| General | `engine_search`, `engine_whole_load` | where a string occurs, a whole file onto the computer |
| Map | `engine_map_tree`, `engine_map_units` | what the store is made of |
| PHP files | `engine_php_*` | the root scripts, the modules and the libraries |
| Templates | `engine_html_*` | the modules' templates |
| Template groups | `engine_template_*` | whole template groups |
| Statics | `engine_static_*`, `engine_static_dir_*` | css, js and other files of the statics, their folders, the server's logs |
| Images | `engine_image_*`, `engine_image_dir_*` | the templates' images, fonts and icons, their folders |
| Versions | `engine_history_*` | the history of a file's saved versions |
| Configuration and cache | `engine_dev_*` | the parameters of `config.json`, clearing the cache |
| Database | `engine_db_*` | the structure of the tables, queries, locks |
| Element files | `engine_files_*` | the files attached to the store's rows |

## The Path

Files and folders are addressed by a path from the store's root. The engine writes the paths with the `./../` prefix — that is how they come in the map. In the parameters the prefix is optional: `units/melbis_cataloge.php` and `./../units/melbis_cataloge.php` are one and the same file.

A path does not go outside the store. A path with `..` and a path to `config.json` get the refusal `ACCESS_DENIED` — that is a border, not a missing right. The parameters of the configuration are read by `engine_dev_config`.

## The Part of the Store

For every folder and file the map names the part of the store it lies in — `branch`. By it one sees which tools to work with it.

| `branch` | Where it lies |
|---|---|
| `root` | the site's root: `index.php`, `.htaccess` and other files |
| `unit` | `units/`: the modules, the libraries and the groups of modules by the start of the name |
| `html` | `templates/<group>/units/`: the modules' templates and their files |
| `static` | `templates/<group>/statics/`: the statics |
| `image` | `templates/<group>/images/`: images, fonts, icons |
| `log` | `core/log/`: the server's logs |
| `template` | the other folders inside a template group |

## The Map in the Session's Memory

The parts of the map — the tree of files, the modules' functions, the database's tables, the configuration — the server reads from the store at the first call and keeps until the end of the session. The `reload` parameter reads a part anew: this is needed when the files have been changed outside the session, from the program for example.

The tree is re-read by itself after every creation, rename and deletion, and also after a module with a manifest has been saved.

## Tables in the Answers

Long lists come as a table: in `columns` the names of the columns are listed once, in `rows` — the rows with the values in the same order.
