<?php

set_time_limit(0);

include_once '../db/dbmongo.php';
include_once '../db/mongoFunctions.php';

$cards = file_get_contents('../db/SVrarLegend.xml');
$cardsArr = simplexml_load_string($cards);

foreach ($cardsArr as $card) {
    if ($card['style'] == "display: inline-block;") {
        $array = [
            'name' => (string) $card['data-cname'],
            'rarity' => 'Legendary'
        ];
        $query = ['name' => $array['name']];
        error_log(__FILE__ . ' line ' . __LINE__ . ": " . print_r(json_encode($query), true));
        $result = update($query, $array, 'Shadowverse', 'cards');
    }
}
