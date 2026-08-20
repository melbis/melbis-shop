<?php
/***************************************************************************************************
 * @version 6.5.0.402 @ 2026-08-20
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdAdd   - Loads goods with their sections, characteristics and parameters, in one call
 *
 * Allowed  - Weighs every section of the call at once, by both rights
 * Defaults - Reads the fields of one stream that stand for the columns of its rows
 * Code     - Builds a code of a goods by the mask, when the row carries none
 * ValueMap - Reads the values of one characteristic by the word people call them
 *
 * Four streams tied by ref, a key of the caller; the pictures are a call of their own
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_IMPORT_GOODS;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;
use MELBIS_INC_AGENT_STORE as STORE;

// The columns a call may write into a goods; in the call they carry a store_ ahead
const FIELDS_STORE = "provider_id, brand_id, code_shop, code_prov, code_made, meas, name, intro,
                      descr, review, no_visible, status_key, kind_key, state_key, clann,
                      clann_title, clann_descr, clann_root, relate_id, rating, disc_group_id, how,
                      pprice, pprice_curr_id, rprice, rprice_curr_id, price, price_curr_id, price2,
                      price2_curr_id, price3, price3_curr_id, relate_type, relate_proc,
                      proc_price2, proc_price3, min_order, step_order, seo_psu, seo_title, in_xml,
                      templ_key, create_time, update_time, edit_time, exist_time, option_code,
                      award_cnt, award_avg";

// The columns a call may write into a characteristic standing on a goods
const FIELDS_INFO = "value_id, value_dec, value_txt";

// The columns a call may write into a parameter standing on a goods
const FIELDS_PARAM = "value_id, value_name, value_set_sum, value_curr_id, pos";


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $defaults = Defaults($mParam, 'store_');
    $info_def = Defaults($mParam, 'info_');
    $param_def = Defaults($mParam, 'param_');

    // Gathers every section of the call and weighs them once: a load stops before it starts
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
            'message' => 'No section named: a goods hanging nowhere is a goods no tool of the '.
                         'store sees. Name topic_id in the rows, or store_topic_id for all of them'
            ];
    }

    $gate = Allowed($mUserId, $topics);
    if ( $gate !== true ) return $gate;

    if ( $mParam['value_add'] )
    {
        $gate = UTIL\RightOper($mUserId, 'PUT_INFO_VALUE', 'Adding a value of a characteristic');
        if ( $gate !== true ) return $gate;
    }

    $tables = ['{DBNICK}_store', '{DBNICK}_topic_store', '{DBNICK}_store_info',
               '{DBNICK}_store_param', '{DBNICK}_info_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // Writes the goods and keeps the map of every ref to the id it was born with
    $now = MELBIS()->DateTime();
    $ids = MELBIS()->SqlGenIdBlock('store', count($mParam['goods']));
    $made = [];
    $rows = [];
    $hang = [];
    $write = [];
    $num = 0;
    foreach ( $mParam['goods'] as $one )
    {
        $fields = UTIL\Only(array_merge($defaults, $one), FIELDS_STORE);

        $row = $fields;
        $row['id'] = $ids[$num];
        $num++;
        if ( !isset($row['create_time']) ) $row['create_time'] = $now;
        if ( !isset($row['update_time']) ) $row['update_time'] = $now;
        if ( !isset($row['edit_time']) ) $row['edit_time'] = $now;
        if ( !isset($row['exist_time']) ) $row['exist_time'] = $now;
        if ( !isset($row['code_shop']) ) $row['code_shop'] = Code($mParam['code_mask'] ?? '',
                                                                  $row['id'], $num);

        // Rows are gathered by the set of their columns: one statement writes one such set
        $shape = implode(',', array_keys($row));
        $write[$shape][] = $row;

        // Gathers the goods by section, so one section is hung in one act
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
        $done = STORE\Link($topic_id, $link);
        $hung += count($done['hung']);
    }

    // Meets a word against the values of its own info_id, that map read once for the whole call
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
        $fields = UTIL\Only($one, FIELDS_INFO);

        // A value said in words takes the id of that word, and a new word is born when asked
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

        // A value by id, a number and a text write different columns, so they go in apart
        $shape = implode(',', array_keys($row));
        $write[$shape][] = $row;
        $info_how++;
    }

    foreach ( $write as $pack )
    {
        MELBIS()->SqlInsertBlock(__LINE__, '{DBNICK}_store_info', $pack);
    }

    // The parameters go by their ids alone: nothing of them grows out of the goods
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

        $row = UTIL\Only($one, FIELDS_PARAM);
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

    UTIL\TablesUnlock($tables);

    $message = count($rows).' goods born, '.$hung.' hung in sections, '.$info_how.
               ' characteristics, '.$param_how.' parameters';
    if ( $born > 0 ) $message .= '; '.$born.' new value(s) of the characteristics were added';
    if ( count($lost) > 0 )
    {
        $said = implode(', ', $lost);
        $message .= '. Left out, nothing of the call points at them: '.$said;
    }
    $message .= '. The pictures are a call of their own - the Files tool takes them with the ids '.
                'of this answer';

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
    // A load writes both halves of a goods, so a section is weighed by for_frame and for_price
    $list = implode(',', $mTopics);

    foreach ( ['descr', 'price'] as $place )
    {
        $allow = UTIL\RightTable('topic', $mUserId, $place);

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
                'message' => 'The sections ['.$said.'] are not yours to fill: a load writes the '.
                             'description and the price of a goods, so both rights are asked on '.
                             'every section it names'
                ];
        }
    }

    return true;
}


/**
 * Function Defaults
 **/
function Defaults($mParam, $mPrefix)
{
    // Takes the fields carrying the prefix of a stream and strips it: the defaults of its rows
    $defaults = [];
    $wide = strlen($mPrefix);
    foreach ( $mParam as $name => $value )
    {
        if ( substr($name, 0, $wide) != $mPrefix ) continue;

        $defaults[substr($name, $wide)] = $value;
    }

    return $defaults;
}


/**
 * Function Code
 **/
function Code($mMask, $mId, $mNum)
{
    // Puts the id, the day and the number of the row into the marks of the mask
    if ( $mMask == '' ) return '';

    $day = substr(MELBIS()->DateTime(), 0, 10);

    $code = str_replace('{id}', $mId, $mMask);
    $code = str_replace('{date}', $day, $code);

    // The number takes the width the mask asks for: {num:4} is 0001
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
    // Reads the values of one characteristic as a map of the word to its id
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
