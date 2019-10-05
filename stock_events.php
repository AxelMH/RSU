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
    $location = filter_input(INPUT_POST, 'location');
    $foil = (bool) filter_input(INPUT_POST, 'foil');

    $array = [
        'cardname' => $cardname,
        'print' => $print,
        'location' => $location,
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

    $list = trim(filter_input(INPUT_POST, 'list'));
    $listLines = explode(PHP_EOL, $list);

    $print = filter_input(INPUT_POST, 'printings');
    $location = filter_input(INPUT_POST, 'location');
    $foil = (bool) filter_input(INPUT_POST, 'foil');

    foreach ($listLines as $card) {
        $explosion = explode(" ", $card);
        if (is_numeric(trim($explosion[0], " x"))) {
            $qty = (int) trim(array_shift($explosion), " x");
        } else {
            $qty = 1;
        }
        $cardname = trim(implode(' ', $explosion));

        //validate the cardname exists
        if (empty($cardname)) {
            $_SESSION['message'] .= "<span style='color:red'>Could not process <b>$card</b>.</span><br>";
            continue;
        }

        //check cardname is an existing card
        $foundCard = findOne(['name' => $cardname], $dbName, 'cards');
        if (empty($foundCard)) {
            $_SESSION['message'] .= "<span style='color:red'><b>$cardname</b> NOT found.</span><br>";
            continue;
        }

        //check card is in set
        if (!in_array($print, $foundCard['printings'])) {
            $_SESSION['message'] .= "<span style='color:red'><b>$cardname</b> NOT in set $print.</span><br>";
            continue;
        }

        //import card
        $array = [
            'cardname' => $cardname,
            'print' => $print,
            'location' => $location,
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
    }

    header("Location: ./mtg_mass_stock_import.php");
    die();
}
