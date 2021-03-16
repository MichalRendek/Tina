<?php
require_once "config.php";

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM `data_from_senzors`";
    $stmt = $db->query($query);
    $data = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        array_push($data, $row);
    echo json_encode($data);
} catch (\mysql_xdevapi\Exception){
    echo "Some error happened";
}