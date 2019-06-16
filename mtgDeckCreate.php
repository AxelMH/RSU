<?php
include './db/dbmongo.php';
include './db/mongoFunctions.php';
?>

<html>
    <title></title>
    <body>
        <h1>Create deck</h1>
        Format: <select id="format">
            <?php
            distinct('legalities', 'MTG', 'cards');
            ?>
            <option value="commander">Commander</option>
            <option value="standard">Standard</option>
        </select>
    </body>
</html>
<?php

