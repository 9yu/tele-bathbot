<?php
/**
 * Telegram Bot example.
 * @author Gabriele Grillo <gabry.grillo@alice.it>
 */
include("Telegram.php");

// Set the bot TOKEN
$bot_id = "252491293:AAGJe0CThTpjkBBWAdRVcHB2zY0B3zZs_uU";
// Instances the class
$telegram = new Telegram($bot_id);

/* If you need to manually take some parameters
*  $result = $telegram->getData();
*  $text = $result["message"] ["text"];
*  $chat_id = $result["message"] ["chat"]["id"];
*/

// Take text and chat_id from the message
$text = $telegram->Text();
$chat_id = $telegram->ChatID();

function curl_get_contents($url,$timeout) { 
	$curlHandle = curl_init(); 
	curl_setopt( $curlHandle , CURLOPT_URL, $url ); 
	curl_setopt( $curlHandle , CURLOPT_RETURNTRANSFER, 1 ); 
	curl_setopt( $curlHandle , CURLOPT_TIMEOUT, $timeout ); 
	$result = curl_exec( $curlHandle ); 
	curl_close( $curlHandle ); 
	return $result; 
}

/*
function hitokoto() {
	$rand = chr(rand(97,100));
	$get = curl_get_contents("http://api.hitokoto.cn/?c=$rand");
	if ( $get === false ) {
		$content = "503";
		$from = "";
	} else {
		$de_json = json_decode($get, true);
		$content = $de_json['hitokoto'];
		$from = $de_json['from'];
	};
	$return = $content . '    ——'  . $from;
	return $return;
}*/


// Check if the text is a command
if(!is_null($text) && !is_null($chat_id)){
	if ($text == "/ifgumhastakenabath" || $text == "/ifgumhastakenabath@gumtakebath_bot") {
		$reply = "Gum 今天洗澡了吗？";
		// 不会回复 at 人
		$content = array('chat_id' => $chat_id, 'text' => $reply);
		$telegram->sendMessage($content);
		sleep(2);
		$reply = "当然没有洗 ╮(╯▽╰)╭";
		$content = array('chat_id' => $chat_id, 'text' => $reply);
		$telegram->sendMessage($content);
	}
	
	if ($text == "/hitokoto" || $text == "/hitokoto@gumtakebath_bot") {
		$rand = chr(rand(97,100));
		$get = curl_get_contents("http://api.hitokoto.cn/?c=$rand",9);
		if ( $get === false ) {
			$return = "_(:з」∠)_    ——bot";
		} else {
			$de_json = json_decode($get, true);
			$content = $de_json['hitokoto'];
			$from = $de_json['from'];
			$return = $content . '    ——'  . $from;
		};
		$content = array('chat_id' => $chat_id, 'text' => $return);
		$telegram->sendMessage($content);
	}

	if ($text == "/gumisbad" || $text == "/gumisbad@gumtakebath_bot") {
		date_default_timezone_set("Asia/Shanghai");
		$zone1 = strtotime(date("y-m-d h:i:s"));
		$zone2 = strtotime("1996-3-31 00:00:00");
		$gumtime = ceil(($zone1-$zone2)/86400);
		$reply = "Gum 已经有 $gumtime 天没有洗澡了。";
		$content = array('chat_id' => $chat_id, 'text' => $reply);
		$telegram->sendMessage($content);
	}
	
	if ($text == "/doujin_pic_random" || $text == "/doujin_pic_random@gumtakebath_bot") {
		$num = rand(930000, 982528);
		$nump = floor($num/2000);
		$url = "http://img.doujinshi.org/big/$nump/$num.jpg";
		$get = curl_get_contents($url,9);
		if ( $get === false ) {
			$content = array('chat_id' => $chat_id, 'text' => "_(:з」∠)_    ——bot");
			$telegram->sendMessage($content);
		} else {
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


	if ( substr_count($text, "/pic ") == 1 ) {
		$tag = substr($text, 5);
		$get = curl_get_contents("http://danbooru.donmai.us/posts.json?tags=$tag",9);
		if ( $get === false ) {
			$return = "网络错误。";
			$content = array('chat_id' => $chat_id, 'text' => $return);
			$telegram->sendMessage($content);
		} elseif ( $get === "[]" ) {
			$return = "无搜索结果。";
			$content = array('chat_id' => $chat_id, 'text' => $return);
			$telegram->sendMessage($content);
		} else {
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
	
	/*if ($text == "/server_status" || $text == "/server_status@gumtakebath_bot") {
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
	}*/
}

?>
