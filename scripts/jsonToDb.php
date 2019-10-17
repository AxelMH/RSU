<?php
//D:\Documents\PHP\RSU\scripts\jsonToDb.php
//http://localhost/RSU/scripts/jsonToDb.php

set_time_limit(0);

include_once '../db/dbmongo.php';
include_once '../db/mongoFunctions.php';
$dbName = 'MTG';
//$json = file_get_contents('../db/AllCards.json');
//
//foreach (json_decode($json, true) as $data) {
//    $array = [
//        '_id' => uniqid(),
//        "name" => $data["name"],
//        "manaCost" => $data["manaCost"] ?? null,
//        "CMC" => (int) $data["convertedManaCost"] ?? 0,
//        "colors" => $data["colors"],
//        "colorIdentity" => $data["colorIdentity"],
//        "supertypes" => $data["supertypes"],
//        "types" => $data["types"],
//        "subtypes" => $data["subtypes"],
//        "type" => $data["type"],
//        "printings" => $data["printings"],
//        "text" => $data["text"] ?? null,
//        "power" => $data["power"] ?? null,
//        "toughness" => $data["toughness"] ?? null,
//        "loyalty" => $data["loyalty"] ?? null,
//        "legalities" => $data["legalities"],
//    ];
//
//    save($array, 'MTG', 'cards');
//}
//$json = file_get_contents('../db/SetList.json');
//
//foreach (json_decode($json, true) as $data) {
//    $array = [
//        '_id' => uniqid(),
//        "name" => $data["name"],
//        "code" => $data["code"],
//        "type" => $data["type"],
//        "releaseDate" => $data["releaseDate"],
//    ];
//
//    save($array, 'MTG', 'sets');
//}

//new sets
//path to set.json
$json = json_decode(file_get_contents('../db/PELD.json'), true);

$setArray = [
    '_id' => uniqid(),
    "name" => $json["name"],
    "code" => $json["code"],
    "type" => $json["type"],
    "releaseDate" => $json["releaseDate"],
];

save($setArray, $dbName, 'sets');

foreach ($json['cards'] as $card) {
    $cardArray = [
        "name" => $card["name"],
        "manaCost" => $card["manaCost"] ?? null,
        "CMC" => (int) $card["convertedManaCost"] ?? 0,
        "colors" => $card["colors"],
        "colorIdentity" => $card["colorIdentity"],
        "supertypes" => $card["supertypes"],
        "types" => $card["types"],
        "subtypes" => $card["subtypes"],
        "type" => $card["type"],
        "printings" => $card["printings"],
        "text" => $card["text"] ?? null,
        "power" => $card["power"] ?? null,
        "toughness" => $card["toughness"] ?? null,
        "loyalty" => $card["loyalty"] ?? null,
        "legalities" => $card["legalities"],
    ];

    $found = findOne(["name" => $card["name"]], $dbName, 'cards');
    if ($found) {
        if (update($found, $cardArray, $dbName, 'cards')) {
            echo "<b>$card[name]</b> updated successfully<br>";
        } else {
            echo "<span style='color:red'><b>$card[name]</b> tried to update but failed</span><br>";
        }
    } else {
        $cardArray ["_id"] = uniqid();
        if (save($cardArray, 'MTG', 'cards')) {
            echo "<b>$card[name]</b> added successfully<br>";
        } else {
            echo "<span style='color:red'><b>$card[name]</b> tried to add but failed</span><br>";
        }
    }
}
