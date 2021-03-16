<?php
require_once "database_php_operations/config.php";

// Create connection
$conn = new mysqli($hostname, $username, $password, $dbname);
//
$insert_to_db = 0;
$insert_to_db_delay = 0;

$sql = "SELECT * FROM `settings` WHERE 1";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $result = $result->fetch_assoc();
    $insert_to_db = $result["insert_to_db"];
    $insert_to_db_delay = $result["insert_time_delay"];
} else {
    echo "Error: " . $sql  . $conn->error;
}

$sql = "SELECT * FROM data_from_senzors ORDER BY ID DESC LIMIT 1";
$result = $conn->query($sql);
$last_insert_timestamp = 0;
if ($result = $result->fetch_assoc()) {
    $last_insert_timestamp = $result["time_stamp"];
} else {
    echo "Error: " . $sql  . $conn->error;
}

require __DIR__.'/vendor/autoload.php';

use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount('tina-project-fd72d-firebase-adminsdk-ucqy2-74362db9c2.json')
    ->withDatabaseUri('https://tina-project-fd72d-default-rtdb.firebaseio.com');

$database = $factory->createDatabase();

if(isset($_POST['humidity'])){
    $postData = [
        'temperature' => $_POST["temperature"],
        'humidity' => $_POST["humidity"],
        'illuminance' => $_POST["illuminance"]
    ];

    $reference = $database->getReference('realtime_data')->update($postData);
}
echo date("Y-m-d H:i:s",strtotime($last_insert_timestamp))."\n";
echo date("Y-m-d H:i:s",strtotime($last_insert_timestamp) + $insert_to_db_delay)."\n";
echo date("Y-m-d H:i:s")."\n";
var_dump(date("Y-m-d H:i:s") > date("Y-m-d H:i:s",strtotime($last_insert_timestamp) + $insert_to_db_delay));

if(isset($_POST['humidity']) && $insert_to_db && date("Y-m-d H:i:s") > date("Y-m-d H:i:s",strtotime($last_insert_timestamp) + $insert_to_db_delay)){

    $sql = "INSERT INTO `data_from_senzors`(`temperature`, `humidity`, `illuminance`) VALUES (".$_POST["temperature"].", ".$_POST["humidity"].", ".$_POST["illuminance"]." )";

    $inserted_id = 0;
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
        $inserted_id = $conn->insert_id;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $result = $conn->query("SELECT `time_stamp` FROM `data_from_senzors` WHERE `ID`=".$inserted_id);

    $conn->close();

    $postData = [
        'ID' => $inserted_id,
        'temperature' => $_POST["temperature"],
        'humidity' => $_POST["humidity"],
        'illuminance' => $_POST["illuminance"],
        'time_stamp' => $result->fetch_assoc()["time_stamp"]
    ];

    $reference = $database->getReference('sensors_data')->push($postData);
}
?>