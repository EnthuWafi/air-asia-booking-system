<?php

session_start();
require("../../includes/functions.inc.php");


login_required();

if (isset($_SESSION['user_id'])) {
    $user = $_SESSION['user_id'];
}

$flightInfo = null;
$returnFlight = null;
$departureFlight = null;

//check session values
if (isset($_SESSION["flightInfo"])) {
    $flightInfo = $_SESSION["flightInfo"];
}
else{
    header("Location: /index.php");
    die();
}

if (isset($flightInfo["return_flight_id"])) {
    $returnFlight = retrieveFlight($flightInfo["return_flight_id"], $flightInfo["travel_class"], $flightInfo["passenger_count"]);
}

$departureFlight = retrieveFlight($flightInfo["departure_flight_id"], $flightInfo["travel_class"], $flightInfo["passenger_count"]);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Flight Proceed</title>
</head>

<body>
    <div class="d-flex flex-row">
        <a class="navbar-brand" href="/index.php">
            <img class="img-fluid w-50" src="/assets/img/airasiacom_logo.svg">
        </a>
    </div>
    <div class="container">
        <h1>Flight Proceed</h1>
        <p>Flight Details</p>
        <div id="departure-flight">
            <?php
            //departure
            if (isset($departureFlight)){
                $departDate = date_create($departureFlight["departure_time"]);
                $departDate_date = date_format($departDate, 'd M Y');
                $departDate_time = date_format($departDate, 'H:i');

                $arrivalDate = date_create($departureFlight["arrival_time"]);
                $arrivalDate_date = date_format($arrivalDate, 'd M Y');
                $arrivalDate_time = date_format($arrivalDate, 'H:i');

                echo "<div class='w-50'>
<h4>Selected Departure Flight</h4>
<p>{$departureFlight["origin_airport_country"]} ({$departureFlight["origin_airport_code"]}) -> 
{$departureFlight["destination_airport_state"]} ({$departureFlight["destination_airport_code"]})</p>
<span>{$flightInfo["passenger_count"]} Passengers</span><br>
<div class='modal-body row'>
  <div class='col-md-6'>
    <span>{$departDate_time}</span><br>
    <small>{$departDate_date}</small>
  </div>
  <div class='col-md-6'>
<span>{$departureFlight["origin_airport_state"]} ({$departureFlight["origin_airport_code"]})</span><br>
<small>{$departureFlight["origin_airport_name"]}</small>
  </div>
</div>
<br>
<div class='row'>
  <div class='col-md-6'>
    <span>{$arrivalDate_time}</span><br>
    <small>{$arrivalDate_date}</small>
  </div>
  <div class='col-md-6'>
<span>{$departureFlight["destination_airport_state"]} ({$departureFlight["destination_airport_code"]})</span><br>
<small>{$departureFlight["destination_airport_name"]}</small>
  </div>
</div>


    </div>";
            }
            ?>
        </div>
        <hr>
        <div id="return-flight">
            <?php
            //departure
            if (isset($returnFlight)){
                $departDate = date_create($returnFlight["departure_time"]);
                $departDate_date = date_format($departDate, 'd M Y');
                $departDate_time = date_format($departDate, 'H:i');

                $arrivalDate = date_create($returnFlight["arrival_time"]);
                $arrivalDate_date = date_format($arrivalDate, 'd M Y');
                $arrivalDate_time = date_format($arrivalDate, 'H:i');

                echo "<div class='w-50'>
<h4>Selected Return Flight</h4>
<p>{$returnFlight["origin_airport_country"]} ({$returnFlight["origin_airport_code"]}) -> 
{$returnFlight["destination_airport_state"]} ({$returnFlight["destination_airport_code"]})</p>
<span>{$flightInfo["passenger_count"]} Passengers</span><br>
<div class='modal-body row'>
  <div class='col-md-6'>
    <span>{$departDate_time}</span><br>
    <small>{$departDate_date}</small>
  </div>
  <div class='col-md-6'>
<span>{$returnFlight["origin_airport_state"]} ({$returnFlight["origin_airport_code"]})</span><br>
<small>{$returnFlight["origin_airport_name"]}</small>
  </div>
</div>
<br>
<div class='modal-body row'>
  <div class='col-md-6'>
    <span>{$arrivalDate_time}</span><br>
    <small>{$arrivalDate_date}</small>
  </div>
  <div class='col-md-6'>
<span>{$returnFlight["destination_airport_state"]} ({$returnFlight["destination_airport_code"]})</span><br>
<small>{$returnFlight["destination_airport_name"]}</small>
  </div>
</div>
    </div>";
            }
            ?>
        </div>
        <!-- checkout here -->
        <button id="btn-proceed">Proceed</button>
    </div>
    <script type="module" src="/assets/js/proceed.js"></script>
</body>
</html>