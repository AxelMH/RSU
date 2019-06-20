<?php

session_start();
include_once './db/dbmongo.php';
include_once './db/mongoFunctions.php';

$dbName = 'MTG';
$_SESSION['message'] = '';

$deckString = filter_input(INPUT_POST, 'deck');
$deckname = filter_input(INPUT_POST, 'deckname');
$deckArray = explode(PHP_EOL, $deckString);
$deck = [];
foreach ($deckArray as $card) {
    $qty = (int) explode('x', $card)[0];
    $cardname = trim(explode('x', $card, 2)[1]);

    $found = find(['name' => $cardname], $dbName, 'cards');
    if (!empty($found) && !empty($cardname) && !empty($qty)) {
        $deck[$cardname] = $qty;
    } else {
        $_SESSION['message'] .= "$cardname not found<br>";
    }
}
$array = [
    '_id' => uniqid(),
    'deckname' => $deckname,
    'deck' => $deck
];
$result = save($array, $dbName, 'decks');
error_log(__FILE__ . ' line ' . __LINE__ . ": " . print_r($result, true));
$_SESSION['message'] .= "$deckname succesfully imported<br>";
header("Location: ./mtg_deck_import.php");
die();
