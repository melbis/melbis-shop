<?php
/***************************************************************************************************
 * 
 * index.php - index page
 * 
 ***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/                             

// Require
require_once('config.php');
require_once('units/melbis_inc.php');

// Create connect with DataBase 
$gDb = new MySql('MELBIS_INC_halt');
$gDb->Connect(__FILE__, __LINE__);

// Create Parser
$gParser = new Parser('MELBIS_INC_halt', $gDb);

// Define self constants
$gParser->DefineSelfConst();
$gParser->DefineSelfVars('CONST');

// Define session
$gSession = $gParser->DefineSession(DB_USER_NAME.'_MELBIS_SHOP');

// Define lazy load script
$gParser->DefineLazyScript($gSitePath.'/lazy.php');

// Define module
if ( !isset($gGet['mod']) ) $gGet['mod'] = 'melbis_base_page'; 

// Parse
$gParser->Parse($gSitePath, $gTemplate, $gGet['mod'], [serialize($gGet), serialize($gPost)], $gUseCache, $gBuild);

// Publish
$gParser->Publish();

// Possible report
$gParser->Report();

?>