<?php

session_start();
require("../../includes/functions.inc.php");


login_required();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require("../../includes/book.inc.php");
}

if (!$_SESSION["flightInfo"]) {
    header("Location: /index.php");
    die();
}

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

$ageCategoriesArr = ["adult", "child", "infant", "senior"];


?>
<!DOCTYPE html>
<html>

<head>
    <?php head(); ?>
    <title>Flight Book</title>
</head>

<body>
<div class="d-flex flex-row">
    <a class="navbar-brand" href="/index.php">
        <img class="img-fluid w-50" src="/assets/img/airasiacom_logo.svg">
    </a>
</div>
<h1>Flight Booking</h1>
<hr>


<div class="container">
    <form action="<?php current_page() ?>" method="post" enctype="multipart/form-data">
        <div id="step-1" class="step">
            <h2>Guest Details</h2>
            <div>
                <?php
                    foreach ($ageCategoriesArr as $key) {
                        for ($i = 0, $n = $flightInfo[$key]; $i < $n; $i++) {
                            echo "<div class='row'>
<h5>" . (ucfirst($key)) . " " . ($i + 1) . "</h5><br>
<input type='text' name='passengers[{$key}][{$i}][first_name]' placeholder='First Name'>
<input type='text' name='passengers[{$key}][{$i}][last_name]' placeholder='Last Name'>
<select name='passengers[{$key}][{$i}][gender]'>
    <option value='Male'>Male</option>
    <option value='Female'>Female</option>
</select>
<input type='date' name='passengers[{$key}][{$i}][dob]' placeholder='Date of Birth'>

<input type='hidden' name='passengers[{$key}][{$i}][special_assistance]' value='0'>
<label>
    <span>Special Assistance: </span>
    <input type='checkbox' name='passengers[{$key}][{$i}][special_assistance]' value='1'>
</label>

</div>
";
                        }
                }
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
                    <label for="consent">
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
                <div class="w-75 row">
                    <div class='col-4'>
                        <h4>Departure</h4>
                        <?php
                        //loop here to iterate over passenger
                        foreach ($ageCategoriesArr as $key) {
                            for ($i = 0, $n = $flightInfo[$key]; $i < $n; $i++) {
                                echo "<div class='row'>
                                <h5>" . (ucfirst($key)) . " " . ($i + 1) . "</h5>
                                <select name='passengers[{$key}][{$i}][departure_baggage]'>";
                                foreach ($baggageOptions as $baggage) {
                                    echo "<option value='{$baggage["baggage_price_code"]}'>
        {$baggage["baggage_name"]}</option>";
                                }
                                echo "</select></div>";
                            }
                        }
                        ?>
                    </div>
                    <div class='col-4'>
                        <h4>Return</h4>
                            <?php
                            if (isset($returnFlight)) {
                                foreach ($ageCategoriesArr as $key){
                                    for ($i = 0, $n = $flightInfo[$key]; $i < $n; $i++) {
                                        echo "<div class='row'>
                                <h5>" . (ucfirst($key)) . " " . ($i + 1) . "</h5>
                                <select name='passengers[{$key}][{$i}][return_baggage]'>";
                                        foreach ($baggageOptions as $baggage) {
                                            echo "<option value='{$baggage["baggage_price_code"]}'>
        {$baggage["baggage_name"]}</option>";
                                        }
                                        echo "</select></div>";
                                    }
                                }
                            }
                            ?>
                    </div>
                </div>
            </div>

            <h3>Seating</h3>

            <div id="seat">
                <div class="w-75 row">
                    <div class="col-4">
                        <h4>Departure</h4>
                            <?php

                            //loop here to iterate over passenger
                            $travelClassArr = travelClassAssoc($flightInfo["travel_class"]);
                            foreach ($ageCategoriesArr as $key){
                                for ($i = 0, $n = $flightInfo[$key]; $i < $n; $i++) {
                                    //seat
                                    echo "<div class='row'>
                                <h5>" . (ucfirst($key)) . " " . ($i + 1) . "</h5>
<button class='btn btn-primary' type='button' data-bs-toggle='collapse' data-bs-target='#collapseDepartureSeat{$key}{$i}' aria-expanded='false' aria-controls='collapseDepartureSeat{$key}{$i}'>Seat Choice</button>
<div class='collapse' id='collapseDepartureSeat{$key}{$i}'><div class='card card-body'>";

                                    echo "<div class='row'>";
                                    $capacity = $travelClassArr["class"] . "_capacity";
                                    for ($j = 0, $m = $departureFlight[$capacity]; $j < $m; $j++) {
                                        $disabled = false;
                                        if ($departureFlightAddons != null) {
                                            foreach ($departureFlightAddons as $addon) {
                                                if ($addon["seat_number"] == ($j + 1)) {
                                                    $disabled = true;
                                                    break;
                                                }
                                            }
                                        }
                                        if (($j + 1) % 6 == 0) {
                                            echo "</div><div class='row'>";
                                        }
                                        echo "<div class='col-auto'><input type='radio' name='passengers[{$key}][{$i}][departure_seat]' value='"
                                            . ($j + 1) . "' " . (!$disabled ? "" : "disabled") . "></div>";
                                    }
                                    echo "</div></div></div></div>";
                                }
                            }

                            ?>
                    </div>
                    <div class="col-4">
                        <h4>Return</h4>
                        <div class="row">
                            <?php

                            if (isset($returnFlight))
                            //loop here to iterate over passenger
                            $travelClassArr = travelClassAssoc($flightInfo["travel_class"]);
                            foreach ($ageCategoriesArr as $key){
                                for ($i = 0, $n = $flightInfo[$key]; $i < $n; $i++) {
                                //seat
                                echo "<div class='row'>
                                <h5>" . (ucfirst($key)) . " " . ($i + 1) . "</h5>
                                <button class='btn btn-primary' type='button' data-bs-toggle='collapse' 
data-bs-target='#collapseReturnSeat{$key}{$i}' aria-expanded='false' aria-controls='collapseReturnSeat{$key}{$i}'>Seat Choice</button>
<div class='collapse' id='collapseReturnSeat{$key}{$i}'>";
                                    echo "<div class='row'>";

                                    $capacity = $travelClassArr["class"] . "_capacity";
                                    for ($j = 0, $m = $returnFlight[$capacity]; $j < $m; $j++) {
                                        $disabled = false;
                                        if ($returnFlightAddons != null) {
                                            foreach ($returnFlightAddons as $addon) {
                                                if ($addon["seat_number"] == ($j + 1)) {
                                                    $disabled = true;
                                                    break;
                                                }
                                            }
                                        }
                                        if (($j + 1) % 6 == 0) {
                                            echo "</div><div class='row'>";
                                        }
                                        echo "<div class='col-auto'><input type='radio' name='passengers[{$key}][{$i}][return_seat]' value='"
                                            . ($j + 1) . "' " . (!$disabled ? "" : "disabled") . "></div>";
                                    }
                                    echo "</div></div></div></div>";
                                }

                            }

                            ?>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        <div id="step-3" class="step">
            <h2>Payment Information</h2>
            <!--            qr code here-->

            <!--            submit here-->
            Submit PDF file here: <input type="file" name="payment_file">
            <button type="submit" name="submit" value="true">Submit</button>
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
        </div>

    </form>
</div>

<script type="module" src="/assets/js/book.js"></script>
</body>

</html>