<?php

session_start();
include_once './db/dbmongo.php';
include_once './db/mongoFunctions.php';

$dbName = 'MTG';

$action = filter_input(INPUT_POST, 'action');

if ($action == 'printings') {
    $cardname = filter_input(INPUT_POST, 'card');
    $card = findOne(['name' => $cardname], $dbName, 'cards', ['projection' => ['printings' => 1]]);

    echo json_encode($card['printings']);
}

if ($action == 'add') {
    $collName = 'stock';
    $cardname = filter_input(INPUT_POST, 'card');
    $qty = (int) filter_input(INPUT_POST, 'quantity');
    $print = filter_input(INPUT_POST, 'printings');
    $foil = (bool) filter_input(INPUT_POST, 'foil');

    $array = [
        'cardname' => $cardname,
        'print' => $print,
        'foil' => $foil,
    ];
    $found = findOne($array, $dbName, $collName);
    if ($found) {
        $found['qty'] += $qty;
        update($array, $found, $dbName, $collName);
    } else {
        $array['_id'] = uniqid();
        $array['qty'] = $qty;
        save($array, $dbName, $collName);
    }

    $_SESSION['message'] = "$qty $cardname successfully added to stock.";
    header("Location: ./mtgStock.php");
    die();
}

if ($action == 'addList') {
    $collName = 'stock';
    $_SESSION['message'] = '';

    $list = filter_input(INPUT_POST, 'list');
    $listLine = explode(PHP_EOL, $list);

    $print = filter_input(INPUT_POST, 'printings');
    $foil = (bool) filter_input(INPUT_POST, 'foil');

    foreach ($listLine as $card) {
        //TODO: check examples with formats N cardname and Nx cardname
        $explosion = explode(" ", $card);
        if (is_int(trim($explosion[0], " x"))) {
            $qty = (int) trim(array_shift($explosion), " x");
        } else {
            $qty = 1;
        }
        $cardname = trim(implode(' ', $explosion));

        //TODO: check expansion
        $foundCard = find(['name' => $cardname], $dbName, 'cards');
        if (!empty($foundCard) && !empty($cardname) && !empty($qty)) {
            $array = [
                'cardname' => $cardname,
                'print' => $print,
                'foil' => $foil,
            ];
            $inStock = findOne($array, $dbName, $collName);
            if ($inStock) {
                $inStock['qty'] += $qty;
                update($array, $inStock, $dbName, $collName);
            } else {
                $array['_id'] = uniqid();
                $array['qty'] = $qty;
                save($array, $dbName, $collName);
            }

            $_SESSION['message'] .= "$qty <b>$cardname</b> successfully added to stock.<br>";
        } else {
            $_SESSION['message'] .= "<span style='color:red'><b>$cardname</b> NOT found.</span><br>";
        }
    }

    header("Location: ./mtg_mass_stock_import.php");
    die();
}
