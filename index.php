<?php
/***************************************************************************************************
 * 
 * index.php - index page
 * 
 ***************************************************************************************************
 * @version 6.5.0.215 @ 2026-04-09
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/                             

// Require
require_once('units/melbis_inc.php');

// Connect to DataBase 
$gDb = new \Melbis\MelbisShop\MySql('MELBIS_INC_halt');
$gDb->Connect(__FILE__, __LINE__);

// Create Parser
$gParser = new \Melbis\MelbisShop\Parser('MELBIS_INC_halt', $gDb);

// Define self constants
$gParser->DefineSelfConst();

// Define session
$gParser->DefineSession('MELBIS_SHOP');

// Define lang
$lang_list = [ MELBIS_LANG ];
$lang_get = $_GET['lang'] ?? MELBIS_LANG;
$gLang = ( in_array($lang_get, $lang_list) ) ? $lang_get : MELBIS_LANG;

// Entry point
$entry_point = ( isset($_GET['mod']) ) ? $_GET['mod'] : 'melbis_base_page';
$entry_param = [$gLang, serialize($_GET), serialize($_POST)];  

// Lazy loading
if ( isset($_GET['lazy']) ) 
{    
    $entry_point = $_POST['mod'];
    $entry_param = $_POST['params'];
}

// Parsing
$gParser->Parse($entry_point, $entry_param);

// Publish
$gParser->Publish();

// Possible report
$gParser->Report();

?>