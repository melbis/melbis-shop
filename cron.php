<?php
/***************************************************************************************************
 * @version 6.5.0.264 @ 2026-05-13
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov 
 **************************************************************************************************/                                        
 
// Melbis start
require 'units/melbis.php';        

// Run
MELBIS()->Run('melbis_cron');

// Publish
MELBIS()->Publish();

// Possible report
MELBIS()->Report();

?>
