<?php
/***************************************************************************************************
 * @version 6.5.0.401 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList        - Reads the goods of the given ids or sections, with their sections and parameters
 * CmdAdd         - Adds a goods and hangs it in a section at once
 * CmdUpdate      - Changes the given columns of goods, by id
 * CmdUnlink      - Takes goods out of a section and names those left hanging nowhere
 * CmdParamAdd    - Sets a parameter on goods, one row of store_param on each
 * CmdParamUpdate - Changes the given columns of store_param rows, by id
 * CmdParamRemove - Deletes store_param rows by id
 *
 * StoreAllowed   - Of the goods asked for, the ones this person may price
 * ParamAllowed   - Of the rows asked for, the ones on such goods
 *
 * The commercial half of a goods; the right is for_price, given out on the sections it hangs in
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_STORE_PRICE;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;
use MELBIS_INC_AGENT_STORE as STORE;

// The columns every answer here carries, the set the Prices window of the program loads
const FIELDS_READ = "s.id, s.provider_id, s.brand_id, s.code_shop, s.code_prov, s.code_made, s.meas,
                s.name, s.no_visible, s.status_key, s.kind_key, s.state_key, s.rating,
                s.disc_group_id, s.how, s.pprice, s.pprice_curr_id, s.rprice, s.rprice_curr_id,
                s.price, s.price_curr_id, s.clann, s.clann_title, s.clann_root, s.relate_id,
                s.price2, s.price2_curr_id, s.price3, s.price3_curr_id, s.relate_type,
                s.relate_proc, s.proc_price2, s.proc_price3, s.min_order, s.step_order, s.in_xml,
                s.create_time, s.update_time, s.exist_time, s.award_cnt, s.award_avg";

// The same set, as the columns a call may write
const FIELDS_WRITE = "provider_id, brand_id, code_shop, code_prov, code_made, name, meas,
                      no_visible, status_key, kind_key, state_key, relate_id, rating,
                      disc_group_id, how, pprice, pprice_curr_id, rprice, rprice_curr_id, price,
                      price_curr_id, price2, price2_curr_id, price3, price3_curr_id, relate_type,
                      relate_proc, proc_price2, proc_price3, min_order, step_order, in_xml,
                      create_time, update_time, exist_time, award_cnt, award_avg";

// The columns a call may write into a parameter standing on a goods
const FIELDS_PARAM = "value_id, value_name, value_set_sum, value_curr_id";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $ids = StoreAllowed($mUserId, $mParam['store_id'] ?? [], $mParam['topic_id'] ?? [],
                 $mParam['topic_sub'] ?? false);
    if ( count($ids) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing of yours to price was named or found there'
            ];
    }

    $fields = FIELDS_READ;
    $list = implode(',', $ids);

    $command = "SELECT $fields
                  FROM {DBNICK}_store s
                 WHERE s.id IN ( $list )
              ORDER BY s.id
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_topic_store
                 WHERE store_id IN ( $list )
              ORDER BY store_id, pos
               ";
    $topics = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_store_param
                 WHERE store_id IN ( $list )
              ORDER BY store_id, param_id, pos
               ";
    $params = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($rows).' goods',
        'tables'  => [
            'store'       => $rows,
            'topic_store' => $topics,
            'store_param' => $params
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $topic_id = $mParam['topic_id'];
    if ( !UTIL\RightOne('topic', $mUserId, 'price', $topic_id) )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$topic_id.'] is not yours to price'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_WRITE);
    $now = MELBIS()->DateTime('now');

    $tables = ['{DBNICK}_store', '{DBNICK}_topic_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $row = $fields;
    $row['id'] = MELBIS()->SqlGenId('store');
    if ( !isset($row['create_time']) ) $row['create_time'] = $now;
    if ( !isset($row['update_time']) ) $row['update_time'] = $now;
    if ( !isset($row['exist_time']) ) $row['exist_time'] = $now;

    // Gives a fresh goods the plain view: templ_key is no column of this window
    if ( !isset($row['templ_key']) ) $row['templ_key'] = 'kDefault';
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_store', $row);

    // Hangs it in the section at once: a goods in no section is seen by the Recovery tool alone
    $hang = [
        [
            'id'   => $row['id'],
            'name' => $row['name'] ?? ''
            ]
        ];
    STORE\Link($topic_id, $hang);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'id'      => $row['id'],
        'message' => 'The goods ['.$row['id'].'] is in the section ['.$topic_id.']'
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $ids = StoreAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The goods ['.$list.'] hang in no section of yours: the price right is '.
                         'given out on the sections a goods hangs in, in the program'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_WRITE);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $now = MELBIS()->DateTime('now');
    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        if ( !isset($row['update_time']) ) $row['update_time'] = $now;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_store', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' goods changed: '.$changed
        ];
}


/**
 * Function CmdUnlink
 **/
function CmdUnlink($mUserId, $mParam)
{
    $topic_id = $mParam['topic_id'];
    if ( !UTIL\RightOne('topic', $mUserId, 'price', $topic_id) )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$topic_id.'] is not yours to price'
            ];
    }

    $ids = StoreAllowed($mUserId, $mParam['store_id']);
    if ( count($ids) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'None of those goods are yours to price'
            ];
    }

    $tables = ['{DBNICK}_topic_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $where = [
            'topic_id' => $topic_id,
            'store_id' => $id
            ];
        MELBIS()->SqlDelete(__LINE__, '{DBNICK}_topic_store', $where);
    }

    UTIL\TablesUnlock($tables);

    // Names the goods left in no section: only the Recovery tool reaches them now
    $list = implode(',', $ids);
    $command = "SELECT s.id
                  FROM {DBNICK}_store s
             LEFT JOIN {DBNICK}_topic_store ts
                    ON ts.store_id = s.id
                 WHERE s.id IN ( $list )
                   AND ts.id IS NULL
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    $message = count($ids).' goods taken out of the section ['.$topic_id.']';
    if ( count($rows) > 0 )
    {
        $gone = array_column($rows, 'id');
        $said = implode(', ', $gone);
        $message .= '. The goods ['.$said.'] hang nowhere now and only the Recovery tool sees them';
    }

    return [
        'result'  => true,
        'message' => $message
        ];
}


/**
 * Function CmdParamAdd
 **/
function CmdParamAdd($mUserId, $mParam)
{
    $ids = StoreAllowed($mUserId, $mParam['store_id']);
    $lost = array_diff($mParam['store_id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The goods ['.$list.'] are not yours to price'
            ];
    }

    $param_id = $mParam['param_id'];
    $fields = UTIL\Only($mParam, FIELDS_PARAM);

    $tables = ['{DBNICK}_store_param'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $command = "SELECT MAX(pos)
                      FROM {DBNICK}_store_param
                     WHERE store_id = :STORE_ID
                       AND param_id = :PARAM_ID
                   ";
        $param_last = [
            'store_id' => $id,
            'param_id' => $param_id
            ];
        $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_last);

        $row = $fields;
        $row['id'] = MELBIS()->SqlGenId('store_param');
        $row['store_id'] = $id;
        $row['param_id'] = $param_id;
        $row['pos'] = $last + 1;
        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_store_param', $row);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => 'The parameter ['.$param_id.'] stands on '.count($ids).' goods'
        ];
}


/**
 * Function CmdParamUpdate
 **/
function CmdParamUpdate($mUserId, $mParam)
{
    $ids = ParamAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The rows ['.$list.'] stand on goods that are not yours to price, or are '.
                         'not there at all - CmdList answers the rows of a goods with their ids'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_PARAM);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_store_param'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_store_param', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' rows changed: '.$changed
        ];
}


/**
 * Function CmdParamRemove
 **/
function CmdParamRemove($mUserId, $mParam)
{
    $ids = ParamAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The rows ['.$list.'] stand on goods that are not yours to price, or are '.
                         'not there at all'
            ];
    }

    $tables = ['{DBNICK}_store_param'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $where = [
            'id' => $id
            ];
        MELBIS()->SqlDelete(__LINE__, '{DBNICK}_store_param', $where);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' rows gone'
        ];
}


/**
 * Function StoreAllowed
 **/
function StoreAllowed($mUserId, $mIds, $mTopics = [], $mSub = false)
{
    // Reads the goods this person may price, out of the ids and the sections of the call
    $allow = UTIL\RightTable('topic', $mUserId, 'price');

    $where = [];
    $join = '';
    if ( count($mIds) > 0 )
    {
        $list = implode(',', $mIds);
        $where[] = "s.id IN ( $list )";
    }
    if ( count($mTopics) > 0 )
    {
        if ( $mSub )
        {
            $branch = UTIL\TreeBranchTable('topic', $mTopics);
            $join = "JOIN $branch b ON b.id = ts.topic_id";
        }
        else
        {
            $list = implode(',', $mTopics);
            $where[] = "ts.topic_id IN ( $list )";
        }
    }

    if ( count($where) == 0 && $join == '' ) return [];
    if ( count($where) == 0 ) $where[] = '1';
    $filter = implode(' AND ', $where);

    $command = "SELECT s.id
                  FROM {DBNICK}_store s
                  JOIN {DBNICK}_topic_store ts
                    ON ts.store_id = s.id
                  JOIN $allow at
                    ON at.id = ts.topic_id
                       $join
                 WHERE $filter
              GROUP BY s.id
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return array_column($rows, 'id');
}


/**
 * Function ParamAllowed
 **/
function ParamAllowed($mUserId, $mIds)
{
    // Reads the store_param rows standing on goods whose prices this person may set
    $allow = UTIL\RightTable('topic', $mUserId, 'price');
    $list = implode(',', $mIds);

    $command = "SELECT sp.id
                  FROM {DBNICK}_store_param sp
                  JOIN {DBNICK}_topic_store ts
                    ON ts.store_id = sp.store_id
                  JOIN $allow at
                    ON at.id = ts.topic_id
                 WHERE sp.id IN ( $list )
              GROUP BY sp.id
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return array_column($rows, 'id');
}



?>
