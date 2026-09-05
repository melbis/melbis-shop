<?php
/***************************************************************************************************
 * @version 6.5.1.426 @ 2026-09-05
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Allowed  - Weighs the sections
 * Defaults - The defaults of one stream
 * Code     - Builds a code
 * ValueMap - The values of one characteristic
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_IMPORT_GOODS;

// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;
use MELBIS_INC_AGENT_STORE as STORE;


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $defaults = Defaults($mParam, 'store');
    $info_def = Defaults($mParam, 'info');
    $param_def = Defaults($mParam, 'param');

    // Weighs every section at once
    $topics = [];
    foreach ( $mParam['goods'] as $row )
    {
        $named = $row['topic_id'] ?? $defaults['topic_id'] ?? [];
        if ( !is_array($named) ) $named = [$named];

        foreach ( $named as $one )
        {
            $topics[(int)$one] = (int)$one;
        }
    }

    if ( count($topics) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'No section named for the goods'
            ];
    }

    $gate = Allowed($mUserId, $topics);
    if ( $gate !== true ) return $gate;

    if ( $mParam['value_add'] )
    {
    }

    $tables = ['{DBNICK}_store', '{DBNICK}_topic_store', '{DBNICK}_store_info',
               '{DBNICK}_store_param', '{DBNICK}_info_value'];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    // The map ref to id
    $ids = MELBIS()->SqlGenIdBlock('store', count($mParam['goods']));
    $made = [];
    $rows = [];
    $hang = [];
    $write = [];
    $num = 0;
    foreach ( $mParam['goods'] as $one )
    {
        // Every field is a column
        $fields = array_merge($defaults, $one);
        unset($fields['ref'], $fields['topic_id']);

        $row = STORE\DefaultFill($fields);
        $row['id'] = $ids[$num];
        $num++;
        if ( !isset($row['code_shop']) ) $row['code_shop'] = Code($mParam['code_mask'] ?? '',
                                                                  $row['id'], $num);

        // One statement per column set
        $shape = implode(',', array_keys($row));
        $write[$shape][] = $row;

        // One section hung at once
        $named = $one['topic_id'] ?? $defaults['topic_id'] ?? [];
        if ( !is_array($named) ) $named = [$named];

        foreach ( $named as $topic_id )
        {
            $hang[(int)$topic_id][] = [
                'id'   => $row['id'],
                'name' => $row['name'] ?? ''
                ];
        }

        $ref = (string)( $one['ref'] ?? $num );
        $made[$ref] = $row['id'];

        $rows[] = [
            'ref'       => $ref,
            'id'        => $row['id'],
            'code_shop' => $row['code_shop'],
            'name'      => $row['name'] ?? ''
            ];
    }

    foreach ( $write as $pack )
    {
        MELBIS()->SqlInsertBlock(__LINE__, '{DBNICK}_store', $pack);
    }

    $hung = 0;
    foreach ( $hang as $topic_id => $link )
    {
        $list = array_column($link, 'id');
        STORE\LinkTopic($topic_id, $list);
        $hung += count($list);
    }

    // A word against its values
    $maps = [];
    $born = 0;
    $info_how = 0;
    $write = [];
    $lost = [];
    foreach ( ( $mParam['info'] ?? [] ) as $said )
    {
        $one = array_merge($info_def, $said);
        $ref = (string)( $one['ref'] ?? '' );
        if ( !isset($made[$ref]) )
        {
            $lost[$ref] = $ref;
            continue;
        }

        $info_id = (int)( $one['info_id'] ?? 0 );

        // The address is no column
        $fields = $one;
        unset($fields['ref'], $fields['info_id'], $fields['value']);

        // A word takes the id
        if ( isset($one['value']) )
        {
            $word = trim((string)$one['value']);
            if ( !isset($maps[$info_id]) ) $maps[$info_id] = ValueMap($info_id);

            if ( !isset($maps[$info_id][$word]) )
            {
                if ( !$mParam['value_add'] )
                {
                    $lost['value: '.$word] = 'value: '.$word;
                    continue;
                }

                $value = [
                    'id'      => MELBIS()->SqlGenId('info_value'),
                    'info_id' => $info_id,
                    'name'    => $word,
                    'pos'     => count($maps[$info_id]) + 1
                    ];
                MELBIS()->SqlInsert(__LINE__, '{DBNICK}_info_value', $value);

                $maps[$info_id][$word] = $value['id'];
                $born++;
            }

            $fields['value_id'] = $maps[$info_id][$word];
        }

        $row = $fields;
        $row['store_id'] = $made[$ref];
        $row['info_id'] = $info_id;

        // Three columns, three sets apart
        $shape = implode(',', array_keys($row));
        $write[$shape][] = $row;
        $info_how++;
    }

    foreach ( $write as $pack )
    {
        MELBIS()->SqlInsertBlock(__LINE__, '{DBNICK}_store_info', $pack);
    }

    // The parameters go by id
    $param_how = 0;
    $write = [];
    foreach ( ( $mParam['param'] ?? [] ) as $said )
    {
        $one = array_merge($param_def, $said);
        $ref = (string)( $one['ref'] ?? '' );
        if ( !isset($made[$ref]) )
        {
            $lost[$ref] = $ref;
            continue;
        }

        // Every field is a column
        $row = $one;
        unset($row['ref'], $row['param_id']);

        $row['id'] = MELBIS()->SqlGenId('store_param');
        $row['store_id'] = $made[$ref];
        $row['param_id'] = (int)( $one['param_id'] ?? 0 );

        $shape = implode(',', array_keys($row));
        $write[$shape][] = $row;
        $param_how++;
    }

    foreach ( $write as $pack )
    {
        MELBIS()->SqlInsertBlock(__LINE__, '{DBNICK}_store_param', $pack);
    }

    SYS\TablesUnlock($tables, $mUserId);

    $message = count($rows).' goods born, '.$hung.' hung, '.$info_how.' info, '.$param_how.' param';
    if ( $born > 0 ) $message .= '; '.$born.' new value(s) of the characteristics were added';
    if ( count($lost) > 0 )
    {
        $said = implode(', ', $lost);
        $message .= '. Left out, nothing of the call points at them: '.$said;
    }

    return [
        'result'  => true,
        'message' => $message,
        'tables'  => [
            'goods' => $rows
            ]
        ];
}


/**
 * Function Allowed
 **/
function Allowed($mUserId, $mTopics)
{
    // Weighed by both rights
    $list = implode(',', $mTopics);

    foreach ( ['descr', 'price'] as $place )
    {
        $allow = SYS\RightTable('topic', $mUserId, $place);

        $command = "SELECT id
                      FROM $allow
                     WHERE id IN ( $list )
                   ";
        $rows = MELBIS()->SqlSelect(__LINE__, $command);
        $lost = array_diff($mTopics, array_column($rows, 'id'));

        if ( count($lost) > 0 )
        {
            $said = implode(', ', $lost);

            return [
                'result'  => false,
                'message' => 'The sections ['.$said.'] are not yours'
                ];
        }
    }

    return true;
}


/**
 * Function Defaults
 **/
function Defaults($mParam, $mStream)
{
    // The stream before the column
    $defaults = [];
    foreach ( $mParam as $name => $value )
    {
        $said = explode('.', $name, 2);
        if ( count($said) < 2 || $said[0] != $mStream ) continue;

        $defaults[$said[1]] = $value;
    }

    return $defaults;
}


/**
 * Function Code
 **/
function Code($mMask, $mId, $mNum)
{
    // The marks of the mask
    if ( $mMask == '' ) return '';

    $day = substr(MELBIS()->DateTime(), 0, 10);

    $code = str_replace('{id}', $mId, $mMask);
    $code = str_replace('{date}', $day, $code);

    // The width the mask asks
    $code = preg_replace_callback('/\{num(:(\d+))?\}/',
                                  function($mFound) use ($mNum)
                                  {
                                      $wide = (int)( $mFound[2] ?? 0 );
                                      return str_pad($mNum, $wide, '0', STR_PAD_LEFT);
                                  },
                                  $code);

    return $code;
}


/**
 * Function ValueMap
 **/
function ValueMap($mInfoId)
{
    // The values, word to id
    $command = "SELECT id, name
                  FROM {DBNICK}_info_value
                 WHERE info_id = :INFO_ID
               ";
    $param_value = [
        'info_id' => $mInfoId
        ];
    $rows = MELBIS()->SqlSelect(__LINE__, $command, $param_value);

    $map = [];
    foreach ( $rows as $row )
    {
        $map[trim($row['name'])] = $row['id'];
    }

    return $map;
}


?>
