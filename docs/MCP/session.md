# The Session

A session is the MCP server's work with one store under one login. Everything the agent does with the store happens inside an open session. Four tools of the `session_` family open it and serve it, and none of them requires a right: a session has to open before anything can be forbidden.

## The Order

1. [`session_init`](session_init.md) — look around: which store the session will
   sign in to and whether the program is running. It changes nothing and may be
   called as often as you like.
2. [`session_connect`](session_connect.md) — open the session with the topic of the
   conversation. The answer tells everything about it and brings the agent's common
   rules.
3. Work. If a tool has rules of its own, its first call refuses with them. The
   agent accepts the rules with the
   [`session_rules_accept`](session_rules_accept.md) tool and repeats the call.
4. Cleanup. The server does not clear the conversation folders by itself:
   [`session_clear`](session_clear.md) shows them and removes the ones the user
   names.

## Why the Agent Names Only the Topic at Sign-in

The agent names the conversation with one word, the topic: the server keeps the conversation folder under it, and after a break the same topic brings the agent back to its data. Beyond that the agent chooses nothing at sign-in — neither the store nor the login. The store is the one last opened in the program, or the one the server is pinned to. The login is that of the person sitting at the program, or the one saved in its settings. So the agent will not sign in where the person has not signed in, and the password does not get into the correspondence. Only a person can switch the store, and only in the program. How exactly the server finds the store and the login-password pair — "[Sign-in, Rights, License](access.md)".

## What the Session Remembers

At sign-in the server remembers, and holds until the end of the session:

| What | Why |
|---|---|
| the store and the login | every request goes to this store under this login |
| the topic of the conversation | everything the agent and the tools write to this computer goes into the folder of this topic |
| the granted rights | a call of a tool that is not granted the server refuses at once, without going to the store |
| the critical notes of the memory: how many there are and which have been read | until they have all been read, the other tools refuse |
| the accepted rules of the tools and the random number for their codes | accepted rules are not asked for again, and a code from another session does not fit |

The license the session does not remember: the server looks for the license file anew at every request.

## When the Session Ends

The session lives in the server's process. It ends when:

* **the agent application has restarted the server.** Some applications, Claude
  Code among them, do it by themselves, in the middle of the work as well. The
  next call gets `Not connected`, and the agent signs in anew with the same topic:
  the conversation folder and its data are still in place;
* **the agent has called `session_connect` again.** The session opens anew, and
  everything remembered is reset.

## When to Sign in Again

| What happened | Why |
|---|---|
| a `STORE_SWITCHED` refusal has come | another store is open in the program, and a server that is not pinned works only with the program's store |
| the owner has granted or withdrawn a right | the rights are remembered at sign-in |
| a `Not connected` refusal has come | there is no session |
