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
        $get = $_POST["get"];
        if ($get === "tasks") {
            $content = explode("\r\n", addslashes(file_get_contents("files/" . $email . "/tasks.txt")));
        } elseif ($get === "done") {
            $content = explode("\r\n", addslashes(file_get_contents("files/" . $email . "/done.txt")));
        }
        $success = "All tasks have been downloaded successfully.";
    } else {
        $error = "That password seems to be incorrect.";
    }
} else {
    $error = "There doesn't seem to be an account registered with that email address.";
}
header("Content-Type: application/json");
if ($success) {
?>{
    "success": "<? print($success); ?>",
    "content": [
<? foreach ($content as $i => $line) { ?>
        <? print("\"" . $line . "\"" . ($i === count($content) - 1 ? "" : ",")); ?>
<? } ?>
    ]
}<?
} elseif ($error) {
?>{
    "error": "<? print($error); ?>"
}<?
}