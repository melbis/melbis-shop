# Activity Log

The application talks to the store in commands: every action — opening a reference directory, saving prices, sending a file — is a request to the server and a reply to it. The **"Activity Log"** window shows this exchange line by line and is the first place worth looking when an operation does not go through.

## What the Log Shows

Every request is recorded with its time and its content: the command name, its parameters, the name of the file being sent, marks showing that a data archive went along with the command. The server's reply is recorded on the next line. Long replies are truncated — the log shows the beginning, which is enough to see the outcome.

The **"Show response headers"** checkbox adds the connection's service headers to every reply — they are needed when sorting out problems with the connection, a proxy, or caching, and in ordinary work they only get in the way of reading.

The **"Clear"** button empties the log, and the **"Keep on top of other windows"** checkbox holds the window above the rest. The log lives only in the application's memory: on the next start it is empty.

## When the Log Helps

The server's reply shows the reason for a refusal, and it is more precise than a general error message: the license key has expired or does not match, the password is wrong, the employee has no right to the command, the store is closed for maintenance, access from this IP address is forbidden, the command did not fit into the time allowed, or the volume of data exceeded what is permitted.

If the server does not answer for longer than it should, the application offers to stop waiting — interrupting closes the connection but does not undo the part of the work already done on the server.

Requests from the AI assistant land here as well, so the log also shows which commands it has been running.
