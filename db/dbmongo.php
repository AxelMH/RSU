<?php

//$client = new MongoDB\Client(
//    "mongodb+srv://Raven:<password>@ravensrandomcrap-hkeyy.azure.mongodb.net/test?retryWrites=true&w=majority");
//$mongoConnect="mongodb+srv://Raven:Tacosdetrip4jun2019@ravensrandomcrap-hkeyy.azure.mongodb.net/test?retryWrites=true&w=majority";
$mongoConnect="mongodb://Raven:Tacosdetrip4jun2019@ravensrandomcrap-shard-00-00-hkeyy.azure.mongodb.net:27017,ravensrandomcrap-shard-00-01-hkeyy.azure.mongodb.net:27017,ravensrandomcrap-shard-00-02-hkeyy.azure.mongodb.net:27017/test?ssl=true&replicaSet=RavensRandomCrap-shard-0&authSource=admin&retryWrites=true&w=majority";

$authMongoArray = [];
if ( !isset ($mongoConnect) ) {
    $mongoConnect = "mongodb://localhost:27017";
}

if(isset($_SESSION['dbString'])) {
    $mongoConnect = $_SESSION['dbString'];
}


if(defined('MONGO_HOST_MANAGER')) {
    $mongoConnect = MONGO_HOST_MANAGER;
}

if(isset($_SESSION['mongoAuth'])){
    $dbAuth = $_SESSION['mongoAuth']['db'];
    $dbUser = $_SESSION['mongoAuth']['username'];
    $dbPass = $_SESSION['mongoAuth']['password'];
    $authMongoArray = ['username' => $dbUser, 'password' => $dbPass, 'authSource' => $dbAuth];
}

$manager = new MongoDB\Driver\Manager ($mongoConnect,$authMongoArray);

/** Set variable to be used by Database Class */
if(defined('DB_MONGO') && DB_MONGO){
    define('DB_MONGO_CONNECT', $mongoConnect);
    define('DB_MONGO_AUTH', $authMongoArray);
}

