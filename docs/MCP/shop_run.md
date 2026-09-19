# shop_run

Runs the agent's module on the store's storefront through the entry point `agent.php` and returns the module's answer.

## The Right

Not required; to create and to change a module — "Direct access → Development → Modify data" (`AGENT_ENGINE_DEV_WRITE`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| `mod` | string | the module's name without `.php`, starts with `agent_`: `agent_price_audit` |
| [`params`] | object | the values for the module by their names |

The values go the same way as the fields of a form. A string, a number or a yes/no comes to the module as a string: `5`, `true`; `null` as an empty string. A list of such values comes as an array, an empty list does not go at all. An object and a list inside a list are a refusal. All the values come in one serialized argument: `post: serial` is declared in the manifest, the values lie in `$mVars['post']`.

## The Module

A module is created by [`engine_php_add`](engine_php_add.md) and written by [`engine_php_save`](engine_php_save.md) with `ajax_load: 1` — that is the entry point's flag. For example, the module `units/agent_hello.php` with `param_info` `post: serial`:

```php
<?php
function AGENT_HELLO($mVars)
{
    $name = $mVars['post']['name'] ?? 'world';
    $text = 'Hello, '.$name;

    return $text;
}
?>
```

The call `{"mod": "agent_hello", "params": {"name": "Melbis"}}` will answer `Hello, Melbis`. How the module, its manifest and its parameters are made — "[Modular Scripts](../Dev/unit.md)".

## The Answer

```json
{"mod": "agent_hello", "status": 200, "bytes": 13, "time": 47,
 "file": "D:\\Melbis\\shop.example.com\\mcp\\melbis\\runs\\agent_hello.txt", "cut": false}
```

Then as text — the module's answer, up to 8000 bytes.

| Field | What it is |
|---|---|
| `mod` | the module |
| `status` | the HTTP answer's code — 200 on success |
| `bytes` | the size of the module's answer |
| `time` | how long the request took, ms |
| `file` | where the whole answer is saved; every run of the module overwrites the file |
| `cut` | the answer is longer than 8000 bytes: in the text is its beginning, the whole of it — in `file` |

## Refusals

If the storefront has answered with a code other than 200, the module did not run or has fallen. The refusal shows `mod`, `status` and the beginning of the storefront's answer, names the file where the whole answer lies, and the storefront's log `core/log/melbis/front.log` — there the engine writes the message, the file and the line of the fall. The log is read by [`engine_static_load`](engine_static_load.md).

| The beginning of the storefront's answer | When | What to do |
|---|---|---|
| `NO_MODULE: there is no units/<module>.php` | there is no such module | check the name against the map, create the module with `engine_php_add` |
| `NO_ENTRY_POINT: set entry_point = 1 for unit <module>` | the module is not an entry point | save it with `ajax_load: 1` |
| `ACCESS_DENIED` | the session key did not fit: the store's server has been restarted or the key is older than a day | open the session anew |
| another answer with the code 404 | there is no `agent.php` in the site's root | update the store |

The refusals before the request to the storefront:

| Answer | When |
|---|---|
| `mod is required…` | no module is named |
| `The entry point runs the modules of the agent only…` | the module's name does not start with `agent_` |
| `The field [<name>] of params takes a value or a list of values…` | an object or a list inside a list in `params` |
| `This store gave out no key for the entry point…` | `session_connect` got no key: the key is kept in APCu, and something is wrong with APCu on the server |

The common refusals — "[Answers and Refusals](answers.md)".
