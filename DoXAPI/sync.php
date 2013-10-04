<?php
require("/home/pi/www/lib/keystore.php");
$conn = mysqli_connect(keystore("mysql", "db"), keystore("mysql", "user"), keystore("mysql", "pass"), "dox_api");
session_start();
header("Content-Type: application/json");
if ($_SESSION["auth"]) {
    if ($_POST["tasks"]) {
        // upload tasks
    }
    if ($_POST["done"]) {
        // upload done
    }
?>{
    "tasks": [<?
    $tData = mysqli_query($conn, "SELECT * FROM `tasks` WHERE `uid` = " . $_SESSION["auth"]["uid"] . ";");
    $tNum = mysqli_num_rows($tData);
    for ($i = 0; $i < $tNum; $i++) {
        $row = mysqli_fetch_row($tData);
?>

        <?
        print('"' . addslashes($row[3]) . '"');
        if ($i + 1 < $tNum) {
            print(",");
        } else {
?>
    <?
        }
    }
?>
    ],
    "done": [<?
    $dData = mysqli_query($conn, "SELECT * FROM `done` WHERE `uid` = " . $_SESSION["auth"]["uid"] . ";");
    $dNum = mysqli_num_rows($dData);
    for ($i = 0; $i < $dNum; $i++) {
        $row = mysqli_fetch_row($dData);
?>
        <?
        print('"' . addslashes($row[2]) . '"');
        if ($i + 1 < $dNum) {
            print(",");
        } else {
?>
    <?
        }
    }
?>
    ]
}<?
} else {
?>{
    "error": "There is no user logged in, or the session has expired."
}<?
}
