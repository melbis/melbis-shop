<?php
/***************************************************************************************************
 * 
 * lazy.php - Lazy loading module via AJAX
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

// Parse 
$gParser->ParseLazy($gSitePath, $gTemplate, $_POST['mod'], $_POST['params'], $gUseCache, $gBuild);

// Publish
$gParser->Publish();

?>