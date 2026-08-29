/************************************************************************************************************
 * @version 6.5.1.415 @ 2026-08-29
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 ************************************************************************************************************/

INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('1', '0', 'SELECT ss.store_id AS id
  FROM {DB' 'NICK}_store_stock ss
  JOIN {DB' 'NICK}_provider_stock ps
    ON ss.provider_stock_id = ps.id
 WHERE ps.id = :STOCK@QUERY_STOCK
   AND ss.how >= :HOW_FROM
   AND ss.how <= :HOW_TO', '1');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('2', '0', 'SELECT s.id
  FROM {DB' 'NICK}_store s
  LEFT JOIN {DB' 'NICK}_store_stock ss
    ON ss.store_id = s.id
 WHERE s.how > 0
   AND ss.id IS NULL', '2');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('3', '0', 'SELECT id
  FROM ( SELECT s.id, s.how, SUM(ss.how) AS stock_how
           FROM {DB' 'NICK}_store s
           JOIN {DB' 'NICK}_store_stock ss
             ON ss.store_id = s.id
       GROUP BY s.id, s.how
       ) AS res
 WHERE how <> stock_how', '3');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('4', '0', 'SELECT s.id
  FROM {DB' 'NICK}_store s
  LEFT JOIN {DB' 'NICK}_files_store f
    ON f.elem_id = s.id
 WHERE f.id IS NULL', '4');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('5', '0', 'SELECT id
  FROM {DB' 'NICK}_store
 WHERE code_made <> ''''
   AND code_made IN ( SELECT code_made
                        FROM {DB' 'NICK}_store
                    GROUP BY code_made
                      HAVING COUNT(*) > 1 )', '5');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('6', '0', 'SELECT id
  FROM {DB' 'NICK}_store
 WHERE update_time < DATE_SUB(NOW(), INTERVAL :DAYS_AGO DAY)', '6');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('7', '0', 'SELECT id
  FROM {DB' 'NICK}_store
 WHERE tax_group_id IS NULL', '7');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('8', '0', 'SELECT sp.store_id AS id
  FROM {DB' 'NICK}_store_param sp
  JOIN {DB' 'NICK}_param p
    ON sp.param_id = p.id
 WHERE p.id = :PARAM@QUERY_PARAM
   AND sp.value_set_sum >= :SUM_FROM
   AND sp.value_set_sum <= :SUM_TO', '8');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('9', '0', 'SELECT si.store_id AS id
  FROM {DB' 'NICK}_store_info si
  JOIN {DB' 'NICK}_info_value iv
    ON si.value_id = iv.id
 WHERE iv.id = :VALUE@QUERY_INFO_VALUE', '9');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('10', '0', 'SELECT id
  FROM ( SELECT si.store_id AS id,
                SUM(IF(i.id = :INFO@QUERY_INFO, 1, 0)) AS has
           FROM {DB' 'NICK}_store_info si
           JOIN {DB' 'NICK}_info i
             ON si.info_id = i.id
       GROUP BY si.store_id
       ) AS res
 WHERE has = 0', '10');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('11', '0', 'SELECT s.id
  FROM {DB' 'NICK}_store s
  JOIN {DB' 'NICK}_tax_group tg
    ON s.tax_group_id = tg.id
 WHERE tg.id = :TAX_GROUP@QUERY_TAX_GROUP', '11');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('12', '1', 'SELECT o.id
  FROM {DB' 'NICK}_orders o
  JOIN {DB' 'NICK}_orders_store os
    ON os.version_id = o.version_id
  JOIN {DB' 'NICK}_provider_stock ps
    ON os.store_stock_id = ps.id
 WHERE ps.id = :STOCK@QUERY_STOCK
 GROUP BY o.id', '1');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('13', '1', 'SELECT o.id
  FROM {DB' 'NICK}_orders o
  JOIN {DB' 'NICK}_orders_version ov
    ON o.version_id = ov.id
 WHERE ov.total_sum >= :SUM_FROM
   AND ov.total_sum <= :SUM_TO', '2');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('14', '1', 'SELECT o.id
  FROM {DB' 'NICK}_orders o
  JOIN {DB' 'NICK}_orders_store os
    ON os.version_id = o.version_id
 WHERE os.store_code_shop = '':CODE_SHOP''
 GROUP BY o.id', '3');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('15', '1', 'SELECT id
  FROM ( SELECT o.id, COUNT(ov.id) AS how
           FROM {DB' 'NICK}_orders o
           JOIN {DB' 'NICK}_orders_version ov
             ON ov.order_id = o.id
       GROUP BY o.id
       ) AS res
 WHERE how >= :VERSIONS_FROM', '4');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('16', '2', 'SELECT c.id
  FROM {DB' 'NICK}_client c
  LEFT JOIN {DB' 'NICK}_orders_version ov
    ON ov.client_id = c.id
 WHERE ov.id IS NULL', '1');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('17', '2', 'SELECT client_id AS id
  FROM ( SELECT ov.client_id, SUM(ov.total_sum) AS total
           FROM {DB' 'NICK}_orders o
           JOIN {DB' 'NICK}_orders_version ov
             ON o.version_id = ov.id
       GROUP BY ov.client_id
       ) AS res
 WHERE total >= :TOTAL_FROM', '2');
INSERT INTO {DBNICK}_user_filter (id, place, sql_txt, pos) VALUES ('18', '2', 'SELECT client_id AS id
  FROM ( SELECT ov.client_id, MAX(ov.date_time) AS last_time
           FROM {DB' 'NICK}_orders o
           JOIN {DB' 'NICK}_orders_version ov
             ON o.version_id = ov.id
       GROUP BY ov.client_id
       ) AS res
 WHERE last_time < DATE_SUB(NOW(), INTERVAL :DAYS_AGO DAY)', '3');
UPDATE {DBNICK}_generator SET gen_value = 18 WHERE table_name = 'user_filter';
