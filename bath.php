<?php
require('Telegram.php');

$bot_id = "252491293:AAGJe0CThTpjkBBWAdRVcHB2zY0B3zZs_uU";
$telegram = new Telegram($bot_id);
$text = $telegram->Text();
$chat_id = $telegram->ChatID();
$data = $telegram->getData();
$message_id = $data['message']['message_id'];
//$callback_query = $telegram->Callback_Query();

function curl_get_contents($url,$timeout) { 
	$curlHandle = curl_init(); 
	curl_setopt( $curlHandle , CURLOPT_URL, $url ); 
	curl_setopt( $curlHandle , CURLOPT_RETURNTRANSFER, 1 ); 
	curl_setopt( $curlHandle , CURLOPT_TIMEOUT, $timeout ); 
	$result = curl_exec( $curlHandle ); 
	curl_close( $curlHandle ); 
	return $result; 
}

// 连接数据库
$dbcon = pg_connect('host=ec2-184-73-167-43.compute-1.amazonaws.com port=5432 dbname=d6sqqclb90mkt3 user=
veosnlmajlloas password=
c1bb3c279f83f6f79e54f4848e31a135a47a7fb9adf850a7860727e0622714d9');
if ( $dbcon === FALSE )
{
	exit;
}

// 第一次创建表
// bath_monster -- type  /    hp     /   remain   /  monster_id   /  attacker_username /    died    /  attacker_name /
//           varchar(10) /   (int)   /   (int)    /    (int)      /    varchar(50)     /  (boolean) /	varchar(50)  /
//               monster /    200    /    100     /     99        /       123456       /    true    /    first_name  /
//                 boss  /    3000   /    2000    /      1        /         null       /    false   /      null      /
//
// bath_user --    type  /    hp     /  strength  /  level     /   username    /   name       /
//           varchar(10) /  (bigint) /  (bigint)  /  (biginit) /  varchar(50)  /  varchar(50) /
//                yuusya /    10     /     20     /    1       /     aaaaa     /   first_name /
//
// bath_data --    power   /   down_hp_day  /   id   /
//               (bigint)  /    (bigint)    /  (int) /
//               200000000 /     10000      /    1   /

// TESTING!
include('part/test.php');

// 次数统计 & 快速回复
include('part/main.php');

// 一言
include('part/hitokoto.php');

// 随机同人志封面
include('part/rand_doujinshi_pic.php');

// Danbooru 图片搜索
include('part/danbooru_search.php');

// 服务器运行状态
//include('part/servers_status.php');

// RPG
include('part/RPG.php');

?>
