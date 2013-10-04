<?php
require("/home/pi/www/lib/keystore.php");
$conn = mysqli_connect(keystore("mysql", "db"), keystore("mysql", "user"), keystore("mysql", "pass"), "dox_api");
session_start();
header("Content-Type: application/json");
if ($_SESSION["auth"]) {
    if ($_SERVER["CONTENT_TYPE"] == "application/json") {
        $json = json_decode(file_get_contents("php://input"), true);
        if (file_get_contents("php://input") && is_null($json)) {
?>{
    "error": "Failed to parse JSON request."
}<?
            die();
        }
        $successCount = 0;
        $errorCount = 0;
        foreach (array("tasks", "done") as $type) {
            if ($json[$type]) {
                foreach ($json[$type] as $sid => $task) {
                    $data = mysqli_query($conn, 'SELECT * FROM `' . $type . '` WHERE `sid` = "' . mysqli_real_escape_string($conn, $sid) . '" AND `uid` = ' . $_SESSION["auth"]["uid"] . ';');
                    if (mysqli_num_rows($data) === 0) {
                        if (mysqli_query($conn, 'INSERT INTO `' . $type . '` (`sid`, `uid`, `task`) VALUES ("' . mysqli_real_escape_string($conn, $sid) . '", '
                                                . $_SESSION["auth"]["uid"] . ', "' . mysqli_real_escape_string($conn, $task) . '");')) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                    } else {
                        if (mysqli_query($conn, 'UPDATE `' . $type . '` SET `task` = "' . mysqli_real_escape_string($conn, $task) . '" WHERE `sid` = "'
                                                . $sid . '" AND `uid` = ' . $_SESSION["auth"]["uid"] . ';')) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                    }
                }
            }
        }
        $success = $successCount . " task" . ($successCount === 1 ? "" : "s") . " synced.";
        $error = $success . "  " . $errorCount . " task" . ($errorCount === 1 ? "" : "s") . " failed to sync.";
    }
?>{
<?
    foreach (array("tasks", "done") as $type) {
?>
    "<? print($type); ?>": {<?
        $tData = mysqli_query($conn, 'SELECT * FROM `' . $type . '` WHERE `uid` = ' . $_SESSION["auth"]["uid"] . ';');
        $tNum = mysqli_num_rows($tData);
        for ($i = 0; $i < $tNum; $i++) {
            $row = mysqli_fetch_row($tData);
            $sid = $row[1];
            $task = $row[3];
?>

        <?
            print('"' .  $sid. '": "' . addslashes($task) . '"');
            if ($i + 1 < $tNum) {
                print(",");
            } else {
?>
    <?
            }
        }
?>

    },
<?
    }
    if ($errorCount) {
?>
    "error": "<? print($error); ?>"
<?
    } elseif ($successCount) {
?>
    "success": "<? print($success); ?>"
<?
    } else {
?>
    "success": "Nothing to upload."
<?
    }
?>
}<?
} else {
?>{
    "error": "There is no user logged in, or the session has expired."
}<?
}
