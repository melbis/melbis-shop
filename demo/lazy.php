<?php
/***************************************************************************************************
 * 
 * lazy.php - Lazy loading module via AJAX
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

// Define session
$gSession = $gParser->DefineSession(DB_USER_NAME.'_MELBIS_SHOP');

// Parse 
$gParser->ParseLazy($gSitePath, $gTemplate, $gPost['mod'], $gPost['params'], $gUseCache, $gBuild);

// Publish
$gParser->Publish();

?>