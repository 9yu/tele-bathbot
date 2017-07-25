<?php

if ( substr_count($text, "/crystal ") === 1 ) 
{

function udpGet($sendMsg = '', $ip = gethostbyname('us-la-a.server.yunet.work'), $port = '9000'){
    $handle = stream_socket_client("udp://{$ip}:{$port}", $errno, $errstr);
    if( !$handle ){
        die("ERROR: {$errno} - {$errstr}\n");
    }
    fwrite($handle, $sendMsg."\n");
    $result = fread($handle, 1024);
    fclose($handle);
    return $result;
}

	if ( strlen($text) > 12 )
	{
		$param = substr(trim($text), 9);
		$param = explode(' ', $param);
	}

	if ( $param[0] === '9' )
	{

		if ( $param[1] === 'ping' )
		{
			$send = array(
			 'api-key' = '9',
			 'type' = 'ping'
				);
			$send = json_encode($send);
			$return = udpGet($send);
			$content = array('chat_id' => $chat_id, 'text' => $return);
			$telegram->sendMessage($content);
		}

		if ( $param[1] === 'add' )
		{
			$send = array(
			 'api-key' = '9',
			 'type' = 'add',
			 'port' = $param[2] // port
		 'password' = $param[3] // pass 
				);
			$send = json_encode($send);
			$return = udpGet($send);
			$content = array('chat_id' => $chat_id, 'text' => $return);
			$telegram->sendMessage($content);
		}

		if ( $param[1] === 'remove' )
		{
			$send = array(
			 'api-key' = '9',
			 'type' = 'remove',
			 'port' = $param[2] // port
				);
			$send = json_encode($send);
			$return = udpGet($send);
			$content = array('chat_id' => $chat_id, 'text' => $return);
			$telegram->sendMessage($content);
		}


	}

}