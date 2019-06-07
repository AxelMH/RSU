<?php
set_time_limit(0);

include_once '../db/dbmongo.php';
include_once '../db/mongoFunctions.php';

$json = file_get_contents('../db/SVAllCards.json');

foreach (array_keys(json_decode($json, true))as $cardname) {
    $array = [
        '_id' => uniqid(),
        "name" => $cardname
    ];

    save($array, 'Shadowverse', 'cards');
}