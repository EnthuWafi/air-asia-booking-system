<?php

session_start();
require("../../includes/functions.inc.php");


customer_login_required();


$flightInfo = null;
$returnFlight = null;
$departureFlight = null;

//check session values
if (isset($_SESSION["book"]["flightInfo"])) {
    $flightInfo = $_SESSION["book"]["flightInfo"];
}
else{
    makeToast("warning", "Flight info was not set! Please search a flight again!", "Warning");
    header("Location: /index.php");
    die();
}

try{
    //check if similar booking exists in user booking
    $userBookings = retrieveAllUserBookings($_SESSION["user_data"]["user_id"]);
    //flights
    //ok first retrieve from flights again
    $departureFlight = retrieveFlightSearch($flightInfo["departure_flight_id"], $flightInfo["travel_class"], $flightInfo["passenger_count"]);
    $returnFlight = null;

    if ($flightInfo["trip_type"] == "RETURN") {
        $returnFlight = retrieveFlightSearch($flightInfo["return_flight_id"], $flightInfo["travel_class"], $flightInfo["passenger_count"]);
    }

    if ($userBookings != null) {
        foreach ($userBookings as $booking) {
            $bookingFlights = retrieveBookingFlights($booking["booking_id"]);
            foreach ($bookingFlights as $flight) {
                if ($flight["flight_id"] == $departureFlight["flight_id"]){
                    throw new Exception("Flight has already been booked by user before!<br>Please book another flight!");
                }
                if (!empty($returnFlight)){
                    if ($flight["flight_id"] == $returnFlight["flight_id"]){
                        throw new Exception("Flight has already been booked by user before!<br>Please book another flight!");
                    }
                }
            }
        }
    }

}
catch (exception $e){
    makeToast("error", $e->getMessage(), "Error");
    header("Location: /flight/search.php");
    die();
}

displayToast();
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
        <button id="btn-proceed" class="btn btn-outline-primary">Proceed</button>
    </div>
    <?php body_script_tag_content(); ?>
    <script type="module" src="/assets/js/proceed.js"></script>
</body>
</html>