<?php
include_once './db/dbmongo.php';
include_once './db/mongoFunctions.php';

$dbName = 'MTG';
?>
<html>
    <title></title>
    <body>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Qty</th>
                    <th>Print</th>
                    <th>Foil</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $cards = find([], $dbName, 'stock');
                foreach ($cards as $value) {
                    echo '<tr>';
                    echo "<td>$value[cardname]</td>";
                    echo "<td>$value[qty]</td>";
                    echo "<td>$value[print]</td>";
                    echo "<td>$value[foil]</td>";
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </body>
</html>