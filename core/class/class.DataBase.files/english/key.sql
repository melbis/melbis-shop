/************************************************************************************************************
 * 
 * key_set.sql - Server database initial set
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

INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('95', 'code 95', '------------- Catalog and goods  ----------------', '0', '0', '0', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('24', 'TOPIC_ALT_KIND_KEY', 'Alternative catalogs', '95', '1', '1', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('22', 'code 22', 'Sections', '95', '1', '2', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('19', 'TOPIC_KIND_KEY', 'Type', '22', '2', '3', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('11', 'TOPIC_TEMPL_KEY', 'Style', '22', '2', '4', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('21', 'TOPIC_ORDER_KEY', 'Sorting', '22', '2', '5', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('12', 'FILES_TOPIC', 'File groups', '22', '2', '6', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('43', 'code 43', 'Goods', '95', '1', '7', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('44', 'STORE_KIND_KEY', 'Type', '43', '2', '8', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('45', 'STORE_STATUS_KEY', 'Status', '43', '2', '9', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('98', 'STORE_STATE_KEY', 'State', '43', '2', '10', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('46', 'STORE_TEMPL_KEY', 'Style', '43', '2', '11', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('47', 'FILES_STORE', 'File groups', '43', '2', '12', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('62', 'code 62', 'Binding', '43', '2', '13', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('140', 'STORE_SET_OBJ_KEY', 'Object', '62', '3', '14', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('49', 'STORE_SET_KIND_KEY', 'Type', '62', '3', '15', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('1', 'code 1', '----------- Basic directories -----------', '0', '0', '16', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('137', 'code 137', 'Providers', '1', '1', '17', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('138', 'PROVIDER_KIND_KEY', 'Type', '137', '2', '18', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('139', 'PROVIDER_STATE_KEY', 'State', '137', '2', '19', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('108', 'code 108', 'Brands', '1', '1', '20', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('109', 'BRAND_KIND_KEY', 'Type', '108', '2', '21', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('110', 'FILES_BRAND', 'File groups', '108', '2', '22', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('32', 'code 32', 'Features', '1', '1', '23', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('33', 'code 33', 'Name', '32', '2', '24', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('2', 'INFO_KIND_KEY', 'Type', '33', '3', '25', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('97', 'INFO_TYPE_KEY', 'Content', '33', '3', '26', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('4', 'FILES_INFO', 'File groups', '33', '3', '27', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('34', 'code 34', 'Value', '32', '2', '28', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('52', 'INFO_VALUE_KIND_KEY', 'Type', '34', '3', '29', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('5', 'FILES_INFO_VALUE', 'File groups', '34', '3', '30', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('92', 'code 92', 'Parameters', '1', '1', '31', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('93', 'PARAM_KIND_KEY', 'Type', '92', '2', '32', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('50', 'code 50', 'Discounts', '1', '1', '33', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('51', 'DISC_KIND_KEY', 'Type', '50', '2', '34', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('94', 'code 94', '------------------ Components --------------------', '0', '0', '35', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('91', 'code 91', 'Filters of goods', '94', '1', '36', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('69', 'TOPIC_FILTER_KIND_KEY', 'Type', '91', '2', '37', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('142', 'code 142', 'Multilanguage', '94', '1', '38', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('143', 'code 143', 'Language', '142', '2', '39', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('144', 'LANG_KIND_KEY', 'Type', '143', '3', '40', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('145', 'code 145', 'Category', '142', '2', '41', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('146', 'TRANS_KIND_KEY', 'Type', '145', '3', '42', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('149', 'TRANS_TABLE_KEY', 'Data', '145', '3', '43', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('147', 'code 147', 'Original', '142', '2', '44', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('148', 'TRANS_ORIGIN_KIND_KEY', 'Type', '147', '3', '45', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('54', 'code 54', 'Promo blocks', '94', '1', '46', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('55', 'ADVERT_KIND_KEY', 'Type', '54', '2', '47', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('57', 'code 57', 'Text', '54', '2', '48', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('56', 'ADVERT_TEXT_KIND_KEY', 'Type', '57', '3', '49', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('58', 'FILES_ADVERT_TEXT', 'File groups', '57', '3', '50', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('101', 'code 101', 'Goods', '54', '2', '51', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('60', 'ADVERT_GOODS_KIND_KEY', 'Type', '101', '3', '52', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('102', 'code 102', 'Binding', '54', '2', '53', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('103', 'ADVERT_LINK_OBJ_KEY', 'Object', '102', '3', '54', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('61', 'ADVERT_LINK_KIND_KEY', 'Type', '102', '3', '55', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('127', 'code 127', 'Scheduler', '94', '1', '56', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('128', 'TASK_KIND_KEY', 'Type', '127', '2', '57', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('129', 'TASK_STATE_KEY', 'Status', '127', '2', '58', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('130', 'code 130', 'Module options', '94', '1', '59', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('131', 'code 131', 'Name', '130', '2', '60', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('133', 'WEB_KEY_KIND_KEY', 'Type', '131', '3', '61', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('134', 'FILES_WEB_KEY', 'File groups', '131', '3', '62', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('132', 'code 132', 'Value', '130', '2', '63', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('135', 'WEB_KEY_VALUE_KIND_KEY', 'Type', '132', '3', '64', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('136', 'FILES_WEB_KEY_VALUE', 'File groups', '132', '3', '65', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('70', 'code 70', '-------------- Buyers and orders --------------', '0', '0', '66', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('71', 'code 71', 'Buyers', '70', '1', '67', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('72', 'code 72', 'Registration', '71', '2', '68', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('86', 'code 86', 'Field', '72', '3', '69', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('73', 'FIELD_KIND_KEY', 'Type', '86', '4', '70', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('74', 'FIELD_SPEC_KEY', 'Special', '86', '4', '71', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('87', 'code 87', 'Value', '72', '3', '72', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('75', 'FIELD_VALUE_KIND_KEY', 'Type', '87', '4', '73', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('76', 'code 76', 'Orders', '70', '1', '74', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('111', 'code 111', 'Goods options', '76', '2', '75', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('112', 'code 112', 'Option', '111', '3', '76', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('113', 'ORDER_STORE_OPTION_KIND_KEY', 'Type', '112', '4', '77', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('88', 'code 88', 'Order options', '76', '2', '78', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('77', 'code 77', 'Option', '88', '3', '79', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('78', 'ORDER_OPTION_KIND_KEY', 'Type', '77', '4', '80', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('84', 'code 84', 'Option value', '88', '3', '81', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('79', 'ORDER_OPTION_VALUE_KIND_KEY', 'Type', '84', '4', '82', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('106', 'code 106', '------------- Called modules ---------------', '0', '0', '83', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('80', 'code 80', 'Orders', '106', '1', '84', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('81', 'MODULE_ORDER_VERSION_CALC', 'Calculation', '80', '2', '85', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('83', 'MODULE_ORDER_VERSION_ADD', 'Editing', '80', '2', '86', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('107', 'ORDER_TRANSPORT', 'Transmit parameters', '80', '2', '87', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('118', 'code 118', 'Dispatcher', '106', '1', '88', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('116', 'MODULE_NOTIFICATION', 'Event detection', '118', '2', '89', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('117', 'EVENT_NOTIFICATION', 'List of events', '118', '2', '90', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('150', 'code 150', 'Multilanguage', '106', '1', '91', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('151', 'MODULE_TRANSLATE_LINE', 'Translator', '150', '2', '92', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('8', 'FILES_KEY_VALUE', '------------- Editor settings -------------', '0', '0', '93', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('66', 'code 66', 'Image Editor', '8', '1', '94', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('9', 'FILES_PROFILE', 'Profiles', '66', '2', '95', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('7', 'FILES_MASK', 'Mask files', '66', '2', '96', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('67', 'code 67', 'HTML editor', '8', '1', '97', '1');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('65', 'HTML_OPTION', 'Settings', '67', '2', '98', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('123', 'HTML_TAG', 'Tags', '67', '2', '99', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('124', 'HTML_CLASS', 'Classes', '67', '2', '100', '0');
INSERT INTO {DBNICK}_key (id, code, name, tindex, tlevel, absindex, folder) VALUES ('125', 'HTML_TEMPLATE', 'Templates', '67', '2', '101', '0');
UPDATE {DBNICK}_generator SET gen_value = 152 WHERE table_name = 'key';

INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('1', 'INFO_KIND_KEY', 'kDefault', 'By default', '1', '1');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('3', 'FILES_INFO', 'kBase', 'Original file', '1', '1');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('4', 'FILES_INFO_VALUE', 'kBase', 'Original file', '1', '1');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('5', 'FILES_INFO', 'kDefault', 'Default icon', '0', '4');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('7', 'FILES_INFO_VALUE', 'kDefault', 'Default icon', '0', '4');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('10', 'FILES_INFO', 'kDescr', 'File from description', '1', '3');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('11', 'FILES_INFO_VALUE', 'kDescr', 'File from description', '1', '3');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('15', 'FILES_KEY_VALUE', 'kBase', 'Базовый файл', '1', '1');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('16', 'FILES_MASK', 'Unmasked', '', '1', '1');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('17', 'FILES_PROFILE', 'Sample', '<MELBISSHOP ShopVersion="Melbis Shop Commercial v6.2.1.345"><JPEG FileType="0" Compress="90" Width="200" Hight="200" Smart="True"/><FILE KindKey="kDefault"/><MASK File="files/1899/12_30/00_00/" Pos="0" Alpha="0"/><CANVAS Range="255" Border="5" Color="16777215"/><ROTATE Rotate="0" Mirror="False"/><EFFECTS Red="0" Green="0" Blue="0" Intensive="0" Contrast="0" Sharpen="10"/></MELBISSHOP>', '0', '1');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('20', 'TOPIC_TEMPL_KEY', 'kDefault', 'By default', '1', '1');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('22', 'FILES_TOPIC', 'kBase', 'Original file', '1', '1');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('24', 'FILES_TOPIC', 'kDescr', 'File from description', '1', '3');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('25', 'FILES_TOPIC', 'kDefault', 'Default icon', '0', '4');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('35', 'TOPIC_KIND_KEY', 'kGoods', 'Goods section', '1', '2');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('37', 'TOPIC_KIND_KEY', 'kLink', 'Link to URL', '1', '4');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('39', 'TOPIC_ORDER_KEY', 'kDefault', 'By default', '1', '1');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('40', 'TOPIC_ALT_KIND_KEY', 'kTree', 'Tree', '1', '1');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('41', 'TOPIC_ALT_KIND_KEY', 'kCenter', 'Center', '0', '2');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('70', 'STORE_KIND_KEY', 'kDefault', 'By default', '1', '70');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('71', 'STORE_TEMPL_KEY', 'kDefault', 'By default', '1', '71');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('72', 'STORE_STATUS_KEY', 'kExist', 'In stock', '1', '72');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('73', 'STORE_STATUS_KEY', 'kAbsent', 'Absent', '1', '73');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('74', 'STORE_STATUS_KEY', 'kExistOrder', 'Expected', '0', '74');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('75', 'STORE_STATUS_KEY', 'kAbsentOrder', 'Not expected', '0', '75');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('76', 'FILES_STORE', 'kBase', 'Original file', '1', '76');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('78', 'FILES_STORE', 'kDescr', 'File from description', '1', '78');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('79', 'FILES_STORE', 'kDefault', 'Default icon', '0', '79');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('80', 'FILES_STORE', 'kDescrImg', 'Large image', '0', '80');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('82', 'STORE_SET_KIND_KEY', 'kDefault', 'By default', '1', '82');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('83', 'DISC_KIND_KEY', 'kOrder', 'From the order amount', '1', '83');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('85', 'DISC_KIND_KEY', 'kBalanceSum', 'From the amount of the customers balance sheet', '0', '85');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('86', 'INFO_VALUE_KIND_KEY', 'kDefault', 'By default', '1', '86');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('94', 'ADVERT_KIND_KEY', 'kDefault', 'By default', '1', '94');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('100', 'ADVERT_TEXT_KIND_KEY', 'kDefault', 'By default', '1', '100');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('101', 'FILES_ADVERT_TEXT', 'kBase', 'Original file', '1', '101');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('103', 'FILES_ADVERT_TEXT', 'kDescr', 'File from description', '1', '103');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('104', 'FILES_ADVERT_TEXT', 'kDefault', 'Default icon', '0', '104');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('105', 'ADVERT_GOODS_KIND_KEY', 'kDefault', 'By default', '1', '105');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('107', 'ADVERT_LINK_KIND_KEY', 'kId', 'By ID', '1', '107');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('110', 'HTML_OPTION', 'kCSS', '', '1', '110');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('114', 'TOPIC_FILTER_KIND_KEY', 'kDefault', 'By default', '1', '114');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('115', 'FIELD_KIND_KEY', 'kDefault', 'By default', '1', '115');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('116', 'FIELD_SPEC_KEY', 'kDefault', 'By default', '1', '116');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('117', 'FIELD_VALUE_KIND_KEY', 'kDefault', 'By default', '1', '117');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('120', 'ORDER_OPTION_KIND_KEY', 'kDefault', 'By default', '1', '120');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('121', 'ORDER_OPTION_VALUE_KIND_KEY', 'kDefault', 'By default', '1', '121');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('123', 'MODULE_ORDER_VERSION_CALC', 'melbis_inc_logic.php', 'MELBIS_INC_LOGIC_order_calc', '0', '123');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('125', 'MODULE_ORDER_VERSION_ADD', 'melbis_inc_logic.php', 'MELBIS_INC_LOGIC_order_edit', '0', '125');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('131', 'PARAM_KIND_KEY', 'kDefault', 'By default', '1', '131');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('135', 'INFO_TYPE_KEY', 'kSet', 'Набор значений', '1', '135');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('137', 'INFO_TYPE_KEY', 'kDecimal', 'Number', '1', '137');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('138', 'STORE_STATE_KEY', 'kDefault', 'By default', '1', '138');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('145', 'STORE_STATE_KEY', 'kImport', 'Imported', '0', '145');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('146', 'STORE_STATE_KEY', 'kDescrNeed', 'Write a description', '0', '146');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('147', 'STORE_STATE_KEY', 'kDescrUp', 'Update description', '0', '147');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('148', 'STORE_STATE_KEY', 'kOk', 'Finished', '0', '148');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('149', 'ADVERT_LINK_OBJ_KEY', 'kTopic', 'To sections', '1', '149');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('150', 'ADVERT_LINK_OBJ_KEY', 'kGoods', 'To goods', '1', '150');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('153', 'ORDER_TRANSPORT', 'kDefault', 'By default', '1', '153');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('154', 'BRAND_KIND_KEY', 'kDefault', 'By default', '1', '154');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('155', 'FILES_BRAND', 'kBase', 'Original file', '1', '155');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('156', 'FILES_BRAND', 'kDescr', 'File from description', '1', '156');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('157', 'FILES_BRAND', 'kDefault', 'Default icon', '0', '157');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('159', 'ORDER_STORE_OPTION_KIND_KEY', 'kDefault', 'By default', '1', '159');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('161', 'MODULE_NOTIFICATION', 'melbis_inc_logic.php', 'MELBIS_INC_LOGIC_notify_events', '0', '161');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('163', 'EVENT_NOTIFICATION', 'kNewOrder', 'New order', '0', '163');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('164', 'EVENT_NOTIFICATION', 'kNewClient', 'New customer', '0', '164');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('178', 'FILES_KEY_VALUE', 'kAttach', 'Дополнительный файл', '1', '178');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('181', 'HTML_TAG', 'p', '<P> Paragraph', '1', '1');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('182', 'HTML_TAG', 'div', '<DIV> Section', '1', '3');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('183', 'HTML_TAG', 'span', '<SPAN> Fragment', '1', '2');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('184', 'HTML_TAG', 'h1', '<H1> Header 1', '1', '5');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('191', 'HTML_TAG', 'h2', '<H2> Header 2', '1', '6');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('192', 'HTML_TAG', 'h3', '<H3> Header 3', '1', '7');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('193', 'HTML_TAG', 'h4', '<H4> Header 4', '1', '8');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('194', 'HTML_TAG', 'h5', '<H5> Header 5', '1', '9');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('195', 'HTML_TAG', 'h6', '<H6> Header 6', '1', '10');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('196', 'HTML_TAG', 'a', '<A> Link, Anchor', '1', '4');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('253', 'HTML_TEMPLATE', 'Таблица', '<table  border="1" width="100%" cellpadding="2" cellspacing="2"><tr><td>1</td><td>2</td></tr><tr><td>3</td><td>4</td></tr></table>', '0', '253');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('257', 'HTML_OPTION', 'kContainer', '', '1', '257');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('261', 'TASK_KIND_KEY', 'kDefault', 'By default', '1', '261');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('262', 'TASK_KIND_KEY', 'kUrgent', 'Urgent', '1', '262');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('263', 'TASK_KIND_KEY', 'kWish', 'Wishful', '1', '263');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('264', 'TASK_KIND_KEY', 'kPriority1', 'Priority #1', '0', '264');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('265', 'TASK_KIND_KEY', 'kPriority2', 'Priority #2', '0', '265');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('266', 'TASK_KIND_KEY', 'kPriority3', 'Priority #3', '0', '266');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('267', 'TASK_STATE_KEY', 'kNew', 'New', '1', '267');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('268', 'TASK_STATE_KEY', 'kAccept', 'Accepted', '1', '268');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('269', 'TASK_STATE_KEY', 'kHold', 'Postponed', '1', '269');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('270', 'TASK_STATE_KEY', 'kDone', 'Done', '1', '270');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('271', 'TASK_STATE_KEY', 'kStop', 'Stopped', '1', '271');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('272', 'TASK_STATE_KEY', 'kTrans', 'Delegated', '1', '272');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('273', 'TASK_STATE_KEY', 'kExplain', 'Requires clarification', '1', '273');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('274', 'TASK_STATE_KEY', 'kComment', 'Commentary', '1', '274');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('275', 'TASK_STATE_KEY', 'kClose', 'Closed', '1', '275');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('277', 'WEB_KEY_KIND_KEY', 'kDefault', 'By default', '1', '277');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('278', 'WEB_KEY_VALUE_KIND_KEY', 'kDefault', 'By default', '1', '278');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('279', 'FILES_WEB_KEY', 'kBase', 'Original file', '1', '279');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('280', 'FILES_WEB_KEY', 'kDescr', 'File from description', '1', '280');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('281', 'FILES_WEB_KEY', 'kDefault', 'Default icon', '0', '281');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('282', 'FILES_WEB_KEY_VALUE', 'kBase', 'Original file', '1', '282');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('283', 'FILES_WEB_KEY_VALUE', 'kDescr', 'File from description', '1', '283');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('284', 'FILES_WEB_KEY_VALUE', 'kDefault', 'Default icon', '0', '284');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('286', 'PROVIDER_KIND_KEY', 'kDefault', 'By default', '1', '286');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('287', 'PROVIDER_STATE_KEY', 'kDefault', 'By default', '1', '287');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('288', 'INFO_TYPE_KEY', 'kText', 'Text', '1', '288');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('289', 'STORE_SET_OBJ_KEY', 'kGoods', 'To the goods', '1', '289');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('290', 'STORE_SET_OBJ_KEY', 'kTopic', 'To the section', '1', '290');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('291', 'HTML_OPTION', 'kPostProcess', 'editor.find("span.Apple-tab-span").replaceWith("&nbsp;");', '1', '291');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('292', 'LANG_KIND_KEY', 'kDefault', 'By default', '1', '292');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('293', 'TRANS_KIND_KEY', 'kDefault', 'By default', '1', '293');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('294', 'TRANS_ORIGIN_KIND_KEY', 'kDefault', 'By default', '1', '294');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('295', 'TRANS_TABLE_KEY', 'kDefault', 'По умолчанию', '1', '295');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('296', 'TRANS_TABLE_KEY', 'kTopic', 'Sections', '0', '296');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('297', 'TRANS_TABLE_KEY', 'kStore', 'Goods', '0', '297');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('298', 'TRANS_TABLE_KEY', 'kTags', 'Tags', '0', '298');
INSERT INTO {DBNICK}_key_value (id, key_code, key_name, value_txt, sys_key, pos) VALUES ('299', 'MODULE_TRANSLATE_LINE', 'melbis_inc_translate.php', 'MELBIS_INC_TRANSLATE_service', '0', '299');
UPDATE {DBNICK}_generator SET gen_value = 299 WHERE table_name = 'key_value';
