<?php

include_once '../db/dbmongo.php';
$chapters = find([], 'SINoALICE', 'chapters');

?>
<html>
    <head>
    </head>
    <body>
        <?php include_once '../styles/topbar.php'; ?>
        <div class="container">

            <table>
                <thead>
                    <tr>
                        <td>Event</td>
                        <td>Mode</td>
                        <td>Chapter</td>
                        <td>AP</td>
                        <td>Exp</td>
                        <td>Gold</td>
                        <td>Difficulty</td>
                        <td>Drops</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($chapters as $chapter) {
                        echo '<tr>';
                        echo "<td>$chapter[event]</td>";
                        echo "<td>$chapter[mode]</td>";
                        echo "<td>$chapter[chapter]</td>";
                        echo "<td>$chapter[ap]</td>";
                        echo "<td>$chapter[exp]</td>";
                        echo "<td>$chapter[gold]</td>";
                        echo "<td>$chapter[difficulty]</td>";
                        echo "<td>" . implode(', ', $chapter['drops']) . "</td>";
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>