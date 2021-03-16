<?php
require_once "config.php";

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "UPDATE `settings` SET `insert_time_delay`=".$_POST["delay"]." WHERE 1";
    $db->query($query);
    echo "Delay set right";
} catch (\mysql_xdevapi\Exception){
    echo "Some error happened";
}
?>