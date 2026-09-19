# Templates

The `engine_html_*` section works with the templates of modules — the `.htm` markup files a module builds its HTML from.

## Where They Live

```
templates/<group>/units/<module>/<template>.htm
```

A template's path always consists of these parts.

**A template group** is a complete set of the storefront's templates. There can be several groups. If a template is missing from a group or is empty, the engine takes the template of the same name from the main group, so an edit of a template in the main group changes such groups too. In detail — "[Template Groups](../Dev/tpl_group.md)".

**`main.htm`** appears together with the module, when `engine_php_add` creates it. A module may have any number of other templates; `engine_html_add` adds them.

Not only templates lie in the module's folder. Its `css`, `js` and `psv` that go into bundles are statics: the section "[Statics](engine_static.md)" reads and saves them.

## How a Template Is Rendered

A module builds its HTML out of the templates by the file name without `.htm` — with the `TplParse` method and the other `Tpl*` methods. Another template of the same module can be included right in the markup with the `{^NAME}` tag: `{^ORDER_ABSENT}` will include `order_absent.htm` from the same folder. In detail — "[Template Engine Methods](../Dev/tpl_api.md)" and "[Template Syntax](../Dev/tpl_syntax.md)".

## What Saving Does

* **Rewrites the file whole** and **creates nothing**: a new template is added by
  `engine_html_add`.
* **Resets its module's cache** — the basic one and Trick — in all the template
  groups at once, and with it the Smart statistics of that module.
* **Writes a version** of the file, if that is turned on in the program's Workbench
  settings on this computer.
* **Gives no remarks**: the code check works only for modules.

A template needs no build number: the browser does not keep the HTML of a page.
