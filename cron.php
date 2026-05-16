<?php
/***************************************************************************************************
 * @version 6.5.0.272 @ 2026-05-16
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov 
 **************************************************************************************************/                                        
 
// Melbis start
require 'units/melbis.php';        

// Tasks
MELBIS()->CronAdd('* * * * *', 'https://shop.com/?mod=melbis_cron');

// Run
MELBIS()->CronRun();


?>
