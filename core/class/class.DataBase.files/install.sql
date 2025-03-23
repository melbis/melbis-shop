/************************************************************************************************************
 * 
 * install.sql - Server database structure
 * 
 ************************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 ************************************************************************************************************/

/**
 * Parsing vars
 *--------------
 * {DBNICK} - Prefix for tables, one for all
 * {CHARSET} - Charset data in table
 **/


/****************************************************************
 *
 * Common system
 *
 ***************************************************************/


/**
 * Table generator
 **/
DROP TABLE IF EXISTS {DBNICK}_generator;
CREATE TABLE {DBNICK}_generator (
   table_name 	CHAR(50) DEFAULT '' NOT NULL,	
   gen_value 	INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (table_name)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table generator_u
 **/
DROP TABLE IF EXISTS {DBNICK}_generator_u;
CREATE TABLE {DBNICK}_generator_u (
   table_name 	CHAR(50) DEFAULT '' NOT NULL,	
   user_id 	INT UNSIGNED DEFAULT '0' NOT NULL,
   gen_value 	INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (table_name, user_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};



/**
 * Table oper
 **/
DROP TABLE IF EXISTS {DBNICK}_oper;
CREATE TABLE {DBNICK}_oper (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   name 	CHAR(100) DEFAULT '' NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   folder 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   command 	CHAR(100) DEFAULT '' NOT NULL,
   allow_from	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   allow_to	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   PRIMARY KEY (id),
   KEY command (command(10))
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table oper_table
 **/
DROP TABLE IF EXISTS {DBNICK}_oper_table;
CREATE TABLE {DBNICK}_oper_table (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   oper_id 	INT UNSIGNED DEFAULT NULL,
   server_table CHAR(100) DEFAULT '' NOT NULL,
   local_table 	CHAR(100) DEFAULT '' NOT NULL,
   fields_list 	CHAR(255) DEFAULT '' NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY oper_id (oper_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table oper_right
 **/
DROP TABLE IF EXISTS {DBNICK}_oper_right;
CREATE TABLE {DBNICK}_oper_right (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   oper_id 	INT UNSIGNED DEFAULT NULL,
   user_id 	INT UNSIGNED DEFAULT NULL,
   group_id 	INT UNSIGNED DEFAULT NULL,
   PRIMARY KEY (id),
   KEY oper_id (oper_id),
   KEY user_id (user_id),
   KEY group_id (group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table oper_block
 **/
DROP TABLE IF EXISTS {DBNICK}_oper_block;
CREATE TABLE {DBNICK}_oper_block (
   table_name 	CHAR(100) DEFAULT '' NOT NULL,	
   oper_id 	INT UNSIGNED DEFAULT NULL,
   user_id 	INT UNSIGNED DEFAULT NULL,
   from_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   successful	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   UNIQUE (user_id, table_name)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};

/**
 * Table key
 **/
DROP TABLE IF EXISTS {DBNICK}_key;
CREATE TABLE {DBNICK}_key (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   code 	CHAR(100) DEFAULT '' NOT NULL,
   name 	CHAR(100) DEFAULT '' NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   folder 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table key_value
 **/
DROP TABLE IF EXISTS {DBNICK}_key_value;
CREATE TABLE {DBNICK}_key_value (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   key_code	CHAR(100) DEFAULT '' NOT NULL,
   key_name	CHAR(100) DEFAULT '' NOT NULL,
   value_txt 	TEXT DEFAULT ('') NOT NULL,
   sys_key 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY	key_code (key_code(10))
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};



/**
 * Table files_key_value
 **/
DROP TABLE IF EXISTS {DBNICK}_files_key_value;
CREATE TABLE {DBNICK}_files_key_value (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   elem_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};





/****************************************************************
 *
 * Users relative
 *
 ***************************************************************/

/**
 * Table user
 **/
DROP TABLE IF EXISTS {DBNICK}_user;
CREATE TABLE {DBNICK}_user (
   id 			INT UNSIGNED DEFAULT '0' NOT NULL,
   group_id 		INT UNSIGNED DEFAULT NULL,
   login 		CHAR(100) DEFAULT '' NOT NULL,
   pass_code 		CHAR(32) DEFAULT '' NOT NULL,
   name 		CHAR(255) DEFAULT '' NOT NULL,
   phone 		CHAR(255) DEFAULT '' NOT NULL,
   email		CHAR(255) DEFAULT '' NOT NULL,
   params		MEDIUMTEXT DEFAULT ('') NOT NULL,
   add_group_id 	INT UNSIGNED DEFAULT NULL,
   is_blocked 		TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY group_id (group_id),
   KEY add_group_id (add_group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};

/**
 * Table user_group
 **/
DROP TABLE IF EXISTS {DBNICK}_user_group;
CREATE TABLE {DBNICK}_user_group (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(100) DEFAULT '' NOT NULL,
   phone 	CHAR(255) DEFAULT '' NOT NULL,
   email	CHAR(255) DEFAULT '' NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


DROP TABLE IF EXISTS {DBNICK}_user_chat;
CREATE TABLE {DBNICK}_user_chat (
   user_id 	INT UNSIGNED DEFAULT NULL,
   to_user_id	INT UNSIGNED DEFAULT NULL,
   message_txt	TEXT DEFAULT ('') NOT NULL,
   date_time	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   KEY date_time (date_time)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};

/**
 * Table user_online
 **/
DROP TABLE IF EXISTS {DBNICK}_user_online;
CREATE TABLE {DBNICK}_user_online (
   user_id	INT UNSIGNED DEFAULT NULL,
   last_time	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   UNIQUE KEY 	user_id (user_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table user_log
 **/
DROP TABLE IF EXISTS {DBNICK}_user_log;
CREATE TABLE {DBNICK}_user_log (
   id 		INT UNSIGNED NOT NULL AUTO_INCREMENT,
   user_id 	INT UNSIGNED DEFAULT NULL,
   user_ip	INT UNSIGNED DEFAULT '0' NOT NULL,
   command 	CHAR(100) DEFAULT '' NOT NULL,
   for_command 	CHAR(100) DEFAULT '' NOT NULL,
   options	TEXT DEFAULT ('') NOT NULL,
   vars		TEXT DEFAULT ('') NOT NULL, 
   date_time	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};



/**
 * Table user_filter
 **/
DROP TABLE IF EXISTS {DBNICK}_user_filter;
CREATE TABLE {DBNICK}_user_filter (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,   
   place 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   sql_txt	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos	 	INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table user_file_version
 **/
DROP TABLE IF EXISTS {DBNICK}_user_file_version;
CREATE TABLE {DBNICK}_user_file_version (   
   id 		INT UNSIGNED NOT NULL AUTO_INCREMENT,  
   user_id 	INT UNSIGNED DEFAULT NULL,
   path 	CHAR(255) DEFAULT '' NOT NULL,
   content	MEDIUMTEXT DEFAULT ('') NOT NULL,
   version 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   PRIMARY KEY (id),
   KEY user_id (user_id),
   KEY path (path(50))
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table user_task
 **/
DROP TABLE IF EXISTS {DBNICK}_user_task;
CREATE TABLE {DBNICK}_user_task (   
   id 		INT UNSIGNED NOT NULL AUTO_INCREMENT,  
   user_id 	INT UNSIGNED DEFAULT NULL,
   exec_id 	INT UNSIGNED DEFAULT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   state_key 	CHAR(100) DEFAULT '' NOT NULL,
   privy 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   date_time	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   PRIMARY KEY (id),
   KEY user_id (user_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table user_task_note
 **/
DROP TABLE IF EXISTS {DBNICK}_user_task_note;
CREATE TABLE {DBNICK}_user_task_note (   
   id 		INT UNSIGNED NOT NULL AUTO_INCREMENT,  
   task_id 	INT UNSIGNED DEFAULT NULL,   
   user_id 	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   state_key 	CHAR(100) DEFAULT '' NOT NULL,
   content	MEDIUMTEXT DEFAULT ('') NOT NULL,
   date_time	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   PRIMARY KEY (id),
   KEY task_id (task_id),
   KEY user_id (user_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};



/****************************************************************
 *
 * Catalogue 
 *
 ***************************************************************/


/**
 * Table topic
 **/
DROP TABLE IF EXISTS {DBNICK}_topic;
CREATE TABLE {DBNICK}_topic (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   intro 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   descr 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   no_visible 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   link 	CHAR(255) DEFAULT '' NOT NULL,
   seo_psu 	CHAR(255) DEFAULT '' NOT NULL,
   seo_title 	CHAR(255) DEFAULT '' NOT NULL,
   in_xml 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   templ_key 	CHAR(100) DEFAULT '' NOT NULL,
   order_key	CHAR(100) DEFAULT '' NOT NULL,
   order_asc	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   option_code 	TEXT DEFAULT ('') NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table files_topic
 **/
DROP TABLE IF EXISTS {DBNICK}_files_topic;
CREATE TABLE {DBNICK}_files_topic (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   elem_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table topic_alt
 **/
DROP TABLE IF EXISTS {DBNICK}_topic_alt;
CREATE TABLE {DBNICK}_topic_alt (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   topic_id	INT UNSIGNED DEFAULT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY kind_key (kind_key(10))
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table topic_filter
 **/
DROP TABLE IF EXISTS {DBNICK}_topic_filter;
CREATE TABLE {DBNICK}_topic_filter (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   topic_id	INT UNSIGNED DEFAULT NULL,
   info_id	INT UNSIGNED DEFAULT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   use_sub_topic TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY topic_id (topic_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table topic_right
 **/
DROP TABLE IF EXISTS {DBNICK}_topic_right;
CREATE TABLE {DBNICK}_topic_right (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   topic_id 	INT UNSIGNED DEFAULT NULL,
   user_id 	INT UNSIGNED DEFAULT NULL,
   group_id 	INT UNSIGNED DEFAULT NULL,
   for_frame	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   for_price	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   for_ctrl	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY topic_id (topic_id),
   KEY user_id (user_id),
   KEY group_id (group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table topic_key
 **/
DROP TABLE IF EXISTS {DBNICK}_topic_key;
CREATE TABLE {DBNICK}_topic_key (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   folder 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   descr	MEDIUMTEXT DEFAULT ('') NOT NULL,
   mask_edit	CHAR(255) DEFAULT '' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table topic_key_value
 **/
DROP TABLE IF EXISTS {DBNICK}_topic_key_value;
CREATE TABLE {DBNICK}_topic_key_value (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   key_id	INT UNSIGNED DEFAULT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY key_id (key_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table topic_key_set
 **/
DROP TABLE IF EXISTS {DBNICK}_topic_key_set;
CREATE TABLE {DBNICK}_topic_key_set (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   topic_id	INT UNSIGNED DEFAULT NULL,
   key_id	INT UNSIGNED DEFAULT NULL,
   value_id	INT UNSIGNED DEFAULT NULL,
   value_txt 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   PRIMARY KEY (id),
   KEY topic_id (topic_id),
   KEY key_id (key_id),
   KEY value_id (value_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table topic_store
 **/
DROP TABLE IF EXISTS {DBNICK}_topic_store;
CREATE TABLE {DBNICK}_topic_store (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   topic_id 	INT UNSIGNED DEFAULT NULL,
   store_id 	INT UNSIGNED DEFAULT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY topic_id (topic_id),
   KEY store_id (store_id),
   KEY topic_store (topic_id,store_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};



/****************************************************************
 *
 * Shop and user libraries 
 *
 ***************************************************************/

/**
 * Table self_key
 **/
DROP TABLE IF EXISTS {DBNICK}_self_key;
CREATE TABLE {DBNICK}_self_key (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   folder 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table self_key_right
 **/
DROP TABLE IF EXISTS {DBNICK}_self_key_right;
CREATE TABLE {DBNICK}_self_key_right (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   key_id 	INT UNSIGNED DEFAULT NULL,
   user_id 	INT UNSIGNED DEFAULT NULL,
   group_id 	INT UNSIGNED DEFAULT NULL,
   PRIMARY KEY (id),
   KEY key_id (key_id),
   KEY user_id (user_id),
   KEY group_id (group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table self_key_value
 **/
DROP TABLE IF EXISTS {DBNICK}_self_key_value;
CREATE TABLE {DBNICK}_self_key_value (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   key_id	INT UNSIGNED DEFAULT NULL,
   code 	CHAR(100) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   descr	MEDIUMTEXT DEFAULT ('') NOT NULL,
   mask_edit	CHAR(255) DEFAULT '' NOT NULL,
   value_txt 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY key_id (key_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table brand
 **/
DROP TABLE IF EXISTS {DBNICK}_brand;
CREATE TABLE {DBNICK}_brand (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   descr 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   params 	CHAR(255) DEFAULT '' NOT NULL,
   seo_code 	CHAR(255) DEFAULT '' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table files_brand
 **/
DROP TABLE IF EXISTS {DBNICK}_files_brand;
CREATE TABLE {DBNICK}_files_brand (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   elem_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table brand_key
 **/
DROP TABLE IF EXISTS {DBNICK}_brand_key;
CREATE TABLE {DBNICK}_brand_key (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   folder 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   descr	MEDIUMTEXT DEFAULT ('') NOT NULL,
   mask_edit	CHAR(255) DEFAULT '' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table brand_key_value
 **/
DROP TABLE IF EXISTS {DBNICK}_brand_key_value;
CREATE TABLE {DBNICK}_brand_key_value (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   key_id	INT UNSIGNED DEFAULT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY key_id (key_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table brand_key_set
 **/
DROP TABLE IF EXISTS {DBNICK}_brand_key_set;
CREATE TABLE {DBNICK}_brand_key_set (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   brand_id	INT UNSIGNED DEFAULT NULL,
   key_id	INT UNSIGNED DEFAULT NULL,
   value_id	INT UNSIGNED DEFAULT NULL,
   value_txt 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   PRIMARY KEY (id),
   KEY brand_id (brand_id),
   KEY key_id (key_id),
   KEY value_id (value_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table provider
 **/
DROP TABLE IF EXISTS {DBNICK}_provider;
CREATE TABLE {DBNICK}_provider (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,   
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   group_id 	INT UNSIGNED DEFAULT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   state_key 	CHAR(100) DEFAULT '' NOT NULL,
   params 	CHAR(255) DEFAULT '' NOT NULL,
   manager 	CHAR(255) DEFAULT '' NOT NULL,
   phones 	CHAR(255) DEFAULT '' NOT NULL,
   emails	CHAR(255) DEFAULT '' NOT NULL,
   store 	CHAR(255) DEFAULT '' NOT NULL,
   serv_addr 	CHAR(255) DEFAULT '' NOT NULL,
   serv_phone 	CHAR(255) DEFAULT '' NOT NULL,
   notice	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table provider_group
 **/
DROP TABLE IF EXISTS {DBNICK}_provider_group;
CREATE TABLE {DBNICK}_provider_group (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(100) DEFAULT '' NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};




/**
 * Table provider_key
 **/
DROP TABLE IF EXISTS {DBNICK}_provider_key;
CREATE TABLE {DBNICK}_provider_key (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   folder 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   descr	MEDIUMTEXT DEFAULT ('') NOT NULL,
   mask_edit	CHAR(255) DEFAULT '' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table provider_key_value
 **/
DROP TABLE IF EXISTS {DBNICK}_provider_key_value;
CREATE TABLE {DBNICK}_provider_key_value (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   key_id	INT UNSIGNED DEFAULT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY key_id (key_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table provider_key_set
 **/
DROP TABLE IF EXISTS {DBNICK}_provider_key_set;
CREATE TABLE {DBNICK}_provider_key_set (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   provider_id	INT UNSIGNED DEFAULT NULL,
   key_id	INT UNSIGNED DEFAULT NULL,
   value_id	INT UNSIGNED DEFAULT NULL,
   value_txt 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   PRIMARY KEY (id),
   KEY provider_id (provider_id),
   KEY key_id (key_id),
   KEY value_id (value_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table info
 **/
DROP TABLE IF EXISTS {DBNICK}_info;
CREATE TABLE {DBNICK}_info (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   descr 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   folder 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   type_key 	CHAR(100) DEFAULT '' NOT NULL,
   in_topic 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   in_goods 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   seo_code 	CHAR(255) DEFAULT '' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table files_info
 **/
DROP TABLE IF EXISTS {DBNICK}_files_info;
CREATE TABLE {DBNICK}_files_info (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   elem_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table info_value
 **/
DROP TABLE IF EXISTS {DBNICK}_info_value;
CREATE TABLE {DBNICK}_info_value (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   info_id 	INT UNSIGNED DEFAULT NULL,
   name 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   descr 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   params 	CHAR(255) DEFAULT '' NOT NULL,
   seo_code 	CHAR(255) DEFAULT '' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY info_id (info_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table files_info_value
 **/
DROP TABLE IF EXISTS {DBNICK}_files_info_value;
CREATE TABLE {DBNICK}_files_info_value (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   elem_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table info_right
 **/
DROP TABLE IF EXISTS {DBNICK}_info_right;
CREATE TABLE {DBNICK}_info_right (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   info_id 	INT UNSIGNED DEFAULT NULL,
   user_id 	INT UNSIGNED DEFAULT NULL,
   group_id 	INT UNSIGNED DEFAULT NULL,
   for_info	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   for_value	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY info_id (info_id),
   KEY user_id (user_id),
   KEY group_id (group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table info_key
 **/
DROP TABLE IF EXISTS {DBNICK}_info_key;
CREATE TABLE {DBNICK}_info_key (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   folder 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   descr	MEDIUMTEXT DEFAULT ('') NOT NULL,
   mask_edit	CHAR(255) DEFAULT '' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table info_key_value
 **/
DROP TABLE IF EXISTS {DBNICK}_info_key_value;
CREATE TABLE {DBNICK}_info_key_value (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   key_id	INT UNSIGNED DEFAULT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY key_id (key_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table info_key_set
 **/
DROP TABLE IF EXISTS {DBNICK}_info_key_set;
CREATE TABLE {DBNICK}_info_key_set (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   info_id	INT UNSIGNED DEFAULT NULL,
   key_id	INT UNSIGNED DEFAULT NULL,
   value_id	INT UNSIGNED DEFAULT NULL,
   value_txt 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   PRIMARY KEY (id),
   KEY info_id (info_id),
   KEY key_id (key_id),
   KEY value_id (value_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table currency
 **/
DROP TABLE IF EXISTS {DBNICK}_currency;
CREATE TABLE {DBNICK}_currency (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(32) DEFAULT '' NOT NULL,
   multiplex 	DECIMAL(10,4) DEFAULT '0' NOT NULL,
   division 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   provider_id 	INT UNSIGNED DEFAULT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table disc_group
 **/
DROP TABLE IF EXISTS {DBNICK}_disc_group;
CREATE TABLE {DBNICK}_disc_group (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table disc_rate
 **/
DROP TABLE IF EXISTS {DBNICK}_disc_rate;
CREATE TABLE {DBNICK}_disc_rate (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   group_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   from_sum	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   sum_curr_id	INT UNSIGNED DEFAULT NULL,
   disc_proc	DECIMAL(10,4) DEFAULT '0' NOT NULL,
   begin_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   end_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   PRIMARY KEY (id),
   KEY group_id (group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table param
 **/
DROP TABLE IF EXISTS {DBNICK}_param;
CREATE TABLE {DBNICK}_param (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   fixed_set 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   custom_sum 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table param_value
 **/
DROP TABLE IF EXISTS {DBNICK}_param_value;
CREATE TABLE {DBNICK}_param_value (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   param_id	INT UNSIGNED DEFAULT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   set_sum 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   sum_curr_id 	INT UNSIGNED DEFAULT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY param_id (param_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table store_param
 **/
DROP TABLE IF EXISTS {DBNICK}_store_param;
CREATE TABLE {DBNICK}_store_param (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   store_id 	INT UNSIGNED DEFAULT NULL,
   param_id 	INT UNSIGNED DEFAULT NULL,
   value_id 	INT UNSIGNED DEFAULT NULL,
   value_name	CHAR(255) DEFAULT '' NOT NULL,
   value_set_sum DECIMAL(10,2) DEFAULT '0' NOT NULL,
   value_curr_id INT UNSIGNED DEFAULT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY store_id (store_id),
   KEY param_id (param_id),
   KEY value_id (value_id),
   KEY store_value (store_id, value_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table lang
 **/
DROP TABLE IF EXISTS {DBNICK}_lang;
CREATE TABLE {DBNICK}_lang (
   id 		   INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	      CHAR(32) DEFAULT '' NOT NULL,
   name 	      CHAR(255) DEFAULT '' NOT NULL,
   descr       MEDIUMTEXT DEFAULT ('') NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   params	   CHAR(255) DEFAULT '' NOT NULL,
   is_origin 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   pos         INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};

/**
 * Table files_lang
 **/
DROP TABLE IF EXISTS {DBNICK}_files_lang;
CREATE TABLE {DBNICK}_files_lang (
   id		      INT UNSIGNED DEFAULT '0' NOT NULL,
   elem_id	   INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		   INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table trans
 **/
DROP TABLE IF EXISTS {DBNICK}_trans;
CREATE TABLE {DBNICK}_trans (
   id 		   INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	      CHAR(32) DEFAULT '' NOT NULL,
   name 	      CHAR(255) DEFAULT '' NOT NULL,
   tindex 	   INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	   INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   folder 	   TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   table_key 	CHAR(32) DEFAULT '' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};



/**
 * Table trans_origin
 **/
DROP TABLE IF EXISTS {DBNICK}_trans_origin;
CREATE TABLE {DBNICK}_trans_origin (
   id 		   INT UNSIGNED DEFAULT '0' NOT NULL,
   trans_id	   INT UNSIGNED DEFAULT NULL,
   item_id	   INT UNSIGNED DEFAULT NULL,
   item_code 	CHAR(32) DEFAULT '' NOT NULL,
   context     CHAR(255) DEFAULT '' NOT NULL,
   origin      MEDIUMTEXT DEFAULT ('') NOT NULL,
   crc 	      CHAR(32) DEFAULT '' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   params      CHAR(255) DEFAULT '' NOT NULL,
   is_active   TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   is_html     TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   pos         INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY trans_id (trans_id),
   KEY item_id (item_id),
   KEY item_code (item_code)   
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table trans_lang
 **/
DROP TABLE IF EXISTS {DBNICK}_trans_lang;
CREATE TABLE {DBNICK}_trans_lang (
   id 		   INT UNSIGNED DEFAULT '0' NOT NULL,
   lang_id	   INT UNSIGNED DEFAULT NULL,
   trans_id	   INT UNSIGNED DEFAULT NULL,
   origin_id   INT UNSIGNED DEFAULT NULL,
   translate   MEDIUMTEXT DEFAULT ('') NOT NULL,
   is_approve  TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   is_error    TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY lang_id (lang_id),
   KEY trans_id (trans_id),
   KEY origin_id (origin_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table advert
 **/
DROP TABLE IF EXISTS {DBNICK}_advert;
CREATE TABLE {DBNICK}_advert (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   folder 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table advert_text
 **/
DROP TABLE IF EXISTS {DBNICK}_advert_text;
CREATE TABLE {DBNICK}_advert_text (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   advert_id	INT UNSIGNED DEFAULT NULL,
   date_time    DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   descr        MEDIUMTEXT DEFAULT ('') NOT NULL,
   kind_key     CHAR(100) DEFAULT '' NOT NULL,
   params       CHAR(255) DEFAULT '' NOT NULL,
   pos          INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table files_advert_text
 **/
DROP TABLE IF EXISTS {DBNICK}_files_advert_text;
CREATE TABLE {DBNICK}_files_advert_text (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   elem_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table advert_goods
 **/
DROP TABLE IF EXISTS {DBNICK}_advert_goods;
CREATE TABLE {DBNICK}_advert_goods (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   advert_id 	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   elem_id 	INT UNSIGNED DEFAULT NULL,
   params       CHAR(255) DEFAULT '' NOT NULL,
   comment      CHAR(255) DEFAULT '' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY advert_id (advert_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table advert_link
 **/
DROP TABLE IF EXISTS {DBNICK}_advert_link;
CREATE TABLE {DBNICK}_advert_link (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   advert_id 	INT UNSIGNED DEFAULT NULL,
   obj_key 	CHAR(100) DEFAULT '' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   param_id     INT UNSIGNED DEFAULT NULL,
   params   	CHAR(255) DEFAULT '' NOT NULL,
   comment      CHAR(255) DEFAULT '' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY advert_id (advert_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};




/****************************************************************
 *
 * Store relative
 *
 ***************************************************************/

/**
 * Table store
 **/
DROP TABLE IF EXISTS {DBNICK}_store;
CREATE TABLE {DBNICK}_store (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   provider_id 	INT UNSIGNED DEFAULT NULL,
   brand_id 	INT UNSIGNED DEFAULT NULL,
   code_shop 	CHAR(255) DEFAULT '' NOT NULL,
   code_prov 	CHAR(255) DEFAULT '' NOT NULL,
   code_made 	CHAR(255) DEFAULT '' NOT NULL,
   meas 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   intro 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   descr 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   review 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   no_visible 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   status_key 	CHAR(100) DEFAULT '' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   state_key 	CHAR(100) DEFAULT '' NOT NULL,
   clann 	INT UNSIGNED DEFAULT '0' NOT NULL,
   clann_title 	CHAR(255) DEFAULT '' NOT NULL,
   clann_descr 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   clann_root 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   relate_id	INT UNSIGNED DEFAULT '0' NOT NULL,
   rating 	TINYINT DEFAULT '0' NOT NULL,
   disc_group_id INT UNSIGNED DEFAULT NULL,
   how 		INT DEFAULT '0' NOT NULL,
   pprice 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   pprice_curr_id INT UNSIGNED DEFAULT NULL,
   rprice 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   rprice_curr_id INT UNSIGNED DEFAULT NULL,
   price 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   price_curr_id INT UNSIGNED DEFAULT NULL,
   price2 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   price2_curr_id INT UNSIGNED DEFAULT NULL,
   price3 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   price3_curr_id INT UNSIGNED DEFAULT NULL,
   relate_type	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   relate_proc 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   proc_price2 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   proc_price3 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   min_order 	INT DEFAULT '0' NOT NULL,
   step_order 	INT DEFAULT '0' NOT NULL,
   seo_psu 	CHAR(255) DEFAULT '' NOT NULL,
   seo_title 	CHAR(255) DEFAULT '' NOT NULL,
   in_xml 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   templ_key 	CHAR(100) DEFAULT '' NOT NULL,
   create_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   update_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   edit_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   exist_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   option_code 	TEXT DEFAULT ('') NOT NULL,
   award_cnt 	INT DEFAULT '0' NOT NULL,
   award_avg 	DECIMAL(10,4) DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY code_shop (code_shop(10)),
   KEY code_prov (code_prov(10)),
   KEY code_made (code_made(10)),
   KEY provider_id (provider_id),
   KEY brand_id (brand_id),
   KEY relate_id (relate_id),
   KEY clann (clann)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};



/**
 * Table files_store
 **/
DROP TABLE IF EXISTS {DBNICK}_files_store;
CREATE TABLE {DBNICK}_files_store (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   elem_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table store_set
 **/
DROP TABLE IF EXISTS {DBNICK}_store_set;
CREATE TABLE {DBNICK}_store_set (
   id 		INT UNSIGNED NOT NULL AUTO_INCREMENT,  
   store_id 	INT UNSIGNED DEFAULT NULL,
   obj_key 	CHAR(100) DEFAULT '' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   elem_id 	INT UNSIGNED DEFAULT NULL,   
   params 	CHAR(255) DEFAULT '' NOT NULL,
   comment 	CHAR(255) DEFAULT '' NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY store_id (store_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table store_info
 **/
DROP TABLE IF EXISTS {DBNICK}_store_info;
CREATE TABLE {DBNICK}_store_info (
   id 		INT UNSIGNED NOT NULL AUTO_INCREMENT,
   store_id 	INT UNSIGNED DEFAULT NULL,
   info_id 	INT UNSIGNED DEFAULT NULL,
   value_id 	INT UNSIGNED DEFAULT NULL,
   value_dec 	DECIMAL(10,4) DEFAULT '0' NOT NULL,
   value_txt 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   PRIMARY KEY (id),
   KEY store_id (store_id),
   KEY info_id (info_id),
   KEY value_id (value_id),
   KEY store_value (store_id, value_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table store_comment
 **/
DROP TABLE IF EXISTS {DBNICK}_store_comment;
CREATE TABLE {DBNICK}_store_comment (
   id 		INT UNSIGNED NOT NULL AUTO_INCREMENT,
   store_id 	INT UNSIGNED DEFAULT NULL,
   parent_id 	INT UNSIGNED DEFAULT NULL,
   user_name	CHAR(255) DEFAULT '' NOT NULL,
   user_email	CHAR(255) DEFAULT '' NOT NULL,
   user_ip	INT UNSIGNED DEFAULT '0' NOT NULL,
   params 	MEDIUMTEXT DEFAULT (''),
   award        TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   positive     MEDIUMTEXT DEFAULT ('') NOT NULL,
   negative     MEDIUMTEXT DEFAULT ('') NOT NULL,
   summary      MEDIUMTEXT DEFAULT ('') NOT NULL,
   pro_cnt	INT DEFAULT '0' NOT NULL,
   contra_cnt	INT DEFAULT '0' NOT NULL,
   create_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   PRIMARY KEY (id),
   KEY store_id (store_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table store_comment_vote
 **/
DROP TABLE IF EXISTS {DBNICK}_store_comment_vote;
CREATE TABLE {DBNICK}_store_comment_vote (
   id 		INT UNSIGNED NOT NULL AUTO_INCREMENT,
   comment_id 	INT UNSIGNED DEFAULT NULL,
   user_ip	INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY comment_id (comment_id),
   KEY user_ip (user_ip)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table store_comment_block
 **/
DROP TABLE IF EXISTS {DBNICK}_store_comment_block;
CREATE TABLE {DBNICK}_store_comment_block (
   id 		INT UNSIGNED NOT NULL AUTO_INCREMENT,
   from_ip	INT UNSIGNED DEFAULT '0' NOT NULL,
   to_ip	INT UNSIGNED DEFAULT '0' NOT NULL,
   lock_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};



/****************************************************************
 *
 * Personal store
 *
 ***************************************************************/
 

/**
 * Table u_info_value
 **/
DROP TABLE IF EXISTS {DBNICK}_u_info_value;
CREATE TABLE {DBNICK}_u_info_value (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   base_id	INT UNSIGNED DEFAULT NULL,
   was_update	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   info_id 	INT UNSIGNED DEFAULT NULL,
   name 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   descr 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   params 	CHAR(255) DEFAULT '' NOT NULL,
   seo_code 	CHAR(255) DEFAULT '' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   UNIQUE (user_id, id),
   KEY base_id (base_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table u_files_info_value
 **/
DROP TABLE IF EXISTS {DBNICK}_u_files_info_value;
CREATE TABLE {DBNICK}_u_files_info_value (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   base_id	INT UNSIGNED DEFAULT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   elem_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   UNIQUE (user_id, id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};



/**
 * Table u_store
 **/
DROP TABLE IF EXISTS {DBNICK}_u_store;
CREATE TABLE {DBNICK}_u_store (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   base_id	INT UNSIGNED DEFAULT NULL,
   was_update	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   was_edit	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   provider_id 	INT UNSIGNED DEFAULT NULL,
   brand_id 	INT UNSIGNED DEFAULT NULL,
   code_shop 	CHAR(255) DEFAULT '' NOT NULL,
   code_prov 	CHAR(255) DEFAULT '' NOT NULL,
   code_made 	CHAR(255) DEFAULT '' NOT NULL,
   meas 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   intro 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   descr 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   review 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   no_visible 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   status_key 	CHAR(100) DEFAULT '' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   state_key 	CHAR(100) DEFAULT '' NOT NULL,
   clann 	INT UNSIGNED DEFAULT '0' NOT NULL,
   clann_title 	CHAR(255) DEFAULT '' NOT NULL,
   clann_descr 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   clann_root 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   relate_id	INT UNSIGNED DEFAULT '0' NOT NULL,
   price 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   price_curr_id INT UNSIGNED DEFAULT NULL,
   seo_psu 	CHAR(255) DEFAULT '' NOT NULL,
   seo_title 	CHAR(255) DEFAULT '' NOT NULL,
   templ_key 	CHAR(100) DEFAULT '' NOT NULL,
   create_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   update_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   exist_time	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   edit_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   option_code 	TEXT DEFAULT ('') NOT NULL,
   UNIQUE (user_id, id),
   KEY base_id (base_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table u_files_store
 **/
DROP TABLE IF EXISTS {DBNICK}_u_files_store;
CREATE TABLE {DBNICK}_u_files_store (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   base_id	INT UNSIGNED DEFAULT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   elem_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   UNIQUE (user_id, id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table u_store_set
 **/
DROP TABLE IF EXISTS {DBNICK}_u_store_set;
CREATE TABLE {DBNICK}_u_store_set (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   store_id 	INT UNSIGNED DEFAULT NULL,
   obj_key 	CHAR(100) DEFAULT '' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   elem_id 	INT UNSIGNED DEFAULT NULL,   
   params 	CHAR(255) DEFAULT '' NOT NULL,
   comment 	CHAR(255) DEFAULT '' NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   UNIQUE (user_id, id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table u_store_info
 **/
DROP TABLE IF EXISTS {DBNICK}_u_store_info;
CREATE TABLE {DBNICK}_u_store_info (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,	
   user_id	INT UNSIGNED DEFAULT NULL,
   store_id 	INT UNSIGNED DEFAULT NULL,
   info_id 	INT UNSIGNED DEFAULT NULL,
   value_id 	INT UNSIGNED DEFAULT NULL,
   value_dec 	DECIMAL(10,4) DEFAULT '0' NOT NULL,
   value_txt 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   UNIQUE (user_id, id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/** 
 * Table u_topic_store 
 **/
DROP TABLE IF EXISTS {DBNICK}_u_topic_store;
CREATE TABLE {DBNICK}_u_topic_store (   
   id		INT UNSIGNED DEFAULT '0' NOT NULL,   
   user_id	INT UNSIGNED DEFAULT NULL,
   base_id	INT UNSIGNED DEFAULT NULL,   
   topic_id 	INT UNSIGNED DEFAULT NULL,   
   store_id 	INT UNSIGNED DEFAULT NULL,   
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,   
   UNIQUE (user_id, id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};




/****************************************************************
 *
 * Client and fields
 *
 ***************************************************************/

/**
 * Table field
 **/
DROP TABLE IF EXISTS {DBNICK}_field;
CREATE TABLE {DBNICK}_field (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   descr 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   folder 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   spec_key 	CHAR(100) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   fixed_set 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   read_only 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   mask_edit	CHAR(255) DEFAULT '' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table files_field
 **/
DROP TABLE IF EXISTS {DBNICK}_files_field;
CREATE TABLE {DBNICK}_files_field (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   elem_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table field_value
 **/
DROP TABLE IF EXISTS {DBNICK}_field_value;
CREATE TABLE {DBNICK}_field_value (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   field_id	INT UNSIGNED DEFAULT NULL,
   code 	CHAR(255) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY field_id (field_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};

/**
 * Table client
 **/
DROP TABLE IF EXISTS {DBNICK}_client;
CREATE TABLE {DBNICK}_client (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   group_id 	INT UNSIGNED DEFAULT NULL,
   login 	CHAR(100) DEFAULT '' NOT NULL,
   pass 	CHAR(100) DEFAULT '' NOT NULL,
   discount	DECIMAL(10,4) DEFAULT '0' NOT NULL,
   order_sum	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   reg_date 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   last_date 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   PRIMARY KEY (id),
   KEY group_id (group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table client_group
 **/
DROP TABLE IF EXISTS {DBNICK}_client_group;
CREATE TABLE {DBNICK}_client_group (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   price_num	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   discount 	DECIMAL(10,4) DEFAULT '0' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table client_rules
 **/
DROP TABLE IF EXISTS {DBNICK}_client_rules;
CREATE TABLE {DBNICK}_client_rules (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   order_sum 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   sum_curr_id 	INT UNSIGNED DEFAULT NULL,
   set_group_id	INT UNSIGNED DEFAULT NULL,
   set_discount	DECIMAL(10,4) DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table client_field
 **/
DROP TABLE IF EXISTS {DBNICK}_client_field;
CREATE TABLE {DBNICK}_client_field (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   client_id 	INT UNSIGNED DEFAULT NULL,
   field_id 	INT UNSIGNED DEFAULT NULL,
   value_id 	INT UNSIGNED DEFAULT NULL,
   value_txt 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   PRIMARY KEY (id),
   KEY client_id (client_id),
   KEY field_id (field_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};





/****************************************************************
 *
 * Orders options
 *
 ***************************************************************/


/**
 * Table order_option
 **/
DROP TABLE IF EXISTS {DBNICK}_order_option;
CREATE TABLE {DBNICK}_order_option (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   descr	MEDIUMTEXT DEFAULT ('') NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   fixed_set 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   needs_calc 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   custom_sum 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table files_order_option 
 **/
DROP TABLE IF EXISTS {DBNICK}_files_order_option;
CREATE TABLE {DBNICK}_files_order_option (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   elem_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table order_option_value
 **/
DROP TABLE IF EXISTS {DBNICK}_order_option_value;
CREATE TABLE {DBNICK}_order_option_value (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   option_id	INT UNSIGNED DEFAULT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   descr	MEDIUMTEXT DEFAULT ('') NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   modify_sum	DECIMAL(10,4) DEFAULT '0' NOT NULL,
   sum_curr_id	INT UNSIGNED DEFAULT NULL,
   oper_num 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   source_num 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   message	MEDIUMTEXT DEFAULT ('') NOT NULL,
   color_set	CHAR(32) DEFAULT '' NOT NULL,
   use_default 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   after_create TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY option_id (option_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table files_order_option_value
 **/
DROP TABLE IF EXISTS {DBNICK}_files_order_option_value;
CREATE TABLE {DBNICK}_files_order_option_value (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   elem_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};

/**
 * Table order_option_right
 **/
DROP TABLE IF EXISTS {DBNICK}_order_option_right;
CREATE TABLE {DBNICK}_order_option_right (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   option_id 	INT UNSIGNED DEFAULT NULL,
   user_id 	INT UNSIGNED DEFAULT NULL,
   group_id 	INT UNSIGNED DEFAULT NULL,
   PRIMARY KEY (id),
   KEY option_id (option_id),
   KEY user_id (user_id),
   KEY group_id (group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table order_right
 **/
DROP TABLE IF EXISTS {DBNICK}_order_right;
CREATE TABLE {DBNICK}_order_right (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   value_id 	INT UNSIGNED DEFAULT NULL,
   user_id 	INT UNSIGNED DEFAULT NULL,
   group_id 	INT UNSIGNED DEFAULT NULL,
   PRIMARY KEY (id),
   KEY value_id (value_id),
   KEY user_id (user_id),
   KEY group_id (group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};

/**
 * Table order_option_block
 **/
DROP TABLE IF EXISTS {DBNICK}_order_option_block;
CREATE TABLE {DBNICK}_order_option_block (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   value1_id 	INT UNSIGNED DEFAULT NULL,
   value2_id 	INT UNSIGNED DEFAULT NULL,
   message	TEXT DEFAULT ('') NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table order_option_limit
 **/
DROP TABLE IF EXISTS {DBNICK}_order_option_limit;
CREATE TABLE {DBNICK}_order_option_limit (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   option_id 	INT UNSIGNED DEFAULT NULL,
   was_value_id INT UNSIGNED DEFAULT NULL,
   set_value_id INT UNSIGNED DEFAULT NULL,
   message	TEXT DEFAULT ('') NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};

/**
 * Table order_option_limit_right
 **/
DROP TABLE IF EXISTS {DBNICK}_order_option_limit_right;
CREATE TABLE {DBNICK}_order_option_limit_right (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   limit_id 	INT UNSIGNED DEFAULT NULL,
   user_id 	INT UNSIGNED DEFAULT NULL,
   group_id 	INT UNSIGNED DEFAULT NULL,
   PRIMARY KEY (id),
   KEY limit_id (limit_id),
   KEY user_id (user_id),
   KEY group_id (group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table order_store_option
 **/
DROP TABLE IF EXISTS {DBNICK}_order_store_option;
CREATE TABLE {DBNICK}_order_store_option (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   fixed_set 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   needs_calc 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   custom_sum 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table order_store_option_value
 **/
DROP TABLE IF EXISTS {DBNICK}_order_store_option_value;
CREATE TABLE {DBNICK}_order_store_option_value (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   option_id	INT UNSIGNED DEFAULT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   modify_sum 	DECIMAL(10,4) DEFAULT '0' NOT NULL,
   sum_curr_id INT UNSIGNED DEFAULT NULL,
   use_default 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   after_create TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY option_id (option_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};

/**
 * Table order_store_option_right
 **/
DROP TABLE IF EXISTS {DBNICK}_order_store_option_right;
CREATE TABLE {DBNICK}_order_store_option_right (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   option_id 	INT UNSIGNED DEFAULT NULL,
   user_id 	INT UNSIGNED DEFAULT NULL,
   group_id 	INT UNSIGNED DEFAULT NULL,
   PRIMARY KEY (id),
   KEY option_id (option_id),
   KEY user_id (user_id),
   KEY group_id (group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table order_store_option_block
 **/
DROP TABLE IF EXISTS {DBNICK}_order_store_option_block;
CREATE TABLE {DBNICK}_order_store_option_block (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   value1_id 	INT UNSIGNED DEFAULT NULL,
   value2_id 	INT UNSIGNED DEFAULT NULL,
   message	TEXT DEFAULT ('') NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};




/****************************************************************
 *
 * Orders archive
 *
 ***************************************************************/


/**
 * Table orders
 **/
DROP TABLE IF EXISTS {DBNICK}_orders;
CREATE TABLE {DBNICK}_orders (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   code 	CHAR(100) DEFAULT '' NOT NULL,
   version_id 	INT UNSIGNED DEFAULT NULL,
   date_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   PRIMARY KEY (id),
   KEY version_id (version_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table orders_version
 **/
DROP TABLE IF EXISTS {DBNICK}_orders_version;
CREATE TABLE {DBNICK}_orders_version (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   order_id 	INT UNSIGNED DEFAULT NULL,
   user_id 	INT UNSIGNED DEFAULT NULL,
   client_id 	INT UNSIGNED DEFAULT NULL,
   date_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,	
   total_sum 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY order_id (order_id),
   KEY user_id (user_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table orders_client_field
 **/
DROP TABLE IF EXISTS {DBNICK}_orders_client_field;
CREATE TABLE {DBNICK}_orders_client_field (
   id 		INT UNSIGNED NOT NULL AUTO_INCREMENT,  
   version_id 	INT UNSIGNED DEFAULT NULL,
   field_id 	INT UNSIGNED DEFAULT NULL,
   field_skey 	CHAR(32) DEFAULT '' NOT NULL,
   field_name 	CHAR(255) DEFAULT '' NOT NULL,
   field_tindex INT UNSIGNED DEFAULT '0' NOT NULL,
   field_tlevel	INT UNSIGNED DEFAULT '0' NOT NULL,
   field_absindex INT UNSIGNED DEFAULT '0' NOT NULL,
   field_folder	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   field_kind_key CHAR(100) DEFAULT '' NOT NULL,
   field_spec_key CHAR(100) DEFAULT '' NOT NULL,
   value_id 	INT UNSIGNED DEFAULT NULL,
   value_skey 	CHAR(32) DEFAULT '' NOT NULL,
   value_code 	CHAR(255) DEFAULT '' NOT NULL,
   value_kind_key CHAR(100) DEFAULT '' NOT NULL,
   value_txt 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   PRIMARY KEY (id),
   KEY version_id (version_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table orders_store
 **/
DROP TABLE IF EXISTS {DBNICK}_orders_store;
CREATE TABLE {DBNICK}_orders_store (
   id 			INT UNSIGNED NOT NULL AUTO_INCREMENT,  
   version_id 		INT UNSIGNED DEFAULT NULL,
   store_id 		INT UNSIGNED DEFAULT NULL,   
   store_provider_id 	INT UNSIGNED DEFAULT NULL,
   store_brand_id 	INT UNSIGNED DEFAULT NULL,
   store_pprice 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   store_rprice 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   store_price 		DECIMAL(10,2) DEFAULT '0' NOT NULL,
   store_price2 	DECIMAL(10,2) DEFAULT '0' NOT NULL,
   store_price3 	DECIMAL(10,2) DEFAULT '0' NOT NULL,   
   store_how 		INT DEFAULT '0' NOT NULL,   
   store_code_shop 	CHAR(255) DEFAULT '' NOT NULL,
   store_code_prov 	CHAR(255) DEFAULT '' NOT NULL,
   store_code_made 	CHAR(255) DEFAULT '' NOT NULL,
   store_meas 		CHAR(32) DEFAULT '' NOT NULL,
   store_name 		TEXT DEFAULT ('') NOT NULL,
   store_kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   store_status_key 	CHAR(100) DEFAULT '' NOT NULL,
   store_state_key 	CHAR(100) DEFAULT '' NOT NULL,
   store_min_order 	INT DEFAULT '0' NOT NULL,
   store_step_order 	INT DEFAULT '0' NOT NULL,
   recalc 		TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   out_price 		DECIMAL(10,2) DEFAULT '0' NOT NULL,
   amount		INT DEFAULT '0' NOT NULL,
   notice 		CHAR(255) DEFAULT '' NOT NULL,
   auto_notice 		CHAR(255) DEFAULT '' NOT NULL,
   pos 			INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY version_id (version_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table orders_store_option
 **/
DROP TABLE IF EXISTS {DBNICK}_orders_store_option;
CREATE TABLE {DBNICK}_orders_store_option (
   id 		INT UNSIGNED NOT NULL AUTO_INCREMENT,  
   version_id	INT UNSIGNED DEFAULT NULL,
   order_store_id INT UNSIGNED DEFAULT NULL,
   option_id	INT UNSIGNED DEFAULT NULL,
   option_skey 	CHAR(32) DEFAULT '' NOT NULL,
   option_name	CHAR(255) DEFAULT '' NOT NULL,
   option_kind_key CHAR(100) DEFAULT '' NOT NULL,
   option_pos	INT UNSIGNED DEFAULT '0' NOT NULL,
   value_id 	INT UNSIGNED DEFAULT NULL,
   value_skey 	CHAR(32) DEFAULT '' NOT NULL,
   value_name 	CHAR(255) DEFAULT '' NOT NULL,
   value_modify_sum DECIMAL(10,4) DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY version_id (version_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table orders_option
 **/
DROP TABLE IF EXISTS {DBNICK}_orders_option;
CREATE TABLE {DBNICK}_orders_option (
   id 			INT UNSIGNED NOT NULL AUTO_INCREMENT,  
   version_id		INT UNSIGNED DEFAULT NULL,
   option_id		INT UNSIGNED DEFAULT NULL,
   option_skey 		CHAR(32) DEFAULT '' NOT NULL,
   option_name		CHAR(255) DEFAULT '' NOT NULL,
   option_kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   option_pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   value_id		INT UNSIGNED DEFAULT NULL,
   value_skey 		CHAR(32) DEFAULT '' NOT NULL,
   value_name 		CHAR(255) DEFAULT '' NOT NULL,
   value_kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   value_modify_sum	DECIMAL(10,4) DEFAULT '0' NOT NULL,
   value_oper_num 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   value_source_num 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   notice 		MEDIUMTEXT DEFAULT ('') NOT NULL,
   PRIMARY KEY (id),
   KEY version_id (version_id),
   KEY option_value (option_id, value_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};





/****************************************************************
 *
 * Web options
 *
 ***************************************************************/


/**
 * Table web_outside
 **/
DROP TABLE IF EXISTS {DBNICK}_web_outside;
CREATE TABLE {DBNICK}_web_outside (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   folder 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   url 		TEXT DEFAULT ('') NOT NULL,
   auth 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table web_outside_right
 **/
DROP TABLE IF EXISTS {DBNICK}_web_outside_right;
CREATE TABLE {DBNICK}_web_outside_right (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   outside_id 	INT UNSIGNED DEFAULT NULL,
   user_id 	INT UNSIGNED DEFAULT NULL,
   group_id 	INT UNSIGNED DEFAULT NULL,
   PRIMARY KEY (id),
   KEY outside_id (outside_id),
   KEY user_id (user_id),
   KEY group_id (group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table web_inside
 **/
DROP TABLE IF EXISTS {DBNICK}_web_inside;
CREATE TABLE {DBNICK}_web_inside (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   place 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   url 		TEXT DEFAULT ('') NOT NULL,
   auth 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   pos	 	INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table web_inside_right
 **/
DROP TABLE IF EXISTS {DBNICK}_web_inside_right;
CREATE TABLE {DBNICK}_web_inside_right (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   inside_id 	INT UNSIGNED DEFAULT NULL,
   user_id 	INT UNSIGNED DEFAULT NULL,
   group_id 	INT UNSIGNED DEFAULT NULL,
   PRIMARY KEY (id),
   KEY inside_id (inside_id),
   KEY user_id (user_id),
   KEY group_id (group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};



/**
 * Table web_key
 **/
DROP TABLE IF EXISTS {DBNICK}_web_key;
CREATE TABLE {DBNICK}_web_key (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   descr 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   folder 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   params 	CHAR(255) DEFAULT '' NOT NULL,
   color_set	CHAR(32) DEFAULT '' NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table files_web_key
 **/
DROP TABLE IF EXISTS {DBNICK}_files_web_key;
CREATE TABLE {DBNICK}_files_web_key (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   elem_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table web_key_value
 **/
DROP TABLE IF EXISTS {DBNICK}_web_key_value;
CREATE TABLE {DBNICK}_web_key_value (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   skey 	CHAR(32) DEFAULT '' NOT NULL,
   web_key_id 	INT UNSIGNED DEFAULT NULL,
   name 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   descr 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   params 	CHAR(255) DEFAULT '' NOT NULL,
   color_set	CHAR(32) DEFAULT '' NOT NULL,
   pos 		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY web_key_id (web_key_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table files_web_key_value
 **/
DROP TABLE IF EXISTS {DBNICK}_files_web_key_value;
CREATE TABLE {DBNICK}_files_web_key_value (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   elem_id	INT UNSIGNED DEFAULT NULL,
   kind_key 	CHAR(100) DEFAULT '' NOT NULL,
   file_name 	CHAR(100) DEFAULT '' NOT NULL,
   file_size	INT UNSIGNED DEFAULT '0' NOT NULL,
   upload_time 	DATETIME DEFAULT '2000-01-01 00:00:00' NOT NULL,
   upload_ok 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   real_name	CHAR(255) DEFAULT '' NOT NULL,
   parent_id	INT UNSIGNED DEFAULT NULL,
   format_xml 	MEDIUMTEXT DEFAULT ('') NOT NULL,
   pos		INT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY elem_id (elem_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table web_key_right
 **/
DROP TABLE IF EXISTS {DBNICK}_web_key_right;
CREATE TABLE {DBNICK}_web_key_right (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   web_key_id 	INT UNSIGNED DEFAULT NULL,
   user_id 	INT UNSIGNED DEFAULT NULL,
   group_id 	INT UNSIGNED DEFAULT NULL,
   for_read	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   for_write	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   for_remove	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY web_key_id (web_key_id),
   KEY user_id (user_id),
   KEY group_id (group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};



/****************************************************************
 *
 * Report
 *
 ***************************************************************/

/**
 * Table report
 **/
DROP TABLE IF EXISTS {DBNICK}_report;
CREATE TABLE {DBNICK}_report (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   name 	CHAR(255) DEFAULT '' NOT NULL,
   tindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   tlevel 	INT UNSIGNED DEFAULT '0' NOT NULL,
   absindex 	INT UNSIGNED DEFAULT '0' NOT NULL,
   folder 	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   report_file	MEDIUMTEXT DEFAULT ('') NOT NULL,
   PRIMARY KEY (id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


/**
 * Table report_right
 **/
DROP TABLE IF EXISTS {DBNICK}_report_right;
CREATE TABLE {DBNICK}_report_right (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   report_id 	INT UNSIGNED DEFAULT NULL,
   user_id 	INT UNSIGNED DEFAULT NULL,
   group_id 	INT UNSIGNED DEFAULT NULL,
   PRIMARY KEY (id),
   KEY report_id (report_id),
   KEY user_id (user_id),
   KEY group_id (group_id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};




/****************************************************************
 *
 * Only Local Temporary tables for Data Operations
 *
 ***************************************************************/


-- Topic List for import data to the User Frame

/**
 * Table tmp_topic
 **/
DROP TABLE IF EXISTS {DBNICK}_tmp_topic;
CREATE TABLE {DBNICK}_tmp_topic (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   access_exist	TINYINT UNSIGNED DEFAULT '0' NOT NULL,	
   UNIQUE (user_id, id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};

-- Info List for import data to the User Frame

/**
 * Table tmp_info
 **/
DROP TABLE IF EXISTS {DBNICK}_tmp_info;
CREATE TABLE {DBNICK}_tmp_info (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   UNIQUE (user_id, id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


-- Order List for get orders for users

/**
 * Table tmp_orders
 **/
DROP TABLE IF EXISTS {DBNICK}_tmp_orders;
CREATE TABLE {DBNICK}_tmp_orders (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   UNIQUE (user_id, id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


-- Client List for get clients for users

/**
 * Table tmp_client
 **/
DROP TABLE IF EXISTS {DBNICK}_tmp_client;
CREATE TABLE {DBNICK}_tmp_client (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   UNIQUE (user_id, id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


-- Key List for get self_key for users

/**
 * Table tmp_self_key
 **/
DROP TABLE IF EXISTS {DBNICK}_tmp_self_key;
CREATE TABLE {DBNICK}_tmp_self_key (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   UNIQUE (user_id, id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


-- Key List for get web_key for users

/**
 * Table tmp_web_key
 **/
DROP TABLE IF EXISTS {DBNICK}_tmp_web_key;
CREATE TABLE {DBNICK}_tmp_web_key (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   for_read	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   for_write	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   for_remove	TINYINT UNSIGNED DEFAULT '0' NOT NULL,
   UNIQUE (user_id, id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


-- Key List for get order_right

/**
 * Table tmp_order_right
 **/
DROP TABLE IF EXISTS {DBNICK}_tmp_order_right;
CREATE TABLE {DBNICK}_tmp_order_right (
   id		INT UNSIGNED DEFAULT '0' NOT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   UNIQUE (user_id, id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


-- Store List for price manager for users

/**
 * Table tmp_store
 **/
DROP TABLE IF EXISTS {DBNICK}_tmp_store;
CREATE TABLE {DBNICK}_tmp_store (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   UNIQUE (user_id, id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


-- Store Comment List

/**
 * Table tmp_store_comment
 **/
DROP TABLE IF EXISTS {DBNICK}_tmp_store_comment;
CREATE TABLE {DBNICK}_tmp_store_comment (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   UNIQUE (user_id, id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


-- Trans List for import data 

/**
 * Table tmp_trans
 **/
DROP TABLE IF EXISTS {DBNICK}_tmp_trans;
CREATE TABLE {DBNICK}_tmp_trans (
   id 		INT UNSIGNED DEFAULT '0' NOT NULL,
   user_id	INT UNSIGNED DEFAULT NULL,
   UNIQUE (user_id, id)
) ENGINE = MYISAM DEFAULT CHARSET={CHARSET};


SET sql_mode = 'STRICT_TRANS_TABLES';                   	