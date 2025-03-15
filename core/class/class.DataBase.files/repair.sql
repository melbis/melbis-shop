/************************************************************************************************************
 * 
 * repair.sql - Server database repair commands
 * 
 ************************************************************************************************************
 * @version 6.3.0
 * @copyright 2019 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 ************************************************************************************************************/

/**
 * Parsing vars
 *--------------
 * {DBNICK} - Prefix for tables, one for all
 *
 **/

REPAIR TABLE {DBNICK}_advert;
REPAIR TABLE {DBNICK}_advert_goods;
REPAIR TABLE {DBNICK}_advert_link;
REPAIR TABLE {DBNICK}_advert_text;
REPAIR TABLE {DBNICK}_brand;
REPAIR TABLE {DBNICK}_brand_key;
REPAIR TABLE {DBNICK}_brand_key_value;
REPAIR TABLE {DBNICK}_brand_key_set;
REPAIR TABLE {DBNICK}_client;
REPAIR TABLE {DBNICK}_client_field;
REPAIR TABLE {DBNICK}_client_group;
REPAIR TABLE {DBNICK}_client_rules;
REPAIR TABLE {DBNICK}_currency;
REPAIR TABLE {DBNICK}_disc_group;
REPAIR TABLE {DBNICK}_disc_rate;
REPAIR TABLE {DBNICK}_field;
REPAIR TABLE {DBNICK}_field_value;
REPAIR TABLE {DBNICK}_files_advert_text;
REPAIR TABLE {DBNICK}_files_brand;
REPAIR TABLE {DBNICK}_files_field;
REPAIR TABLE {DBNICK}_files_info;
REPAIR TABLE {DBNICK}_files_info_value;
REPAIR TABLE {DBNICK}_files_key_value;
REPAIR TABLE {DBNICK}_files_lang;
REPAIR TABLE {DBNICK}_files_order_option;
REPAIR TABLE {DBNICK}_files_order_option_value;
REPAIR TABLE {DBNICK}_files_store;
REPAIR TABLE {DBNICK}_files_topic;
REPAIR TABLE {DBNICK}_files_web_key;
REPAIR TABLE {DBNICK}_files_web_key_value;
REPAIR TABLE {DBNICK}_generator;
REPAIR TABLE {DBNICK}_generator_u;
REPAIR TABLE {DBNICK}_info;
REPAIR TABLE {DBNICK}_info_value;
REPAIR TABLE {DBNICK}_info_right;
REPAIR TABLE {DBNICK}_info_key;
REPAIR TABLE {DBNICK}_info_key_value;
REPAIR TABLE {DBNICK}_info_key_set;
REPAIR TABLE {DBNICK}_key;
REPAIR TABLE {DBNICK}_key_value;
REPAIR TABLE {DBNICK}_lang;
REPAIR TABLE {DBNICK}_trans;
REPAIR TABLE {DBNICK}_trans_origin;
REPAIR TABLE {DBNICK}_trans_lang;
REPAIR TABLE {DBNICK}_oper;
REPAIR TABLE {DBNICK}_oper_right;
REPAIR TABLE {DBNICK}_oper_table;
REPAIR TABLE {DBNICK}_oper_block;
REPAIR TABLE {DBNICK}_order_option;
REPAIR TABLE {DBNICK}_order_option_right;
REPAIR TABLE {DBNICK}_order_option_value;
REPAIR TABLE {DBNICK}_order_option_block;
REPAIR TABLE {DBNICK}_order_option_limit;
REPAIR TABLE {DBNICK}_order_option_limit_right;
REPAIR TABLE {DBNICK}_order_store_option;
REPAIR TABLE {DBNICK}_order_store_option_value;
REPAIR TABLE {DBNICK}_order_store_option_block;
REPAIR TABLE {DBNICK}_order_store_option_right;
REPAIR TABLE {DBNICK}_order_right;
REPAIR TABLE {DBNICK}_orders;
REPAIR TABLE {DBNICK}_orders_version;
REPAIR TABLE {DBNICK}_orders_client_field;
REPAIR TABLE {DBNICK}_orders_store;
REPAIR TABLE {DBNICK}_orders_store_option;
REPAIR TABLE {DBNICK}_orders_option;
REPAIR TABLE {DBNICK}_param;
REPAIR TABLE {DBNICK}_param_value;
REPAIR TABLE {DBNICK}_provider;
REPAIR TABLE {DBNICK}_provider_group;
REPAIR TABLE {DBNICK}_provider_key;
REPAIR TABLE {DBNICK}_provider_key_value;
REPAIR TABLE {DBNICK}_provider_key_set;
REPAIR TABLE {DBNICK}_report;
REPAIR TABLE {DBNICK}_report_right;
REPAIR TABLE {DBNICK}_store;
REPAIR TABLE {DBNICK}_store_info;
REPAIR TABLE {DBNICK}_store_param;
REPAIR TABLE {DBNICK}_store_set;
REPAIR TABLE {DBNICK}_store_comment;
REPAIR TABLE {DBNICK}_store_comment_vote;
REPAIR TABLE {DBNICK}_store_comment_block;
REPAIR TABLE {DBNICK}_self_key;
REPAIR TABLE {DBNICK}_self_key_right;
REPAIR TABLE {DBNICK}_self_key_value;
REPAIR TABLE {DBNICK}_tmp_client;
REPAIR TABLE {DBNICK}_tmp_orders;
REPAIR TABLE {DBNICK}_tmp_store;
REPAIR TABLE {DBNICK}_tmp_store_comment;
REPAIR TABLE {DBNICK}_tmp_topic;
REPAIR TABLE {DBNICK}_tmp_info;
REPAIR TABLE {DBNICK}_tmp_self_key;
REPAIR TABLE {DBNICK}_tmp_web_key;
REPAIR TABLE {DBNICK}_tmp_order_right;
REPAIR TABLE {DBNICK}_topic;
REPAIR TABLE {DBNICK}_topic_alt;
REPAIR TABLE {DBNICK}_topic_filter;
REPAIR TABLE {DBNICK}_topic_right;
REPAIR TABLE {DBNICK}_topic_store;
REPAIR TABLE {DBNICK}_topic_key;
REPAIR TABLE {DBNICK}_topic_key_value;
REPAIR TABLE {DBNICK}_topic_key_set;
REPAIR TABLE {DBNICK}_u_files_info_value;
REPAIR TABLE {DBNICK}_u_files_store;
REPAIR TABLE {DBNICK}_u_info_value;
REPAIR TABLE {DBNICK}_u_store;
REPAIR TABLE {DBNICK}_u_store_info;
REPAIR TABLE {DBNICK}_u_store_set;
REPAIR TABLE {DBNICK}_u_topic_store;
REPAIR TABLE {DBNICK}_user;
REPAIR TABLE {DBNICK}_user_task;
REPAIR TABLE {DBNICK}_user_note;
REPAIR TABLE {DBNICK}_user_chat;
REPAIR TABLE {DBNICK}_user_file_version;
REPAIR TABLE {DBNICK}_user_filter;
REPAIR TABLE {DBNICK}_user_group;
REPAIR TABLE {DBNICK}_user_log;
REPAIR TABLE {DBNICK}_user_online;
REPAIR TABLE {DBNICK}_web_key;
REPAIR TABLE {DBNICK}_web_key_right;
REPAIR TABLE {DBNICK}_web_key_value;
REPAIR TABLE {DBNICK}_web_outside;
REPAIR TABLE {DBNICK}_web_outside_right;
REPAIR TABLE {DBNICK}_web_inside;
REPAIR TABLE {DBNICK}_web_inside_right;

