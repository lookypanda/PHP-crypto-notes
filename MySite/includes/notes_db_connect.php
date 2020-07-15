<?php
require("constants.php");
$note_db_con = mysqli_connect(DB_SERVER,DB_USER, DB_PASS) or die(mysqli_error($note_db_con));
mysqli_select_db($note_db_con,NOTE_DB_NAME) or die("Cannot select NOTE DB");
?>

/**
 * Created by PhpStorm.
 * User: alexk
 * Date: 18.04.2019
 * Time: 14:14
 */