<?php
/***************************************************************************************************
 * @version 6.5.0.277 @ 2026-05-18
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov 
 **************************************************************************************************/                                        
 
// Melbis start
require 'units/melbis.php';        

// Tasks
MELBIS()->CronAdd('* * * * *', 'http://localhost/?mod=melbis_cron');

// Run
MELBIS()->CronRun();


?>
