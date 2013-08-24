<?php
require("/home/pi/www/lib/keystore.php");
$conn = mysqli_connect(keystore("mysql", "db"), keystore("mysql", "user"), keystore("mysql", "pass"), "dox_cloud");
session_start();
$email = $_POST["email"];
$pass = $_POST["pass"];
$data = mysqli_query($conn, "SELECT * FROM `users` WHERE LOWER(`email`) = \"" . strtolower($email) . "\";");
$num = mysqli_num_rows($data);
if ($num) {
    $row = mysqli_fetch_assoc($data);
    if (md5($pass) === $row["pass"]) {
        if (!file_exists("files/" . $email . "/")) {
            mkdir("files/" . $email);
        }
        if ($_POST["totTasks"]) {
            $totTasks = (int) $_POST["totTasks"];
            $tasks = array();
            for ($i = 1; $i < $totTasks + 1; $i++) {
                array_push($tasks, $_REQUEST["task" . $i]);
            }
            $f = fopen("files/" . $email . "/tasks.txt", "w");
            fwrite($f, implode("\r\n", $tasks));
            fclose($f);
        }
        if ($_POST["totDone"]) {
            $totDone = (int) $_POST["totDone"];
            $done = array();
            for ($i = 1; $i < $totDone + 1; $i++) {
                array_push($done, $_REQUEST["done" . $i]);
            }
            $f = fopen("files/" . $email . "/done.txt", "w");
            fwrite($f, implode("\r\n", $done));
            fclose($f);
        }
        $success = "All tasks have been uploaded successfully.";
    } else {
        $error = "That password seems to be incorrect.";
    }
} else {
    $error = "There doesn't seem to be an account registered with that email address.";
}
header("Content-Type: application/json");
if ($success) {
?>{
    "success": "<? print($success); ?>"
}<?
} elseif ($error) {
?>{
    "error": "<? print($error); ?>"
}<?
}