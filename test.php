<?php

$url = "http://api.hitokoto.cn/";
$json = file_get_contents($url);
$de_json = json_decode($json, true);
$hitokoto = $de_json['hitokoto'];
$from = $de_json['from'];
$reply = $hitokoto + "    ——" + $from;
print $reply;

?>