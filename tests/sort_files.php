<?php

$path = 'D:/Pictures/Anime & Manga/Rozen Maiden/';
$cdir = scandir($path);

foreach ($cdir as $filename) {
    if (in_array($filename, [".", "..", "Thumbs.db"])) {
        continue;
    }
    $handle = fopen($path . $filename, 'r');
    $files[$path . $filename] = fstat($handle)['size'];
}

asort($files);

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
