<?php

include_once '../db/dbmongo.php';
include_once '../styles/topbar.php'; ?>
        <div class="container">
<?php
echo "<ul>";
foreach (find([], 'casa', 'ingredientes')as $ingrediente) {
    echo "<li>$ingrediente[qty] $ingrediente[unit] $ingrediente[name]</li>";
}
echo "</ul>";
