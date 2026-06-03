<?php
/***************************************************************************************************
 * @version 6.5.0.283 @ 2026-06-03
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
