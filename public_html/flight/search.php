<?php

session_start();
require("../../includes/functions.inc.php");
customer_login_required();

displayToast();
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
    if (empty($_POST) || !array_keys_isset(["return_flight_id", "departure_flight_id", "trip_type", "travel_class",
                                        "adult","child","infant","senior"], $_POST))
    {
        makeToast("warning", "Missing one of the vital values", "Warning");
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
    unset($_SESSION["book"]);
    $_SESSION["book"]["flightInfo"] = $flightInfo;
    header("Location: /flight/proceed.php");
}

//error checking
try {
    if ($_GET) {
        if (!(array_keys_isset(["origin", "destination", "departure", "return", "travel_class", "adult", "child", "infant",
            "senior", "trip_type"], $_GET))) {
            throw new Exception("Please fill in the missing information!");
        }
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

        $ageCategoryArr = ["adult" => $adult, "child" => $child, "infant" => $infant, "senior" => $senior];
        $passengerCount = $adult + $child + $infant + $senior;

        $departure_flights = retrieveFlightsSearch($origin, $destination, $departure, $travelClass, $passengerCount);

        if ($tripType == "RETURN") {
            $return_flights = retrieveFlightsSearch($destination, $origin, $return, $travelClass, $passengerCount);
        }
    }
}
catch(Exception $e) {
    makeToast("warning", $e->getMessage(), "Warning");
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php head_tag_content() ?>
    <style>
    </style>
    <title><?= config("name") ?> | Flight Search</title>
</head>

<body>

<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php header_bar("Flight Search") ?>

            <!--  BOOKINGS HERE todo -->
            <div class="position-relative ">
                <div class="gradient-primary w-100 position-absolute top-0 start-0 end-0 bottom-0" style="height: 320px; z-index: -1; "></div>
                <div class="row container ms-3">
                    <div class="mt-5">
                        <h1 class="text-white fw-bold">Start Booking Your Flight Now</h1>
                        <h5 class="text-white">Find countless flights options & deals to various destinations around the world</h5>
                    </div>
                </div>
                <div class="row justify-content-center mt-4">
                    <div class="col-9">

                        <div class="bg-white rounded-4 shadow ms-3 p-5">
                            <form action="<?php current_page(); ?>" method="get">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="trip-type">Trip-type</label>
                                        <select id="trip-type" name="trip_type" class="form-select">
                                            <option value="ONE-WAY">One-way Trip</option>
                                            <option value="RETURN">Return-trip</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="travel-class">Travel Class</label>
                                        <select id="travel-class" name="travel_class" class="form-select">
                                            <option value="BUS">Business</option>
                                            <option value="PRE">Premium Economy</option>
                                            <option value="ECO">Economy</option>
                                            <option value="FST">First Class</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <div class="dropdown dropend">
                                            <button class="btn btn-danger dropdown-toggle" type="button" id="passenger-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Guests
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="passenger-toggle">
                                                <div class="row mx-3 mt-2">
                                                    <h4><strong class="icon-red">Guests Count</strong></h4>
                                                    <hr>
                                                </div>

                                                <div class="row mx-3 mb-2">
                                                    <div class="col-md-3">
                                                        <label for="adult">Adult:</label>
                                                        <input type="number" id="adult" name="adult" min="0" max="9" value="<?php echo $_GET["adult"] ?? 1; ?>" class="form-control">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="child">Child:</label>
                                                        <input type="number" id="child" name="child" min="0" max="9" value="<?php echo $_GET["child"] ?? 0; ?>" class="form-control">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="infant">Infant:</label>
                                                        <input type="number" id="infant" name="infant" min="0" max="9" value="<?php echo $_GET["infant"] ?? 0; ?>" class="form-control">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="senior">Senior:</label>
                                                        <input type="number" id="senior" name="senior" min="0" max="9" value="<?php echo $_GET["senior"] ?? 0; ?>" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <label for="origin-select">Origin</label>
                                        <select name="origin" id="origin-select" class="form-select">
                                            <option value="" selected disabled>--- Origin ---</option>
                                            <?php
                                            //airports
                                            foreach ($airports as $airport) {
                                                echo "<option value='{$airport["airport_code"]}'>{$airport["airport_state"]} ({$airport["airport_code"]})
                    </option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="destination-select">Destination</label>
                                        <select name="destination" id="destination-select" value="<?php echo $_GET["destination"] ?? ""; ?>" class="form-select">
                                            <option value="" selected disabled>--- Destination ---</option>
                                            <?php
                                            //airports
                                            foreach ($airports as $airport) {
                                                echo "<option value='{$airport["airport_code"]}'>{$airport["airport_state"]} ({$airport["airport_code"]})
                    </option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="row">
                                            <label for="departure">Departure Date:</label>
                                            <input type="date" id="departure" name="departure" min="" value="<?php echo $_GET["departure"] ?? ""; ?>" class="form-control">
                                        </div>
                                        <div class="row" id="return">
                                            <label for="return">Return Date:</label>
                                            <input type="date" name="return" min="" value="<?php echo $_GET["return"] ?? ""; ?>" class="form-control">
                                        </div>
                                    </div>

                                </div>
                                <div class="text-center mt-4">
                                    <input type="submit" class="btn btn-danger float-end" value="Search">
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
            <div class="container py-2 px-4 pb-5 mt-5" >
                <div class="container">
                    <div id="depart-flight-result">
                        <?php
                        if (isset($departure_flights) && $_GET){
                            search_flightDetails($departure_flights, "Departure", $ageCategoryArr, $travelClass);
                        }
                        ?>
                    </div>
                    <div id="return-flight-result" class="d-none">
                        <?php
                        if (isset($return_flights) && $_GET){
                            search_flightDetails($return_flights, "Return", $ageCategoryArr, $travelClass);
                        }
                        ?>
                    </div>
                </div>
            </div>

            <?php footer(); ?>
        </main>

    </div>
</div>

<?php body_script_tag_content();?>
<script type="text/javascript" src="/assets/js/search.js"></script>
</body>

</html>