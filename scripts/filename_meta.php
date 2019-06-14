<?php

require_once('../libraries/getID3-1.9.15/getid3/getid3.php');

$getID3 = new getID3;

$folder = 'D:/Music/failFiles';

// get all files in folder
$cdir = scandir($folder);
foreach ($cdir as $filename) {
    
    //ignore system folders (same and back folders)
    if (in_array($filename, [".", ".."])) {
        continue;
    }

    //get file metadata
    $fullpath = $folder . '/' . $filename;
    $meta = $getID3->analyze($fullpath);

    //rearrange file metadata in friendly form
    foreach ($meta['tags'] as $type => $values) {
        foreach ($values as $key => $value) {
            $name = $value[0];
            $data[$key] = $name;
        }
    }

    //copy renamed file to folder and delete current file
    if (!empty($data['artist']) && !empty($data['title'])) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        $newFilename = 'D:/Music/' . $data['artist'] . ' - ' . $data['title'] . '.' . $ext;
        copy($fullpath, $newFilename);
        unlink($fullpath);
    }

    unset($data);
}

