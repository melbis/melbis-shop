<?php
/***************************************************************************************************
 * @version 6.5.0.402 @ 2026-08-20
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 ***************************************************************************************************
 *
 * CmdList    - Reads every profile, its recipe in plain words
 * CmdAdd     - Adds a profile: name and size, the rest by the defaults of the registry
 * CmdUpdate  - Changes the given words of one profile, the rest stay as they were
 * CmdRemove  - Deletes one profile from the registry, by name
 *
 * ProfileXml - Turns the words of a recipe back into the body the program's editor opens
 * MustSet    - Weighs the words of the call and lays them over the set standing now
 * Between    - Holds a number to its range, or refuses it by name
 * System     - Refuses a profile the program itself relies on
 *
 * A profile is a row of FILES_PROFILE; the agent speaks words, the tool writes the XML
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_FILE_PROFILE;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;
use MELBIS_INC_AGENT_FILE as FILE;


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
        'message' => count($profiles).' picture profile(s). A derived picture takes the whole '.
                     'recipe: the frame is cut by hand in the program, the rest is written here',
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
            'message' => 'A profile needs a name - that is the word people and CmdMake call it by'
            ];
    }

    $was = FILE\ProfileOne($name);
    if ( isset($was['id']) )
    {
        return [
            'result'  => false,
            'message' => 'The profile ['.$name.'] is already in the registry - CmdUpdate changes it'
            ];
    }

    // The door filled the words the call left out, so a fresh profile is complete from birth
    $ready = MustSet($mParam, []);
    if ( !$ready['result'] ) return $ready;

    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_key_value
                 WHERE key_code = 'FILES_PROFILE'
               ";
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    $table = '{DBNICK}_key_value';
    $lock = UTIL\TablesLock([$table]);
    if ( !$lock['result'] ) return $lock;

    $fields = [
        'id'        => MELBIS()->SqlGenId('key_value'),
        'key_code'  => 'FILES_PROFILE',
        'key_name'  => $name,
        'value_txt' => ProfileXml($ready['set']),
        'sys_key'   => 0,
        'pos'       => $last + 1
        ];
    MELBIS()->SqlInsert(__LINE__, $table, $fields);

    UTIL\TablesUnlock([$table]);

    $now = FILE\ProfileOne($name);

    return [
        'result'  => true,
        'id'      => $fields['id'],
        'message' => 'The profile ['.$name.'] is in the registry',
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
            'message' => 'No profile ['.$name.'] in the registry - CmdList answers them'
            ];
    }

    $current = FILE\ProfileShow($was, true);
    if ( isset($current['broken']) )
    {
        return [
            'result'  => false,
            'message' => 'The recipe of ['.$name.'] is not readable, so nothing can lay over it - '.
                         'the program\'s editor owns that row'
            ];
    }

    $fields = [];
    $said = [];

    // The name is the identity of a profile, so a new one is weighed against the ones taken
    if ( isset($mParam['rename']) )
    {
        $rename = trim((string)$mParam['rename']);
        if ( $rename == '' )
        {
            return [
                'result'  => false,
                'message' => 'The rename came empty - a profile cannot lose its name'
                ];
        }
        if ( $was['sys_key'] > 0 ) return System($was, 'renamed');

        $taken = FILE\ProfileOne($rename);
        if ( isset($taken['id']) && $taken['id'] != $was['id'] )
        {
            return [
                'result'  => false,
                'message' => 'The name ['.$rename.'] is already a profile of its own'
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
            'message' => 'Nothing to change: name a field of the recipe, or rename'
            ];
    }

    if ( count($ready['said']) > 0 )
    {
        $fields['value_txt'] = ProfileXml($ready['set']);
    }

    $table = '{DBNICK}_key_value';
    $lock = UTIL\TablesLock([$table]);
    if ( !$lock['result'] ) return $lock;

    $fields['id'] = $was['id'];
    MELBIS()->SqlUpdate(__LINE__, $table, $fields, 'id');

    UTIL\TablesUnlock([$table]);

    $now = FILE\ProfileOne($fields['key_name'] ?? $name);
    $changed = implode(', ', $said);

    return [
        'result'  => true,
        'message' => 'The profile ['.$name.'] changed: '.$changed,
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
            'message' => 'No profile ['.$name.'] in the registry - CmdList answers them'
            ];
    }

    if ( $was['sys_key'] > 0 ) return System($was, 'removed');

    $table = '{DBNICK}_key_value';
    $lock = UTIL\TablesLock([$table]);
    if ( !$lock['result'] ) return $lock;

    MELBIS()->SqlDelete(__LINE__, $table, 'id', $was['id']);

    UTIL\TablesUnlock([$table]);

    // Sweeps what hung on the gone row, files among them, by the map of the engine
    $swept = UTIL\DependSweep('key_value');

    $said = [];
    foreach ( $swept as $where => $how )
    {
        if ( $how === 0 ) continue;

        $said[] = $where.': '.$how;
    }
    $told = implode(', ', $said);

    $message = 'The profile ['.$name.'] is out of the registry. The pictures already derived by '.
               'it stay where they hang';
    if ( $told != '' ) $message .= '. Swept with it - '.$told;

    return [
        'result'  => true,
        'message' => $message
        ];
}


/**
 * Function ProfileXml
 **/
function ProfileXml($mSet)
{
    // Builds the XML of a recipe the way the editor of the program writes it, word by word
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
    // The door held the types already, so what is weighed here is the sense of the recipe
    $said = [];

    if ( isset($mParam['type']) )
    {
        $type = strtolower(trim((string)$mParam['type']));
        if ( $type != 'jpeg' && $type != 'png' )
        {
            return [
                'result'  => false,
                'message' => 'The type takes jpeg or png, and ['.$type.'] is neither'
                ];
        }

        $mSet['type'] = $type;
        $said[] = 'type';
    }

    // The numbers of a recipe with the range each of them is held to
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

    // A mask comes as a word, and the path to its picture is looked up here
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
                    'message' => 'No mask ['.$word.'] with a picture in the registry. A mask is a '.
                                 'value of FILES_MASK in the base settings, and its picture hangs '.
                                 'by the Files tool: entity key_value, elem_id the id of that value'
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

    // An empty mask_file takes the mask off, and the other words of it keep their places
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
    // The door said it is a number; here the recipe says which numbers it allows
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
        'message' => 'The profile ['.$mWas['key_name'].'] is one the program itself relies on, '.
                     'and cannot be '.$mWord.'. Its recipe is another matter: the fields may be '.
                     'changed on any profile'
        ];
}

?>
