<?php

if ( substr($text, 0, 4) === '/rpg' )
//if( substr_count($text, "/rpg") === 1 )
{
	//include('RPG_test.php');
	// * 全局操作
	
	if( strlen($text) > 10 )       // /rpg ATTACK MONSTER
	{
		$rpg_param = substr(trim($text), 5);
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

	// 一键退出
	if( $rpg_param[0] === 'EXIT' && $rpg_param[1] === 'GAME')
	{
		unlink("data.target.$username.cache.json");
		unlink("data.chara.$username.cache.json");
		unlink("data.history.$username.cache.json");
		exit;
	}


	$chara = array();
	
	    //  1. 查询缓存
	if( file_exists("data.chara.$username.cache.json") )
	{
		$fs_chara = json_decode( file_get_contents("data.chara.$username.cache.json"), true);
		$chara['level'] = $fs_chara['level']; 			// 等级
		$chara['max_hp'] = $fs_chara['max_hp'];		    // MAX HP
		$chara['remain_hp'] = $fs_chara['remain_hp'];	// 剩余 HP
		$chara['str'] = $fs_chara['str'];				// 力量
		$chara['status'] = $fs_chara['status'];         // 状态
		$chara['target'] = $fs_chara['target'];         // 行为目标
		$chara['turn'] = $fs_chara['turn'];				// 第几回合
	}
	else
	{

	
		// 2. 查询数据库
		$db_param = "SELECT * FROM bath_user WHERE username = " . "'" . $username . "'";
		$db_chara = pg_query($dbcon, $db_param);

		if (pg_num_rows($db_chara) == 0) 
		{
			// * 角色第一次游戏
			// 初始化 hp = 10 , strength = 20 , level = 1
			
			$chara['level'] = 1;
			$chara['max_hp'] = 10;
			$chara['remain_hp'] = 10;
			$chara['str'] = 20;
			$chara['status'] = 'home';
			$chara['target'] = null;
			$chara['turn'] = null;

			// * 第一次游戏提示
			$return_text = "勇者 ". $name . " 大人，恭候多时了。……可惜，如今挥剑恐怕也改变不了什么了。";
			include('RPG_history.php');
			$content = array(
							'text'	=> $return_all,
						 'chat_id'  => $chat_id,
			 'reply_to_message_id'  => $message_id
				);
			$result = $telegram->sendMessage($content);
			include('RPG_delete.php');

		}
		else
		{
			while ( $row = pg_fetch_array($db_chara) )
			{
				$chara['level'] = (int)$row['level'];
				$chara['max_hp'] = (int)$row['hp'];
				$chara['remain_hp'] = (int)$row['hp'];
				$chara['str'] = (int)$row['strength'];
				$chara['status'] = 'home';
				$chara['target'] = null;
				$chara['turn'] = null;
			}

		}

		// 3.写入缓存
		// 写入文件缓存  格式   data.chara.username.cache.json
		//            array(
		//		 'level' => 1,
		//		'max_hp' => 10,
		//   'remain_hp' => 5,
		//		   'str' => 20,
		//		'status' => 'home',
		//	    'target' => null,
		//		 'turn'  => null
		//				)

		file_put_contents("data.chara.$username.cache.json", json_encode($chara));

		$db_insert = "INSERT INTO bath_user VALUES ('yuusya', " . $chara['max_hp'] . ", " . $chara['str'] . ", " . $chara['level'] . ", '" . $username . "', '" . $name ."')";
		pg_query($dbcon, $db_insert);

	}
	
		// TEMP TEST PART!!!!!!!
	/**
		$return_text = json_encode($rpg_param);
		include('RPG_history.php');
		$message_box = array(
			'chat_id' => $chat_id,
			'reply_to_message_id' => $message_id,
			'text' => $return_all
			);
		$result = $telegram->sendMessage($message_box);
		include('RPG_delete.php');

**/
	
	//
	// END 一、角色属性查询
	//

	//
	// 二、角色行为判断
	//

	// 1.行为状态
	if( $rpg_param[0] === 'SELF' && $rpg_param[1] === 'CHECK' )
	{

		$return_text = "【个人属性】 \n Level: " . $chara['level'] . " \n HP: " . $chara['max_hp'] . "  \n 剩余 HP: " . $chara['remain_hp'] . "\n 力量: " . $chara['str'];
		include('RPG_history.php');
		$content = array(
					'chat_id' => $chat_id, 
		'reply_to_message_id' => $message_id,
					'text' => $return_all
			);
		$result = $telegram->sendMessage($content);
		include('RPG_delete.php');
		exit;
	}

	if( $rpg_param[0] === 'EXPLORE' && $chara['status'] === 'home' )
	{
		$chara['status'] = 'explore';
	}


	// 2.探索状态
	
	if( $chara['status'] === 'explore' )
	{
		if( $rpg_param[0] === 'EXPLORE' )
		{

			// IT 开始
			if( $rpg_param[1] === 'IT' )
			{
				// 随机事件
				$rand = rand(0,100);
				if( $rand < 60 )
				{
					// 遇小怪 MONSTER
					$chara['status'] = 'battle';
					$chara['target'] = 'monster';
					$target_hp = rand(200, 400);
					$target_str = rand(20, 200);
					$target_details = array(
						'type' => 'MONSTER',
					  'max_hp' => $target_hp,
				   'remain_hp' => $target_hp,
				         'str' => $target_str
						);
					file_put_contents("data.target.$username.cache.json", json_encode($target_details));
					// 记录怪数据

					// 显示给玩家

					$option = array(
						array($telegram->buildKeyboardButton("/rpg ATTACK IT")),
						array($telegram->buildKeyboardButton("/rpg DEFENSE HIDE")),
						array($telegram->buildKeyboardButton("/rpg ESCAPE !!"))
						);
					$keyb = $telegram->buildKeyBoard($option, $onetime = true);
					$return_text = "【探索】污秽不堪的东西逼近过来了…… \n HP: $target_hp \n 力量: $target_str \n 怎么办是好？";
					include('RPG_history.php');
					$content = array(
								'chat_id' => $chat_id, 
					'reply_to_message_id' => $message_id,
								'reply_markup' => $keyb, 
								'text' => $return_all
						);
					$result = $telegram->sendMessage($content);
					include('RPG_delete.php');

					file_put_contents("data.chara.$username.cache.json", json_encode($chara));

				}
				else
				{
					// 继续探险
					$option = array(
						array($telegram->buildKeyboardButton("/rpg EXPLORE IT")),
						array($telegram->buildKeyboardButton("/rpg RETURN BACK"))
						);
					$keyb = $telegram->buildKeyBoard($option, $onetime = true);
					$return_text = "【探索】渐渐走得有些远了……";
					include('RPG_history.php');
					$content = array(
								'chat_id' => $chat_id, 
					'reply_to_message_id' => $message_id,
								'reply_markup' => $keyb, 
								'text' => $return_all
						);
					$result = $telegram->sendMessage($content);
					include('RPG_delete.php');
				}
			}
			// IT 结束


		}
		// EXPLORE 结束

		if( $rpg_param[0] === 'RETURN' && $rpg_param[1] === 'BACK' )
		{
			$chara['status'] = 'home';
		}

	}

	// 3.战斗状态
	if( $chara['status'] === 'battle' )
	{
		if ( $chara['target'] === 'monster') {
			
			// 读取怪物信息
			$target_details = json_decode( file_get_contents("data.target.$username.cache.json"), true);
			// max_hp
			// remain_hp
			// str

			if ( $chara['turn'] === null )
			{
				$chara['turn'] = 1;
			}

			if ( $chara['turn'] % 3 === 0 )  // 怪物在第三回合行动
			{
				if( $rpg_param[0] === 'ATTACK' )
				{
					$target_details['remain_hp'] = $target_details['remain_hp'] - $chara['str'];
					if ( $target_details['remain_hp'] <= 0 )
					{
						// 怪物死了
						$chara['level'] = $chara['level'] + 1;
						$chara['str'] = $chara['str'] + 20;
						$chara['max_hp'] = $chara['max_hp'] + 10;
						$chara['status'] = 'home';

						// 写入数据库
						$db_insert = "UPDATE bath_user SET type = 'yuusya', hp = " . $chara['max_hp'] . ", strength = " . $chara['str'] . ", level = " . $chara['level'] . ", name = '" . $name ."' WHERE username = '" . $username . "'";
						$db_insert = pg_query($dbcon, $db_insert);


						$db_search = "SELECT * FROM bath_data WHERE id = 1";
						$db_search = pg_query($dbcon, $db_search);
						$row = pg_fetch_array($db_search);
						$heta_power = (int)$row['heta_power'] + $target_details['max_hp'];

						$db_insert = "UPDATE bath_data SET heta_power = " . $heta_power . " WHERE id = 1";
						pg_query($dbcon, $db_insert);

						// 今日污度减少缓存
						// 存在本地
						date_default_timezone_set("Asia/Shanghai");
						$heta_power_a_day_date = date('Y.m.d');
						// data.heta_power_a_day.年.月.日.cache.json
						if ( file_exists("data.heta_power_a_day.$heta_power_a_day_date.cache.json") )
						{
							$heta_power_a_day = json_decode(file_get_contents("data.heta_power_a_day.$heta_power_a_day_date.cache.json"),true);
							$heta_power_a_day = $heta_power_a_day + $target_details['max_hp'];
							file_put_contents("data.heta_power_a_day.$heta_power_a_day_date.cache.json", json_encode($heta_power_a_day));
						}
						else
						{
							$heta_power_a_day = $target_details['max_hp'];
							file_put_contents("data.heta_power_a_day.$heta_power_a_day_date.cache.json", json_encode($heta_power_a_day));
						}


						// 清理本地缓存
						unlink("data.target.$username.cache.json");
						unlink("data.chara.$username.cache.json");

						// 回复用户
						$return_text = "【胜利！】\n胜利了！…… \n 等级 + 1，HP + 10，力量 + 20 \n 现在的属性： \n 等级 " . $chara['level'] . "\n HP " . $chara['max_hp'] . "\n 力量 " . $chara['str'] . "\n 风尘再次扬起…… \n 【污】垢力下降 " . $target_details['max_hp'] . " 点";
						include('RPG_history.php');
						$content = array(
									'chat_id' => $chat_id, 
						'reply_to_message_id' => $message_id,
									'text' => $return_all
							);
						$result = $telegram->sendMessage($content);
						include('RPG_delete.php');

					}
					else
					{
						$chara['remain_hp'] = $chara['remain_hp'] - $target_details['str'];
						if ( $chara['remain_hp'] <= 0 )
						{
							// 清理本地缓存
							unlink("data.target.$username.cache.json");
							unlink("data.chara.$username.cache.json");

							// 回复用户
							$return_text = "【战斗】怪物突然行动起来…… \n " . $name . " 受到了 " . $target_details['str'] . " 点伤害 \n 【死亡！】尸骨横野……风尘扬起……";
							include('RPG_history.php');
							$content = array(
										'chat_id' => $chat_id, 
							'reply_to_message_id' => $message_id,
										'text' => $return_all
								);
							$result = $telegram->sendMessage($content);
							include('RPG_delete.php');
						}
						else // 没被打死
						{
							$chara['turn'] = $chara['turn'] + 1;
							file_put_contents("data.chara.$username.cache.json", json_encode($chara));

							// 回复用户
							$option = array(
								array($telegram->buildKeyboardButton("/rpg ATTACK IT")),
								array($telegram->buildKeyboardButton("/rpg DEFENSE HIDE")),
								array($telegram->buildKeyboardButton("/rpg ESCAPE !!"))
								);
							$keyb = $telegram->buildKeyBoard($option, $onetime = true);
							$return_text = "【战斗】怪物突然行动起来…… \n " . $name . " 受到了 ". $target_details['str'] . " 点伤害";
							include('RPG_history.php');
							$content = array(
										'chat_id' => $chat_id, 
							'reply_to_message_id' => $message_id,
								   'reply_markup' => $keyb, 
										'text' => $return_all
								);
							$result = $telegram->sendMessage($content);
							include('RPG_delete.php');
						}
					}
				} // 攻击结束

				if ( $rpg_param[0] === 'DEFENSE' ) 
				{
					$chara['turn'] = $chara['turn'] + 1;
					// 什么都不做

					// 回复用户
					$option = array(
						array($telegram->buildKeyboardButton("/rpg ATTACK IT")),
						array($telegram->buildKeyboardButton("/rpg DEFENSE HIDE")),
						array($telegram->buildKeyboardButton("/rpg ESCAPE !!"))
						);
					$keyb = $telegram->buildKeyBoard($option, $onetime = true);
					$return_text = "【战斗】躲在了废墟背后，似乎没发生什么……";
					include('RPG_history.php');
					$content = array(
								'chat_id' => $chat_id, 
					'reply_to_message_id' => $message_id,
						'reply_markup' => $keyb,
								'text' => $return_all
						);
					$result = $telegram->sendMessage($content);
					include('RPG_delete.php');

					file_put_contents("data.chara.$username.cache.json", json_encode($chara));
				}

				if ( $rpg_param[0] === 'ESCAPE' )
				{
					$chara['turn'] = null;
					$chara['status'] = 'home';
					$chara['target'] = null;

					// 回复用户
					$return_text = "【逃跑】迅速的往回跑了……似乎没有追上来……";
					include('RPG_history.php');
					$content = array(
								'chat_id' => $chat_id, 
					'reply_to_message_id' => $message_id,
								'text' => $return_all
						);
					$result = $telegram->sendMessage($content);
					include('RPG_delete.php');
				}


			}
			else
			{ // 怪物不动回合
				
				if ( $rpg_param[0] === 'ATTACK' )
				{
					$target_details['remain_hp'] = $target_details['remain_hp'] - $chara['str'];
					if ( $target_details['remain_hp'] <= 0 )
					{
						// 怪物死了
						$chara['level'] = $chara['level'] + 1;
						$chara['str'] = $chara['str'] + 20;
						$chara['max_hp'] = $chara['max_hp'] + 10;
						$chara['status'] = 'home';

						// 写入数据库
						$db_insert = "UPDATE bath_user SET type = 'yuusya', hp = " . $chara['max_hp'] . ", strength = " . $chara['str'] . ", level = " . $chara['level'] . ", name = '" . $name ."' WHERE username = '" . $username . "'";
						$db_insert = pg_query($dbcon, $db_insert);


						$db_search = "SELECT * FROM bath_data WHERE id = 1";
						$db_search = pg_query($dbcon, $db_search);
						$row = pg_fetch_array($db_search);
						$heta_power = (int)$row['heta_power'] + $target_details['max_hp'];

						$db_insert = "UPDATE bath_data SET heta_power = " . $heta_power . " WHERE id = 1";
						pg_query($dbcon, $db_insert);


						// 今日污度减少缓存
						// 存在本地
						date_default_timezone_set("Asia/Shanghai");
						$heta_power_a_day_date = date('Y.m.d');
						// data.heta_power_a_day.年.月.日.cache.json
						if ( file_exists("data.heta_power_a_day.$heta_power_a_day_date.cache.json") )
						{
							$heta_power_a_day = json_decode(file_get_contents("data.heta_power_a_day.$heta_power_a_day_date.cache.json"),true);
							$heta_power_a_day = $heta_power_a_day + $target_details['max_hp'];
							file_put_contents("data.heta_power_a_day.$heta_power_a_day_date.cache.json", json_encode($heta_power_a_day));
						}
						else
						{
							$heta_power_a_day = $target_details['max_hp'];
							file_put_contents("data.heta_power_a_day.$heta_power_a_day_date.cache.json", json_encode($heta_power_a_day));
						}


						// 清理本地缓存
						unlink("data.target.$username.cache.json");
						unlink("data.chara.$username.cache.json");

						// 回复用户
						$return_text = "【胜利！】胜利了！…… \n 等级 + 1，HP + 10，力量 + 20 \n 现在的属性： \n 等级 " . $chara['level'] . "\n HP " . $chara['max_hp'] . "\n 力量 " . $chara['str'] . "\n 风尘再次扬起…… \n 【污】垢力下降 " . $target_details['max_hp'] . " 点";
						include('RPG_history.php');
						$content = array(
									'chat_id' => $chat_id, 
						'reply_to_message_id' => $message_id,
									'text' => $return_all
							);
						$result = $telegram->sendMessage($content);
						include('RPG_delete.php');


					}
					else
					{
						$chara['turn'] = $chara['turn'] + 1;
						// 记录怪物血量
 						file_put_contents("data.target.$username.cache.json", json_encode($target_details));
						// 回复用户
						$option = array(
							array($telegram->buildKeyboardButton("/rpg ATTACK IT")),
							array($telegram->buildKeyboardButton("/rpg DEFENSE HIDE")),
							array($telegram->buildKeyboardButton("/rpg ESCAPE !!"))
							);
						$keyb = $telegram->buildKeyBoard($option, $onetime = true);
						$return_text = "【战斗】攻击造成 ". $chara['str'] . " 点伤害 \n 怪物血量 ". $target_details['remain_hp']. " / " . $target_details['max_hp'] . "\n 怪物一动不动的……";
						include('RPG_history.php');
						$content = array(
									'chat_id' => $chat_id, 
						'reply_to_message_id' => $message_id,
									'reply_markup' => $keyb,
									'text' => $return_all
							);
						$result = $telegram->sendMessage($content);
						include('RPG_delete.php');

						file_put_contents("data.chara.$username.cache.json", json_encode($chara));

 					}
				}

				if ( $rpg_param[0] === 'DEFENSE' ) 
				{
					$chara['turn'] = $chara['turn'] + 1;
					// 什么都不做

					// 回复用户
					$option = array(
						array($telegram->buildKeyboardButton("/rpg ATTACK IT")),
						array($telegram->buildKeyboardButton("/rpg DEFENSE HIDE")),
						array($telegram->buildKeyboardButton("/rpg ESCAPE !!"))
						);
					$keyb = $telegram->buildKeyBoard($option, $onetime = true);
					$return_text = "【战斗】躲在了废墟背后，似乎没发生什么……";
					include('RPG_history.php');
					$content = array(
								'chat_id' => $chat_id, 
					'reply_to_message_id' => $message_id,
						'reply_markup' => $keyb,
								'text' => $return_all
						);
					$result = $telegram->sendMessage($content);
					include('RPG_delete.php');

					file_put_contents("data.chara.$username.cache.json", json_encode($chara));
				}

				if ( $rpg_param[0] === 'ESCAPE' )
				{
					$chara['turn'] = null;
					$chara['status'] = 'home';
					$chara['target'] = null;

					// 回复用户
					$return_text = "【逃跑】迅速的往回跑了……似乎没有追上来……";
					include('RPG_history.php');
					$content = array(
								'chat_id' => $chat_id, 
					'reply_to_message_id' => $message_id,
								'text' => $return_all
						);
					$result = $telegram->sendMessage($content);
					include('RPG_delete.php');
				}

				
			} // 怪物不动回合
		}
	}



    // 4.无所事事状态
	if( $chara['status'] === 'home' )
	{

		// 显示主菜单
		$option = array(
			array($telegram->buildKeyboardButton("/rpg EXPLORE IT")),
			array($telegram->buildKeyboardButton("/rpg SELF CHECK")),
			array($telegram->buildKeyboardButton("/rpg EXIT GAME"))
			);
		$keyb = $telegram->buildKeyBoard($option, $onetime = true);
		$return_text = "“又来到了这脏乱之地。”";
		include('RPG_history.php');
		$content = array(
					'chat_id' => $chat_id, 
		'reply_to_message_id' => $message_id,
					'reply_markup' => $keyb, 
					'text' => $return_all
			);
		$result = $telegram->sendMessage($content);
		include('RPG_delete.php');

	}
	
}