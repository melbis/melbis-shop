# Server

The **"Server"** window manages the store's server directly over SSH: it installs and updates it, shows its state, runs commands, and edits configuration files. Everything is done as **root**, so the section is available to the store owner and is meant for one-off jobs rather than for everyday operation.

The progress of any operation is visible in the **log** at the bottom of the window; the **"Save log"** button writes it out to a text file. The connection is opened for the duration of the operation and closed when it finishes.

## Connection

The **"Connection"** tab holds the parameters shared by the whole window: the server's **IP address**, the **root password**, and the store's **domain name**. The password is not saved: the field is cleared when the window closes.

## Installation

The **"Installation"** tab, the **"Install server"** button — a full automatic installation: the application downloads the project's setup script and deploys the server for the given domain and the current version of the platform.

> **Warning!** Installation deletes all current settings and store data. The
> application asks for confirmation, but once started the operation cannot be
> cancelled.

## Maintenance

The **"Maintenance"** tab:

* **"Update server"** — updating the server to the version matching the current
  version of the application. The result is unpredictable if the server was
  configured by hand, so make a backup before updating.
* **"Status report"** — server diagnostics. It changes nothing and only writes
  information about the state of the store's services to the log.

## Console

The **"Console"** tab runs arbitrary commands: a command is typed into the **"Run command"** field and sent with the **"Send"** button or the Enter key, and the reply goes to the log.

The **"Inside the web container"** checkbox runs the command not on the server itself but inside the store's container, as the web server's user — this is how the engine's environment is checked (versions, file permissions, availability of PHP extensions).

> **Careful!** Without this checkbox the command runs on the server as root.

## Settings

The **"Settings"** tab edits the server's configuration files: **Nginx**, the **MySQL database**, and the **Docker containers**. The order of work: choose the file, press **"Load from server"**, edit the text, press **"Save to server"**.

Only the file that was loaded in this same window can be saved — this rules out writing your edits into someone else's file. The server keeps the previous contents next to it with the `.bak` extension, so one step back is always available.

The **"Restart server"** button stops and starts the store's containers again — this is what applies the edits you have made. The store is unavailable for that time.

> **Careful!** A mistake in a configuration file can stop the store from working.
