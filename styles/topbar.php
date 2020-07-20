<?php
$dir = filter_input(INPUT_SERVER, 'SCRIPT_URL');
$path = array_filter(explode('/', $dir));
if (end($path) == 'index') {
    array_pop($path);
}
?>
<link rel="stylesheet" href="/rsu/styles/main.css" type="text/css">
<link rel="stylesheet" href="/rsu/styles/topbar.css" type="text/css">
<div class="header">
    <div id="logo"><img src="/rsu/images/logo.png" alt="RSU" height="35" width="189"></div>
</div>
<div class="navigation">
    <ul>
        <?php
        $href = '';
        foreach ($path as $page) {
            if ($page == end($path)) {
                echo "<li><span>$page</span></li>";
            } else {
                $href .= '/' . $page;
                echo "<li><a href='$href'>$page</a></li>";
            }
        }
        ?>
    </ul>
</div>

