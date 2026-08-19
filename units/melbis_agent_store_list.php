<?php
/***************************************************************************************************
 * @version 6.5.0.400 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdTopicList  - Reads the goods of the given sections, with or without their branches
 * CmdSearchList - Reads the goods matching the fields of the call
 *
 * Finds goods and nothing more; what stands on them is read by Descriptions and by Prices
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_STORE_LIST;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;

// The columns every answer here carries, the set the Select window of the program loads
const FIELDS_READ = "s.id, s.provider_id, s.brand_id, s.code_shop, s.code_prov, s.code_made,
                s.name, s.no_visible, s.status_key, s.kind_key, s.state_key, s.clann,
                s.clann_title, s.clann_root, s.relate_id, s.price, s.price_curr_id,
                s.in_xml, s.create_time, s.update_time, s.exist_time";


/**
 * Function CmdTopicList
 **/
function CmdTopicList($mUserId, $mParam)
{
    $allow = UTIL\RightTable('topic', $mUserId);
    $fields = FIELDS_READ;

    if ( $mParam['topic_sub'] )
    {
        // The branch of every section walked by the base, as a table the read joins
        $branch = UTIL\TreeBranchTable('topic', $mParam['topic_id']);

        $command = "SELECT $fields
                      FROM {DBNICK}_store s
                      JOIN {DBNICK}_topic_store ts
                        ON ts.store_id = s.id
                      JOIN $allow at
                        ON at.id = ts.topic_id
                      JOIN $branch b
                        ON b.id = ts.topic_id
                  GROUP BY s.id
                  ORDER BY s.id
                   ";
    }
    else
    {
        // The sections themselves, nothing under them
        $list = implode(',', $mParam['topic_id']);

        $command = "SELECT $fields
                      FROM {DBNICK}_store s
                      JOIN {DBNICK}_topic_store ts
                        ON ts.store_id = s.id
                      JOIN $allow at
                        ON at.id = ts.topic_id
                     WHERE ts.topic_id IN ( $list )
                  GROUP BY s.id
                  ORDER BY s.id
                   ";
    }

    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($rows).' goods',
        'tables'  => [
            'store' => $rows
            ]
        ];
}


/**
 * Function CmdSearchList
 **/
function CmdSearchList($mUserId, $mParam)
{
    $fields = FIELDS_READ;

    // These columns are asked as a range: two values are from and to, one value is the floor
    $ranges = ['how', 'price', 'pprice', 'rprice',
               'create_time', 'update_time', 'edit_time', 'exist_time'];

    // These columns are asked by a part of the text, and a list of words means any of them
    $likes = ['code_shop', 'code_prov', 'code_made', 'name', 'descr', 'seo_psu',
              'status_key', 'kind_key', 'state_key'];

    $allow = UTIL\RightTable('topic', $mUserId);

    $where = [];
    $param = [];
    $join = '';

    // The sections narrow the search like any other field, with the branch when asked
    if ( isset($mParam['topic_id']) )
    {
        if ( $mParam['topic_sub'] )
        {
            $branch = UTIL\TreeBranchTable('topic', $mParam['topic_id']);
            $join = "JOIN $branch b ON b.id = ts.topic_id";
        }
        else
        {
            $list = implode(',', $mParam['topic_id']);
            $where[] = "ts.topic_id IN ( $list )";
        }
    }

    $said = ['topic_id', 'topic_sub'];
    foreach ( $mParam as $word => $value )
    {
        if ( in_array($word, $said) ) continue;

        if ( in_array($word, $ranges) )
        {
            $where[] = 's.'.$word.' >= :'.strtoupper($word).'_FROM';
            $param[$word.'_from'] = $value[0];

            if ( isset($value[1]) )
            {
                $where[] = 's.'.$word.' <= :'.strtoupper($word).'_TO';
                $param[$word.'_to'] = $value[1];
            }

            continue;
        }

        if ( in_array($word, $likes) )
        {
            $like = [];
            foreach ( $value as $num => $one )
            {
                $key = $word.'_'.$num;
                $like[] = 's.'.$word.' LIKE :'.strtoupper($key);
                $param[$key] = '%'.$one.'%';
            }

            $where[] = '( '.implode(' OR ', $like).' )';
            continue;
        }

        // A flag goes into the query as 1 or 0
        if ( is_bool($value) )
        {
            $where[] = 's.'.$word.' = :'.strtoupper($word);
            $param[$word] = ( $value ) ? 1 : 0;
            continue;
        }

        // What is left are ids, bound one by one and met exactly
        $keys = [];
        foreach ( (array)$value as $num => $one )
        {
            $key = $word.'_'.$num;
            $keys[] = ':'.strtoupper($key);
            $param[$key] = $one;
        }

        $where[] = 's.'.$word.' IN ( '.implode(', ', $keys).' )';
    }

    // An empty call would answer the whole store, so at least one field is demanded
    if ( count($where) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Name at least one field to look by'
            ];
    }

    $filter = implode(' AND ', $where);

    $command = "SELECT $fields
                  FROM {DBNICK}_store s
                  JOIN {DBNICK}_topic_store ts
                    ON ts.store_id = s.id
                  JOIN $allow at
                    ON at.id = ts.topic_id
                       $join
                 WHERE $filter
              GROUP BY s.id
              ORDER BY s.id
                   ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command, $param);

    return [
        'result'  => true,
        'message' => count($rows).' goods',
        'tables'  => [
            'store' => $rows
            ]
        ];
}

?>
