# engine_map_units

The signatures of the modules' functions: what can be called from a module or a library without reading the whole file.

## The Right

"Direct access → Development → Read data" (`AGENT_ENGINE_DEV_READ`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`source`] | string | one module: the name without `.php`, `melbis_cataloge` for example; with `.php` it is accepted too |
| [`reload`] | yes/no | read the functions from the store anew |

## The Answer

```json
{
  "functions": {"columns": ["source", "name", "signature"],
                "rows": [["melbis_cataloge", "Main", "($mVars)"],
                         ["melbis_inc_auth", "MELBIS_INC_AUTH_command", "($mUserId, $mCommand)"]]}
}
```

| Column | What it is |
|---|---|
| `source` | the module, the name without `.php` |
| `name` | the name of the function. For a module with a namespace — without it: `Main`, `CmdList` |
| `signature` | the function's arguments in brackets |

### The Text

Comes only if there are no functions: there are no modules in the store, or no functions were found in the module named in `source` — then the text reminds that the name is written without `.php`.

## Refusals

It has none of its own. The common refusals — "[Answers and Refusals](answers.md)".
