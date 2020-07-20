<?php
if (!empty(filter_input(INPUT_POST, 'name'))) {
    include_once '../db/dbmongo.php';

    $ingredientsArray = [];
    $ingredients = explode(PHP_EOL, trim(filter_input(INPUT_POST, 'ingredients')));

    foreach ($ingredients as $ingredientLine) {
        if (empty($ingredientLine)) {
            continue;
        }

        $ingredientsLineArray = explode(' ', $ingredientLine);
        $qty = trim(array_shift($ingredientsLineArray));
        $unit = trim(array_shift($ingredientsLineArray));
        $ingredient = trim(implode(' ', $ingredientsLineArray));

        $ingredientsArray[] = [
            'qty' => $qty,
            'unit' => $unit,
            'ingredient' => $ingredient,
        ];
    }

    $doc = [
        "_id" => uniqid(),
        "name" => filter_input(INPUT_POST, 'name'),
        "type" => filter_input(INPUT_POST, 'type'),
        "ingredients" => $ingredientsArray,
        "instructions" => array_filter(explode(PHP_EOL, trim(filter_input(INPUT_POST, 'instructions')))),
    ];

    save($doc, 'casa', 'recetas');
}
?>

<html>
    <head>
    </head>
    <body>
        <?php include_once '../styles/topbar.php'; ?>
        <div class="container">
<?php
if(!empty($doc)){
    echo"$doc[name] agregado exitosamente a recetas.";
}
?>
            <form type="submit" action="" method="post">
                Nombre: <input id="name" name="name" type="text" /><br>
                Tipo: <input  id="type" name="type" type="text" /><br>
                Ingredientes: <textarea id="ingredients" name="ingredients" rows="15" cols="50" placeholder="[cantidad] [unidad] [ingrediente]"></textarea><br>
                Instrucciones: <textarea id="instructions" name="instructions" rows="10" cols="100"></textarea><br>
                <input type="submit" value="Submit">
            </form>
        </div>
    </body>
</html>
