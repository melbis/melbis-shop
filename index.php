<?php
/***************************************************************************************************
 * 
 * index.php - index page
 * 
 ***************************************************************************************************
 * @version 6.5.0.205 @ 2026-04-03
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/                             

// Require
require_once('units/melbis_inc.php');

// Create connect with DataBase 
$gDb = new MySql('MELBIS_INC_halt');
$gDb->Connect(__FILE__, __LINE__);

// Create Parser
$gParser = new Parser('MELBIS_INC_halt', $gDb);

// Define self constants
$gParser->DefineSelfConst();

// Define session
$gParser->DefineSession(DB_USER_NAME.'_MELBIS_SHOP');

// Define lazy load script
$gParser->DefineLazyScript($gSitePath.'lazy.php');

// Define lang
$lang_list = ['en'];
$lang_get = $_GET['lang'] ?? $lang_list[0];
$gLang = ( in_array($lang_get, $lang_list) ) ? $lang_get : $lang_list[0];

// Default module
if ( !isset($_GET['mod']) ) $_GET['mod'] = 'melbis_base_page'; 

// Parse
$gParser->Parse($gSitePath, $gTemplate, $_GET['mod'], [$gLang, serialize($_GET), serialize($_POST)], $gUseCache, $gBuild);

// Publish
$gParser->Publish();

// Possible report
$gParser->Report();

?>