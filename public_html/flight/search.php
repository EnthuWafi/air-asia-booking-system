<?php

session_start();
require("../../includes/functions.inc.php");


$airports = retrieveAirports();
$departure_flights = null;
$return_flights = null;
//passenger count
$adult = null;
$child = null;
$infant = null;
$senior = null;
$travel_class = null;

//save in session
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    //check post values (required!) return flight not required
    if (empty($_POST) || !array_keys_isset_or_not(array("return_flight_id", "departure_flight_id", "trip_type", "travel_class",
                                        "adult","child","infant","senior"), $_POST))
    {
        header("Location: /index.php");
        die();
    }

    //departure flights
    $flightInfo = ["departure_flight_id" => htmlspecialchars($_POST["departure_flight_id"]),
        "trip_type" => htmlspecialchars(strtoupper($_POST["trip_type"])),
        "travel_class" => htmlspecialchars(strtoupper($_POST["travel_class"])),
    "adult" => htmlspecialchars($_POST["adult"]), "child" => htmlspecialchars($_POST["child"]),
        "infant" => htmlspecialchars($_POST["infant"]), "senior" => htmlspecialchars($_POST["senior"]),
        "return_flight_id" => null];
    //passenger count
    $passengerCount = $flightInfo["adult"] + $flightInfo["child"] + $flightInfo["infant"] + $flightInfo["senior"];
    $flightInfo["passenger_count"] = htmlspecialchars($passengerCount);

    //return flights
    if ($flightInfo["trip_type"] == "RETURN"){
        $flightInfo["return_flight_id"] = htmlspecialchars($_POST["return_flight_id"]);
    }

    $_SESSION["flightInfo"] = $flightInfo;
    header("Location: /flight/proceed.php");
}

//error checking
if ($_GET) {
    $origin = filter_var($_GET["origin"], FILTER_SANITIZE_SPECIAL_CHARS);
    $destination = filter_var($_GET["destination"], FILTER_SANITIZE_SPECIAL_CHARS);
    $departure = filter_var($_GET["departure"], FILTER_SANITIZE_SPECIAL_CHARS);
    $return = filter_var($_GET["return"], FILTER_SANITIZE_SPECIAL_CHARS);

    $travelClass = htmlspecialchars($_GET["travel_class"]);
    $adult = filter_var($_GET["adult"], FILTER_SANITIZE_SPECIAL_CHARS);
    $child = filter_var($_GET["child"], FILTER_SANITIZE_SPECIAL_CHARS);
    $infant = filter_var($_GET["infant"], FILTER_SANITIZE_SPECIAL_CHARS);
    $senior = filter_var($_GET["senior"], FILTER_SANITIZE_SPECIAL_CHARS);
    $tripType = htmlspecialchars($_GET["trip_type"]);

    $passengerCount = $adult + $child + $infant + $senior;

    $departure_flights = retrieveFlights($origin, $destination, $departure, $travelClass, $passengerCount);

    if ($tripType == "RETURN"){
        $return_flights = retrieveFlights($destination, $origin, $return, $travelClass, $passengerCount);
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php head() ?>

    <title><?= config("name") ?> | Flight Search</title>
</head>

<body>
    <div class="d-flex flex-row">
        <a class="navbar-brand" href="/index.php">
            <img class="img-fluid w-50" src="/assets/img/airasiacom_logo.svg">
        </a>
    </div>
    <h1>Flight Search</h1>
    <hr>
    <form action="<?php current_page(); ?>" method="get">
        <label for="trip-type">Trip-type</label>
        <select id="trip-type" name="trip_type">
            <option value="ONE-WAY">One-way Trip</option>
            <option value="RETURN">Return-trip</option>
        </select>
        <label for="travel-class">Travel Class</label>
        <select id="travel-class" name="travel_class">
            <option value="BUS">Business</option>
            <option value="PRE">Premium Economy</option>
            <option value="ECO">Economy</option>
            <option value="FST">First Class</option>
        </select>
        <br>
        <label for="adult">Adult: </label><input type="number" id="adult" name="adult" min="0" max="9" value="<?php echo $_GET["adult"] ?? 1; ?>">
        <label for="child">Child:</label><input type="number" id="child" name="child" min="0" max="9" value="<?php echo $_GET["child"] ?? 0; ?>">
        <label for="infant">Infant: </label><input type="number" id="infant" name="infant" min="0" max="9" value="<?php echo $_GET["infant"] ?? 0; ?>">
        <label for="senior">Senior: </label><input type="number" id="senior" name="senior" min="0" max="9" value="<?php echo $_GET["senior"] ?? 0; ?>">

        <br>
        <label for="origin-select">Origin</label><select name="origin" id="origin-select">
            <option></option>
            <?php
            //airports
            foreach ($airports as $airport) {
                echo "<option value='{$airport["airport_code"]}'>{$airport["airport_state"]} ({$airport["airport_code"]})
                </option>";
            }
            ?>
        </select>

        <label for="destination-select">Destination</label><select name="destination" id="destination-select" value="<?php echo $_GET["destination"] ?? ""; ?>">
            <option></option>
            <?php
            //airports
            foreach ($airports as $airport) {
                echo "<option value='{$airport["airport_code"]}'>{$airport["airport_state"]} ({$airport["airport_code"]})
                </option>";
            }
            ?>
        </select>
        <br>
        <label for="departure">Departure Date: </label><input type="date" id="departure" name="departure" min="" value="<?php echo $_GET["departure"] ?? ""; ?>">
        <label for="return">Return Date</label><input type="date" id="return" name="return" min="" value="<?php echo $_GET["return"] ?? ""; ?>">
        <br>
        <input type="submit">
    </form>

    <div class="container">
    <div>
        <ol id="depart-flight-result">
            <?php
            if ($departure_flights != null){
                //departure
                echo "<h2>Departure Flights</h2><hr>";
                foreach ($departure_flights as $flight) {
                    echo "<li><div>
<span>{$flight["departure_time"]} - {$flight["arrival_time"]}</span><br>
<span>From {$flight["origin_airport_name"]} to {$flight["destination_airport_name"]}</span>
<button class='ms-auto' name='departure_flight_id' value='{$flight["flight_id"]}'>Book</button>
</div></li>";
                }
            }
            ?>
        </ol>
    </div>
    <div>
        <ol id="return-flight-result" class="d-none">
            <?php
            if ($return_flights != null){
                //return
                echo "<h2>Return Flights</h2><hr>";
                foreach ($return_flights as $flight) {
                    echo "<li><div>
<span>{$flight["departure_time"]} - {$flight["arrival_time"]}</span><br>
<span>From {$flight["origin_airport_name"]} to {$flight["destination_airport_name"]}</span>
<button class='ms-auto' name='return_flight_id' value='{$flight["flight_id"]}'>Book</button>
</div></li>";
                }
            }
            ?>
        </ol>
    </div>
    </div>
    <script type="text/javascript" src="/assets/js/search.js"></script>
</body>

</html>