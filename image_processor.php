<?php

use Intervention\Image\ImageManagerStatic as Image;

function processImageCommand($chat_id, $command, $lastFile, $imageHash) {
    $outputPath = '';
    $lockFile = '';

    try {
        $image = Image::make(realpath($lastFile));
    } catch (Exception $e) {
        sendMessage($chat_id, " Failed to process image: " . $e->getMessage());
        return null;
    }

    if (strpos($command, 'Crop 512x512') !== false) {
        $image->fit(512, 512);
        $outputPath = "uploads/cropped_" . time() . ".jpg";
    } elseif (strpos($command, 'Convert to black and white') !== false) {
        $image->greyscale();
        $outputPath = "uploads/gray_" . time() . ".jpg";
    } elseif (strpos($command, 'Save as PNG') !== false) {
        $image->encode('png');
        $outputPath = "uploads/converted_" . time() . ".png";
    } elseif (strpos($command, 'Save as JPG') !== false) {
        $image->encode('jpg');
        $outputPath = "uploads/converted_" . time() . ".jpg";
    } elseif (strpos($command, 'Save as TIFF') !== false) {
        if (!$imageHash) return null;
        $lockFile = "logs/lock_{$chat_id}_{$imageHash}_tiff.txt";

        if (file_exists($lockFile)) return null;

        $image->encode('tiff');
        $outputPath = "uploads/converted_" . time() . ".tiff";
        file_put_contents($lockFile, "locked");
    }

    if ($outputPath) {
        $image->save($outputPath);
    }

    return $outputPath;
}