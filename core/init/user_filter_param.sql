/************************************************************************************************************
 * @version 6.5.1.417 @ 2026-08-30
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 ************************************************************************************************************/

INSERT INTO {DBNICK}_user_filter_param (id, alias, sql_txt, pos) VALUES ('1', 'QUERY_STOCK', 'SELECT ps.id AS ID,
       ''['' || p.name || ''] '' || ps.name AS NAME
  FROM cut_provider_stock ps
  JOIN cut_provider p
    ON ps.provider_id = p.id
 ORDER BY p.pos, ps.pos', '1');
INSERT INTO {DBNICK}_user_filter_param (id, alias, sql_txt, pos) VALUES ('2', 'QUERY_PARAM', 'SELECT id AS ID,
       name AS NAME
  FROM cut_param
 ORDER BY pos', '2');
INSERT INTO {DBNICK}_user_filter_param (id, alias, sql_txt, pos) VALUES ('3', 'QUERY_PARAM_VALUE', 'SELECT pv.id AS ID,
       ''['' || p.name || ''] '' || pv.name AS NAME
  FROM cut_param_value pv
  JOIN cut_param p
    ON pv.param_id = p.id
 ORDER BY p.pos, pv.pos', '3');
INSERT INTO {DBNICK}_user_filter_param (id, alias, sql_txt, pos) VALUES ('4', 'QUERY_INFO', 'SELECT id AS ID,
       name AS NAME
  FROM cut_info
 WHERE folder = 0
 ORDER BY absindex', '4');
INSERT INTO {DBNICK}_user_filter_param (id, alias, sql_txt, pos) VALUES ('5', 'QUERY_INFO_VALUE', 'SELECT iv.base_id AS ID,
       ''['' || i.name || ''] '' || CAST(iv.name AS VARCHAR(255)) AS NAME
  FROM u_info_value iv
  JOIN cut_info i
    ON iv.info_id = i.id
 WHERE iv.base_id IS NOT NULL
 ORDER BY i.absindex, iv.pos', '5');
INSERT INTO {DBNICK}_user_filter_param (id, alias, sql_txt, pos) VALUES ('6', 'QUERY_TAX_GROUP', 'SELECT id AS ID,
       name AS NAME
  FROM cut_tax_group
 ORDER BY pos', '6');
UPDATE {DBNICK}_generator SET gen_value = 6 WHERE table_name = 'user_filter_param';
