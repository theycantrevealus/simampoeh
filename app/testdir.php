<?php
    $target = '../../../berkas/ktp';
    if(file_exists($target) && is_dir($target)) {
        if(is_writable($target)) {
            echo 'bisa nulis dan ada';
        } else {
            echo 'g bisa nulis tapi ada';
        }
    } else {
        echo 'g ada';
    }
?>