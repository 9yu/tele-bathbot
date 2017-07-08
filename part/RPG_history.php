<?php

if( file_exists("data.history.$username.cache.json") )
{
	// 有历史记录
	$history = json_decode( file_get_contents("data.history.$username.cache.json"), true);
	// 保存 16 个数据
	if ( count($history) > 8 )
	{
		// 1 - 7
		$return_all = $history[count($history) - 1] . "\n …… \n" . $history[6] . "\n" . $history[5] . "\n" . $history[4] . "\n" . $history[3] . "\n" . $history[2] . "\n" . $history[1] . "\n" . $history[0] . "\n * " . $return_text;
	}
	else
	{   // 最多 7 个
		$return_all = "";
		//for ( $i = count($history) - 1; $i > -1; $i -- ) { 
		//	$return_all .= $history[$i] . "\n"; 
		//}
		$return_all = $return_all . ' * ' . $return_text;
	}

	// 抛弃旧记录
	if( count($history) = 16 )
	{
		unset($history[15]);
	}

	// 写入 json
	array_unshift($history, $return_text);

}
else
{
	$return_all = $return_text;
	$history = array($return_text);
}

file_put_contents("data.history.$username.cache.json", json_encode($history));