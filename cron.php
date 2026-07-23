<?php
/***************************************************************************************************
 * @version 6.5.0.336 @ 2026-07-23
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov 
 **************************************************************************************************/                                        
 
// Melbis start
require 'units/melbis.php';        

// Tasks
MELBIS()->CronAdd('* * * * *', 'http://localhost/?mod=melbis_cron', 100, '_cron.log');

// Run
MELBIS()->CronRun();


?>
