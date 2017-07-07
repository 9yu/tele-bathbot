<?php

if ($text == "/rpg_start" || $text == "/rpg_start@gumtakebath_bot")
{
	// 快捷处理
	$username = $data['message']['from']['username'];
	$name = $data['message']['from']['first_name'];
	//$message_id = $data['message']['message_id'];

	// 角色属性查询
	$db_param = 'SELECT * FROM bath_user WHERE username = ' . "'" . $username . "'";
	$db_chara = pg_query($dbcon, $db_param) or exit;

	if (pg_num_rows($db_chara) == 0) 
	{
		// 角色第一次游戏
		// 初始化 hp = 10 , strength = 20 , level = 1
		$chara_hp = 10;
		$chara_strength = 20;


	}

}

if ( strpos($text, '#rpg_control#') !== FALSE )
{
	$groups_ava = array('-136444736','-1001128716814');
	if (in_array((string)$chat_id, $groups_ava)) {
		$content = array(
			'chat_id' => $chat_id,
		 'message_id' => $message_id
			);
		$result = $telegram->deleteMessage($content);
	}
}