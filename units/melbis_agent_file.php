<?php
/***************************************************************************************************
 * @version 6.5.1.535 @ 2026-11-05
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_FILE;

// Libraries
use MELBIS_INC_AGENT_FILE as FILE;


// ————— Public —————
//==================================================================================================

// Files
//==================================================================================================


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    // The catalogue only: the workspace has doors of its own
    return FILE\FileList($mUserId, $mParam, false);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    return FILE\FileAdd($mUserId, $mParam, false);
}


/**
 * Function CmdMake
 **/
function CmdMake($mUserId, $mParam)
{
    return FILE\FileMake($mUserId, $mParam, false);
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    return FILE\FileUpdate($mUserId, $mParam, false);
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    return FILE\FileRemove($mUserId, $mParam, false);
}


/**
 * Function CmdPos
 **/
function CmdPos($mUserId, $mParam)
{
    return FILE\FilePos($mUserId, $mParam, false);
}


?>
