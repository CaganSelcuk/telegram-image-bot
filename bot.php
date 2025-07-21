<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';
use Intervention\Image\ImageManagerStatic as Image;
try {
    Image::configure(['driver' => 'imagick']);
} catch (Exception $e) {
    file_put_contents("logs/send.log", " Imagick error: " . $e->getMessage() . "\n", FILE_APPEND);
}

if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}

if (!is_dir('downloads')) mkdir('downloads', 0777, true);
if (!is_dir('uploads')) mkdir('uploads', 0777, true);
if (!is_dir('logs')) mkdir('logs', 0777, true);

$token = '7755742272:AAEUG86t7ZiqeQhaB3OFp6sPiQrKnQJA794';
$apiURL = "https://api.telegram.org/bot$token/";

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) exit();

file_put_contents("logs/debug.log", print_r($update, true), FILE_APPEND);

$chat_id = $update["message"]["chat"]["id"] ?? null;
$message = $update["message"]["text"] ?? null;

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

if ($message === '/start') {
    sendMessage($chat_id, " Welcome! Please send an image and choose what to do with it.");
    exit();
}

if (isset($update["message"]["photo"])) {
    $photos = $update["message"]["photo"];
    $file_id = end($photos)["file_id"];

    $file_info = json_decode(file_get_contents($apiURL . "getFile?file_id=$file_id"), true);
    $file_path = $file_info["result"]["file_path"] ?? null;

    if ($file_path) {
        $download_url = "https://api.telegram.org/file/bot$token/$file_path";
        $local_path = "downloads/" . basename($file_path);
        file_put_contents($local_path, file_get_contents($download_url));
        clearstatcache();          
        sleep(1);   

        if (!file_exists($local_path) || filesize($local_path) === 0) {
            sendMessage($chat_id, " Image could not be downloaded or is corrupted.");
            file_put_contents("logs/send.log", " File not downloaded or empty: $local_path\n", FILE_APPEND);
            exit();
        }

        sendMessage($chat_id, " Image received. Please choose an action:", [
            [['text' => 'Crop 512x512']],
            [['text' => 'Convert to black and white']],
            [['text' => 'Save as PNG']],
            [['text' => 'Save as JPG']],
            [['text' => 'Save as TIFF']]
        ]);
    }
    exit();
}

if ($message && $chat_id) {
    $files = glob("downloads/*");
    usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
    $lastFile = $files[0] ?? null;

    file_put_contents("logs/send.log", "🧪 Command: $message\n", FILE_APPEND);

    if (!$lastFile) {
        sendMessage($chat_id, " Please send an image first.");
        exit();
    }

    try {
        $image = Image::make(realpath($lastFile));
    } catch (Exception $e) {
        sendMessage($chat_id, " Failed to process image: " . $e->getMessage());
        file_put_contents("logs/send.log", " Image::make error: " . $e->getMessage() . "\n", FILE_APPEND);
        exit();
    }

    $outputPath = '';
    $msg = trim($message);
    file_put_contents("logs/send.log", "✉️ Received message: [$msg]\n", FILE_APPEND);

    if (strpos($msg, 'Crop 512x512') !== false) {
        $image->fit(512, 512);
        $outputPath = 'uploads/cropped_' . time() . '.jpg';
    } elseif (strpos($msg, 'Convert to black and white') !== false) {
        $image->greyscale();
        $outputPath = 'uploads/gray_' . time() . '.jpg';
    } elseif (strpos($msg, 'Save as PNG') !== false) {
        $image->encode('png');
        $outputPath = 'uploads/converted_' . time() . '.png';
    } elseif (strpos($msg, 'Save as JPG') !== false) {
        $image->encode('jpg');
        $outputPath = 'uploads/converted_' . time() . '.jpg';
    } elseif (strpos($msg, 'Save as TIFF') !== false) {
        $image->encode('tiff');
        $outputPath = 'uploads/converted_' . time() . '.tiff';
    }

    if ($outputPath) {
        $image->save($outputPath);

        if (!file_exists($outputPath)) {
            file_put_contents("logs/send.log", "Image could not be saved: $outputPath\n", FILE_APPEND);
        } else {
            file_put_contents("logs/send.log", "Image saved: $outputPath\n", FILE_APPEND);
        }

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
    } else {
        sendMessage($chat_id, "Unknown command.");
    }
}