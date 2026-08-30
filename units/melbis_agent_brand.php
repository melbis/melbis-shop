<?php
/***************************************************************************************************
 * @version 6.5.1.417 @ 2026-08-30
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_BRAND;

// Libraries
use MELBIS_INC_AGENT_TABLE as TABLE;


/**
 * Function CmdListCut
 **/
function CmdListCut($mUserId, $mParam)
{
    return TABLE\Read('brand');
}


/**
 * Function CmdListFull
 **/
function CmdListFull($mUserId, $mParam)
{
    return TABLE\Read('brand', ['brand_key_set']);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'brand', $mParam);
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'brand', $mParam['id'], $mParam);
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'brand', $mParam['id'], $mParam);
}


/**
 * Function CmdPos
 **/
function CmdPos($mUserId, $mParam)
{
    return TABLE\Pos($mUserId, 'brand', [], $mParam);
}


/**
 * Function CmdKeyAdd
 **/
function CmdKeyAdd($mUserId, $mParam)
{
    return TABLE\KeySetAdd($mUserId, 'brand', $mParam['brand_id'], $mParam);
}


/**
 * Function CmdKeyUpdate
 **/
function CmdKeyUpdate($mUserId, $mParam)
{
    return TABLE\KeySetUpdate($mUserId, 'brand', $mParam['id'], $mParam);
}


/**
 * Function CmdKeyRemove
 **/
function CmdKeyRemove($mUserId, $mParam)
{
    return TABLE\KeySetRemove($mUserId, 'brand', $mParam['id']);
}




?>
