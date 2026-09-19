# System Constants

Thirteen values are available in any template of any module — there is no need to pass them via
`TplAssign`, they are substituted by the platform automatically.

> **They can be transformed with modifiers and compared in conditions.** There is no need to pass
> them via `TplAssign`, but in a template they behave like regular
> variables: `{TEMPLATE|upper}` will apply a modifier, and `{*TEMPLATE==mobi} …
> {TEMPLATE*}` will show a block only for the required group. They are substituted late —
> on the finished output of the module (see "When Substitution Occurs").

## Paths and Styling

| Tag | Value |
|---|---|
| `{ROOT}` | base path of the site |
| `{PATH}` | path to the current template group, same as `{ROOT}templates/{TEMPLATE}` |
| `{TEMPLATE}` | name of the current template group |
| `{MELBIS_COPY}` | ready-made "Powered by Melbis Shop" link with version number |

```html
<link rel="stylesheet" href="{PATH}/statics/bundle.base.css">
<img src="{PATH}/images/design/logo.png">
```

`{PATH}` is the most frequently used of all: every link to a static asset, image, or bundle
is built from it, and when the template group is switched, the paths update automatically.

## Language and Locale

| Tag | Value |
|---|---|
| `{LANG}` | current page language |
| `{CHARSET}` | project encoding |
| `{TIME_ZONE}` | project time zone |

```html
<html lang="{LANG}">
<meta charset="{CHARSET}">
```

The language and template group can be switched from code — using the `LanguageSet` and
`TemplateSet` methods before calling `Run` (see "Template Groups").

## Visitor Session

| Tag | Value |
|---|---|
| `{CSRF_TOKEN}` | form protection token |
| `{PHP_SESS_ID}` | visitor session identifier |

```html
<form method="post" action="/order/">
    <input type="hidden" name="csrf" value="{CSRF_TOKEN}">
</form>
```

> If `Hasn't session` is displayed instead of a value, it means `DefineSession` has not been
> called in the root script — see "Sessions and CSRF".

## Versions and Cache

| Tag | Value |
|---|---|
| `{BUILD}` | **project** build number — the `MELBIS_BUILD` parameter from `config.json` |
| `{SCRIPT_VERSION}` / `{SCRIPT_BUILD}` | **engine** version and build, defined within the platform itself |
| `{LAST_MODIFY}` | date of the last page cache update in HTTP format |

It is important to clearly distinguish the first two: `{BUILD}` is your project — it increments when
files are saved in the Workbench and serves as a static asset version marker; `{SCRIPT_*}`
is the version of the Melbis Shop engine itself and has nothing to do with your code.

```html
<script defer src="{PATH}/statics/bundle.melbis.js?{BUILD}"></script>
```

As long as the number has not changed, the browser loads the file from its own cache; once it increases, the file is downloaded
again. For more details, see "Static Asset Build".

## When Substitution Occurs

System tags are resolved **late — on the finished output of the module**, after
the cache and after the module's own data has been parsed. This has practical implications:

* They work **everywhere** — including inside blocks coming from the cache and inside
  values assembled by other templates.
* The value is always **current for the present request**: language, group, and session token
  are taken "here and now", rather than being baked into the module cache.
* On this same late pass, **modifiers** (`{LANG|upper}`) and
  **conditions** (`{*TEMPLATE==mobi} … {TEMPLATE*}`) are applied to them.
* The downside of late parsing: **inside a condition based on a system tag, only system and global values are visible,
  but not local module variables** — the module has already finished executing by that point. For blocks with row data, use regular
  conditions based on passed variables.

## The Service Tag `{NULL}`

`{NULL}` stands apart — it is a reserved tag available in any template
regardless of what data the module has passed. On its own it always outputs
an empty string and serves as a "carrier" for modifiers that do not require a variable value: this allows a modifier to be applied to static parameters rather than to data from
the database (see the example with a static file in the `|webp` modifier section under
"Modifiers").

`{NULL}` is a regular template engine tag: empty on its own, but modifiers work on it —
that is precisely why it exists.