<?php

if ($text === 'return_param')
{
	$result = json_encode($data);
	$content = array('chat_id' => $chat_id, 'text' => $result);
    $telegram->sendMessage($content);
}

if ($text === 'first_time_init')
{
	$content = 'CREATE TABLE bath_monster
				(
				type        	 	 varchar(10),
				hp           		 int,
				remain        		 int,
				monster_id           int,
				attacker_username	 varchar(50),
				died 				 boolean,
				attacker_name        varchar(50)
				)';
	$insert = pg_query($dbcon, $content);
	if( $insert !== FALSE )
	{
		$content = array('chat_id' => $chat_id, 'text' => 'CREATE TABLE bath_monster SUCCESS');
	    $telegram->sendMessage($content);
	}

	$content = 'CREATE TABLE bath_user
				(
				type        	 	 varchar(10),
				hp           		 bigint,
				strength        	 bigint,
				level          		 bigint,
				username	 	     varchar(50),
				name        	 	 varchar(50)
				)';
	$insert = pg_query($dbcon, $content);
	if( $insert !== FALSE )
	{
		$content = array('chat_id' => $chat_id, 'text' => 'CREATE TABLE bath_user SUCCESS');
	    $telegram->sendMessage($content);
	}


	$content = 'CREATE TABLE bath_data
				(
				power       	 	 bigint,
				down_hp_day       	 bigint,
				id        		 	 int
				)';
	$insert = pg_query($dbcon, $content);
	if( $insert !== FALSE )
	{
		$content = array('chat_id' => $chat_id, 'text' => 'CREATE TABLE bath_data SUCCESS');
	    $telegram->sendMessage($content);
	}
}

