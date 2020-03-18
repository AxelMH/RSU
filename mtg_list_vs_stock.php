<?php
include_once './db/dbmongo.php';
include_once './db/mongoFunctions.php';
include_once './includes/functions.php';
$dbName = 'MTG';

$list = filter_input(INPUT_POST, 'list');
$title = 'List vs Stock';
?>
<html>
    <head>
        <link rel="stylesheet" href="/rsu/styles/mtgStyle.css" type="text/css">
        <title><?= $title ?></title>
    </head>
    <body>
        <?php
        include_once './styles/topbar.php';
        ?>
        <div class="container">
            <form name="listForm" action="" method="post">
                List: <textarea rows="5" cols="50" name="list"></textarea>
                <input type = "submit" value = "submit" />
            </form>

            <?php
            if (!empty($list)) {
                $listArray = listToArray($list);

                foreach ($listLine as $cardname => $qty) {
                    $found = find(['name' => $cardname], $dbName, 'cards');
                    if (!empty($found) && !empty($cardname) && !empty($qty)) {
                        $listArray[$cardname] = $qty;
                    } else {
                        echo "$cardname not found<br>";
                    }
                }

                $stock = find([], $dbName, 'stock', ['sort' => ['cardname' => 1]]);

                $simplifiedStock = [];
                foreach ($stock as $cardInStock) {
                    if (!isset($simplifiedStock[$cardInStock['cardname']])) {
                        $simplifiedStock[$cardInStock['cardname']] = $cardInStock['qty'];
                    } else {
                        $simplifiedStock[$cardInStock['cardname']] += $cardInStock['qty'];
                    }
                }

                $missing = substractFromArray($listArray, $simplifiedStock);
                $owned = substractFromArray($listArray, $missing);

                echo "Cards from list in stock: <br>\n";
                //sort by type or rarity before echoing
                $completeOwned = find(['name' => ['$in' => array_keys($owned)]], $dbName, 'cards', ['sort' => ['types' => 1], 'projection' => ['types' => 1, 'name' => 1]]);
                $countOwned = 0;
                foreach ($completeOwned as $card) {
                    $type = implode(' ', $card['types']);
                    if (empty($prevType) || $prevType != $type) {
                        echo "<br><b>$type</b><br>\n";
                    }
                    $prevType = $type;
                    $cardname = $card['name'];
                    echo "$owned[$cardname] $cardname <br>\n";
                    $countOwned += $owned[$cardname];
                }
                echo "<br><b>Total: $countOwned </b><br>\n";

                echo "Cards missing: <br>\n";
                //sort by type or rarity before echoing
                $completeMissing = find(['name' => ['$in' => array_keys($missing)]], $dbName, 'cards', ['sort' => ['types' => 1], 'projection' => ['types' => 1, 'name' => 1]]);
                $countMissing = 0;
                foreach ($completeMissing as $card) {
                    $type = implode(' ', $card['types']);
                    if (empty($prevType) || $prevType != $type) {
                        echo "<br><b>$type</b><br>\n";
                    }
                    $prevType = $type;
                    $cardname = $card['name'];
                    echo "$missing[$cardname] $cardname <br>\n";
                    $countMissing += $missing[$cardname];
                }
                echo "<br><b>Total: $countMissing </b><br>\n";
            }

            /**
             * Return an array with the result of $array1[key]-$array2[key] for each key.
             * If result is less than 1, $resultArray[key] is unset.
             * @param array $array1
             * @param array $array2
             * @return array
             */
            function substractFromArray(array $array1, array $array2) {
                $resultArray = $array1;
                foreach ($array2 as $key => $value) {
                    if (isset($resultArray[$key])) {
                        $resultArray[$key] -= $value;
                    } else {
                        $resultArray[$key] = -$value;
                    }
                    if ($resultArray[$key] < 1) {
                        unset($resultArray[$key]);
                    }
                }
                return $resultArray;
            }
            ?>
        </div>
    </body>
</html>