<?php
/***************************************************************************************************
 * @version 6.5.1.419 @ 2026-09-01
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_USER_KEY;


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $self_set = MELBIS()->SysSelfKeyValues();

    // The right stands on branch
    $allow = MELBIS()->SysSelfKeyRight($mUserId);

    $settings = [];
    foreach ( $self_set as $self )
    {
        // An unopened branch shows nothing
        if ( $allow !== true && !isset($allow[$self['code']]) ) continue;

        $settings[] = $self;
    }

    return [
        'result'  => true,
        'message' => 'The settings yours to read',
        'tables'  => [
            'self_key_value' => $settings
            ]
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $code = $mParam['code'];
    $said = MELBIS()->SysSelfKeyModify($mUserId, $code, $mParam['value_txt']);

    // One word, four pieces
    if ( $said == 'ABSENT' )
    {
        return [
            'result'  => false,
            'message' => 'No setting ['.$code.'] in the registry'
            ];
    }

    if ( $said == 'DENIED' )
    {
        return [
            'result'  => false,
            'message' => 'The setting ['.$code.'] is not yours'
            ];
    }

    if ( $said == 'BUSY' )
    {
        return [
            'result'  => false,
            'message' => 'The settings are busy - try again'
            ];
    }

    return [
        'result'  => true,
        'message' => 'The setting keeps its new value'
        ];
}


?>
