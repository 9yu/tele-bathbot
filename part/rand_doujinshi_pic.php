<?php

if ($text == "/doujin_pic_random" || $text == "/doujin_pic_random@gumtakebath_bot") 
{
	$num = rand(930000, 982528);
	$nump = floor($num/2000);
	$url = "http://img.doujinshi.org/big/$nump/$num.jpg";
	$get = curl_get_contents($url,9);
	if ( $get === false ) 
	{
		$content = array('chat_id' => $chat_id, 'text' => "_(:з」∠)_    ——bot");
		$telegram->sendMessage($content);
	} 
	else 
	{
		file_put_contents("$num.jpg", curl_get_contents($url,5));
		$img = curl_file_create("$num.jpg",'image/jpeg');
		$content = array('chat_id' => $chat_id, 'photo' => $img );
		$telegram->sendPhoto($content);
	};


	//$url = "http://img.doujinshi.org/big/0/200.jpg";
	//file_put_contents("/pic/200.jpg", file_get_contents($url));
	//$img = curl_file_create('200.jpg','image/jpeg');
	//$content = array('chat_id' => $chat_id, 'photo' => $img );
	//$telegram->sendPhoto($content);		
}