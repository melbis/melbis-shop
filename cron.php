<?php
/***************************************************************************************************
 * @version 6.5.1.432 @ 2026-09-13
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov 
 **************************************************************************************************/                                        
 
// Melbis start
require 'units/melbis.php';        

// Tasks
MELBIS()->CronAdd('* * * * *', 'http://localhost/?mod=melbis_cron', 100, 'cron');

// Run
MELBIS()->CronRun();


?>
