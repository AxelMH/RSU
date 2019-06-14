<?php

set_time_limit(0);
$oldPath = 'J:/otho/Music';
$newPath = 'D:/Music';

error_log('start ' . __FILE__);
copyFiles($oldPath, $newPath);

function copyFiles($oldPath, $newPath) {
    $cdir = scandir($oldPath);

    foreach ($cdir as $key => $value) {
        if (!in_array($value, [".", ".."])) {
            if (is_dir($oldPath . DIRECTORY_SEPARATOR . $value)) {
                if (!is_dir($newPath . DIRECTORY_SEPARATOR . $value)) {
                    mkdir($newPath . DIRECTORY_SEPARATOR . $value);
                }
                copyFiles($oldPath . DIRECTORY_SEPARATOR . $value, $newPath . DIRECTORY_SEPARATOR . $value);
            } else {
                copy($oldPath . DIRECTORY_SEPARATOR . $value, $newPath . DIRECTORY_SEPARATOR . $value . '.mp3');
                if (is_file($newPath . DIRECTORY_SEPARATOR . $value . '.mp3')) {
                    unlink($oldPath . DIRECTORY_SEPARATOR . $value);
                }
            }
        }
    }
    error_log('copied folder ' . $oldPath);
}

error_log('end ' . __FILE__);
