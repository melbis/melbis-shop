# Template Groups

All project markup lives in the `templates/` directory, organized into named
groups. A group is a complete set of `.htm` storefront files: one group provides
one visual appearance. Additional groups are created for a mobile version, an
alternative design, or a separate partner section layout.

This section covers how template files are structured, how groups inherit from
each other, and how to switch groups from code. How the module works with
templates internally is described in the "Templating Engine Methods" section.

## Template File Structure

Templates are stored in the `templates/` directory at the project root. Inside are named template groups:

```
templates/
    default/
        units/
            melbis_base_page/
                main.htm
                page_index.htm
                page_goods.htm
                page_404.htm
            melbis_store_card/
                main.htm
            melbis_cataloge/
                main.htm
```

Each module has its own subdirectory inside `units/`, which stores all its `.htm` templates. A single module can contain any number of templates — they are loaded by `Tpl*` methods using the filename without the extension.

## Active Group

The `default` group is used by default. After creating a new group, it will automatically appear in each module's tree in the IDE.

The active group is set in `config.json` (the `MELBIS_TEMPLATE` parameter) or via the **"Development → Installation"** dialog — this is described in detail in the "Configuration" section. This value remains in effect until the project switches the group from code.

## Shared Code Between Groups

An additional group rarely differs from `default` entirely — usually it contains the same templates with minor modifications. Full duplication is unnecessary; there are two mechanisms for this.

**An empty file inherits the default group.** If an `.htm` file in a group is empty (or absent), the engine takes the file with the same name from the `MELBIS_TEMPLATE` group. An empty `templates/mobi/units/melbis_cataloge/main.htm` will serve the content of `templates/default/units/melbis_cataloge/main.htm`. Such an empty file reads as an explicit marker "everything here is the same as in `default`" — unlike a full copy, where you can't tell later whether it contains differences or not. Inheritance is single-level — only to `default`, not chained; if the file is missing from both the group and `default`, the build will log an error.

**Minor differences — via a group condition.** Divergences are conveniently kept in a single shared file, marking sections with a condition based on the current group:

```html
{*TEMPLATE==default} <div class="page-wide"> {TEMPLATE*}
{*TEMPLATE==mobi}    <div class="page-narrow"> {TEMPLATE*}
```

The file lives in `default`, and `mobi` pulls it in via an empty inheritor file; under each group its own branch is active, because `{TEMPLATE}` differs. You can branch by language the same way — `{*LANG==ru} … {LANG*}`. Condition syntax is covered in the "Template Syntax" section.

Inheritance applies only to `.htm` files: static assets (`.js`/`.css`) are built per their own group (see "Static Asset Build"). A shared script is usually kept as a single file and branched internally by version, passing it from the template — `<script>var TEMPLATE = "{TEMPLATE}";</script>`.

## Switching Group and Language from Code

**`TemplateSet($mTemplate)`** and **`TemplateGet()`** switch and return the
active template group; **`LanguageSet($mLang)`** and **`LanguageGet()`** handle the page
language. Defaults for both are taken from `config.json` (`MELBIS_TEMPLATE` and
`MELBIS_LANG`).

Switching makes sense in the root script, **before calling `Run`** — that way the
choice will apply to all modules on the page:

```php
MELBIS()->TemplateSet('mobile');
MELBIS()->LanguageSet('en');

MELBIS()->Run($entry_point, $entry_param);
```

A typical case is selecting the mobile group based on the visitor's device, or the language based on the URL segment. The device and preferred language are provided by the `AgentDevice` and
`AgentLanguage` methods — see "Visitor".

The reading pair is needed in module code when logic depends on the storefront version or
language:

```php
$is_mobile = ( MELBIS()->TemplateGet() == 'mobile' );
$lang      = MELBIS()->LanguageGet();
```

The multilingual modifiers `inum`, `inums`, and `idate` also depend on the current language
(see "Modifiers"). In the templates themselves, both values are available without PHP —
via the system tags `{TEMPLATE}` and `{LANG}`.

The cache is physically organized by group: each group has its own base cache and
Trick cache directories. Therefore, the same URL served under different groups does not overlap in the cache — two storefront versions can coexist on the same domain.