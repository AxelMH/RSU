<?php

function listToArray(string $list) {
    $listLines = explode(PHP_EOL, trim($list));
    $array = [];

    foreach ($listLines as $card) {
        $explosion = explode(" ", $card);
        if (is_numeric(trim($explosion[0], " x"))) {
            $qty = (int) trim(array_shift($explosion), " x");
        } else {
            $qty = 1;
        }
        $cardname = trim(implode(' ', $explosion));

        $array[$cardname] = $qty;
    }
    return $array;
}
