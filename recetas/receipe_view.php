<?php
include_once '../db/dbmongo.php';
include_once '../styles/topbar.php';
?>
<div class="container">
    <?php
    $receta = findOne(['_id' => filter_input(INPUT_GET, 'id')], 'casa', 'recetas');
    ?>

    <h1><?= $receta['name'] ?></h1>
    <h2><?= $receta['type'] ?></h2>
    <h3>Ingredientes</h3>
    <ul>
        <?php
//todo: ordenar los que si se tienen y los que no
//todo: buscar sin distinción de mayúsculas y minúsculas
        foreach ($receta['ingredients'] as $value) {
            $ingrediente = findOne(['name' => $value['ingredient']], 'casa', 'ingredientes');
            $ingredienteString = '';
            if (!empty($ingrediente)) {
                $ingredienteString = " <span style='color:blue'>$ingrediente[qty] $ingrediente[unit]</span>";
            }

            echo "<li>$value[qty] $value[unit] $value[ingredient]$ingredienteString</li>";
        }
        ?>
    </ul>
    <h3>Instrucciones</h3>
    <ol><span ></span>
        <?php
        foreach ($receta['instructions'] as $value) {
            echo "<p>$value</p>";
        }
        ?>
    </ol>
    <button>elaborar</button>

</div>



