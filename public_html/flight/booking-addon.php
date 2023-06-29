<?php

session_start();
require("../../includes/functions.inc.php");

customer_login_required();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){
                //passenger session
                if (array_keys_isset(["passengers", "phone", "email", "consent"], $_POST)){
                    $contactInfo = ["email"=>htmlspecialchars($_POST["email"]), "phone"=>htmlspecialchars($_POST["phone"]),
                        "consent"=>htmlspecialchars($_POST["consent"])];

                    if ($contactInfo["consent"] != 1) {
                        throw new Exception("You can't proceed without giving your consent!");
                    }

                    $_SESSION["contactInfo"] = $contactInfo;
                    $_SESSION["passengers"] = htmlspecialchars($_POST["passengers"]);

                    header("Location: /flight/booking-payment.php");
                    die();
                }
            }
            else{
                makeToast("warning", "Please refrain from attempting to resubmit previous form", "Warning");
            }
        }
        else {
            throw new exception("Token not found");
        }
    }
    catch (exception $e){
        makeToast("error", $e->getMessage(), "Error");
    }

    header("Location: /flight/booking-addon.php");
    die();
}

if (!isset($_SESSION["flightInfo"])) {
    makeToast("error", "Flight info was not found. Please try searching flight again!", "Error");
    header("Location: /index.php");
    die();
}

$flightInfo = $_SESSION["flightInfo"];
$baggageOptions = retrieveBaggageOptions();

$departureFlight = retrieveFlight($flightInfo["departure_flight_id"]);
$departureFlightAddons = retrieveFlightAddon($departureFlight["flight_id"], $flightInfo["travel_class"]);

$returnFlight = null;
$returnFlightAddons = null;

if (isset($flightInfo["return_flight_id"])) {
    $returnFlight = retrieveFlightSearch($flightInfo["return_flight_id"], $flightInfo["travel_class"], $flightInfo["passenger_count"]);
    $returnFlightAddons = retrieveFlightAddon($returnFlight["flight_id"], $flightInfo["travel_class"]);
}

$ageCategoryArr = ["adult"=>$flightInfo["adult"], "child"=>$flightInfo["child"], "infant"=>$flightInfo["infant"],
    "senior"=>$flightInfo["senior"]];

displayToast();
$token = getToken();
?>
<html>
<head>
    <?php head_tag_content(); ?>
    <style type="text/css" href="/assets/css/plane.css"></style>
    <title><?= config("name") ?> | Booking Flight Add-ons</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php header_bar("Flight Add-ons") ?>

            <div class="container py-2 px-4 pb-5 mt-3 border rounded-4">
                <div class="row mt-4 gx-4 ms-3">
                    <div class="shadow p-3 mb-5 bg-body rounded row">
                        <form method="post" action="<?php current_page(); ?>">
                            <div class="row mt-3">
                                <h2 class="fs-2 mb-3">Passengers Baggage</h2>
                            </div>
                            <div class="row mt-2">
                                <div class='col'>
                                    <?php
                                    $baggageDepart = book_baggageAddon($flightInfo, $baggageOptions, "departure_baggage");
                                    echo "<div class='row'>
                                            <h2 class='fs-4 mb-3'>Departure Flight</h2>
                                          </div>
                                          <div class='row'>
                                            {$baggageDepart}                                    
                                          </div>";
                                    ?>
                                </div>
                                <div class='col'">
                                    <?php
                                    if (isset($returnFlight)) {
                                        $baggageReturn = book_baggageAddon($flightInfo, $baggageOptions, "return_baggage");
                                        echo "<div class='row'>
                                                <h2 class='fs-4 mb-3'>Return Flight</h2>
                                              </div>
                                              <div class='row'>
                                                {$baggageReturn}                                    
                                              </div>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <h2 class="fs-2 mb-3">Passengers Seating</h2>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <?php
                                    $seatingDepart = book_baggageAddon($flightInfo, $baggageOptions, "departure_baggage");
                                    echo "<div class='row'>
                                            <h2 class='fs-4 mb-3'>Departure Flight</h2>
                                          </div>
                                          <div class='row'>
                                                                               
                                          </div>";
                                    book_seatingAddon($flightInfo, $departureFlight, $departureFlightAddons, "departure_seat");
                                    ?>
                                </div>
                                <div class="col" id="return-seat-col">
                                    <?php
                                    if (isset($returnFlight)){
                                        book_seatingAddon($flightInfo, $returnFlight, $returnFlightAddons, "return_seat");
                                    }
                                    ?>
                                </div>
                                <input type="hidden" name="token" value="<?= $token ?>">
                                <a type="button" class="btn btn-outline-primary mt-3 ms-auto" href="/flight/booking-guest.php">Back</a>
                                <button type="submit" class="btn btn-outline-primary mt-3 ms-auto float-end">Next</button>
                            </div>




                        </form>
                    </div>
                </div>
            </div>

            <?php footer(); ?>
        </main>

    </div>
</div>

<?php body_script_tag_content();?>
<script type="text/javascript" src="/assets/js/modal.js"></script>
</body>
</html>
