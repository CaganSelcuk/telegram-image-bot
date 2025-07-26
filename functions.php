<?php

function sendMessage($chat_id, $text, $buttons = null) {
    global $apiURL;

    $params = ['chat_id' => $chat_id, 'text' => $text];

    if ($buttons) {
        $params['reply_markup'] = json_encode([
            'keyboard' => $buttons,
            'resize_keyboard' => true
        ]);
    }

    file_get_contents($apiURL . "sendMessage?" . http_build_query($params));
}