<?php
/***************************************************************************************************
 * @version 6.5.0.292 @ 2026-06-28
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov 
 **************************************************************************************************/                                        
 
// Melbis start
require 'units/melbis.php';

// Define session
MELBIS()->DefineSession('MELBIS_SHOP');            
        
// Define self constants
MELBIS()->DefineSelfConst();

// Entry point
if ( isset($_GET['lazy']) ) 
{    
    // Lazy
    $entry_point = $_POST['mod'];
    $entry_param = $_POST['params'];
}
else 
{
    // Default
    $entry_point = $_GET['mod'] ?? 'melbis_base_page';
    $entry_param = [serialize($_GET), serialize($_POST)];
}

// Run
MELBIS()->Run($entry_point, $entry_param);

// Publish
MELBIS()->Publish();

// Possible report
MELBIS()->Report();

?>
