<?php

require_once 'functions.php';
require_once 'image_processor.php';

$content = file_get_contents("php://input");
$update = json_decode($content, true);
if (!$update) exit();

file_put_contents("logs/debug.log", "UPDATE:\n" . print_r($update, true) . "\n", FILE_APPEND);

$chat_id = $update["message"]["chat"]["id"] ?? null;
$fromBot = $update["message"]["from"]["is_bot"] ?? false;

if (isset($update["message"]["text"]) && !$fromBot && $chat_id) {
    $message = trim($update["message"]["text"]);

    if ($message === '/start') {
        sendMessage($chat_id, "Welcome! Please send an image and choose what to do with it.");
        exit();
    }

    $files = glob("downloads/{$chat_id}_*");
    if (empty($files)) {
        sendMessage($chat_id, "Please send an image first.");
        exit();
    }

    $lastFile = $files[0];
    $imageHash = @file_get_contents("logs/imagehash_{$chat_id}.txt");

    $outputPath = processImageCommand($chat_id, $message, $lastFile, $imageHash);

    if ($outputPath && file_exists($outputPath)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiURL . "sendDocument");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'chat_id' => $chat_id,
            'document' => new CURLFile(realpath($outputPath))
        ]);
        curl_exec($ch);
        curl_close($ch);
        file_put_contents("logs/send.log", "Sent: $outputPath\n", FILE_APPEND);
    }
    exit();
}

// Fotoğraf geldiğinde
if (isset($update["message"]["photo"]) && !$fromBot && $chat_id) {
    $photos = $update["message"]["photo"];
    $file_id = end($photos)["file_id"];
    $file_info = json_decode(file_get_contents($apiURL . "getFile?file_id=$file_id"), true);
    $file_path = $file_info["result"]["file_path"] ?? null;

    if ($file_path) {
        array_map('unlink', glob("downloads/{$chat_id}_*"));
        $download_url = "https://api.telegram.org/file/bot$token/$file_path";
        $local_path = "downloads/{$chat_id}_" . basename($file_path);
        file_put_contents($local_path, file_get_contents($download_url));

        $imageData = file_get_contents($local_path);
        $imageHash = md5($imageData);
        file_put_contents("logs/imagehash_{$chat_id}.txt", $imageHash);

        sendMessage($chat_id, "Image received. Please choose an action:", [
            [['text' => 'Crop 512x512']],
            [['text' => 'Convert to black and white']],
            [['text' => 'Save as PNG']],
            [['text' => 'Save as JPG']],
            [['text' => 'Save as TIFF']]
        ]);
    }

    exit();
}