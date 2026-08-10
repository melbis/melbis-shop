<?php
/***************************************************************************************************
 * @version 6.5.0.370 @ 2026-08-10
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/**
 * Function MELBIS_AGENT_COMMON_key
 * The settings registry for the agent, small enough to hold both faces in one body:
 * ACT_SIGN answers the command map, ACT_DO runs the command that came
 **/
function MELBIS_AGENT_COMMON_key($mAction, $mUserId, $mCommand, $mParam = [])
{
	// Sign
	if ( $mAction == 'ACT_SIGN' )
	{
		return [
			'result'   => true,
			'commands' => [
				'CMD_LIST' => [
					'key_code' => 'the dictionary to read, e.g. TASK_KIND_KEY; omitted - the codes themselves'
					]
				],
			'message'  => 'The commands of the tool with the fields each one takes'
			];
	}

	// Do
	switch ( $mCommand )
	{
		// One static read holds the whole registry, PHP narrows it from there
		case 'CMD_LIST' :
			$rows = MELBIS_INC_AGENT_key_value();
			$codes = array_column($rows, 'key_code');
			$codes = array_unique($codes);
			$codes = array_values($codes);

			// No code named - the answer is the codes themselves
			$key_code = trim($mParam['key_code'] ?? '');
			if ( $key_code == '' )
			{
				$count = count($codes);
				return [
					'result'  => true,
					'count'   => $count,
					'codes'   => $codes,
					'message' => 'The registry holds '.$count.' dictionaries - name one as key_code to read its values'
					];
			}

			if ( !in_array($key_code, $codes) )
			{
				$known = implode(', ', $codes);
				return [
					'result'  => false,
					'message' => 'No dictionary ['.$key_code.'] in the registry. The codes are: '.$known
					];
			}

			$code_set = MELBIS_INC_AGENT_key_value_code($key_code);
			$values = [];
			foreach ( $code_set as $row )
			{
				$values[] = [
					'name'   => $row['key_name'],
					'system' => ( $row['sys_key'] != 0 )
					];
			}

			$count = count($values);

			return [
				'result'   => true,
				'key_code' => $key_code,
				'count'    => $count,
				'values'   => $values,
				'message'  => 'The ['.$key_code.'] dictionary holds '.$count.' values; system ones the program relies on'
				];
	}
}


?>
