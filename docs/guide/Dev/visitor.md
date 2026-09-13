# Visitor

Even before the session starts and before the first database request, something is
already known about the visitor — from the request headers. Where they connected
from, what they are browsing on, and what language they speak. These three things
are returned by the `Agent*` family of methods.

They all share one important trait: the data comes **from outside**, from the client.
It is used to decide what to show, but it cannot be trusted as verified information —
anyone can forge a header.

> **All three methods are for the root script.** They must not be called inside a
> cacheable module: the response will be "baked" into the cache and served to
> visitors it does not belong to. Modules receive this information already in the
> form of a selected template group, page language, or an explicitly passed parameter.

## Visitor IP

```php
$ip = MELBIS()->AgentIp();
```

The method returns the visitor's address taking into account that the site may be
behind a proxy or CDN — forwarding headers are parsed, not just the direct
connection address. Use this for logs, anti-fraud, and region detection instead of
accessing `$_SERVER['REMOTE_ADDR']` directly.

The method does not depend on the session and works regardless of whether
`DefineSession` has been called.

## Device

```php
$device = MELBIS()->AgentDevice();
```

The method returns one of three strings — `phone`, `tablet`, or `desktop` — and is
needed where the storefront is served in different versions from the same address:
it is used to select a template group before calling `Run()`.

Detection happens in two steps. First, the browser itself is queried: Chromium over
HTTPS sends the client hint `Sec-CH-UA-Mobile`, which is not a guess but the
browser's own report about itself. If the hint is absent — Safari, Firefox, and
search bots do not send it — the `User-Agent` is parsed against sets of keywords:
tablet patterns first, then phone patterns. If nothing matches — `desktop`.

The tablet is intentionally separated from the phone: whether to treat it as a
mobile version or a full version is a project decision, not an engine decision.

```php
$device = MELBIS()->AgentDevice();
if ( $device == 'phone' ) MELBIS()->TemplateSet('mobile');

MELBIS()->Run($entry_point, $entry_param);
```

How template groups work and why two versions of a storefront can live on the same
domain is covered in the "Template Groups" section.

The keyword sets can be overridden if needed — entirely or one at a time —
before the first call to `AgentDevice`:

```php
MELBIS()->DefineAgentKeys([
    'tablet'    => '/ipad|tablet|playbook|silk|kindle|nexus 7/i'
    ]);
```

The key must be `phone` or `tablet`, and the value must be a complete regular
expression including delimiters and flags. An unknown key or an invalid pattern
stops the parser with an error: otherwise a typo in a regular expression would
silently send all mobile traffic to the full version. The `Sec-CH-UA-Mobile` hint
is not configurable — there is nothing to improve about a good signal.

The result is computed once per request, so calling the method again costs nothing.

When serving different markup from the same address, add the `Vary: User-Agent`
header to the response — this tells search engines and intermediate caches that the
page content depends on the device.

## Browser Language

```php
$lang = MELBIS()->AgentLanguage(['ru', 'uk'], 'uk');
```

The method parses the `Accept-Language` header and returns the language the visitor
prefers most — as a two-letter code. The first argument is the list of languages the
storefront supports; the second is what to return if none of them match the visitor's
preferences. Without arguments, the method returns the single most preferred language,
whatever it may be.

It is used in the same place as `AgentDevice` — in the root script, to greet a new
visitor in their language: either by inserting the appropriate language prefix into
the URL or by calling `LanguageSet` directly.

**Why you cannot simply take the first two characters of the header.** The browser
sends not one language but a list with weights:

```
uk-UA,uk;q=0.9,ru;q=0.8,en-US;q=0.7,en;q=0.6
```

The weight `q` (from 0 to 1, default 1) defines the preference — the order of
elements formally means nothing, although browsers typically write them in descending
order. With the header `en-US,ru;q=0.9,uk;q=0.8`, the naive "first two characters"
approach yields `en`, and a visitor who explicitly requested Russian as their second
choice gets the default language instead. `AgentLanguage` removes everything the
storefront does not support from the list and picks the highest weight among what
remains.

The method also handles three more things on its own: it reduces regional variants
to the base language (`uk-UA` and `uk` are both `uk`, and the higher weight is
taken), it skips `q=0` — which is an explicit rejection of a language, not a weak
preference — and it ignores `*` and garbage values.

The header is parsed once per request, so repeated calls with different language
lists cost nothing.

The header may be absent entirely — search bots and some API clients do not send it.
In that case the method returns the default value, and this is the normal path, not
an error: a bot should receive the primary language version, not the result of
guessing.