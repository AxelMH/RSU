<html>
    <?php
    /**
     * Compare images in folder pixel by pixel
     */
    $path = '../compare/';
    $cdir = scandir($path);

    foreach ($cdir as $value) {
        if (in_array($value, [".", ".."])) {
            continue;
        }
//        $imagick = new Imagick();
//        $handle = fopen($path . $value, 'r');
//        $imagick->readImageFile($handle);
//        $width = $imagick->getImageWidth();
//        $height = $imagick->getImageHeight();

        if (!isset($mainImg)) {
//            for ($x = 0; $x < $width; $x++) {
//                for ($y = 0; $y < $height; $y++) {
//                    $main[$x][$y] = $imagick->getImagePixelColor($x, $y)->getColor(false);
//                }
//            }
            echo '<img src="' . $path . $value . '"><br>';

            $mainImg = new Imagick();
            $handle = fopen($path . $value, 'r');
            $mainImg->readImageFile($handle);
            $mainWidth = $mainImg->getImageWidth();
            $mainHeight = $mainImg->getImageHeight();
        } else {
            $secImg = new Imagick();
            $handle = fopen($path . $value, 'r');
            $secImg->readImageFile($handle);
            $secWidth = $secImg->getImageWidth();
            $secHeight = $secImg->getImageHeight();

            if ($secWidth != $mainWidth || $secHeight != $mainHeight) {
                $diffs[] = $value;
                continue;
            }
            for ($x = 0; $x < $secWidth; $x++) {
                for ($y = 0; $y < $secHeight; $y++) {
                    if ($mainImg->getImagePixelColor($x, $y)->getColor(false) != $secImg->getImagePixelColor($x, $y)->getColor(false)) {
                        $diffs[] = $value;
                        break 2;
                    }
                }
            }
        }
        fclose($handle);
    }

    if (empty($diffs)) {
        echo 'Todas las imagenes son iguales.';
    } else {
        foreach ($diffs as $key => $diff) {
            echo $key . '. ' . $diff . ' es diferente.<br>';
        }
    }
    ?>
</html>