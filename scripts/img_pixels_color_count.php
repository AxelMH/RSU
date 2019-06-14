<html>
    <head>
        <title>Bead stuff</title>
    </head>
    <body>
        <form enctype="multipart/form-data"  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input type="file" name="csv_file" id="csv_file" accept="image/*"><br>
            <input type="submit" value="Calcular">
        </form>
        <?php
        if (!empty($_FILES)) {
            //read image file
            $imagick = new Imagick();
            $handle = fopen($_FILES['csv_file']['tmp_name'], 'r');
            $imagick->readImageFile($handle);
            $width = $imagick->getImageWidth();
            $height = $imagick->getImageHeight();
            $totalColors = $imagick->getImageColors();

            $count = 0;
            for ($x = 0; $x < $width; $x++) {
                for ($y = 0; $y < $height; $y++) {
                    $color = $imagick->getImagePixelColor($x, $y)->getColor(false);
                    if ($color['a'] != 0) {
                        if (!isset($colors[$color['r'] . ',' . $color['g'] . ',' . $color['b']])) {
                            $colors[$color['r'] . ',' . $color['g'] . ',' . $color['b']] = 1;
                        } else {
                            $colors[$color['r'] . ',' . $color['g'] . ',' . $color['b']] ++;
                        }
                        $count++;
                    }
                }
            }
            fclose($handle);
            ?>
            <table border="1" style="border-collapse:collapse;">
                <tr>
                    <td colspan="5">Tamaño de la imágen:</td>
                    <td style='text-align:right;'><?= $width ?> x <?= $height ?></td>
                </tr>
                <tr>
                    <td colspan="5">Pixeles totales:</td>
                    <td style='text-align:right;'><?= ($width * $height) ?> </td>
                </tr>
                <tr>
                    <td colspan="5">Total de pixeles de color:</td>
                    <td style='text-align:right;'><?= $count ?></td>
                </tr>
                <tr>
                    <td colspan="5">Colores diferentes:</td>
                    <td style='text-align:right;'><?= count($colors) ?></td>
                </tr>
                <tr>
                    <td style="text-align:center;">R</td>
                    <td style="text-align:center;">G</td>
                    <td style="text-align:center;">B</td>
                    <td style="text-align:center;">Cantidad</td>
                    <td style="text-align:center;">Color</td>
                    <td style="text-align:center;">#</td>
                </tr>
                <?php
                $n = 0;

                uksort($colors, function ($one, $two) {
                    return colorToLum($one) - colorToLum($two);
                });

                $colors = sortByColor($colors);

                foreach ($colors as $color => $quantity) {
                    $n++;
                    //get colors in decimal components for RGB
                    $red = explode(',', $color)[0];
                    $green = explode(',', $color)[1];
                    $blue = explode(',', $color)[2];

                    $hexR = str_pad(dechex($red), 2, '0', STR_PAD_LEFT);
                    $hexG = str_pad(dechex($green), 2, '0', STR_PAD_LEFT);
                    $hexB = str_pad(dechex($blue), 2, '0', STR_PAD_LEFT);
                    echo "<tr>";
                    echo "<td style='text-align:right;'>$red</td>";
                    echo "<td style='text-align:right;'>$green</td>";
                    echo "<td style='text-align:right;'>$blue</td>";
                    echo "<td style='text-align:right;'>$quantity</td>";
                    echo "<td bgcolor='#$hexR$hexG$hexB'></td>";
                    echo "<td style='text-align:right;'>$n</td>";
                    echo "</tr>";
                }

                $newColors = retinex($colors);
                $newColors = sortByColor($newColors);
                $newColors = distanceColors($newColors, 25);
                $newColors = retinex($newColors);


                uksort($newColors, function ($one, $two) {
                    return colorToLum($one) - colorToLum($two);
                });

                $newColors = sortByColor($newColors);
                ?>
            </table>
            Después de hacer consideraciones para colores similares:
            <table border="1" style="border-collapse:collapse;">
                <tr>
                    <td colspan="5">Colores diferentes:</td>
                    <td style='text-align:right;'><?= count($newColors) ?></td>
                </tr>
                <tr>
                    <td style="text-align:center;">R</td>
                    <td style="text-align:center;">G</td>
                    <td style="text-align:center;">B</td>
                    <td style="text-align:center;">Cantidad</td>
                    <td style="text-align:center;">Color</td>
                    <td style="text-align:center;">#</td>
                </tr>
                <?php
                $n = 0;

                foreach ($newColors as $color => $quantity) {
                    $n++;
                    //get colors in decimal components for RGB
                    $red = explode(',', $color)[0];
                    $green = explode(',', $color)[1];
                    $blue = explode(',', $color)[2];

                    $hexR = str_pad(dechex($red), 2, '0', STR_PAD_LEFT);
                    $hexG = str_pad(dechex($green), 2, '0', STR_PAD_LEFT);
                    $hexB = str_pad(dechex($blue), 2, '0', STR_PAD_LEFT);
                    echo "<tr>";
                    echo "<td style='text-align:right;'>$red</td>";
                    echo "<td style='text-align:right;'>$green</td>";
                    echo "<td style='text-align:right;'>$blue</td>";
                    echo "<td style='text-align:right;'>$quantity</td>";
                    echo "<td bgcolor='#$hexR$hexG$hexB'></td>";
                    echo "<td style='text-align:right;'>$n</td>";
                    echo "</tr>";
                }

                //Càlculos costos
                $costoBase = 60;
                $costoBolsa = 40;
                $beadsPorBolsa = 930;


                $basesW = ceil($width / 29);
                $basesH = ceil($height / 29);
                $bases = $basesW * $basesH;
                $totalBases = $bases * $costoBase;
                $cobroBases = $totalBases / 4;

                $totalBeads = round($count / $beadsPorBolsa * $costoBolsa, 2);
                ?>

            </table>
            <h2>Costos:</h2>
            <table border="1" style="border-collapse:collapse;">
                <tr>
                    <td>Bases</td>
                    <td><?= $basesW ?>x<?= $basesH ?>=<?= $bases ?></td>                    
                </tr>
                <tr>
                    <td>Costo Total bases</td>
                    <td><?= $totalBases ?></td>                    
                </tr>
                <tr>
                    <td>Costo considerable bases(1/4)</td>
                    <td><?= $cobroBases ?></td>                    
                </tr>
                <tr>
                    <td>Beads</td>
                    <td><?= $count ?></td>                    
                </tr>
                <tr>
                    <td>Costo Total beads</td>
                    <td><?= $totalBeads ?></td>                    
                </tr>
                <tr>
                    <td>Costo Total</td>
                    <td><?= $totalBeads + $cobroBases ?></td>                    
                </tr>
            </table>           

            <?php
        }
        ?>
    </body>
</html>
<?php

function retinex($colors) {
    foreach ($colors as $color => $qty) {
        $red[] = explode(',', $color)[0];
        $green[] = explode(',', $color)[1];
        $blue[] = explode(',', $color)[2];
    }
    $maxR = max($red);
    $maxG = max($green);
    $maxB = max($blue);

    foreach ($colors as $color => $qty) {
        $newR = round(explode(',', $color)[0] / $maxR * 255);
        $newG = round(explode(',', $color)[1] / $maxG * 255);
        $newB = round(explode(',', $color)[2] / $maxB * 255);

        $newColors["$newR,$newG,$newB"] = $qty;
    }
    return $newColors;
}

function distanceColors($colors, $maxDist) {
    $found = false;
    $n = 0;
    foreach ($colors as $color1 => $qty1) {
        $n++;
        $r1 = explode(',', $color1)[0];
        $g1 = explode(',', $color1)[1];
        $b1 = explode(',', $color1)[2];
        $m = 0;
        foreach ($colors as $color2 => $qty2) {
            $m++;
            if ($color1 == $color2) {
                continue;
            }
            $r2 = explode(',', $color2)[0];
            $g2 = explode(',', $color2)[1];
            $b2 = explode(',', $color2)[2];

//            $rmean = ($r2 + $r1) / 2;
            $ur = $r2 + $r1;
            $dr = $r2 - $r1;
            $dg = $g2 - $g1;
            $db = $b2 - $b1;

//            $distance = sqrt(($dr) ** 2 + ($dg) ** 2 + ($db) ** 2);
//            $distance2 = sqrt(2 * ($dr ** 2) + 4 * ($dg ** 2) + 3 * ($db ** 2) + ($rmean * ($dr ** 2 - $db ** 2)) / 256);
            $distance3 = ($dr * $dr * (2 + $ur / 256) + $dg * $dg * 4 + $db * $db * (2 + (255 - $ur) / 256)) ** (1 / 3);

            if ($distance3 <= $maxDist) {
                $found = true;
                $unsetKeys[] = $color1;
                $unsetKeys[] = $color2;
                $rmean = ($r2 + $r1) / 2;
                $gmean = ($g2 + $g1) / 2;
                $bmean = ($b2 + $b1) / 2;
                $add[] = ['color' => $rmean . ',' . $gmean . ',' . $bmean, 'qty' => $qty1 + $qty2];
                break 2;
            }
        }
    }

    if ($found) {
        foreach ($unsetKeys as $value) {
            unset($colors[$value]);
        }
        foreach ($add as $value) {
            $colors[$value['color']] = $value['qty'];
        }
        return distanceColors($colors, $maxDist);
    }
    return $colors;
}

function sortByColor($colors) {
    $reds = [];
    $greens = [];
    $blues = [];
    $otherColors = [];
    foreach ($colors as $color => $qty) {
        $red = explode(',', $color)[0];
        $green = explode(',', $color)[1];
        $blue = explode(',', $color)[2];

        if ($red > $green && $red > $blue) {
            $reds[$color] = $qty;
        } elseif ($green > $red && $green > $blue) {
            $greens[$color] = $qty;
        } elseif ($blue > $red && $blue > $green) {
            $blues[$color] = $qty;
        } else {
            $otherColors[$color] = $qty;
        }
    }
    $sortedArray = array_merge($reds, $greens, $blues, $otherColors);

    return $sortedArray;
}

function colorToLum($color) {
    $red = explode(',', $color)[0];
    $green = explode(',', $color)[1];
    $blue = explode(',', $color)[2];
    return (0.299 * $red + 0.587 * $green + 0.114 * $blue);
}
