<?php

if ($text === "/pic" || $text === "/pic@gumtakebath_bot") 
{
	$reply = "输入 /pic [指定tag] 来搜索图片";
	$content = array('chat_id' => $chat_id, 'text' => $reply);
	$telegram->sendMessage($content);
}


if ( substr_count($text, "/pic ") === 1 ) 
{
	$tag = substr($text, 5);
	$get = curl_get_contents("http://danbooru.donmai.us/posts.json?tags=$tag",9);
	if ( $get === false ) 
	{
		$return = "网络错误。";
		$content = array('chat_id' => $chat_id, 'text' => $return);
		$telegram->sendMessage($content);
	} 
	elseif ( $get === "[]" ) 
	{
		$return = "无搜索结果。";
		$content = array('chat_id' => $chat_id, 'text' => $return);
		$telegram->sendMessage($content);
	} 
	else 
	{
		$json = json_decode($get, true);
		$rand = rand(0,count($json));
		$pic = $json[$rand]['large_file_url'];
		$url = "http://danbooru.donmai.us$pic";
		file_put_contents(substr($pic,-6), curl_get_contents($url,9));
		$img = curl_file_create(substr($pic,-6));
		$content = array('chat_id' => $chat_id, 'photo' => $img );
		$telegram->sendPhoto($content);
	};
}