<?php

$path = 'D:/Pictures/Fotos/';
//$path = 'D:/Pictures/27867555_1425807754195737_910792024842162316_n.jpg';
//$path = 'C:/Users/Raven/Dropbox/FormaRegistroASDRI.pdf';

$cdir = scandir($path);

foreach ($cdir as $filename) {
    if (in_array($filename, [".", "..", "Thumbs.db", 'desktop.ini']) || is_dir($path . $filename)) {
        continue;
    }
    $handle = fopen($path . $filename, 'r');
    $files[$path . $filename] = fstat($handle)['size'];
}

asort($files);
error_log(print_r($files, true));
$prevSize = 0;
$rptImg = [];
foreach ($files as $filename => $size) {
    if ($prevSize != $size) {
        $mainImg = new Imagick();
        $handle = fopen($filename, 'r');
        $mainImg->readImageFile($handle);
        $mainWidth = $mainImg->getImageWidth();
        $mainHeight = $mainImg->getImageHeight();
    } else {
        $secImg = new Imagick();
        $handle = fopen($filename, 'r');
        $secImg->readImageFile($handle);
        $secWidth = $secImg->getImageWidth();
        $secHeight = $secImg->getImageHeight();

        if ($secWidth != $mainWidth || $secHeight != $mainHeight) {
//            $diffs[] = $value;
            continue;
        }
        for ($x = 0; $x < $secWidth; $x++) {
            for ($y = 0; $y < $secHeight; $y++) {
                if ($mainImg->getImagePixelColor($x, $y)->getColor(false) != $secImg->getImagePixelColor($x, $y)->getColor(false)) {
//                    $diffs[] = $value;
                    break 2;
                }
            }
        }
        $rptImg[] = $filename;
    }
    fclose($handle);
    $prevSize = $size;
}

error_log(print_r($rptImg, true));
foreach ($rptImg as $filename) {
    unlink($filename);
}

function urlIsImage($url) {
    $ctx = stream_context_create(['http' => ['method' => 'HEAD']]);
    $fp = @fopen($url, 'rb', false, $ctx);
    if (!$fp) { // Problem with url
        return false;
    }
    $meta = stream_get_meta_data($fp);
    if ($meta === false) { // Problem reading data from url
        fclose($fp);
        return false;
    }
    $wrapper_data = $meta["wrapper_data"];
    if (is_array($wrapper_data)) {
        foreach (array_keys($wrapper_data) as $hh) {
            if (substr($wrapper_data[$hh], 0, 19) == "Content-Type: image") {
                fclose($fp);
                return true;
            }
        }
    }
    fclose($fp);
    return false;
}

function pathIsImage($path){
    return is_array(getimagesize($path));
}