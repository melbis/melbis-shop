<?php
/***************************************************************************************************
 * @version 6.5.0.402 @ 2026-08-20
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Link - Hangs goods at the end of a section, skipping the ones already there
 *
 * Shared by the three tools that hang goods: Location, Recovery and the loader
 **************************************************************************************************/


// Name space
namespace MELBIS_INC_AGENT_STORE;


/**
 * Function Link
 * Hangs the goods at the end of the section and answers two lists: hung, and already standing
 **/
function Link($mTopicId, $mRows)
{
    // Reads what hangs in the section already, so the same goods is not hung twice
    $command = "SELECT store_id
                  FROM {DBNICK}_topic_store
                 WHERE topic_id = :TOPIC_ID
               ";
    $param_hang = [
        'topic_id' => $mTopicId
        ];
    $hang = MELBIS()->SqlSelect(__LINE__, $command, $param_hang);

    $already = [];
    foreach ( $hang as $row )
    {
        $already[(int)$row['store_id']] = 1;
    }

    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_topic_store
                 WHERE topic_id = :TOPIC_ID
               ";
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_hang);

    // Only the fresh rows take ids, and they go in as one insert
    $fresh = [];
    $stood = [];
    foreach ( $mRows as $was )
    {
        if ( isset($already[$was['id']]) )
        {
            $stood[] = $was['name'];
            continue;
        }

        $fresh[] = $was;
    }

    $ids = MELBIS()->SqlGenIdBlock('topic_store', count($fresh));

    $hung = [];
    $link = [];
    foreach ( $fresh as $num => $was )
    {
        $last++;
        $link[] = [
            'id'       => $ids[$num],
            'topic_id' => $mTopicId,
            'store_id' => $was['id'],
            'pos'      => $last
            ];
        $hung[] = $was['name'];
    }

    MELBIS()->SqlInsertBlock(__LINE__, '{DBNICK}_topic_store', $link);

    return [
        'hung'  => $hung,
        'stood' => $stood
        ];
}

?>
