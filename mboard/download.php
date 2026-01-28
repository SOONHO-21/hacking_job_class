<?php
    $file_type = $_GET["file_type"];
    $file_copied = basename($_GET["file_copied"]);  // 취약점 패치 버전
    $safe_dir = "/var/www/uploads/data/";
    $file_path = $safe_dir.$file_copied;

    if(file_exists($file_path) && strpos(realpath($file_path), realpath($safe_dir)) === 0) {    // 취약점 패치 버전
        header("Content-Type: application/octet-stream");
        header('Content-Description: File Transfer');
        header("Content-Disposition: attachment; filename=$file_copied");
        header("Content-Transfer-Encoding:binary");
        header("Cache-Control:cache,must-revalidate");
        header("Pragma: public");
        header("Content-Length: " . filesize($file_path));
        flush();
        readfile($file_path);
        die();
    }
?>