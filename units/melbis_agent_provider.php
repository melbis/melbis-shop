<?php
/***************************************************************************************************
 * @version 6.5.1.416 @ 2026-08-29
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_PROVIDER;

// Libraries
use MELBIS_INC_AGENT_TABLE as TABLE;


/**
 * Function CmdListCut
 **/
function CmdListCut($mUserId, $mParam)
{
    return TABLE\Read('provider_group', ['provider']);
}


/**
 * Function CmdListFull
 **/
function CmdListFull($mUserId, $mParam)
{
    $more = ['provider', 'provider_stock', 'provider_key_set', 'provider_stock_key_set'];

    return TABLE\Read('provider_group', $more);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'provider', $mParam);
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'provider', $mParam['id'], $mParam);
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'provider', $mParam['id'], $mParam);
}


/**
 * Function CmdPos
 **/
function CmdPos($mUserId, $mParam)
{
    return TABLE\Pos($mUserId, 'provider', [], $mParam);
}


/**
 * Function CmdGroupAdd
 **/
function CmdGroupAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'provider_group', $mParam);
}


/**
 * Function CmdGroupUpdate
 **/
function CmdGroupUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'provider_group', $mParam['id'], $mParam);
}


/**
 * Function CmdGroupRemove
 **/
function CmdGroupRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'provider_group', $mParam['id'], $mParam);
}


/**
 * Function CmdGroupPos
 **/
function CmdGroupPos($mUserId, $mParam)
{
    return TABLE\Pos($mUserId, 'provider_group', [], $mParam);
}


/**
 * Function CmdStockAdd
 **/
function CmdStockAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'provider_stock', $mParam);
}


/**
 * Function CmdStockUpdate
 **/
function CmdStockUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'provider_stock', $mParam['id'], $mParam);
}


/**
 * Function CmdStockRemove
 **/
function CmdStockRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'provider_stock', $mParam['id'], $mParam);
}


/**
 * Function CmdStockPos
 **/
function CmdStockPos($mUserId, $mParam)
{
    // The warehouses of one provider
    $scope['provider_id'] = $mParam['provider_id'];

    return TABLE\Pos($mUserId, 'provider_stock', $scope, $mParam);
}


/**
 * Function CmdKeyAdd
 **/
function CmdKeyAdd($mUserId, $mParam)
{
    return TABLE\KeySetAdd($mUserId, 'provider', $mParam['provider_id'], $mParam);
}


/**
 * Function CmdKeyUpdate
 **/
function CmdKeyUpdate($mUserId, $mParam)
{
    return TABLE\KeySetUpdate($mUserId, 'provider', $mParam['id'], $mParam);
}


/**
 * Function CmdKeyRemove
 **/
function CmdKeyRemove($mUserId, $mParam)
{
    return TABLE\KeySetRemove($mUserId, 'provider', $mParam['id']);
}


/**
 * Function CmdStockKeyAdd
 **/
function CmdStockKeyAdd($mUserId, $mParam)
{
    return TABLE\KeySetAdd($mUserId, 'provider_stock', $mParam['provider_stock_id'], $mParam);
}


/**
 * Function CmdStockKeyUpdate
 **/
function CmdStockKeyUpdate($mUserId, $mParam)
{
    return TABLE\KeySetUpdate($mUserId, 'provider_stock', $mParam['id'], $mParam);
}


/**
 * Function CmdStockKeyRemove
 **/
function CmdStockKeyRemove($mUserId, $mParam)
{
    return TABLE\KeySetRemove($mUserId, 'provider_stock', $mParam['id']);
}

?>
