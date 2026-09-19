# Template Groups

The `engine_template_*` section creates, renames and deletes template groups as a whole. What lies inside a group is handled by the sections on templates, statics and images.

## What a Group Is

A group is the `templates/<group>/` folder, a complete set of the storefront's design:

| Folder | What is in it | What works with it |
|---|---|---|
| `units/` | the modules' templates | `engine_html_*` |
| `statics/` | css, js and other statics | [`engine_static_*`](engine_static.md) |
| `images/` | images, fonts, icons | [`engine_image_*`](engine_image.md) |
| `bundles/` | the descriptions of the statics bundles | they change together with the statics files |

A group is named by one folder name, `mobile` for example, and not by a path.

## The Default Group

The default group is named in `config.json`, in the `MELBIS_TEMPLATE` parameter; it can be read through `engine_dev_config`. The storefront is built by it until the project switches the group from code.

If a template is missing from the current group or is empty, the engine takes the template of the same name from the default group. Only the templates are inherited: the statics are built by their own group. In detail — "[Template Groups](../Dev/tpl_group.md)".

That is why the default group can be neither renamed nor deleted here: the configuration would go on naming a group that does not exist, and the other groups would lose their fallback templates.

## A New Group

`engine_template_add` creates an empty group — only the four folders, with no templates, statics or images. The storefront on such a group is not empty: it takes the templates from the default group until the new one has its own.

In the store's map a new group shows the folder of every module at once, even if those folders are not on the disk yet.
