<?php
/***************************************************************************************************
 * @version {MELBIS_VERSION}
 * @copyright {MELBIS_YEAR} Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * MustSet - The call over the recipe
 * Between - Holds a number in range
 * System  - Refuses a profile of program
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
        'value_txt' => FILE\ProfileXml($ready['set'])
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
        $fields['value_txt'] = FILE\ProfileXml($ready['set']);
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
 * Function MustSet
 **/
function MustSet($mParam, $mSet)
{
    // The sense of the recipe
    $said = [];

    if ( isset($mParam['type']) )
    {
        $type = strtolower(trim((string)$mParam['type']));
        if ( !in_array($type, FILE\TYPE_WORD) )
        {
            $known = implode(', ', FILE\TYPE_WORD);
            return [
                'result'  => false,
                'message' => 'The type takes one of: '.$known
                ];
        }

        $mSet['type'] = $type;
        $said[] = 'type';
    }

    // A size, or the megapixels of a picture of its own shape - one kind at a time
    $sized = ( isset($mParam['width']) || isset($mParam['height']) );
    if ( $sized && isset($mParam['resolution']) )
    {
        return [
            'result'  => false,
            'message' => 'Width and height, or resolution - a profile holds one kind of size'
            ];
    }

    if ( isset($mParam['resolution']) )
    {
        $weighed = Between('resolution', $mParam['resolution'], 0.01, 50);
        if ( $weighed !== true ) return $weighed;

        $mSet['resolution'] = round((float)$mParam['resolution'], 2);
        $mSet['width'] = null;
        $mSet['height'] = null;
        $said[] = 'resolution';
    }

    if ( $sized ) $mSet['resolution'] = null;

    // Every number with its range
    $ranges = [
        'quality'      => [4, 100],
        'width'        => [10, 50000],
        'height'       => [10, 50000],
        'range'        => [0, 255],
        'range_border' => [0, 100],
        'border'       => [0, 1000],
        'rotate'       => [-180, 180],
        'mask_alpha'   => [0, 255],
        'red'          => [-255, 255],
        'green'        => [-255, 255],
        'blue'         => [-255, 255],
        'intensive'    => [-255, 255],
        'contrast'     => [-50, 50],
        'sharpen'      => [0, 1000]
        ];
    foreach ( $ranges as $word => $pair )
    {
        if ( !isset($mParam[$word]) ) continue;

        $weighed = Between($word, $mParam[$word], $pair[0], $pair[1]);
        if ( $weighed !== true ) return $weighed;

        $mSet[$word] = (int)$mParam[$word];
        $said[] = $word;
    }

    foreach ( ['smart', 'size_base', 'size_optim', 'group_base', 'mirror', 'canvas_alpha'] as $word )
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

    // Without a size there is no profile
    $sides = ( isset($mSet['width']) && isset($mSet['height']) );
    if ( !$sides && !isset($mSet['resolution']) )
    {
        return [
            'result'  => false,
            'message' => 'A profile takes a size: width and height, or resolution for pictures of their own shape'
            ];
    }

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
    $value = (float)$mValue;
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
