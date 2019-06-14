<?php
if (empty($_FILES)) {
    ?>
    <html>
        <head>
            <title>Bead stuff</title>
        </head>
        <body>
            <form enctype="multipart/form-data"  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <input type="file" name="csv_file" id="csv_file" accept="image/*"><br>
                Marca:<br>
                <input type="checkbox" name="marca[]" id="H" value="Hama"><label for="H">Hama</label><br>
                <input type="checkbox" name="marca[]" id="N" value="Nabbi"><label for="N">Nabbi</label><br>
                <input type="checkbox" name="marca[]" id="P" value="Perler"><label for="P">Perler</label><br>
                <input type="checkbox" name="marca[]" id="A" value="Artkal"><label for="A">Artkal</label><br>
                <div id="err1" class="alert alert-error" style="display:none;"></div><br>
                <input type="submit" value="Calcular">
            </form>
        </body>
    </html>
    <?php
} else {
    //read image file
    $imagick = new Imagick();
    $handle = fopen($_FILES['csv_file']['tmp_name'], 'r');
    $imagick->readImageFile($handle);
    $width = $imagick->getImageWidth();
    $height = $imagick->getImageHeight();
    $totalColors = $imagick->getImageColors();

    $csv = $_FILES['csv_file']['name'] . "\n";
    $csv .= "Tamaño de la imágen:,$width x $height\n";
    $csv .= "Pixeles totales:," . ($width * $height) . "\n";

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
    $csv .= "Total de pixeles de color:," . $count . "\n";
    $csv .= "Colores diferentes:," . count($colors) . "\n";

    //open xml file
    $xml = simplexml_load_file("./perler_colors.xml");

    $threshold = 0; //how much can the bead color vary from the image color
    $csv .= "#,Nombre,Cantidad,Marca,R,G,B,R[cuenta],G[cuenta],B[cuenta],Mayor Diferencia\n";

    $n = 0;
    foreach ($colors as $color => $quantity) {
        $n++;
        //get colors in decimal components for RGB
        $red = explode(',', $color)[0];
        $green = explode(',', $color)[1];
        $blue = explode(',', $color)[2];

        $found = false;
        while (!$found) {
            $threshold++;
            $result = findColor($red, $green, $blue, $threshold, $_REQUEST['marca']);
        }
        $csv .= "$n,$result[name],$quantity,$result[type],$red,$green,$blue,$result[red],$result[green],$result[blue],$threshold\n";
        $threshold = 0;
    }

    //download the file
    $filename = $_FILES['csv_file']['name'];
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=$filename.csv");
    $fp = fopen("php://output", "w");
    fwrite($fp, utf8_decode($csv));
    fclose($fp);
}

function findColor($red, $green, $blue, $threshold, $marca) {
    global $found, $xml;
    foreach ($xml as $value) {
        if (((int) $value['red']) >= ($red - $threshold) && ((int) $value['red']) <= ($red + $threshold)//check for red
                && ((int) $value['green']) >= ($green - $threshold) && ((int) $value['green']) <= ($green + $threshold) //check for green
                && ((int) $value['blue']) >= ($blue - $threshold) && ((int) $value['blue']) <= ($blue + $threshold)//check for blue
                && in_array($value['type'], $marca) //check for type
        ) {
            $found = true;
            break;
        }
    }
    return($value);
}
