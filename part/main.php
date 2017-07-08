<?php

if ($text === "/ifgumhastakenabath" || $text === "/ifgumhastakenabath@gumtakebath_bot") 
{
	$reply = "Gum 今天洗澡了吗？";
	// 不会回复 at 人
	$content = array('chat_id' => $chat_id, 'text' => $reply);
	$telegram->sendMessage($content);
	sleep(2);
	$reply = "当然没有洗 ╮(╯▽╰)╭";
	$content = array('chat_id' => $chat_id, 'text' => $reply);
	$telegram->sendMessage($content);
}

if ($text === "/gumisbad" || $text === "/gumisbad@gumtakebath_bot") 
{
	date_default_timezone_set("Asia/Shanghai");
	$zone1 = strtotime(date("y-m-d h:i:s"));
	$zone2 = strtotime("1996-3-31 00:00:00");
	$gumtime = ceil(($zone1-$zone2)/86400);

	$heta_power_a_day_date = date('Y.m.d');
	if( file_exists("../data.heta_power_a_day.$heta_power_a_day_date.cache.json") )
	{
		$heta_power_a_day = json_decode(file_get_contents("../data.heta_power_a_day.$heta_power_a_day_date.cache.json"),true);
	}
	else
	{
		$heta_power_a_day = 0;
	}
	

	$db_search = "SELECT * FROM bath_data WHERE id = 1";
	$row = pg_fetch_array($db_search);
	$heta_power = (int)$row['heta_power'];

	$power = $gumtime * 600;

	$jian = 600 - $heta_power_a_day;
	if( $jian <= 0 )
	{
		$jian_behave = $heta_power_a_day - 600;
		$reply = "Gum 的脏力点数为 " . $heta_power . "点（今日下降 ". $jian_behave ."），已经有 " . $gumtime . " 天没有洗澡了。";
	}
	else
	{
		$jian_behave = 600 - $heta_power_a_day;
		$reply = "Gum 的脏力点数为 " . $heta_power . "点（今日增加 ". $jian_behave ."），已经有 " . $gumtime . " 天没有洗澡了。";
	}

	$content = array('chat_id' => $chat_id, 'text' => $reply);
	$telegram->sendMessage($content);
}