<?php

// 删除上次 id
if( is_file("data.delete.$username.cache.json") )
{
	$delete = json_decode(file_get_contents("data.delete.$username.cache.json"), true);
	$telegram->deleteMessage(array( 'chat_id' => $delete['chat_id'], 'message_id' => $delete['message_id'] ));
}

// 记录下次删除 id
$delete = array(
	'chat_id' => $chat_id,
 'message_id' => $result['result']['message_id'] 
	);
file_put_contents("data.delete.$username.cache.json", json_encode($delete));