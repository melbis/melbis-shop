<?php
/***************************************************************************************************
 * @version 6.5.1.418 @ 2026-09-01
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_ADVERT;

// Libraries
use MELBIS_INC_AGENT_TABLE as TABLE;


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $more = ['advert_text', 'advert_goods', 'advert_link', 'advert_key_set'];

    return TABLE\Read('advert', $more);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    return TABLE\TreeAdd($mUserId, 'advert', $mParam);
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'advert', $mParam['id'], $mParam);
}


/**
 * Function CmdMove
 **/
function CmdMove($mUserId, $mParam)
{
    return TABLE\TreeMove($mUserId, 'advert', $mParam);
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    return TABLE\TreeRemove($mUserId, 'advert', $mParam['id'], $mParam);
}


/**
 * Function CmdTextAdd
 **/
function CmdTextAdd($mUserId, $mParam)
{
    return TABLE\AddBlock($mUserId, 'advert_text', 'advert_id', $mParam['advert_id'], $mParam);
}


/**
 * Function CmdTextUpdate
 **/
function CmdTextUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'advert_text', $mParam['id'], $mParam);
}


/**
 * Function CmdTextRemove
 **/
function CmdTextRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'advert_text', $mParam['id'], $mParam);
}


/**
 * Function CmdTextPos
 **/
function CmdTextPos($mUserId, $mParam)
{
    // The texts of one block
    $scope['advert_id'] = $mParam['advert_id'];

    return TABLE\Pos($mUserId, 'advert_text', $scope, $mParam);
}


/**
 * Function CmdGoodsAdd
 **/
function CmdGoodsAdd($mUserId, $mParam)
{
    return TABLE\AddBlock($mUserId, 'advert_goods', 'advert_id', $mParam['advert_id'], $mParam);
}


/**
 * Function CmdGoodsUpdate
 **/
function CmdGoodsUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'advert_goods', $mParam['id'], $mParam);
}


/**
 * Function CmdGoodsRemove
 **/
function CmdGoodsRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'advert_goods', $mParam['id'], $mParam);
}


/**
 * Function CmdGoodsPos
 **/
function CmdGoodsPos($mUserId, $mParam)
{
    // The goods of one block
    $scope['advert_id'] = $mParam['advert_id'];

    return TABLE\Pos($mUserId, 'advert_goods', $scope, $mParam);
}


/**
 * Function CmdLinkAdd
 **/
function CmdLinkAdd($mUserId, $mParam)
{
    return TABLE\AddBlock($mUserId, 'advert_link', 'advert_id', $mParam['advert_id'], $mParam);
}


/**
 * Function CmdLinkUpdate
 **/
function CmdLinkUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'advert_link', $mParam['id'], $mParam);
}


/**
 * Function CmdLinkRemove
 **/
function CmdLinkRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'advert_link', $mParam['id'], $mParam);
}


/**
 * Function CmdLinkPos
 **/
function CmdLinkPos($mUserId, $mParam)
{
    // The places of one block
    $scope['advert_id'] = $mParam['advert_id'];

    return TABLE\Pos($mUserId, 'advert_link', $scope, $mParam);
}


/**
 * Function CmdKeyAdd
 **/
function CmdKeyAdd($mUserId, $mParam)
{
    return TABLE\KeySetAdd($mUserId, 'advert', $mParam['advert_id'], $mParam);
}


/**
 * Function CmdKeyUpdate
 **/
function CmdKeyUpdate($mUserId, $mParam)
{
    return TABLE\KeySetUpdate($mUserId, 'advert', $mParam['id'], $mParam);
}


/**
 * Function CmdKeyRemove
 **/
function CmdKeyRemove($mUserId, $mParam)
{
    return TABLE\KeySetRemove($mUserId, 'advert', $mParam['id']);
}

?>
