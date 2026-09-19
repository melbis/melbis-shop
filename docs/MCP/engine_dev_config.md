# engine_dev_config

Gives out the store's parameters from `config.json`. The values of the secrets are hidden.

## The Right

"Direct access → Development → Get configuration" (`AGENT_ENGINE_DEV_CONFIG`).

## Parameters

| Parameter | Type | What it is |
|---|---|---|
| [`reload`] | yes/no | read the configuration anew rather than from the session's memory |

## The Answer

```json
{"config": {"MELBIS_DB_HOST_NAME": "localhost:3311", "MELBIS_DB_USER_PASS": "<hidden>",
            "MELBIS_DB_NICK": "ms", "MELBIS_CACHE": "True", "MELBIS_TEMPLATE": "default",
            "MELBIS_BUILD": "25", "MELBIS_DEBUG_CODE": "<hidden>", "APIKEY_LIQPAY_KEY": "<hidden>",
            "MELBIS_MYIP": "203.0.113.7"}}
```

| Field | What it is |
|---|---|
| `config` | the keys of `config.json` in the file's order, the values as strings. `MELBIS_CACHE` and `MELBIS_USER_LOG` come as `True` or `False`. The time zone lists `MELBIS_TIME_ZONE_LIST` and `MELBIS_TIME_ZONE_KEYS` do not come, and for a store in demo mode `MELBIS_ADMIN_HERE` is added |

**The hidden values.** `<hidden>` instead of the value comes for every key whose name does not begin with `MELBIS_`, and for the passwords and the codes among the `MELBIS_` keys.

**`MELBIS_MYIP`** is not a parameter of the file: it is the address of the computer the request came from, as the server sees it. It is written into the list of allowed addresses when access to the store is restricted.

## Refusals

| Answer | When |
|---|---|
| `PHP version … or higher is required!` | the server has an old version of PHP |
| `PHP module … not found!` | the server does not have the PHP module needed |
| `Wrong mode for /core/tmp/, need read/write access mode` and the same ones for `/core/cache/`, `/core/trick/`, `/profiles/`, `/files/`, `/templates/`, `/units/` and the site's root | the engine has no right to write into this folder |

Reading the configuration first checks the server: the version of PHP, its modules and the rights to the service folders. These refusals speak of the server's setup, not of the configuration itself.

The common refusals — "[Answers and Refusals](answers.md)".
