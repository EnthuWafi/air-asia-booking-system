<?php

session_start();
require("../../includes/functions.inc.php");


customer_login_required();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    confirm_booking();

    //go to invoice ig
    header("Location: /flight/confirm.php");
}

if (!$_SESSION["flightInfo"]) {
    header("Location: /index.php");
    die();
}

$_SESSION["token"] = create_token();

$baggageOptions = retrieveBaggageOptions();

$flightInfo = $_SESSION["flightInfo"];

$departureFlight = retrieveFlight($flightInfo["departure_flight_id"], $flightInfo["travel_class"], $flightInfo["passenger_count"]);
$departureFlightAddons = retrieveFlightAddon($departureFlight["aircraft_id"], $flightInfo["travel_class"]);

$returnFlight = null;
$returnFlightAddons = null;

if (isset($flightInfo["return_flight_id"])) {
    $returnFlight = retrieveFlight($flightInfo["return_flight_id"], $flightInfo["travel_class"], $flightInfo["passenger_count"]);
    $returnFlightAddons = retrieveFlightAddon($returnFlight["aircraft_id"], $flightInfo["travel_class"]);
}

$ageCategoryArr = ["adult"=>$flightInfo["adult"], "child"=>$flightInfo["child"], "infant"=>$flightInfo["infant"],
    "senior"=>$flightInfo["senior"]];

?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <title>Flight Book</title>
</head>

<body>
<div class="d-flex flex-row">
    <a class="navbar-brand" href="/index.php">
        <img class="img-fluid w-50" src="/assets/img/airasiacom_logo.svg" alt="airasia">
    </a>
</div>
<h1>Flight Booking</h1>
<hr>

<div class="container">
<div class="row justify-content-center">
    <div class="col-9">
        <div class="shadow p-5 bg-body rounded">
        <form action="<?php current_page() ?>" id="myForm" method="post" enctype="multipart/form-data">
            <div id="step-1" class="step">
                <h2>Guest Details</h2>
                <div>
                    <?php
                    book_guestDetails($flightInfo);
                    ?>
                    <h2>Contact Details</h2>
                    <div class="row">
                        <div class="col">
                            Phone Number: <input type="tel" name="phone" required>
                        </div>
                        <div class="col">
                            Email: <input type="email" name="email" required>
                        </div>
                    </div>
                    <div class="row">
                        <input type="hidden" name="consent" value="0">
                        <label>
                            <input class="me-auto" type="checkbox" id="consent" name="consent" value="1" required>
                            I agree to AirAsia's terms and conditions.
                        </label>
                    </div>
                </div>
            </div>
            <div id="step-2" class="step">
                <h2>Flight Add-on</h2>
                <h3>Baggage</h3>
                <div id="baggage">
                    <div class="w-100 row">
                        <div class='col-4' id="departure-baggage-col">
                            <h4>Departure</h4>
                            <?php
                            book_baggageAddon($flightInfo, $baggageOptions, "departure_baggage");
                            ?>
                        </div>
                        <div class='col-4 ms-3' id="return-baggage-col">
                            <h4>Return</h4>
                            <?php
                            if (isset($returnFlight)) {
                                book_baggageAddon($flightInfo, $baggageOptions, "return_baggage");
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <h3>Seating</h3>

                <div id="seat">
                    <div class="row" id="departure-seat-col">
                        <div class="col-11">
                            <h4>Departure</h4>
                            <?php
                                book_seatingAddon($flightInfo, $departureFlight, $departureFlightAddons, "departure_seat");
                            ?>
                        </div>
                        <div class="col-11" id="return-seat-col">
                            <h4>Return</h4>
                            <?php
                            if (isset($returnFlight)){
                                book_seatingAddon($flightInfo, $returnFlight, $returnFlightAddons, "return_seat");
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="step-3" class="step">
                <h2>Payment</h2>
                <!-- qr code here-->

                <!-- submit here-->
                Submit PDF file here: <input type="file" name="payment_file">
                <button type="submit" name="submit" value="true">Submit</button>
            </div>
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>">
        </form>
        </div>
    </div>
    <div class="col-3">
        <div class="shadow p-3 mb-5 pt-3 bg-body rounded sticky-top">
            <h2>Total Price: </h2>
            <h3 id="total-cost">RM </h3>
        </div>
    </div>
</div>
</div>
<?php footer(); ?>
<?php body_script_tag_content(); ?>
<script>
    //baggage array
    const baggageJSON = <?= json_encode(retrieveBaggageOptions()); ?>;

    const originalCostDeparture = <?= json_encode(calculateFlightPriceNoBaggage($departureFlight["flight_base_price"], $ageCategoryArr,
        $flightInfo["travel_class"])) ?>;

    const originalCostReturn = <?= isset($returnFlight) ? json_encode(calculateFlightPriceNoBaggage($returnFlight["flight_base_price"], $ageCategoryArr,
        $flightInfo["travel_class"])) : 0 ?>;

</script>
<script type="text/javascript" src="/assets/js/book.js"></script>
</body>

</html>