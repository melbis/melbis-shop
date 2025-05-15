/************************************************************************************************************
 * 
 * init_set.sql - Server database initial set
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
 *
 **/


/****************************************************************
 *
 * Generators
 *
 ***************************************************************/

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('advert', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('advert_text', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('advert_goods', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('advert_link', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('lang', 1);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('trans', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('trans_origin', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('trans_lang', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('brand', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('brand_key', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('brand_key_value', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('brand_key_set', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('currency', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('client', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('client_field', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('client_group', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('client_rules', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('disc_group', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('disc_rate', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('files_advert_text', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('files_lang', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('files_brand', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('files_field', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('files_info', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('files_info_value', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('files_key_value', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('files_topic', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('files_store', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('files_order_option', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('files_order_option_value', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('field', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('field_value', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('key', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('key_value', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('oper', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('oper_right', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('oper_table', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('orders', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('orders_version', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('order_right', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('order_option', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('order_option_value', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('order_option_block', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('order_option_limit', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('order_option_limit_right', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('order_option_right', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('order_store_option', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('order_store_option_value', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('order_store_option_block', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('order_store_option_right', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('info', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('info_value', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('info_right', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('info_key', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('info_key_value', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('info_key_set', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('param', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('param_value', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('provider', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('provider_group', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('provider_key', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('provider_key_value', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('provider_key_set', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('report', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('report_right', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('store', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('store_param', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('store_set', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('store_clann', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('self_key', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('self_key_right', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('self_key_value', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('topic', 1);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('topic_store', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('topic_right', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('topic_alt', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('topic_filter', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('topic_key', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('topic_key_value', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('topic_key_set', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('user', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('user_group', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('user_filter', 0);

INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('web_outside', 5);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('web_outside_right', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('web_inside', 5);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('web_inside_right', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('web_key', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('web_key_right', 0);
INSERT INTO {DBNICK}_generator (table_name, gen_value) VALUES ('web_key_value', 0);



/****************************************************************
 *
 * First Page
 *
 ***************************************************************/

INSERT INTO {DBNICK}_topic (id, name, kind_key, templ_key, order_key) VALUES (1, 'Основа', 'kFirst', 'kDefault', 'kDefault');

/****************************************************************
 *
 * Language
 *
 ***************************************************************/

INSERT INTO {DBNICK}_lang (id, skey, name, kind_key, is_origin, pos) VALUES (1, 'en', 'English', 'kDefault', 1, 1);

/****************************************************************
 *
 * Modules
 *
 ***************************************************************/

INSERT INTO {DBNICK}_web_outside VALUES ('1','','Магазин','0','0','0','0','https://shop.com','0');
INSERT INTO {DBNICK}_web_outside VALUES ('2','TEST','Тестовый модуль','0','0','1','0','https://shop.com/?mod=melbis_web_test','1');
INSERT INTO {DBNICK}_web_outside VALUES ('3','','Поисковая система','0','0','2','0','https://google.com','0');

INSERT INTO {DBNICK}_web_inside VALUES ('1','TEST','0','Тестовый модуль','https://shop.com/?mod=melbis_web_test','1','1');
INSERT INTO {DBNICK}_web_inside VALUES ('2','TEST','1','Тестовый модуль','https://shop.com/?mod=melbis_web_test','1','1');
INSERT INTO {DBNICK}_web_inside VALUES ('3','TEST','2','Тестовый модуль','https://shop.com/?mod=melbis_web_test','1','1');
INSERT INTO {DBNICK}_web_inside VALUES ('4','TEST','3','Тестовый модуль','https://shop.com/?mod=melbis_web_test','1','1');
INSERT INTO {DBNICK}_web_inside VALUES ('5','TEST','4','Тестовый модуль','https://shop.com/?mod=melbis_web_test','1','1');

