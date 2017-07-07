<?php

if ($text == "/hitokoto" || $text == "/hitokoto@gumtakebath_bot") 
{
	$rand = chr(rand(97,100));
	$get = curl_get_contents("http://api.hitokoto.cn/?c=$rand",9);
	if ( $get === false ) 
	{
		$return = "_(:з」∠)_    ——bot";
	} 
	else 
	{
		$de_json = json_decode($get, true);
		$content = $de_json['hitokoto'];
		$from = $de_json['from'];
		$return = $content . '    ——'  . $from;
	};
	$content = array('chat_id' => $chat_id, 'text' => $return);
	$telegram->sendMessage($content);
}