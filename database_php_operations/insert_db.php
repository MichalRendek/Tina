<?php
require_once "config.php";

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "UPDATE `settings` SET `insert_to_db`=".$_POST["insert"]." WHERE 1";
    $db->query($query);
    echo "Insert set right";
} catch (\mysql_xdevapi\Exception){
    echo "Some error happened";
}
?>