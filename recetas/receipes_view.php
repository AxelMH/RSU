<?php

include_once '../db/dbmongo.php';
include_once '../styles/topbar.php';

if(!empty(filter_input(INPUT_GET, 'delete'))){
    delete('casa', 'recetas', ['_id'=>filter_input(INPUT_GET, 'delete')]);
}
echo '        <div class="container">';
foreach (distinct('type', 'casa', 'recetas') as $type) {

    echo "<h2>$type</h2>";
    echo "<ul>";
    foreach (find(['type' => $type], 'casa', 'recetas')as $receta) {
        echo "<li><a href='receipe_view?id=$receta[_id]'>$receta[name]</a> <a href='receipes_view?delete=$receta[_id]'>Eliminar</a></li>";
    }
    echo "</ul>";
}
