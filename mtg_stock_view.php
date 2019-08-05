<?php
set_time_limit(0);
session_start();
include './db/dbmongo.php';
include './db/mongoFunctions.php';

$db = 'MTG';
$title = "MTG Stock View"
?>

<html>
    <head>
        <link rel="stylesheet" href="/rsu/styles/mtgStyle.css" type="text/css">
        <title><?= $title ?></title>
    </head>
    <body>
        <?php include_once './styles/topbar.php'; ?>
        <div class="container">
            <?php

            //The comparison function must return an integer less than, equal to, or greater than zero 
            //if the first argument is considered to be respectively less than, equal to, or greater than the second
            function cmp($a, $b) {
                $valueA = getCompareValue($a);
                $valueB = getCompareValue($b);
                return $valueA - $valueB;
            }

//            function cmp($a, $b) {
//                return $a['compare'] - $b['compare'];
//            }

            /**
             * one digit for number of colors 
             * five for colors on binary
             * two for cmc
             * @param type $card
             */
            function getCompareValue($card) {
                $return = 0;
                if (sizeof($card['colors']) == 0) {
                    $return += 60000000;
                } else {
                    $return += 10000000 * sizeof($card['colors']);
                }
//                if (in_array('W', $card['colors'])) {
//                    $return += 100;
//                }
//                if (in_array('U', $card['colors'])) {
//                    $return += 1000;
//                }
//                if (in_array('B', $card['colors'])) {
//                    $return += 10000;
//                }
//                if (in_array('R', $card['colors'])) {
//                    $return += 100000;
//                }
//                if (in_array('G', $card['colors'])) {
//                    $return += 1000000;
//                }
                $return += getColorValues($card['colors']) + $card['CMC'];
                return $return;
            }

            function getColorValues($colors) {
                $return = 0;
                if (in_array('W', $colors)) {
                    $return += 100;
                }
                if (in_array('U', $colors)) {
                    $return += 1000;
                }
                if (in_array('B', $colors)) {
                    $return += 10000;
                }
                if (in_array('R', $colors)) {
                    $return += 100000;
                }
                if (in_array('G', $colors)) {
                    $return += 1000000;
                }
                return $return;
            }

            function getSingleColorValues($color) {
                if ($color=='W') {
                    return 1;
                }
                if ($color=='U') {
                    return 2;
                }
                if ($color=='B') {
                    return 3;
                }
                if ($color=='R') {
                    return 4;
                }
                if ($color=='G') {
                    return 5;
                }
            }

            function cmpColors($a, $b) {
                $valueA = getSingleColorValues($a);
                $valueB = getSingleColorValues($b);
                return $valueA - $valueB;
            }

            $cards = find([], $db, 'stock', ['limit' => 500]);
//            $cards = find([], $db, 'stock');
            foreach ($cards as $key => $card) {
                $details = findOne(['name' => $card['cardname']], $db, 'cards');
                $cards[$key]['colors'] = $details['colors'];
                $cards[$key]['CMC'] = $details['CMC'];
//                $cards[$key]['compare'] = getCompareValue($cards[$key]);
            }

            usort($cards, 'cmp');

            echo '<table>';
            echo '<tr>';
            echo '<td>Cantidad</td>';
            echo '<td>Nombre</td>';
            echo '<td>Expansión</td>';
            echo '<td>Foil</td>';
            echo '<td>CMC</td>';
            echo '<td>Colores</td>';
//            echo '<td>compare</td>';
            echo '</tr>';
            foreach ($cards as $card) {
                error_log(__FILE__ . ' line ' . __LINE__ . ': ' . print_r($card['colors'], true));

                usort($card['colors'], 'cmpColors');
                if (!isset($prevCardColors) || $card['colors'] != $prevCardColors) {
                    echo '<tr>';
                    echo "<td colspan='6'>" . implode("", $card["colors"]) . "</td>";
                    echo '</tr>';
                }
                echo '<tr>';
                echo "<td>$card[qty]</td>";
                echo "<td>$card[cardname]</td>";
                echo "<td>$card[print]</td>";
                echo "<td>$card[foil]</td>";
                echo "<td>$card[CMC]</td>";
                echo "<td>" . implode("", $card["colors"]) . "</td>";
//                echo "<td>$card[compare]</td>";
                echo '</tr>';
                $prevCardColors = $card['colors'];
            }
            echo '</table>';
            ?>


        </div>
    </body>

</html>