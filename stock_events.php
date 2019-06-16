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
        update($array, $array, $dbName, $collName);
    } else {
        $array['_id'] = uniqid();
        $array['qty'] = $qty;
        save($array, $dbName, $collName);
    }

    $_SESSION['message'] = "$qty $cardname successfully added to stock.";
    header("Location: ./mtgStock.php");
    die();
}
