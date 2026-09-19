# Statics

The `engine_static_*` section works with the files the storefront gives the browser as is — css, js, `.psv` and others — and with the statics folders. The server's logs are read here as well.

## Where It Lies

```
templates/<group>/statics/…
templates/<group>/units/<module>/…
<file in the site's root>
```

**`statics/`** is the template group's common statics: libraries, styles, scripts. The folders inside it are created, renamed and deleted by `engine_static_dir_*`. `statics/` itself is the root: it can be neither renamed nor deleted.

**A module's folder** in a template group keeps the module's own css, js and `.psv` next to the templates. That is statics too: the tools of this section read and save it. The `.htm` templates themselves are handled by `engine_html_*`.

**The site's root** — `.htaccess`, `robots.txt` and the other files except php: they are statics too. The root's php files are the root scripts of `engine_php_*`.

Outside these places the tools of this section create, save and delete nothing. A statics file can also be moved into the images folder.

Every group has statics of its own: unlike the templates, they are not inherited from the main group.

## Bundles

A bundle glues several css or js files into one file `statics/bundle.<name>` of the same group. How the build works, what the priorities and the `.psv` files mean — "[Static Build](../Dev/tpl_bundle.md)".

Which bundles a file belongs to is written in its bundle description. Here it is the `bundle` field made of two lines — the same ones as above the editor in the Workbench:

| Field | What it is |
|---|---|
| `param_info` | the bundles separated by commas, each with its priority after a colon: `melbis.css: 10, print.css: 2` |
| `unit_info` | the file description |

`engine_static_load` gives back the file's `bundle`, `engine_static_save` takes it in the same shape.

## What Saving Does

* **Overwrites the file whole** and **creates nothing**: a new file is added by
  `engine_static_add`.
* **Rebuilds all the group's bundles** if the file belongs to a bundle or `bundle` was
  passed with it. The file is written before the build: if the build has refused — the
  `.psv`, for example, gave no array of keys back — the file is already saved, while the
  bundle the build stopped at has stayed as it was.
* **Writes the file's version** if that is turned on in the Workbench settings of the
  program on this computer.
* **Resets the module's cache** — the basic one and Trick, and the Smart statistics with
  them — for a file from a module's folder. Statics from `statics/` does not touch the
  cache.

The store's build number is raised only on `build`: while the number stays the same, the visitors' browsers keep the old css and js they already have.

## Renaming, Moving and Deleting

The bundle description follows the file: it moves together with it and is taken off on deletion. The built `statics/bundle.*` files at that are **not rebuilt** — what was in them stays, and a deleted file goes on arriving at the storefront. The `bundled` field in the answer tells how many of the affected files had a bundle description.

The group's bundles are rebuilt by saving any of its files that belongs to a bundle — even with the old body and without `bundle`. Moving a file to another group leaves both groups unbuilt.

If the last file has left a bundle, `statics/bundle.<name>` stays on the disk as it was: there is nothing left to build it from. Such a file is deleted by `engine_static_remove`.

## The Server's Logs

`engine_static_load` reads the `core/log/` logs as well; the list of them with the date and the size is given by `engine_map_tree`. From a log larger than a megabyte the last megabyte comes, the whole log is downloaded by `engine_whole_load`. What each log writes — "[Logs, Errors, and Metrics](../Dev/log.md)".

The containers' output, the MySQL log and the server's system log are not among them: in a standard installation they do not get into `core/log/`.
