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
		$url = "https://sslapi.hitokoto.cn/?text=1";
		$html = file_get_contents($url);
		$content = array('chat_id' => $chat_id, 'text' => $html);
		$telegram->sendMessage($content);
	}

	if ($text == "/gumisbad" || $text == "/gumisbad@gumtakebath_bot") {
		$zone1 = strtotime(date("y-m-d h:i:s"));
		$zone2 = strtotime("1996-3-31 00:00:00");
		$gumtime = ceil(($zone1-$zone2)/86400);
		$reply = "Gum 已经有 $gumtime 天没有洗澡了。";
		$content = array('chat_id' => $chat_id, 'text' => $reply);
		$telegram->sendMessage($content);
	}
	
	if ($text == "/doujin_pic_random" || $text == "/doujin_pic_random@gumtakebath_bot") {
		$num = rand(1, 976400);
		$nump = floor($num/2000);
		$url = "http://img.doujinshi.org/big/$nump/$num.jpg";
		file_put_contents("$num.jpg", file_get_contents($url));
		$img = curl_file_create("$num.jpg",'image/jpeg');
		$content = array('chat_id' => $chat_id, 'photo' => $img );
		$telegram->sendPhoto($content);
	
		//$url = "http://img.doujinshi.org/big/0/200.jpg";
		//file_put_contents("/pic/200.jpg", file_get_contents($url));
		//$img = curl_file_create('200.jpg','image/jpeg');
		//$content = array('chat_id' => $chat_id, 'photo' => $img );
		//$telegram->sendPhoto($content);		
	}
	
	if ($text == "/server_status" || $text == "/server_status@gumtakebath_bot") {
		$gum = "http://cache.www.gametracker.com/server_info/115.159.120.160:27015/b_560_95_1.png";
		$yu = "http://cache.www.gametracker.com/server_info/119.254.111.244:27015/b_560_95_1.png";
		file_put_contents("b_560_95_1.png", file_get_contents($gum));
		$img = curl_file_create("b_560_95_1.png",'image/png');
		$content = array('chat_id' => $chat_id, 'photo' => $img );
		$telegram->sendPhoto($content);
		file_put_contents("b_560_95_1.png", file_get_contents($yu));
		$img = curl_file_create("b_560_95_1.png",'image/png');
		$content = array('chat_id' => $chat_id, 'photo' => $img );
		$telegram->sendPhoto($content);
	}
}

?>
