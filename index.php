<?php
/***************************************************************************************************
 * @version 6.5.0.258 @ 2026-05-07
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov 
 **************************************************************************************************/                                        
 
// Melbis start
require 'units/melbis.php';

// Define self constants
MELBIS()->DefineSelfConst();

// Define session
MELBIS()->DefineSession('MELBIS_SHOP');

// Define lang
$lang_list = [ MELBIS_LANG ];
$lang_get = $_GET['lang'] ?? MELBIS_LANG;
$lang = ( in_array($lang_get, $lang_list) ) ? $lang_get : MELBIS_LANG;
MELBIS()->LanguageSet($lang);

// Entry point
$entry_point = $_GET['mod'] ?? 'kasdim_base_page';
$entry_param = [$lang, serialize($_GET), serialize($_POST)];

// Lazy loading
if ( isset($_GET['lazy']) ) 
{    
    $entry_point = $_POST['mod'];
    $entry_param = $_POST['params'];
}

// Run
MELBIS()->Run($entry_point, $entry_param);

// Publish
MELBIS()->Publish();

// Possible report
MELBIS()->Report();

?>
