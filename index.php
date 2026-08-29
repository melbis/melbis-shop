<?php
/***************************************************************************************************
 * @version 6.5.1.416 @ 2026-08-29
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

// No AI
if ( strncasecmp($entry_point, 'agent_', 6) == 0 )
{
    header('HTTP/1.1 403 Forbidden');

    exit('ACCESS_DENIED');
}

// Run
MELBIS()->Run($entry_point, $entry_param);

// Publish
MELBIS()->Publish();

// Possible report
MELBIS()->Report();

?>
