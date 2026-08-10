<?php
/***************************************************************************************************
 * @version 6.5.0.370 @ 2026-08-10
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/**
 * Function AGENT_CLAUDE_SAMPLE
 * Says what the entry point hands a module: the names it got and what it can do with them
 **/
function AGENT_CLAUDE_SAMPLE($mVars)
{
    $post = $mVars['post'] ?? [];

    $hello = $post['hello'] ?? '';
    $how = (int)( $post['how'] ?? 0 );

    $keys = array_keys($mVars);
    $post_keys = array_keys($post);

    $answer = [
        'time'      => date('Y-m-d H:i:s'),
        'nick'      => MELBIS_DB_NICK,
        'keys'      => $keys,
        'post_keys' => $post_keys,
        'hello'     => $hello,
        'doubled'   => $how * 2
        ];

    return json_encode($answer, JSON_UNESCAPED_UNICODE);
}


?>