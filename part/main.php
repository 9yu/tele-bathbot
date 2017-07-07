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
	$reply = "Gum 已经有 $gumtime 天没有洗澡了。";
	$content = array('chat_id' => $chat_id, 'text' => $reply);
	$telegram->sendMessage($content);
}