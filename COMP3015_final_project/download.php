<?php
if (count($_GET) > 0 && isset($_GET['filename'])) {
    $filename = $_GET['filename'];
    $filepath = 'uploads/' . $filename;
    if (!file_exists($filepath)) {
        echo "file not found: $filepath";
        exit();
    }
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $image = imagecreatefromjpeg($filepath);
    imagejpeg($image);
}
?>