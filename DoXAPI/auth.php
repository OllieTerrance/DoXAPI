<?php
require("/home/pi/www/lib/keystore.php");
$conn = mysqli_connect(keystore("mysql", "db"), keystore("mysql", "user"), keystore("mysql", "pass"), "dox_api");
session_start();
if ($_POST["submit"]) {
    $email = $_POST["email"];
    $pass = $_POST["pass"];
    if (!isset($email) || $email === "") {
        $error = "You need to enter your email address.";
    } elseif (!preg_match("/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/", $email)) {
        $error = "Your email address seems incorrect.";
    } elseif (!isset($pass) || $pass === "") {
        $error = "You need to enter your password.";
    } elseif (strlen($pass) < 4) {
        $error = "Your password is too short – make it at least 4 characters.";
    } else {
        $data = mysqli_query($conn, "SELECT * FROM `users` WHERE LOWER(`email`) = \"" . strtolower(mysqli_real_escape_string($conn, $email)) . "\";");
        $num = mysqli_num_rows($data);
        if ($_POST["submit"] === "login") {
            if ($num) {
                $row = mysqli_fetch_assoc($data);
                if (md5($pass) === $row["pass"]) {
                    $success = "Awesome, you're now logged in!";
                    $uid = $row["uid"];
                } else {
                    $error = "That password seems to be incorrect.";
                }
            } else {
                $error = "There doesn't seem to be an account registered with that email address.";
            }
        } elseif ($_POST["submit"] === "register") {
            if ($num) {
                $error = "There's already an account registered with that email address.";
            } else {
                if (mysqli_query($conn, "INSERT INTO `users` (`email`, `pass`) VALUES (\"" . mysqli_real_escape_string($conn, $email) . "\", \"" . md5($pass) . "\");")) {
                    $success = "Boom, your account has been created!";
                    $uid = mysqli_insert_id($conn);
                } else {
                    $error = "Oops, something went wrong there...";
                }
            }
        } elseif ($_REQUEST["submit"] === "logout") {
            if ($_SESSION["auth"]) {
                session_destroy();
                $success = "You have been logged out.";
            } else {
                $error = "You don't appear to be logged in.";
            }
        }
    }
    header("Content-Type: application/json");
    if ($success) {
        $_SESSION["auth"] = array("uid" => $uid, "email" => $email);
?>{
    "success": "<? print($success); ?>"
}<?
    } elseif ($error) {
?>{
    "error": "<? print($error); ?>"
}<?
    }
}
