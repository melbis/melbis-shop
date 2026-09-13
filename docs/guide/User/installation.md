# Installation

The **"Installation"** window sets the store's initial parameters on the server: database access, language and character sets, parser behaviour, access restrictions, and it also performs a full copy and restore of the store.

The settings are read from the server when the window opens and written back with the **"Save"** button; **"Cancel"** closes the window without changing anything.

> The section is meant for the initial setup and for one-off jobs. A mistake in
> these parameters stops the whole store.

## System

**Database** — **Host**, **Database name**, **User**, **Password**, **Table prefix**, **Table charset**, **Table engine**, and **Temporary tables**. The prefix makes it possible to keep several stores in one database. The **"Additional commands for the DBMS"** field is executed on every connection — that is where the settings a particular server requires are put.

The group's buttons:

* **"Check"** — a trial connection with the parameters entered; it changes nothing.
  Any editing is worth starting with it.
* **"Installation"** — creating the store's tables. The reference data is filled in
  the language chosen in the **"Settings language"** field next to it.
* **"Set"** — reinstalling the language settings only: the names in the reference
  directories are replaced with the defaults for the chosen language.
* **"Repair"** — repairing damaged tables by the means of the DBMS.

> **Warning!** "Installation" destroys all the store's data. The application asks
> for confirmation.

**Store and application** — **Time zone**, **Primary language**, **Encoding**, and **Character set**.

**Parser** — **Caching** (on or off as a whole), **Primary template**, **Site path**, **Build**, and **Debugger password**. These parameters are covered in more detail in the developer guide, "[Configuration](../Dev/config.md)".

## Security

The **"Access from IP"** field limits the circle of those who may work with the store: individual addresses and masks of the form `192.168.0-127.*` are accepted. The administrators group is always allowed in, so you cannot lock yourself out with this field. The **"Mine"** button fills in the address you are working from right now.

The **"Blocking user access for backup"** group — the **start time** and the **end time** of the daily window when the server does not let users into the store. It exists so that a backup taken by the hosting's own means captures the data in a quiet state.

The **"Keep a log of store users' actions"** checkbox turns on the recording of employees' actions.

### Demonstration Mode

While the marker file `admin.here` lies in the store's root, the store runs in demonstration mode: no license is required, user rights are not checked — whoever signs in counts as an administrator — and no action log is kept. The marker is placed by the server installation so that a new store can be entered before there is either a license or a single employee.

In this mode a warning and a **"Switch to working mode"** button are visible at the top of the window. It deletes the marker, after which authentication, licensing, and the log come into force. Demonstration mode cannot be brought back from the application — the file is created only on the server.

## Secrets

The **"Secrets"** tab holds the keys the store introduces itself to other services with: the keys of payment systems, the tokens of delivery and mailing services, its own signatures for links. These are values that must be shown to nobody and that a working store nevertheless needs every day.

The table has two columns — **Constant** and **Value**; a long value is visible in full and edited in the field below the table. A row is added with the button on the panel or with the `Insert` key, and deleted with the button next to it or with the `Delete` key, with a confirmation.

The **Constant** is the name the key is visible by in the code of the store's modules, so it must consist of Latin letters, digits and the underscore, and must not start with a digit. Names beginning with `MELBIS_` are taken by the platform itself: with that prefix it separates its own parameters from yours. An unsuitable name will not get through — the save will not start until the row is corrected.

> The values of the secrets live only while the window is open: they are not saved on the workstation, they do not go into the store's backup, and on a new server they are entered afresh. Saving writes the whole set to the server at once, so a row deleted here deletes the key on the server as well, and a key added to the configuration file by hand will disappear on the very first save.

For the developer who writes the store's modules these keys are available as ordinary PHP constants — more on that in the developer guide, "[Configuration](../Dev/config.md)".

## Copying

The **"Copying"** tab collects the store into a single zip archive. The required kinds of data are ticked: the **database**, **files** (product and section images), **PHP modules and HTML templates**, **local settings** (table and column sizes, profiles); the **"Select all"** button ticks everything at once.

> Copying a large store takes a long time. This function is intended for
> developers — to make a demo version, for example. For the store owner, a backup
> by the hosting's own means will be faster and more reliable. Before starting,
> make sure nobody is working with the store.

## Restoring

The **"Restoring"** tab deploys a store from such an archive. The **"Load store"** button becomes available only after the confirmation checkbox, because the operation replaces the entire contents of the store.

The progress is visible step by step: **unpacking the archive**, **loading modules and templates**, **loading the database**, **loading files**. A step for which there is no data in the archive stays greyed out.

> **Warning!** All the store's current data will be lost.

The archive does not carry the database connection parameters: the configuration is the face of a particular installation, and the server keeps its own. So after restoring, check the "System" tab — the data is deployed into the database named there.

A restored store may turn out to be in demonstration mode — it can then be brought back to ordinary work with the **"Switch to working mode"** button.
