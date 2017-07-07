<?php

$content = array('chat_id' => $chat_id, 'text' => 'ok');
$result = $telegram->sendMessage($content);