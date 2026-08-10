<?php
/***************************************************************************************************
 * @version 6.5.0.370 @ 2026-08-10
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/**
 * Function MELBIS_INC_AGENT_lock
 * Takes the tables into work; a busy table comes back as a ready refusal to hand up
 **/
function MELBIS_INC_AGENT_lock($mTables)
{
	$taken = MELBIS()->SqlTableLock(__LINE__, $mTables);
	if ( $taken )
	{
		return [
			'result' => true
			];
	}

	$names = [];
	foreach ( (array)$mTables as $table )
	{
		$names[] = str_replace('{DBNICK}_', '', $table);
	}
	$list = implode(', ', $names);

	return [
		'result'  => false,
		'message' => 'The ['.$list.'] tables are taken into work right now - try again in a moment'
		];
}


/**
 * Function MELBIS_INC_AGENT_key_value
 * The whole settings registry in one universal query: the only shape SqlSelectStatic caches once,
 * however rare the codes asked for - narrowing down is PHP work and costs nothing
 **/
function MELBIS_INC_AGENT_key_value()
{
	$command = "SELECT key_code, key_name, sys_key
	              FROM {DBNICK}_key_value
	          ORDER BY key_code, pos
	           ";

	return MELBIS()->SqlSelectStatic(__LINE__, $command);
}


/**
 * Function MELBIS_INC_AGENT_key_value_code
 * The values of one dictionary, narrowed in PHP from the same universal read
 **/
function MELBIS_INC_AGENT_key_value_code($mKeyCode)
{
	$rows = MELBIS_INC_AGENT_key_value();

	$values = [];
	foreach ( $rows as $row )
	{
		if ( $row['key_code'] != $mKeyCode ) continue;

		$values[] = $row;
	}

	return $values;
}

?>
