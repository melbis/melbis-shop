<?php
/***************************************************************************************************
 * @version 6.5.0.270 @ 2026-05-15
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov 
 **************************************************************************************************/                                        
 
// Melbis start
require 'units/melbis.php';        

// Tasks
MELBIS()->CronAdd('0 0 * * *', 'https://shop.melbis.com/?mod=melbis_cron');

// Run
MELBIS()->CronRun();


?>
