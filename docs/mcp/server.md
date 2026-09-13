# The server of the Store

The machine the engine lives on: how it was set up, what stands on it and what the
owner can do with it. **You have no access to the server** — no SSH, no files outside
the root of the site, and no tool for it will appear. This chapter is for something
else: so that your advice to the owner is exact rather than "have a look at the logs".

> **Two rules, more important than anything else in this file.**
>
> **1. What is described here is an example, not this server.** Everything below is the
> standard installation out of the box. The real server of a Store may be set up in any
> other way: another host and plan, other versions, its own edits in `nginx.conf` and
> `my.cnf`, an external database, several sites on one machine, no Docker at all. Not one
> number here is a fact about the Store at hand until you have checked it. Any
> investigation begins with finding out how **this particular** server is built — see
> "Finding out how this particular server is built".
>
> **2. Never ask for SSH.** Not access, not the root password, not a key, not "give it to
> me for a minute, I will be quicker myself" — on no pretext and in no wording.
> Everything needed the owner does themselves in the **Server** window of the Program:
> there is the status report there, the console, and the editor of the configuration
> files. Your part is to lead them through that form step by step.

## The current data is in the public repository

The point of truth for the server side is
**[github.com/melbis/melbis-shop](https://github.com/melbis/melbis-shop)**. The built
distribution of the engine is there, and so are:

- **`setup.sh`, `update.sh`, `verify.sh`** — the ones that actually run. The Program
  keeps no copies: on a button press it sends the server a single SSH line, and the
  **server itself** fetches the script of the version line the Program is on and pipes
  it to bash. Read them on that `v<version>` branch: for the newest line it holds the
  same commit as the front page of the repository, for an older Program it does not,
  and the branch is the one that is right. A server with no way out to github fails
  there, and nothing runs at all.
- **Releases** — the current version, what went into a release (changes of the core, for
  users, for developers, fixes) and which build is marked as the recommended stable one.
- **Wiki** — the instructions for moving to a particular version.
- **`Dockerfile`, `config.json`, `readme.md`** — what the image is built of and with
  which PHP settings.

The versions and the numbers in this file are a guide to how things are arranged. **When
the answer depends on an exact value, check it against the repository**, and if you have
no network access, say so honestly and ask the owner to open the page.

## How the Store got here

The standard path for an owner is the five steps of the "starting a store" section of
the melbis.com documentation:

1. **A virtual server** at a host (DigitalOcean in the example): Ubuntu LTS x64, the
   minimal plan of 1 vCPU / 1 GB RAM / 25 GB disk, `root` login by password. Out of it
   come an IP address and a password.
2. **A domain** at a registrar (Cloudflare in the example) and an `@` A-record onto that
   IP. Cloudflare here is not only the registrar but the bot filter in front of the Store
   — which is why nginx is set to read the visitor's real IP from the `CF-Connecting-IP`
   header.
3. **The Melbis Shop Program** on the Windows computer of every member of staff. The
   local database is a working snapshot, not a store of record: it is restored from the
   server.
4. **Installing the server** — from the Program itself: the server window
   (`FServerRemote`), tab `TSInstall`. Up to five minutes, after which the site already answers, but
   with an error: the engine is unpacked, the database is still empty.
5. **Initialising the database** — the installation window of the shop
   (`FServerSetup`, not the server one): the language of the storefront, the time zone, the encoding,
   the Install button, then Save. After that the starting storefront appears on the
   domain, the login is `admin`/`admin`, and the owner changes the password straight
   away.

That is how a Store is installed **by default**. The owner may have walked that path
only part of the way, installed the server by hand, moved the Store from an older
machine or handed the setup to an administrator — and then nothing below will match. Ask,
do not assume.

## What stands on the server after a standard installation

The whole household of the Store is in one folder, `/var/melbis`:

| Path | What it is |
|---|---|
| `www/` | the root of the site: `index.php`, `units/`, `templates/`, `files/`, `config.json` — exactly what you change with the `engine_*` commands |
| `db/` | the MySQL files |
| `cache/` | a RAM disk (tmpfs, 1 GB), mounted into the container as `core/cache` |
| `trick/`, `log/` | `core/trick` and `core/log`; apache and nginx write their journals **there too**, which is why all three channels are visible to the engine and readable by you — see "The server journals" in [files.md](files.md) |
| `certs/`, `certbot/` | the Let's Encrypt certificate and the folder for its challenge |
| `docker-compose.yml`, `nginx.conf`, `my.cnf` | the three configuration files the owner edits straight from the Program |

Three containers run:

| Container | Image | What is inside |
|---|---|---|
| `melbis_db` | `mysql:8.4` | the `melbis` database, the `melbis` user, `utf8mb4`; MyISAM tables, Memory for the temporary ones (from `config.json`) |
| `melbis_shop` | `melbis/melbis-shop:v<version>` | `php:8.3-apache`, the extensions curl, mysqli, xml, zip, iconv, gd, intl, APCu, the ionCube Loader — the whole `core/` in the image is encoded |
| `melbis_proxy` | `nginx:latest` | ports 80 and 443, TLS, proxying to apache |

The host itself: a 4 GB swap file, `logrotate` over `/var/melbis/log/*/*.log` (daily,
seven copies, compressed), `fail2ban`, `ufw` with 22/80/443 open, and two cron jobs — the
certificate renewal at 3:00 and `php cron.php` once a minute (that is the scheduler of
the platform — `../Guide/Russian/Dev/cron.md`).

**There are no container journals:** all three services are given `logging: driver: none`
in `docker-compose.yml`. What to look at is the files in `/var/melbis/log` — and there is
no need to ask the owner for them, you read them yourself as `core/log/...`.

The Program keeps no scripts for installing, updating or reporting. On a button press
it sends over SSH one line of the shape `wget -qO- .../v<version>/update.sh | bash -s
-- <version>`: the fetching is done by the server, from the branch of the Program's
version, and the version — for an installation, the domain as well — goes to the
script as an argument.

## The numbers people trip over

Default values. They explain most of the "inexplicable" refusals — and they are the ones
worth checking first, because every one of them could have been changed.

| The limit | Value | Where it is set | What happens above it |
|---|---|---|---|
| size of a request | **64 MB** | `client_max_body_size` in `nginx.conf` | 413 from nginx, the request never reaches PHP |
| upload size in PHP | 100 MB | `post_max_size`, `upload_max_filesize` of the image | unreachable: the real ceiling is the same 64 MB of nginx |
| files in one request | 100 in the image, and PHP's own default is 20 | `max_file_uploads` of the image | the pack is refused whole, and the refusal names the real number — so it is the answer, not this table, that tells you what this Store takes |
| script time | **30 s** | `max_execution_time` of the image | a long import is cut off, even though the proxy timeouts are 300 s |
| script memory | 512 MB | `memory_limit` of the image | |
| APCu | 64 MB | `apc.shm_size` of the image | here live the static query cache and the Smart statistics — the levels are in `../Guide/Russian/Dev/cache.md`; after a restart of the container it is empty |
| file cache | 1 GB | tmpfs `/var/melbis/cache` | a RAM disk: empty after a reboot of the server, and that is normal |
| MySQL index buffer | 512 MB | `key_buffer_size` in `my.cnf` | the main MyISAM buffer; `innodb_buffer_pool_size` (128 MB) hardly works here |
| a pack of files at a time | 8 MB | `MaxFileSize` in `Shop.ini`, `[DataTransfer]` | this one is on your side already: the remainder is marked `skipped` by `engine_files_load` and fetched by the next call |

Speak of these numbers as defaults: not "you have 64 megabytes" but "by default it is 64
MB there — open `nginx.conf` on the `TSConfig` tab and look at the `client_max_body_size`
line". The difference matters: in the second case the two of you are looking at a fact, in
the first you are guessing.

One conclusion from the table is worth keeping in mind: **the memory of a minimal server
is already allotted**, and the reserve is held by swap. Raising the MySQL buffers "to make
it faster" on such a machine is the straight road to the system starting to kill
processes. Resources are added by upgrading the plan at the host, not by editing `my.cnf`.

## The Server window in the Program

The owner opens it from the development section of the menu. Five tabs, everything over
SSH as `root`; the password is not kept in the Program.

- `TSConnect` — the IP, the root password, the domain name (without `https://` and
  `www.`). Filled in once, before any of the actions below.
- `TSInstall` — one button, `BInstall`, and it installs a server **from scratch:** the
  script takes the containers down along with their volumes, deletes `db`, `www`,
  `cache`, `log` and installs everything anew. Never offer it as a way to fix
  something, and never as a way to update.
- `TSService` — two buttons, and they are not alike. `BVerify` is the status report:
  uptime and load average, `free -h`, the free space on `/`, the sizes of `db`,
  `www/files`, `cache`, `trick`, `log`. It changes nothing, and any conversation about
  "it is slow" or "the space ran out" is worth starting from it. `BUpdate` beside it
  updates the server **in place** — containers down → fresh images pulled → up → old
  images cleaned → `apt upgrade` — and the window itself warns that the result is not
  guaranteed and a backup is strongly advised. Name the two apart when you send the
  owner there.
- `TSConsole` — one command at a time, the answer into the log of the window. The "inside
  the web container" switch runs it as `docker exec -u www-data melbis_shop …`; without
  the switch it runs on the host as root. Dictate the whole command and explain what it
  does; a reading one is always preferable to a changing one.
- `TSConfig` — the editor of the three files: `nginx.conf`, `my.cnf`,
  `docker-compose.yml`. The order is strict: **load from the server → edit → save to the
  server**, and only the file that was read in this window can be saved. The previous
  version stays beside it as `<file>.bak` — one step back is always there. The changes
  come into force after the restart button of that tab (`docker compose down` and `up`),
  during which the Store is unavailable.

`config.json` is **not** in that list: the owner edits it through the installation window
of the shop (`FServerSetup`).

How to word advice: name the file, the section and the whole line — "in `my.cnf`, in the
`[mysqld]` section, replace `key_buffer_size = 512M` with `key_buffer_size = 256M`" —
rather than "reduce the buffer". The owner sees the text of the file in the window and
substitutes the line literally. And always say what will happen after the restart and how
to roll it back (`.bak`).

The window is `FServerRemote` and its tabs are `TSConnect`, `TSInstall`, `TSService`,
`TSConsole` and `TSConfig` — those component names are the stable anchors, and the words
above only describe what each tab is for. **The caption the owner actually sees is in
`Lang/<locale>/FServerRemote.xml`** — take it from there when you name a place to
them, and never translate one yourself.

## Finding out how this particular server is built

The order of a systematic investigation, at the owner's request. They do all of it — you
dictate and read the answers. Not one of the steps below changes anything.

1. **Ask in words.** Who installed the server and when, whether they went the standard
   way from the Program, whether anybody edited the settings afterwards, whether other
   sites live on this machine, whether the plan was changed. Half of the discrepancies come
   out here and for free.
2. **The status report** (`FServerRemote`, tab `TSService`, button `BVerify` —
   `BUpdate` stands beside it on the same tab and is not part of this step). Uptime and
   load, memory, free space, the sizes of the database, the files, the cache, the journals.
   The only snapshot of a server that is taken whole and without risk — every investigation
   starts from it.
3. **`TSConfig` → load from the server** for the file needed: `nginx.conf`, `my.cnf`,
   `docker-compose.yml`. The owner sees the real text in the window and can send it to you.
   That is how the real limits, buffers, volumes and the tag of the image are checked —
   instead of the numbers from this file. Reading is safe: until "save to the server" is
   pressed, nothing on the server changes.
4. **`engine_dev_config`** — the configuration of the engine (`config.json`): the cache,
   the time zone, the encodings, the table type. This one you take yourself.
5. **The console — with reading commands only**, one at a time: `docker ps`, `df -h`,
   `free -h`, `nginx -t`, `php -v`. With a line for each about what it shows: everything
   runs as root, and it is the owner who presses the button.
6. And only now — the conclusions and the suggestions.

Whatever was missing, name it plainly: "this particular fact is missing, look here". An
imagined server is worse than an unknown one: it produces confident and wrong advice.

## The versions of the Program and of the engine

The engine checks the versions on every request, yours included: this MCP server sends the
same `version`, `build` and `require` as the Program, taking them from `Shop.ini`.

| The answer | What it means | The advice to the owner |
|---|---|---|
| `VERSION_SERVER` | the Program needs an engine newer than the installed one | update the server in place — `BUpdate` on tab `TSService`, never `TSInstall`, which installs from scratch |
| `VERSION_CLIENT` | the engine needs a newer Program | update the Program from melbis.com |
| `VERSION_FAIL` | the versions do not match **exactly** — the engine compares all three parts, so 6.5.0 against 6.5.1 parts them just as 6.5 against 6.6 would; the answer names both sides | an update of the image does not change the version by itself: the `image:` tag was written into `docker-compose.yml` at installation, and changing it is the owner's decision |

Before saying "update", look at the **Releases** in the repository: which build is marked
as the recommended stable one and what went into it. That is what tells advice from a
guess — and it also shows whether the update solves the problem the whole thing was
started for.

## The configuration of the engine

- **`engine_dev_config`** — the parameters of the Store's `config.json` (charsets, cache,
  time zone, the default template and so on). Secret values (passwords, keys, tokens) come
  as `<hidden>`: the key is visible, so a secret exists and has been cut out, but the value
  itself never reaches your context. Do not ask for the real values of secrets and do not
  print them.
- **Writing the configuration** (`SAVE_SERVER_PARAMS`) is deliberately **not** exposed as
  a tool. The engine rewrites `config.json` whole (any key not passed is lost), and `LOAD`
  gives the secrets hidden — so there is no safe load-modify-save cycle. The configuration
  is changed by the owner through the Program.

## What not to advise

- **Asking for SSH access**, the root password, a key, or "send me access, I will be
  quicker myself". This is not a matter of convenience: the keys to the server are the
  owner's property, and the whole arrangement of the work is such that they stay with them.
- **Leaning on the numbers in this file as facts.** The check first, the conclusion after.
- **Installing a server** on a working Store — that is wiping the data.
- Deleting or moving `/var/melbis/db` by hand — the database lies there whole.
- Editing anything in `www/core`: the engine reads `core/` from the image (the folder is
  covered by a volume of its own), and what lies on the host disk it does not see. The code
  there is ionCube-encoded anyway.
- Changing the MySQL password: it is written both in `docker-compose.yml` and in
  `config.json`, and once they part they bring the Store down.
- Any "optimisations" before the status report. The numbers first, the advice after.

And one observation about `shop_page`: it goes to the Store from outside, which means
through Cloudflare and nginx. An unusual answer (403, 503, a browser-check page) may come
from them and not from the engine — so before looking for the cause in a module, check
against the status report and ask the owner whether the protection is on.
