<?php
/***************************************************************************************************
 * @version 6.5.0.402 @ 2026-08-20
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList        - Reads the whole catalogue and the five tables hanging off it
 * CmdAdd         - Adds a section under a parent, with the rights of that parent
 * CmdUpdate      - Changes the given columns of sections, by id
 * CmdMove        - Moves a section under another parent, its branch with it
 * CmdRemove      - Deletes sections with their branches
 * CmdRightAdd    - Grants a right on sections, to a person or to a group
 * CmdRightUpdate - Changes the given columns of topic_right rows, by id
 * CmdRightRemove - Deletes topic_right rows by id
 * CmdKeyAdd      - Sets an option of the registry on sections
 * CmdKeyUpdate   - Changes the given columns of topic_key_set rows, by id
 * CmdKeyRemove   - Deletes topic_key_set rows by id
 * CmdAltAdd      - Adds a node to an alternative catalogue, standing for a section
 * CmdAltUpdate   - Changes the given columns of nodes, by id
 * CmdAltMove     - Moves a node under another parent of its own catalogue
 * CmdAltRemove   - Deletes nodes with their branches
 *
 * TopicAllowed   - Of the sections asked for, the ones this person may shape
 * KeyAllowed     - Of the rows asked for, the ones on a section of theirs
 * AltTopic       - Weighs the section a node of an alternative catalogue stands for
 * AltKind        - Reads which alternative catalogue a node belongs to
 *
 * Shaping a section is the Location right on it; the shape of the tree belongs to the engine
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_TOPIC;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;

// The columns a call may write into a section; the shape of the tree is the engine's
const FIELDS_WRITE = "skey, name, intro, descr, no_visible, kind_key, link, seo_psu, seo_title,
                      in_xml, templ_key, order_key, order_asc, option_code";

// The columns a call may write into a right
const FIELDS_RIGHT = "user_id, group_id, for_frame, for_price, for_ctrl";

// The columns a call may write into an option row
const FIELDS_KEY = "key_id, value_id, value_txt";

// The columns a call may write into a node; the kind of the catalogue is its scope
const FIELDS_ALT = "name, topic_id";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $command = "SELECT *
                  FROM {DBNICK}_topic
              ORDER BY absindex
               ";
    $topics = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_topic_right
              ORDER BY topic_id, id
               ";
    $rights = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_topic_alt
              ORDER BY kind_key, absindex
               ";
    $alt = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_topic_key
              ORDER BY absindex
               ";
    $options = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_topic_key_value
              ORDER BY key_id, pos
               ";
    $values = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_topic_key_set
              ORDER BY topic_id, id
               ";
    $keys = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($topics).' sections, '.count($rights).' rights, '.count($alt).
                     ' rows of the alternative catalogues, '.count($options).' options, '.
                     count($keys).' settings',
        'tables'  => [
            'topic'           => $topics,
            'topic_right'     => $rights,
            'topic_alt'       => $alt,
            'topic_key'       => $options,
            'topic_key_value' => $values,
            'topic_key_set'   => $keys
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_TOPIC', 'Shaping the catalogue');
    if ( $gate !== true ) return $gate;

    $parent_id = $mParam['parent_id'];

    // Weighs the parent: a section is born under one this person may shape, the root included
    $named = TopicAllowed($mUserId, [$parent_id]);
    if ( count($named) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$parent_id.'] is not yours to shape: that is the Location '.
                         'right on it, given out on the section itself, in the program'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_WRITE);

    $tables = ['{DBNICK}_topic', '{DBNICK}_topic_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // The engine seats the section in the tree, and the rights of the parent come along
    $id = UTIL\TreeNodeAdd('topic', $parent_id, $fields);

    UTIL\TablesUnlock($tables);

    if ( $id == 0 )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$parent_id.'] is out of the tree, so nothing can go under it'
            ];
    }

    return [
        'result'  => true,
        'id'      => $id,
        'message' => 'The section ['.$id.'] is under ['.$parent_id.'], with the rights of the parent'
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_TOPIC', 'Shaping the catalogue');
    if ( $gate !== true ) return $gate;

    $ids = TopicAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The sections ['.$list.'] are not yours to shape: that is the Location '.
                         'right on them, given out on the section itself, in the program'
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

    $tables = ['{DBNICK}_topic'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_topic', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' sections changed: '.$changed
        ];
}


/**
 * Function CmdMove
 **/
function CmdMove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_TOPIC', 'Shaping the catalogue');
    if ( $gate !== true ) return $gate;

    $id = $mParam['id'];
    $parent_id = $mParam['parent_id'];

    $named = TopicAllowed($mUserId, [$id, $parent_id]);
    if ( count($named) < 2 )
    {
        return [
            'result'  => false,
            'message' => 'Both the section and the parent it goes under are yours to shape, or the '.
                         'move does not happen: that is the Location right on each of them'
            ];
    }

    $tables = ['{DBNICK}_topic'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $done = MELBIS()->SysTreeMove('topic', $id, $parent_id);

    UTIL\TablesUnlock($tables);

    if ( !$done )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$parent_id.'] sits inside the branch of ['.$id.'], or is '.
                         'out of the tree: nothing was moved'
            ];
    }

    return [
        'result'  => true,
        'message' => 'The section ['.$id.'] is under ['.$parent_id.'] now, with its whole branch'
        ];
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_TOPIC', 'Shaping the catalogue');
    if ( $gate !== true ) return $gate;

    $ids = TopicAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The sections ['.$list.'] are not yours to shape: that is the Location '.
                         'right on them'
            ];
    }

    // Walks the branch under every id: the whole of it goes, not the root alone
    $branch = [];
    foreach ( $ids as $id )
    {
        $mine = UTIL\TreeBranch('topic', $id);
        foreach ( $mine as $one )
        {
            $branch[$one] = $one;
        }
    }
    $list = implode(',', $branch);

    $command = "SELECT COUNT(DISTINCT store_id)
                  FROM {DBNICK}_topic_store
                 WHERE topic_id IN ( $list )
               ";
    $goods = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    if ( !$mParam['recursive'] )
    {
        $message = 'About to delete '.count($branch).' section(s) with the branch under them';
        if ( $goods > 0 )
        {
            $message .= '. '.$goods.' goods hang there and lose those sections; the ones left '.
                        'hanging nowhere are seen only by the Recovery tool';
        }
        $message .= '. Say recursive to go on';

        return [
            'result'  => false,
            'message' => $message
            ];
    }

    $tables = ['{DBNICK}_topic', '{DBNICK}_topic_right', '{DBNICK}_topic_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $how = 0;
    foreach ( $ids as $id )
    {
        $gone = MELBIS()->SysTreeDelete('topic', $id);
        if ( $gone !== false ) $how += $gone;
    }

    UTIL\TablesUnlock($tables);

    // Sweeps the rights and the options of the gone sections, by the map of the engine
    $swept = UTIL\DependSweep('topic');

    $message = $how.' section(s) gone';
    $message .= UTIL\DependSaid($swept);

    return [
        'result'  => true,
        'message' => $message
        ];
}


/**
 * Function CmdRightAdd
 **/
function CmdRightAdd($mUserId, $mParam)
{
    // Handing out rights is an operation of the engine, not a right on the section
    $gate = UTIL\RightOper($mUserId, 'PUT_TOPIC_RIGHT', 'Handing out the rights on a section');
    if ( $gate !== true ) return $gate;

    $fields = UTIL\Only($mParam, FIELDS_RIGHT);
    if ( !isset($fields['user_id']) && !isset($fields['group_id']) )
    {
        return [
            'result'  => false,
            'message' => 'A right belongs to a person or to a group: name user_id or group_id'
            ];
    }

    $tables = ['{DBNICK}_topic_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // Every grant answers its own id: a right is changed and taken back by it later
    $made = [];
    foreach ( $mParam['topic_id'] as $id )
    {
        $row = $fields;
        $row['id'] = MELBIS()->SqlGenId('topic_right');
        $row['topic_id'] = $id;
        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_topic_right', $row);
        $made[] = $row['id'];
    }

    UTIL\TablesUnlock($tables);

    // One right is one id, and rights on several nodes are a list of them
    $said = ( count($made) == 1 ) ? $made[0] : $made;

    return [
        'result'  => true,
        'id'      => $said,
        'message' => 'The right ['.implode(',', $made).'] stands on '.count($mParam['topic_id']).' section(s)'
        ];
}


/**
 * Function CmdRightUpdate
 **/
function CmdRightUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_TOPIC_RIGHT', 'Handing out the rights on a section');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('topic_right', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No rows ['.$list.'] of topic_right - CmdList answers them with their ids'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_RIGHT);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_topic_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_topic_right', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' rows changed: '.$changed
        ];
}


/**
 * Function CmdRightRemove
 **/
function CmdRightRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_TOPIC_RIGHT', 'Handing out the rights on a section');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('topic_right', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No rows ['.$list.'] of topic_right'
            ];
    }

    $tables = ['{DBNICK}_topic_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $where = [
            'id' => $id
            ];
        MELBIS()->SqlDelete(__LINE__, '{DBNICK}_topic_right', $where);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' rows gone. A section with no rights left on it is seen only by '.
                     'whoever holds PUT_TOPIC_RIGHT'
        ];
}


/**
 * Function CmdKeyAdd
 **/
function CmdKeyAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_TOPIC_KEY_SET', 'Setting the options of a section');
    if ( $gate !== true ) return $gate;

    $ids = TopicAllowed($mUserId, $mParam['topic_id']);
    $lost = array_diff($mParam['topic_id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The sections ['.$list.'] are not yours to shape'
            ];
    }

    // The option and its value belong to the tree of this family, and a stray id is refused
    $option = UTIL\OptionPair('topic', $mParam);
    if ( $option !== true ) return $option;

    $fields = UTIL\Only($mParam, FIELDS_KEY);

    $tables = ['{DBNICK}_topic_key_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = MELBIS()->SqlGenId('topic_key_set');
        $row['topic_id'] = $id;
        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_topic_key_set', $row);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => 'The option stands on '.count($ids).' section(s)'
        ];
}


/**
 * Function CmdKeyUpdate
 **/
function CmdKeyUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_TOPIC_KEY_SET', 'Setting the options of a section');
    if ( $gate !== true ) return $gate;

    $ids = KeyAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The rows ['.$list.'] stand on sections that are not yours to shape, or '.
                         'are not there at all'
            ];
    }

    // The pair as it will stand after the change: a row keeps whatever the call left unsaid
    $list = implode(',', $ids);
    $command = "SELECT id, key_id, value_id
                  FROM {DBNICK}_topic_key_set
                 WHERE id IN ( $list )
               ";
    $was_set = MELBIS()->SqlSelect(__LINE__, $command);
    foreach ( $was_set as $was )
    {
        $option = UTIL\OptionPair('topic', $mParam, $was);
        if ( $option !== true ) return $option;
    }

    $fields = UTIL\Only($mParam, FIELDS_KEY);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_topic_key_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_topic_key_set', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' rows changed: '.$changed
        ];
}


/**
 * Function CmdKeyRemove
 **/
function CmdKeyRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_TOPIC_KEY_SET', 'Setting the options of a section');
    if ( $gate !== true ) return $gate;

    $ids = KeyAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The rows ['.$list.'] stand on sections that are not yours to shape, or '.
                         'are not there at all'
            ];
    }

    $tables = ['{DBNICK}_topic_key_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $where = [
            'id' => $id
            ];
        MELBIS()->SqlDelete(__LINE__, '{DBNICK}_topic_key_set', $where);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' rows gone'
        ];
}



/**
 * Function KeyAllowed
 **/
function KeyAllowed($mUserId, $mIds)
{
    // Reads the option rows standing on sections this person may shape
    $allow = UTIL\RightTable('topic', $mUserId, 'place');
    $list = implode(',', $mIds);

    $command = "SELECT ks.id
                  FROM {DBNICK}_topic_key_set ks
                  JOIN $allow at
                    ON at.id = ks.topic_id
                 WHERE ks.id IN ( $list )
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return array_column($rows, 'id');
}


/**
 * Function AltTopic
 * The section a node of an alternative catalogue stands for, and nothing is made without one
 **/
function AltTopic($mTopicId)
{
    $command = "SELECT id
                  FROM {DBNICK}_topic
                 WHERE id = :ID
               ";
    $param_topic = [
        'id' => $mTopicId
        ];
    $row = MELBIS()->SqlSelectFlat(__LINE__, $command, $param_topic);

    if ( !isset($row['id']) )
    {
        return [
            'result'  => false,
            'message' => 'No section ['.$mTopicId.'] in the main catalogue - a node of an alternative '.
                         'catalogue is a section under another name, and CmdList answers the sections '.
                         'with their ids'
            ];
    }

    return true;
}


/**
 * Function CmdAltAdd
 **/
function CmdAltAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_TOPIC_ALT', 'Shaping an alternative catalogue');
    if ( $gate !== true ) return $gate;

    $stands = AltTopic($mParam['topic_id']);
    if ( $stands !== true ) return $stands;

    // One table holds every alternative catalogue, so the kind is the scope of the tree
    $scope = [
        'kind_key' => $mParam['kind_key']
        ];
    $fields = UTIL\Only($mParam, FIELDS_ALT);

    $tables = ['{DBNICK}_topic_alt'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $id = UTIL\TreeNodeAdd('topic_alt', $mParam['parent_id'], $fields, $scope);

    UTIL\TablesUnlock($tables);

    if ( $id == 0 )
    {
        return [
            'result'  => false,
            'message' => 'The node ['.$mParam['parent_id'].'] is not in the ['.$mParam['kind_key'].
                         '] catalogue, so nothing can go under it'
            ];
    }

    return [
        'result'  => true,
        'id'      => $id,
        'message' => 'The node ['.$id.'] is in the ['.$mParam['kind_key'].'] catalogue'
        ];
}


/**
 * Function CmdAltUpdate
 **/
function CmdAltUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_TOPIC_ALT', 'Shaping an alternative catalogue');
    if ( $gate !== true ) return $gate;

    // A node may be pointed at another section, never at none
    if ( isset($mParam['topic_id']) )
    {
        $stands = AltTopic($mParam['topic_id']);
        if ( $stands !== true ) return $stands;
    }

    $ids = UTIL\Exists('topic_alt', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No nodes ['.$list.'] of the alternative catalogues'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_ALT);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_topic_alt'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_topic_alt', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' nodes changed: '.$changed
        ];
}


/**
 * Function CmdAltMove
 **/
function CmdAltMove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_TOPIC_ALT', 'Shaping an alternative catalogue');
    if ( $gate !== true ) return $gate;

    $kind = AltKind($mParam['id']);
    if ( $kind == '' )
    {
        return [
            'result'  => false,
            'message' => 'No node ['.$mParam['id'].'] of the alternative catalogues'
            ];
    }

    // A node moves inside its own catalogue, so the kind of it is read first
    $scope = [
        'kind_key' => $kind
        ];

    $tables = ['{DBNICK}_topic_alt'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $done = MELBIS()->SysTreeMove('topic_alt', $mParam['id'], $mParam['parent_id'], $scope);

    UTIL\TablesUnlock($tables);

    if ( !$done )
    {
        return [
            'result'  => false,
            'message' => 'The node ['.$mParam['parent_id'].'] sits inside the branch of ['.
                         $mParam['id'].'], or is not in the ['.$kind.'] catalogue: nothing moved'
            ];
    }

    return [
        'result'  => true,
        'message' => 'The node ['.$mParam['id'].'] is under ['.$mParam['parent_id'].'] now'
        ];
}


/**
 * Function CmdAltRemove
 **/
function CmdAltRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_TOPIC_ALT', 'Shaping an alternative catalogue');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('topic_alt', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No nodes ['.$list.'] of the alternative catalogues'
            ];
    }

    $branch = [];
    foreach ( $ids as $id )
    {
        $kind = AltKind($id);
        $scope = [
            'kind_key' => $kind
            ];
        $mine = UTIL\TreeBranch('topic_alt', $id, $scope);
        foreach ( $mine as $one )
        {
            $branch[$one] = $one;
        }
    }

    if ( !$mParam['recursive'] )
    {
        return [
            'result'  => false,
            'message' => 'About to delete '.count($branch).' node(s) with the branch under them. '.
                         'Say recursive to go on; the sections they point at stay as they are'
            ];
    }

    $tables = ['{DBNICK}_topic_alt'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $how = 0;
    foreach ( $ids as $id )
    {
        $kind = AltKind($id);
        if ( $kind == '' ) continue;

        $scope = [
            'kind_key' => $kind
            ];
        $gone = MELBIS()->SysTreeDelete('topic_alt', $id, $scope);
        if ( $gone !== false ) $how += $gone;
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => $how.' node(s) gone'
        ];
}



/**
 * Function AltKind
 **/
function AltKind($mId)
{
    // Reads the kind of the catalogue a node stands in
    $command = "SELECT kind_key
                  FROM {DBNICK}_topic_alt
                 WHERE id = :ID
               ";
    $param_node = [
        'id' => $mId
        ];
    $row = MELBIS()->SqlSelectFlat(__LINE__, $command, $param_node);

    return $row['kind_key'] ?? '';
}


/**
 * Function TopicAllowed
 **/
function TopicAllowed($mUserId, $mIds)
{
    // Reads the sections this person may shape, by the Location right standing on them
    $allow = UTIL\RightTable('topic', $mUserId, 'place');
    $list = implode(',', $mIds);

    $command = "SELECT t.id
                  FROM {DBNICK}_topic t
                  JOIN $allow at
                    ON at.id = t.id
                 WHERE t.id IN ( $list )
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return array_column($rows, 'id');
}


?>
