<?php

set_time_limit(0);
$path = 'D:/Music';

error_log('start ' . __FILE__);
removeEmptyFolders($path);

function removeEmptyFolders($path) {
    $cdir = scandir($path);

    if (($key = array_search('.', $cdir)) !== false) {
        unset($cdir[$key]);
    }
    if (($key = array_search('..', $cdir)) !== false) {
        unset($cdir[$key]);
    }

    if (!empty($cdir)) {
        foreach ($cdir as $value) {
            if (is_dir($path . DIRECTORY_SEPARATOR . $value)) {
                removeEmptyFolders($path . DIRECTORY_SEPARATOR . $value);
            } 
        }
    } else {
        rmdir($path);
        error_log('removed folder ' . $path);
    }
}

error_log('end ' . __FILE__);
