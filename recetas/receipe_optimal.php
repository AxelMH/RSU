<?php

include_once '../db/dbmongo.php';
include_once '../styles/topbar.php'; ?>
        <div class="container">
        <?php 

//obtener lista de ingredientes
$stock = distinct('name', 'casa', 'ingredientes');
$ignoreIngredients=['sal','azucar','pimienta','mantequilla','aceite','agua',];
//encontrar recetas que tengan al menos uno de los ingredientes que se tienen
$filter = [];
foreach ($stock as $ingredient) {
    if(in_array($ingredient, $ignoreIngredients)){
        continue;
    }
        
    $filter['$or'][] = ['ingredients.ingredient' => $ingredient];
}
$recetas = find($filter, 'casa', 'recetas');

echo "<ul>";
foreach ($recetas as $receta) {
    echo "<li><a href='receipe_view?id=$receta[_id]'>$receta[name]</a></li>";
}
echo "</ul>";
