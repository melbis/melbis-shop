<?php
/***************************************************************************************************
 * @version 6.5.0.402 @ 2026-08-20
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList       - Reads goods by id or by section, with their sections and characteristics
 * CmdAdd        - Adds a goods and hangs it in a section at once
 * CmdUpdate     - Changes the given columns of goods, by id
 * CmdInfoAdd    - Sets a characteristic on goods, one row of store_info on each
 * CmdInfoUpdate - Changes the given columns of store_info rows, by id
 * CmdInfoRemove - Deletes store_info rows by id
 * CmdSetAdd     - Ties another object of the store to goods, one row of store_set on each
 * CmdSetUpdate  - Changes the given columns of store_set rows, by id
 * CmdSetRemove  - Deletes store_set rows by id
 * CmdClannNew   - Makes a fresh clan over two or more goods
 *
 * StoreAllowed  - Of the goods asked for, the ones whose descriptions this person may write
 * InfoAllowed   - Of the rows asked for, the ones on such goods
 * SetAllowed    - Of the rows asked for, the ones on such goods
 *
 * What a buyer reads about a goods; the right is for_frame, given out on the sections it hangs in
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_STORE_DESCR;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;
use MELBIS_INC_AGENT_STORE as STORE;

// The columns every answer here carries, the set the personal frame of the program loads
const FIELDS_READ = "s.id, s.provider_id, s.brand_id, s.code_shop, s.code_prov, s.code_made,
                s.meas, s.name, s.intro, s.descr, s.review, s.no_visible, s.status_key,
                s.kind_key, s.state_key, s.clann, s.clann_title, s.clann_descr, s.clann_root,
                s.relate_id, s.price, s.price_curr_id, s.seo_psu, s.seo_title, s.templ_key,
                s.create_time, s.update_time, s.exist_time, s.edit_time, s.option_code";

// The same set, as the columns a call may write
const FIELDS_WRITE = "provider_id, brand_id, code_shop, code_prov, code_made, meas, name, intro,
                      descr, review, no_visible, status_key, kind_key, state_key, clann,
                      clann_title, clann_descr, clann_root, relate_id, price, price_curr_id,
                      seo_psu, seo_title, templ_key, create_time, update_time, exist_time,
                      edit_time, option_code";

// The columns a call may write into a characteristic standing on a goods
const FIELDS_INFO = "value_id, value_dec, value_txt";

// The columns a call may write into a tie of two objects
const FIELDS_SET = "obj_key, kind_key, elem_id, params, comment, pos";


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
            'message' => 'Nothing of yours to describe was named or found there'
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
                  FROM {DBNICK}_store_info
                 WHERE store_id IN ( $list )
              ORDER BY store_id, info_id, id
               ";
    $infos = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_store_set
                 WHERE store_id IN ( $list )
              ORDER BY store_id, pos
               ";
    $sets = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($rows).' goods',
        'tables'  => [
            'store'       => $rows,
            'topic_store' => $topics,
            'store_info'  => $infos,
            'store_set'   => $sets
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $topic_id = $mParam['topic_id'];
    $named = StoreAllowed($mUserId, [], [$topic_id]);
    if ( count($named) == 0 && !UTIL\RightOne('topic', $mUserId, 'descr', $topic_id) )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$topic_id.'] is not yours to describe'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_WRITE);
    $now = MELBIS()->DateTime('now');

    $tables = ['{DBNICK}_store', '{DBNICK}_topic_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // Stamps the three days of a goods with now, unless the call carries its own
    $row = $fields;
    $row['id'] = MELBIS()->SqlGenId('store');
    if ( !isset($row['create_time']) ) $row['create_time'] = $now;
    if ( !isset($row['update_time']) ) $row['update_time'] = $now;
    if ( !isset($row['edit_time']) ) $row['edit_time'] = $now;
    if ( !isset($row['exist_time']) ) $row['exist_time'] = $now;
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
            'message' => 'The goods ['.$list.'] hang in no section of yours: the description right '.
                         'is given out on the sections a goods hangs in, in the program'
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
        if ( !isset($row['edit_time']) ) $row['edit_time'] = $now;
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
 * Function CmdInfoAdd
 **/
function CmdInfoAdd($mUserId, $mParam)
{
    $ids = StoreAllowed($mUserId, $mParam['store_id']);
    $lost = array_diff($mParam['store_id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The goods ['.$list.'] are not yours to describe'
            ];
    }

    $info_id = $mParam['info_id'];
    $fields = UTIL\Only($mParam, FIELDS_INFO);

    $tables = ['{DBNICK}_store_info'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['store_id'] = $id;
        $row['info_id'] = $info_id;
        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_store_info', $row);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => 'The characteristic ['.$info_id.'] stands on '.count($ids).' goods'
        ];
}


/**
 * Function CmdInfoUpdate
 **/
function CmdInfoUpdate($mUserId, $mParam)
{
    $ids = InfoAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The rows ['.$list.'] stand on goods that are not yours to describe, or '.
                         'are not there at all - CmdList answers the rows of a goods with their ids'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_INFO);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_store_info'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_store_info', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' rows changed: '.$changed
        ];
}


/**
 * Function CmdInfoRemove
 **/
function CmdInfoRemove($mUserId, $mParam)
{
    $ids = InfoAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The rows ['.$list.'] stand on goods that are not yours to describe, or '.
                         'are not there at all'
            ];
    }

    $tables = ['{DBNICK}_store_info'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $where = [
            'id' => $id
            ];
        MELBIS()->SqlDelete(__LINE__, '{DBNICK}_store_info', $where);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' rows gone'
        ];
}


/**
 * Function CmdSetAdd
 **/
function CmdSetAdd($mUserId, $mParam)
{
    $ids = StoreAllowed($mUserId, $mParam['store_id']);
    $lost = array_diff($mParam['store_id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The goods ['.$list.'] are not yours to describe'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_SET);

    $tables = ['{DBNICK}_store_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $command = "SELECT MAX(pos)
                      FROM {DBNICK}_store_set
                     WHERE store_id = :STORE_ID
                   ";
        $param_last = [
            'store_id' => $id
            ];
        $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_last);

        $row = $fields;
        $row['store_id'] = $id;
        if ( !isset($row['pos']) ) $row['pos'] = $last + 1;
        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_store_set', $row);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => 'Tied to '.count($ids).' goods'
        ];
}


/**
 * Function CmdSetUpdate
 **/
function CmdSetUpdate($mUserId, $mParam)
{
    $ids = SetAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The rows ['.$list.'] stand on goods that are not yours to describe, or '.
                         'are not there at all - CmdList answers the rows of a goods with their ids'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_SET);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_store_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_store_set', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' rows changed: '.$changed
        ];
}


/**
 * Function CmdSetRemove
 **/
function CmdSetRemove($mUserId, $mParam)
{
    $ids = SetAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The rows ['.$list.'] stand on goods that are not yours to describe, or '.
                         'are not there at all'
            ];
    }

    $tables = ['{DBNICK}_store_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $where = [
            'id' => $id
            ];
        MELBIS()->SqlDelete(__LINE__, '{DBNICK}_store_set', $where);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' rows gone'
        ];
}


/**
 * Function CmdClannNew
 **/
function CmdClannNew($mUserId, $mParam)
{
    $ids = StoreAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The goods ['.$list.'] are not yours to describe'
            ];
    }

    // A clan holds the variants of one thing, so anything less than two goods is refused
    if ( count($ids) < 2 )
    {
        return [
            'result'  => false,
            'message' => 'A clan holds the variants of one goods together: name at least two of them'
            ];
    }

    $root = $mParam['root'] ?? 0;
    if ( $root == 0 ) $root = reset($ids);
    if ( !in_array($root, $ids) )
    {
        return [
            'result'  => false,
            'message' => 'The goods ['.$root.'] leading the clan is not among the ones named'
            ];
    }

    $tables = ['{DBNICK}_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $clann = MELBIS()->SqlGenId('store_clann');
    $now = MELBIS()->DateTime('now');

    foreach ( $ids as $id )
    {
        $row = [
            'id'          => $id,
            'clann'       => $clann,
            'clann_root'  => ( $id == $root ) ? 1 : 0,
            'update_time' => $now,
            'edit_time'   => $now
            ];
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_store', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'clann'   => $clann,
        'message' => 'The clan '.$clann.' holds '.count($ids).' goods, ['.$root.'] leads it. '.
                     'CmdUpdate with clann '.$clann.' puts one more in'
        ];
}



/**
 * Function StoreAllowed
 **/
function StoreAllowed($mUserId, $mIds, $mTopics = [], $mSub = false)
{
    // Reads the goods this person may describe, out of the ids and the sections of the call
    $allow = UTIL\RightTable('topic', $mUserId, 'descr');

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
 * Function InfoAllowed
 **/
function InfoAllowed($mUserId, $mIds)
{
    // Reads the store_info rows standing on goods this person may describe
    $allow = UTIL\RightTable('topic', $mUserId, 'descr');
    $list = implode(',', $mIds);

    $command = "SELECT si.id
                  FROM {DBNICK}_store_info si
                  JOIN {DBNICK}_topic_store ts
                    ON ts.store_id = si.store_id
                  JOIN $allow at
                    ON at.id = ts.topic_id
                 WHERE si.id IN ( $list )
              GROUP BY si.id
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return array_column($rows, 'id');
}


/**
 * Function SetAllowed
 **/
function SetAllowed($mUserId, $mIds)
{
    // Reads the store_set rows standing on goods this person may describe
    $allow = UTIL\RightTable('topic', $mUserId, 'descr');
    $list = implode(',', $mIds);

    $command = "SELECT ss.id
                  FROM {DBNICK}_store_set ss
                  JOIN {DBNICK}_topic_store ts
                    ON ts.store_id = ss.store_id
                  JOIN $allow at
                    ON at.id = ts.topic_id
                 WHERE ss.id IN ( $list )
              GROUP BY ss.id
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return array_column($rows, 'id');
}


?>
