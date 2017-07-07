<?php

if ( substr(trim($text), 0, 4) === '/rpg' )
{
	// * 全局操作
	if( strlen(trim($text)) > 10 )       // /rpg ATTACK MONSTER
	{
		$rpg_param = substr(trim($text), 5);
		if( strpos($rpg_param, '　') ) // 全角转半角空格
		{
			$rpg_param = str_replace('　', ' ', $rpg_param);
		}
		if ( strpos($rpg_param, ' ') )
		{
			$rpg_param = explode(' ', $rpg_param);  // 数组
		}
	}

	// * 快捷处理
	$username = $data['message']['from']['username'];
	$name = $data['message']['from']['first_name'];
	//$message_id = $data['message']['message_id'];

	//
	// 一、角色属性查询
	//

	$chara = array();

	    //  1. 查询缓存
	if( is_file("data.chara.$username.cache.json") )
	{
		$fs_chara = json_decode( file_get_contents("data.chara.$username.cache.json"), true);
		$chara['level'] = $fs_chara['level']; 			// 等级
		$chara['max_hp'] = $fs_chara['max_hp'];		    // MAX HP
		$chara['remain_hp'] = $fs_chara['remain_hp'];	// 剩余 HP
		$chara['str'] = $fs_chara['str'];				// 力量
		$chara['status'] = $fs_chara['status'];         // 状态
		$chara['target'] = $fs_chara['target'];         // 行为目标
	}
	else
	{
		// 2. 查询数据库
		$db_param = "SELECT * FROM bath_user WHERE username = " . "'" . $username . "'";
		$db_chara = pg_query($dbcon, $db_param) or exit;

		if (pg_num_rows($db_chara) == 0) 
		{
			// * 角色第一次游戏
			// 初始化 hp = 10 , strength = 20 , level = 1
			$chara['level'] = 1;
			$chara['max_hp'] = 10;
			$chara['remain_hp'] = 10;
			$chara['str'] = 20;
			$chara['status'] = null;
			$chara['target'] = null;

			// * 第一次游戏提示
			$content = array(
							'text'	=> "勇者 $name 大人，恭候多时了。……可惜，如今挥剑恐怕也改变不了什么了。",
						 'chat_id'  => $chat_id,
			 'reply_to_message_id'  => $message_id
				);
			$telegram->sendMessage($content);

		}
		else
		{
			while( $row = pg_fetch_array($db_chara) )
			{
				$chara['level'] = $row['level'];
				$chara['max_hp'] = $row['hp'];
				$chara['remain_hp'] = $row['hp'];
				$chara['str'] = $row['strength'];
				$chara['status'] = null;
				$chara['target'] = null;
			}
		}

		// 3.写入缓存
		// 写入文件缓存  格式   data.chara.username.cache.json
		//            array(
		//		 'level' => 1,
		//		'max_hp' => 10,
		//   'remain_hp' => 5,
		//		   'str' => 20,
		//		'status' => null,
		//	    'target' => null
		//				)

		file_put_contents("data.chara.$username.cache.json", json_encode($chara));

	}

	//
	// END 一、角色属性查询
	//

	//
	// 二、角色行为判断
	//

    // 1.无所事事状态
	if( $chara['status'] === null )
	{
		// 显示主菜单
		$option = array(
			array($telegram->buildKeyboardButton("探索"), $telegram->buildKeyboardButton("自己"))
			);
		$keyb = $telegram->buildKeyBoard($option, $onetime = false);
		$content = array(
					'chat_id' => $chat_id, 
		'reply_to_message_id' => $message_id,
					'reply_markup' => $keyb, 
					'text' => "“又来到了这脏乱之地。”"
			);
		$telegram->sendMessage($content);
	}

}




if ( strpos($text, '#rpg_control#') !== FALSE )
{
	$groups_ava = array(-136444736, -1001128716814);
	if (in_array($chat_id, $groups_ava)) {
		$content = array(
			'chat_id' => $chat_id,
		 'message_id' => $message_id
			);
		$result = $telegram->deleteMessage($content);
	}
}