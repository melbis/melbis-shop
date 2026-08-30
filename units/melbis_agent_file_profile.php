<?php
/***************************************************************************************************
 * @version 6.5.1.417 @ 2026-08-30
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * ProfileXml - The recipe into its body
 * MustSet    - The call over the recipe
 * Between    - Holds a number in range
 * System     - Refuses a profile of program
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_FILE_PROFILE;

// Libraries
use MELBIS_INC_AGENT_FILE as FILE;
use MELBIS_INC_AGENT_TABLE as TABLE;


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $rows = FILE\ProfileAll();

    $profiles = [];
    foreach ( $rows as $row )
    {
        $profiles[] = FILE\ProfileShow($row);
    }

    return [
        'result'  => true,
        'message' => 'The picture profiles of the store',
        'tables'  => [
            'profile' => $profiles
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $name = trim((string)$mParam['name']);
    if ( $name == '' )
    {
        return [
            'result'  => false,
            'message' => 'A profile takes a name'
            ];
    }

    $was = FILE\ProfileOne($name);
    if ( isset($was['id']) )
    {
        return [
            'result'  => false,
            'message' => 'The profile ['.$name.'] is already there'
            ];
    }

    // The door filled the rest
    $ready = MustSet($mParam, []);
    if ( !$ready['result'] ) return $ready;

    $fields = [
        'key_code'  => 'FILES_PROFILE',
        'key_name'  => $name,
        'value_txt' => ProfileXml($ready['set'])
        ];

    $said = TABLE\Add($mUserId, 'key_value', $fields);
    if ( !$said['result'] ) return $said;

    $now = FILE\ProfileOne($name);

    return [
        'result'  => true,
        'id'      => $said['id'],
        'message' => 'The profile is in the registry',
        'tables'  => [
            'profile' => [FILE\ProfileShow($now)]
            ]
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $name = trim((string)$mParam['name']);
    $was = FILE\ProfileOne($name);
    if ( !isset($was['id']) )
    {
        return [
            'result'  => false,
            'message' => 'No profile ['.$name.'] in the registry'
            ];
    }

    $current = FILE\ProfileShow($was, true);
    if ( isset($current['broken']) )
    {
        return [
            'result'  => false,
            'message' => 'The recipe of ['.$name.'] is unreadable'
            ];
    }

    $fields = [];
    $said = [];

    // A name weighed against taken
    if ( isset($mParam['rename']) )
    {
        $rename = trim((string)$mParam['rename']);
        if ( $rename == '' )
        {
            return [
                'result'  => false,
                'message' => 'The new name came empty'
                ];
        }
        if ( $was['sys_key'] > 0 ) return System($was, 'renamed');

        $taken = FILE\ProfileOne($rename);
        if ( isset($taken['id']) && $taken['id'] != $was['id'] )
        {
            return [
                'result'  => false,
                'message' => 'The name ['.$name.'] is taken'
                ];
        }

        $fields['key_name'] = $rename;
        $said[] = 'rename';
    }

    $ready = MustSet($mParam, $current);
    if ( !$ready['result'] ) return $ready;

    $said = array_merge($said, $ready['said']);
    if ( count($said) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    if ( count($ready['said']) > 0 )
    {
        $fields['value_txt'] = ProfileXml($ready['set']);
    }

    $done = TABLE\Update($mUserId, 'key_value', [$was['id']], $fields);
    if ( !$done['result'] ) return $done;

    $now = FILE\ProfileOne($fields['key_name'] ?? $name);
    $changed = implode(', ', $said);

    return [
        'result'  => true,
        'message' => 'The profile is changed',
        'tables'  => [
            'profile' => [FILE\ProfileShow($now)]
            ]
        ];
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $name = trim((string)$mParam['name']);
    $was = FILE\ProfileOne($name);
    if ( !isset($was['id']) )
    {
        return [
            'result'  => false,
            'message' => 'No profile ['.$name.'] in the registry'
            ];
    }

    if ( $was['sys_key'] > 0 ) return System($was, 'removed');

    return TABLE\Remove($mUserId, 'key_value', [$was['id']], $mParam);
}


/**
 * Function ProfileXml
 **/
function ProfileXml($mSet)
{
    // The XML the editor writes
    $stamp = 'Melbis Shop v'.MELBIS_SCRIPT_VERSION.'.'.MELBIS_SCRIPT_BUILD;
    $word = function($mText) { return htmlspecialchars((string)$mText, ENT_QUOTES); };
    $flag = function($mValue) { return ( $mValue ) ? 'True' : 'False'; };

    return '<MELBISSHOP ShopVersion="'.$word($stamp).'">'.
           '<JPEG FileType="'.( ( $mSet['type'] == 'png' ) ? 1 : 0 ).'"'.
                ' Compress="'.$mSet['quality'].'"'.
                ' Width="'.$mSet['width'].'"'.
                ' Hight="'.$mSet['height'].'"'.
                ' Smart="'.$flag($mSet['smart']).'"/>'.
           '<FILE KindKey="'.$word($mSet['group']).'"/>'.
           '<MASK File="'.$word(( $mSet['mask_file'] == '' ) ? FILE\MASK_NONE : $mSet['mask_file']).'"'.
                ' Pos="'.array_search($mSet['mask_pos'], FILE\MASK_POS).'"'.
                ' Alpha="'.$mSet['mask_alpha'].'"/>'.
           '<CANVAS Range="'.$mSet['range'].'"'.
                ' Border="'.$mSet['border'].'"'.
                ' Color="'.FILE\ColorWord($mSet['background'], true).'"/>'.
           '<ROTATE Rotate="'.$mSet['rotate'].'"'.
                ' Mirror="'.$flag($mSet['mirror']).'"/>'.
           '<EFFECTS Red="'.$mSet['red'].'"'.
                ' Green="'.$mSet['green'].'"'.
                ' Blue="'.$mSet['blue'].'"'.
                ' Intensive="'.$mSet['intensive'].'"'.
                ' Contrast="'.$mSet['contrast'].'"'.
                ' Sharpen="'.$mSet['sharpen'].'"/>'.
           '</MELBISSHOP>';
}


/**
 * Function MustSet
 **/
function MustSet($mParam, $mSet)
{
    // The sense of the recipe
    $said = [];

    if ( isset($mParam['type']) )
    {
        $type = strtolower(trim((string)$mParam['type']));
        if ( $type != 'jpeg' && $type != 'png' )
        {
            return [
                'result'  => false,
                'message' => 'The type takes jpeg or png'
                ];
        }

        $mSet['type'] = $type;
        $said[] = 'type';
    }

    // Every number with its range
    $ranges = [
        'quality'    => [4, 100],
        'width'      => [1, 10000],
        'height'     => [1, 10000],
        'range'      => [0, 255],
        'border'     => [0, 1000],
        'rotate'     => [-180, 180],
        'mask_alpha' => [0, 255],
        'red'        => [-255, 255],
        'green'      => [-255, 255],
        'blue'       => [-255, 255],
        'intensive'  => [-255, 255],
        'contrast'   => [-50, 50],
        'sharpen'    => [0, 1000]
        ];
    foreach ( $ranges as $word => $pair )
    {
        if ( !isset($mParam[$word]) ) continue;

        $weighed = Between($word, $mParam[$word], $pair[0], $pair[1]);
        if ( $weighed !== true ) return $weighed;

        $mSet[$word] = (int)$mParam[$word];
        $said[] = $word;
    }

    foreach ( ['smart', 'mirror'] as $word )
    {
        if ( !isset($mParam[$word]) ) continue;

        $mSet[$word] = ( $mParam[$word] ) ? true : false;
        $said[] = $word;
    }

    if ( isset($mParam['group']) )
    {
        $mSet['group'] = trim((string)$mParam['group']);
        $said[] = 'group';
    }

    // A mask word into path
    if ( isset($mParam['mask']) )
    {
        $word = trim((string)$mParam['mask']);
        if ( $word == '' || strtolower($word) == 'none' )
        {
            $mSet['mask_file'] = '';
        }
        else
        {
            $path = '';
            foreach ( FILE\MaskMap() as $mask )
            {
                if ( $mask['name'] == $word ) $path = $mask['path'];
            }

            if ( $path == '' )
            {
                return [
                    'result'  => false,
                    'message' => 'No mask ['.$word.'] with a picture'
                    ];
            }

            $mSet['mask_file'] = $path;
        }

        $said[] = 'mask';
    }

    if ( isset($mParam['mask_pos']) )
    {
        $pos = strtolower(trim((string)$mParam['mask_pos']));
        if ( !in_array($pos, FILE\MASK_POS) )
        {
            $known = implode(', ', FILE\MASK_POS);
            return [
                'result'  => false,
                'message' => 'The mask_pos takes one of: '.$known
                ];
        }

        $mSet['mask_pos'] = $pos;
        $said[] = 'mask_pos';
    }

    if ( isset($mParam['background']) )
    {
        $color = strtoupper(trim((string)$mParam['background']));
        if ( !preg_match('/^#[0-9A-F]{6}$/', $color) )
        {
            return [
                'result'  => false,
                'message' => 'The background goes as #RRGGBB, like #FFFFFF'
                ];
        }

        $mSet['background'] = $color;
        $said[] = 'background';
    }

    // An empty mask_file takes off
    $mSet['mask_file'] = $mSet['mask_file'] ?? '';

    return [
        'result' => true,
        'set'    => $mSet,
        'said'   => $said
        ];
}


/**
 * Function Between
 **/
function Between($mWord, $mValue, $mFrom, $mTo)
{
    // Which numbers the recipe allows
    $value = (int)$mValue;
    if ( $value >= $mFrom && $value <= $mTo ) return true;

    return [
        'result'  => false,
        'message' => 'The '.$mWord.' runs from '.$mFrom.' to '.$mTo
        ];
}


/**
 * Function System
 **/
function System($mWas, $mWord)
{
    return [
        'result'  => false,
        'message' => 'The profile ['.$mWas['key_name'].'] is the shop\'s own'
        ];
}

?>
