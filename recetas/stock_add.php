<?php
if (!empty(filter_input(INPUT_POST, 'name'))) {
    include '../db/dbmongo.php';
    include '../db/mongoFunctions.php';


    $doc = [
        "_id" => uniqid(),
        "qty" => filter_input(INPUT_POST, 'qty'),
        "unit" => filter_input(INPUT_POST, 'unit'),
        "name" => filter_input(INPUT_POST, 'name'),
        "time" => date("Y-m-d H:i:s")
    ];

//    error_log(__FILE__ . ' line ' . __LINE__ . ': ' . print_r($doc, true));
    $saved = save($doc, 'casa', 'ingredientes');
    //todo: agregar dropdown de unidades
    $units = ['pieza', 'g', 'kg', 'l', 'ml'];
}
?>

<html>
    <head>
    </head>
    <body>
        <?php include_once '../styles/topbar.php'; ?>
        <div class="container">

            <?php
            if (!empty($saved)) {
                echo "$doc[qty] $doc[unit] de $doc[name] agregados exitosamente al inventario";
            }
            ?>
            <form type="submit" action="" method="post">
                Cantidad: <input  id="qty" name="qty" type="text" /><br>
                Unidad: <input  id="unit" name="unit" type="text" /><br>
                Nombre: <input id="name" name="name" type="text" /><br>
                <input type="submit" value="Submit">
            </form>
        </div>
    </body>
</html>
