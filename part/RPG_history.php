<?php

if( file_exists("data.history.$username.cache.json") )
{
	$history = json_decode( file_get_contents("data.history.$username.cache.json"), true);
	if( count($history) >= 10 )
	{
		// 0 -7 共 8 条
		$return_all = "";
		$return_all .= $history[count($history) - 1] . "\n……\n";
		for ( $i = 7 ; $i >= 0; $i -- )  
		{ 
			$return_all .= $history[$i] . "\n";
		}
		$return_all .= " * " . $return_text;
	}
	else
	{
		$return_all = "";
		for ( $i = count($history) - 1;  $i >= 0 ;  $i -- ) 
		{ 
			$return_all .= $history[$i] . "\n";
		}
		$return_all .= " * " . $return_text;
	}

	array_unshift($history, $return_text);

}
else
{
	$return_all = $return_text;
	$history = array($return_text);
}

file_put_contents("data.history.$username.cache.json", json_encode($history));