<?php
require_once "database_php_operations/config.php";

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM `data_from_senzors`";
    $stmt = $db->query($query);
    $data = json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

    $query = "SELECT * FROM `settings`";
    $stmt = $db->query($query);
    $settings = json_encode($stmt->fetch(PDO::FETCH_ASSOC));
} catch (\mysql_xdevapi\Exception){}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link rel="stylesheet" href="fonts/fonts.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.9/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.9/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.9/firebase-database.js"></script>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

    <title>Arduino realtime panel</title>
</head>
<body>
    <div class="container mt-3">
        <div class="row">
            <div class="col">
                <h1 class="text-center border-bottom pb-3">Arduino realtime panel</h1>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col d-flex justify-content-center">
                <canvas data-type="radial-gauge"
                        data-title="Illuminance"
                        data-major-ticks="0, 100, 200, 300, 400, 500, 600, 700, 800"
                        data-value="0"
                        data-value-int = 1
                        data-width="400"
                        data-height="400"
                        data-bar-width="10"
                        data-bar-shadow="5"
                        data-units="Lux"
                        data-max-value="800"
                        data-highlights="false"
                        data-color-bar-progress="rgba(246,207,4,.75)"
                        data-value-box-stroke="2"
                        data-color-value-box-shadow="2"
                        data-font-value="Led"
                ></canvas>
            </div>
            <div class="col d-flex justify-content-center">
                <canvas data-type="radial-gauge"
                        data-title="Temperature"
                        data-units="°C"
                        data-value="0"
                        data-max-value="40"
                        data-min-value="-40"
                        data-value-int = 1
                        data-major-ticks="-40,-30, -20, -10, 0, 10, 20, 30, 40"
                        data-width="400"
                        data-height="400"
                        data-bar-width="20"
                        data-bar-shadow="1"
                        data-color-bar-progress="#fff"
                        data-color-bar="#fff"
                        data-border-shadow-width="0"
                        data-border-inner-width="0"
                        data-border-outer-width="0"
                        data-border-middle-width="0"
                        data-highlights="false"
                        data-value-box-stroke="2"
                        data-color-value-box-shadow="2"
                        data-needle="false"
                        data-bar-start-position="middle"
                        data-font-value="Led"
                ></canvas>
            </div>
            <div class="col d-flex justify-content-center">
                <canvas data-type="radial-gauge"
                        data-title="Humidity"
                        data-value="0"
                        data-value-int = 1
                        data-width="400"
                        data-height="400"
                        data-bar-width="10"
                        data-bar-shadow="5"
                        data-units="%"
                        data-highlights="false"
                        data-color-bar-progress="rgba(4,197,246,.75)"
                        data-value-box-stroke="2"
                        data-color-value-box-shadow="2"
                        data-font-value="Led"
                ></canvas>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-8">
                <div id="chart"></div>
            </div>
            <div class="col-lg-4 d-flex align-items-center">
                <div class="m-auto">
                    <h4 class="text-center">Zapisovanie dát do databázy</h4>
                    <div class="form-check form-switch d-flex justify-content-center my-3 pb-3 border-bottom">
                        <input class="form-check-input own-switch" type="checkbox" id="insert_to_db">
                    </div>
                    <h4 class="text-center">Interval zápisu (sekundy)</h4>
                    <div class="my-3">
                        <input type="number" min="1" class="form-control" id="interval_write" aria-describedby="interval">
                        <div id="interval_write_help" class="form-text text-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
    <script async src="gauge.min.js"></script>
    <script>var phpdata = <?php echo $data; ?>, settings = <?php echo $settings; ?></script>
    <script src="javascript.js"></script>
</body>
</html>