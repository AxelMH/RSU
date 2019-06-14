<?php

set_time_limit(0);

include_once '../db/dbmongo.php';
include_once '../db/mongoFunctions.php';

$json = file_get_contents('../db/AllCards.json');

foreach (json_decode($json, true) as $data) {
    $array = [
        '_id' => uniqid(),
        "name" => $data["name"],
        "manaCost" => $data["manaCost"] ?? null,
        "CMC" => (int) $data["convertedManaCost"] ?? 0,
        "colors" => $data["colors"],
        "colorIdentity" => $data["colorIdentity"],
        "supertypes" => $data["supertypes"],
        "types" => $data["types"],
        "subtypes" => $data["subtypes"],
        "type" => $data["type"],
        "printings" => $data["printings"],
        "text" => $data["text"] ?? null,
        "power" => $data["power"] ?? null,
        "toughness" => $data["toughness"] ?? null,
        "loyalty" => $data["loyalty"] ?? null,
        "legalities" => $data["legalities"],
    ];

//    if(empty($data["manaCost"])){
//        error_log(print_r($data, true));
//    }

    save($array, 'MTG', 'cards');
}