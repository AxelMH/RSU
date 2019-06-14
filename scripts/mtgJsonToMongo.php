<?php

$json = file_get_contents("C:/Apache24/htdocs/bd/AllCards.json");
$allCards = json_decode($json, true);

foreach ($allCards as $card) {
    $cardArray['_id'] = uniqid();
    $cardArray['name'] = $card['name'];
    $cardArray['manaCost'] = $card['manaCost'];
    $cardArray['convertedManaCost'] = $card['convertedManaCost'];
    $cardArray['colors'] = $card['colors'];
    $cardArray['colorIdentity'] = $card['colorIdentity'];
    $cardArray['supertypes'] = $card['supertypes'];
    $cardArray['types'] = $card['types'];
    $cardArray['subtypes'] = $card['subtypes'];
    $cardArray['type'] = $card['type'];
    $cardArray['printings'] = $card['printings'];
    $cardArray['text'] = $card['text'];
    $cardArray['power'] = $card['power'];
    $cardArray['toughness'] = $card['toughness'];
    $cardArray['legalities'] = $card['legalities'];
    echo $card['name'] . '<br>';
}
    