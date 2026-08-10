/************************************************************************************************************
 * @version 6.5.0.370 @ 2026-08-10
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 ************************************************************************************************************/


INSERT INTO {DBNICK}_agent_tool (id, name, descr, tindex, tlevel, absindex, folder, use_unit, use_func) VALUES ('2', 'Business', '', '0', '0', '0', '1', '', '');
INSERT INTO {DBNICK}_agent_tool (id, name, descr, tindex, tlevel, absindex, folder, use_unit, use_func) VALUES ('1', 'Scheduler', 'The scheduler of the program: tasks and their feed - the owner and the staff see them in their task lists. The authorized person signs as the author. Not sure of the executor login - read the Users tool first.', '2', '1', '1', '0', 'melbis_agent_task.php', 'MELBIS_AGENT_TASK');
INSERT INTO {DBNICK}_agent_tool (id, name, descr, tindex, tlevel, absindex, folder, use_unit, use_func) VALUES ('3', 'System', '', '0', '0', '2', '1', '', '');
INSERT INTO {DBNICK}_agent_tool (id, name, descr, tindex, tlevel, absindex, folder, use_unit, use_func) VALUES ('4', 'Users', 'Users of the store: who they are, adding, changing, blocking, deleting.', '3', '1', '3', '0', 'melbis_agent_user.php', 'MELBIS_AGENT_USER');
INSERT INTO {DBNICK}_agent_tool (id, name, descr, tindex, tlevel, absindex, folder, use_unit, use_func) VALUES ('5', 'Basic Settings', 'The settings registry: the dictionaries behind values like kDefault or kNew (TASK_KIND_KEY and the rest). Ask before writing a value you are not sure about.', '3', '1', '4', '0', 'melbis_agent_common.php', 'MELBIS_AGENT_COMMON_key');
UPDATE {DBNICK}_generator SET gen_value = 5 WHERE table_name = 'agent_tool';

INSERT INTO {DBNICK}_agent_tool_param (id, tool_id, command, descr, pos) VALUES ('1', '1', 'CMD_TASK_LIST', 'The open tasks the person may see; closed=yes - the last closed ones.', '1');
INSERT INTO {DBNICK}_agent_tool_param (id, tool_id, command, descr, pos) VALUES ('2', '1', 'CMD_TASK_ADD', 'A new task: the authorized person becomes its author. A login or kind miss answers with the valid values.', '2');
INSERT INTO {DBNICK}_agent_tool_param (id, tool_id, command, descr, pos) VALUES ('4', '5', 'CMD_LIST', 'The dictionary codes, or the values of the one key_code names.', '1');
INSERT INTO {DBNICK}_agent_tool_param (id, tool_id, command, descr, pos) VALUES ('6', '4', 'CMD_ADD', 'A new user. No password field: the tool generates one and answers it once - hand it to the person, advise to change it, never store it.', '2');
INSERT INTO {DBNICK}_agent_tool_param (id, tool_id, command, descr, pos) VALUES ('7', '4', 'CMD_UPDATE', 'Changes one user by id: name, groups, phone, email, blocked (yes|no).', '3');
INSERT INTO {DBNICK}_agent_tool_param (id, tool_id, command, descr, pos) VALUES ('8', '4', 'CMD_REMOVE', 'Deletes one user by id. One with history is refused - block via CMD_UPDATE instead.', '4');
INSERT INTO {DBNICK}_agent_tool_param (id, tool_id, command, descr, pos) VALUES ('9', '4', 'CMD_LIST', 'The users of the store: find - a substring of the login or the name; omitted - everyone.', '1');
INSERT INTO {DBNICK}_agent_tool_param (id, tool_id, command, descr, pos) VALUES ('10', '1', 'CMD_NOTE_LIST', 'The feed: task_id - one task with its notes; omitted - the last notes of every open task.', '3');
INSERT INTO {DBNICK}_agent_tool_param (id, tool_id, command, descr, pos) VALUES ('11', '1', 'CMD_NOTE_ADD', 'An entry into the task feed; a TASK_STATE_KEY state moves the task, omitted - just a comment.', '4');
UPDATE {DBNICK}_generator SET gen_value = 11 WHERE table_name = 'agent_tool_param';
