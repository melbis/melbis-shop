<?php
/***************************************************************************************************
 * @version 6.5.1.419 @ 2026-09-01
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_TAX;

// Libraries
use MELBIS_INC_AGENT_TABLE as TABLE;


/**
 * Function CmdListCut
 **/
function CmdListCut($mUserId, $mParam)
{
    return TABLE\Read('tax_group');
}


/**
 * Function CmdListFull
 **/
function CmdListFull($mUserId, $mParam)
{
    $more = ['tax_rate', 'tax_area', 'tax_rule', 'tax_area_key_set'];

    return TABLE\Read('tax_group', $more);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'tax_group', $mParam);
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'tax_group', $mParam['id'], $mParam);
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'tax_group', $mParam['id'], $mParam);
}


/**
 * Function CmdPos
 **/
function CmdPos($mUserId, $mParam)
{
    return TABLE\Pos($mUserId, 'tax_group', [], $mParam);
}


/**
 * Function CmdRateAdd
 **/
function CmdRateAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'tax_rate', $mParam);
}


/**
 * Function CmdRateUpdate
 **/
function CmdRateUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'tax_rate', $mParam['id'], $mParam);
}


/**
 * Function CmdRateRemove
 **/
function CmdRateRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'tax_rate', $mParam['id'], $mParam);
}


/**
 * Function CmdAreaAdd
 **/
function CmdAreaAdd($mUserId, $mParam)
{
    return TABLE\TreeAdd($mUserId, 'tax_area', $mParam);
}


/**
 * Function CmdAreaUpdate
 **/
function CmdAreaUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'tax_area', $mParam['id'], $mParam);
}


/**
 * Function CmdAreaMove
 **/
function CmdAreaMove($mUserId, $mParam)
{
    return TABLE\TreeMove($mUserId, 'tax_area', $mParam);
}


/**
 * Function CmdAreaRemove
 **/
function CmdAreaRemove($mUserId, $mParam)
{
    return TABLE\TreeRemove($mUserId, 'tax_area', $mParam['id'], $mParam);
}


/**
 * Function CmdRuleAdd
 **/
function CmdRuleAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'tax_rule', $mParam);
}


/**
 * Function CmdRuleUpdate
 **/
function CmdRuleUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'tax_rule', $mParam['id'], $mParam);
}


/**
 * Function CmdRuleRemove
 **/
function CmdRuleRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'tax_rule', $mParam['id'], $mParam);
}


/**
 * Function CmdKeyAdd
 **/
function CmdKeyAdd($mUserId, $mParam)
{
    return TABLE\KeySetAdd($mUserId, 'tax_area', $mParam['tax_area_id'], $mParam);
}


/**
 * Function CmdKeyUpdate
 **/
function CmdKeyUpdate($mUserId, $mParam)
{
    return TABLE\KeySetUpdate($mUserId, 'tax_area', $mParam['id'], $mParam);
}


/**
 * Function CmdKeyRemove
 **/
function CmdKeyRemove($mUserId, $mParam)
{
    return TABLE\KeySetRemove($mUserId, 'tax_area', $mParam['id']);
}

?>
