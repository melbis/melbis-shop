<?php
/***************************************************************************************************
 * @version 6.5.0.410 @ 2026-08-28
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/

// Melbis start
require 'units/melbis.php';

// Who is calling
$login = preg_replace('/[^a-z_0-9]/i', '', $_POST['login'] ?? '');
$secret = $_POST['secret'] ?? '';
$mod = preg_replace('/[^a-z_0-9]/i', '', $_POST['mod'] ?? '');

// The key the agent got from AGENT_CONNECT, kept in APCu under its login
$key = ( $login == '' ) ? false : apcu_fetch('MELBIS_AGENT_SECRET_'.$login);

if ( $key === false || $secret == '' || $secret !== $key )
{
    header('HTTP/1.1 403 Forbidden');

    exit('ACCESS_DENIED');
}

// This entry point runs the scripts of the agent and nothing else
if ( substr($mod, 0, 6) != 'agent_' )
{
    header('HTTP/1.1 403 Forbidden');

    exit('ACCESS_DENIED: agent.php runs agent_* modules only');
}

// Define self constants
MELBIS()->DefineSelfConst();

// A wrong module name must answer, not fall through to the storefront page
if ( !MELBIS()->UnitExists($mod) )
{
    header('HTTP/1.1 404 Not Found');

    exit('NO_MODULE: there is no units/'.$mod.'.php');
}
if ( !MELBIS()->UnitExists($mod, true) )
{
    header('HTTP/1.1 403 Forbidden');

    exit('NO_ENTRY_POINT: set entry_point = 1 for unit '.$mod);
}

// The key is ours, not the module's
unset($_POST['login'], $_POST['secret'], $_POST['mod']);

// Run
MELBIS()->Run($mod, [serialize($_POST)]);

// Publish
MELBIS()->Publish();

?>