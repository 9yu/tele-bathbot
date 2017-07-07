<?php

if ($text == "/server_status" || $text == "/server_status@gumtakebath_bot") 
{
	$s1 = "http://cache.www.gametracker.com/server_info/115.159.120.160:27015/b_560_95_1.png";
	$s2 = "http://cache.www.gametracker.com/server_info/119.254.111.244:27015/b_560_95_3.png";
	$s3 = "http://cache.www.gametracker.com/server_info/119.254.111.244:27016/b_560_95_2.png";
	
	file_put_contents("s1.png", file_get_contents($s1));
	file_put_contents("s2.png", file_get_contents($s2));
	file_put_contents("s3.png", file_get_contents($s3));
	
	$img = curl_file_create("s1.png",'image/png');
	$content = array('chat_id' => $chat_id, 'photo' => $img );
	$telegram->sendPhoto($content);
	
	$img = curl_file_create("s2.png",'image/png');
	$content = array('chat_id' => $chat_id, 'photo' => $img );
	$telegram->sendPhoto($content);
	
	$img = curl_file_create("s3.png",'image/png');
	$content = array('chat_id' => $chat_id, 'photo' => $img );
	$telegram->sendPhoto($content);
}