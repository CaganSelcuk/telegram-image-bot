<?php
$path = 'downloads/file_8.jpg'; // Gerçek dosya adını yaz

if (!file_exists($path)) {
    echo "Dosya yok.";
    exit;
}

try {
    $im = new Imagick($path);
    echo "✅ Imagick resmi başarıyla açtı.";
} catch (Exception $e) {
    echo "❌ Imagick hata verdi: " . $e->getMessage();
}