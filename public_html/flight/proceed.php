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
    $ageCategoryArr = ["adult"=>$flightInfo["adult"], "child"=>$flightInfo["child"],
        "senior"=>$flightInfo["senior"],"infant"=>$flightInfo["infant"]];

    $departureFlightCost = calculateFlightPriceAlternate($departureFlight["flight_base_price"], $ageCategoryArr, $flightInfo["travel_class"],
        ["XSM"=>$flightInfo["passenger_count"]]);
    $departureDiscount = $departureFlight["flight_discount"];
    $departureDiscountCost = $departureFlightCost * $departureDiscount;

    if ($flightInfo["trip_type"] == "RETURN") {
        $returnFlightCost = calculateFlightPriceAlternate($returnFlight["flight_base_price"], $ageCategoryArr, $flightInfo["travel_class"],
            ["XSM"=>$flightInfo["passenger_count"]]);
        $returnDiscount = $returnFlight["flight_discount"];
        $returnDiscountCost = $returnFlightCost * $returnDiscount;
    }

    $total = $departureFlightCost + ($returnFlightCost ?? 0);
    $discountTotal = $departureDiscountCost + ($returnDiscountCost ?? 0);

    $netTotal = $departureFlightCost + ($returnFlightCost ?? 0) - $discountTotal;
}
catch (Exception $e){
    makeToast("error", $e->getMessage(), "Error");
    header("Location: /flight/search.php");
    die();
}



displayToast();
?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <style>
        *{
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            box-sizing: border-box;
            outline: none; border: none;
            text-transform: capitalize;
        }
        body{
            max-width: 1600px;
            margin: auto;
        }
        .parent{
            padding: 2rem 2rem;
            text-align: center;
        }
        .box{
            padding: 2rem 2rem;
            border-radius: 4.5%;
            text-align: center;
            background-color: #F5F5F5;
        }
        .child{
            display: inline-block;
            height: auto;
            width: auto;
            padding: 0 1rem 0 1rem;
            vertical-align: middle;
            vertical-align: top;
        }
        .child-1{
            display: inline-block;
            height: auto;
            width: 100%;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            padding: 1rem 1rem;
            border-radius: 4.5%;
        }
        .child-1 table{
            width: 100%;
        }
        .child-2{
            display: inline-block;
            height: auto;
            width: 100%;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            padding: 1rem 1rem;
            border-radius: 4.5%;
        }
        .child-2 table{
            width: 100%;
        }
        .child-3{
            display: inline-block;
            height: auto;
            width: 350px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            padding: 1rem 1rem;
            border-radius: 4.5%;
        }
        .child-3 table{
            width: 100%;
        }
        .child-3 .proceed-btn{
            background-color: #FF0303;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .child-3 .proceed-btn:hover{
            background: #ED2B2A;
        }
        .top-border {
            border-top-width: 2px;
            border-top-style: solid;
            border-top-color: black;
        }
    </style>
    <title><?= config("name") ?> | Flight Proceed</title>
</head>

<body>

<div class="container-fluid">
<div class="row flex-nowrap">
    <div class="col-auto px-0">
        <?php side_bar() ?>
    </div>
    <main class="col ps-md-2 pt-2">

        <div class="bg-light">
            <?php header_bar("Flight Proceed") ?>
            <div class="container py-5">
                <div class="row ms-3">
                    <h1 class="ms-3 mb-4">Flight Details</h1>
                    <hr/>
                    <div class="col-7">
                        <div class="child-1 p-4">
                            <h3>Selected Departure Flight</h3>

                            <?php
                            flightProceed_flightDetails($departureFlight, $flightInfo);
                            ?>
                        </div>
                        <?php
                        if (isset($returnFlight)){
                            ?>
                            <div class="child-2 p-4 mt-3">
                                <h3>Selected Return Flight</h3>

                                <?php
                                flightProceed_flightDetails($returnFlight, $flightInfo);
                                ?>
                            </div>
                            <?php
                        }
                        ?>

                    </div>
                    <div class="col text-end">
                        <div class="child sticky-top">
                            <div class="child-3 text-center">
                                <h2 class="title text-center mb-3">Price Details</h2>

                                <table class="table table-hover">
                                    <caption class="small">Disclaimer: these prices are with all 5kg baggage. Actual price may differ.</caption>
                                    <thead>
                                    <tr>
                                        <th scope="col" class="text-start">Item</th>
                                        <th scope="col" class="text-end">Price</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    <tr class="top-border">
                                        <td align="left" class="fw-bold">Depart</td>
                                        <td align="right">RM<?= number_format((float)$departureFlightCost, 2, '.',',') ?></td>
                                    </tr>
                                    <?php
                                    if ($flightInfo["trip_type"] == "RETURN") {
                                        ?>

                                        <tr>
                                            <td class="fw-bold text-start">Return</td>
                                            <td class="text-end">RM<?= number_format((float)$returnFlightCost, 2, '.',',') ?></td>
                                        </tr>

                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                    <tfoot>
                                    <tr class="top-border">
                                        <td align="left" class="fw-bold">Subtotal</td>
                                        <td align="right">RM<?= number_format((float)$total, 2, '.',',') ?></td>
                                    </tr>
                                    <tr>
                                        <td align="left" class="fw-bold">Discount</td>
                                        <td align="right">-RM<?= number_format((float)$discountTotal, 2, '.',',') ?></td>
                                    </tr>
                                    <tr class="top-border">
                                        <td align="left" class="fw-bold">Total Price</td>
                                        <td align="right">RM<?= number_format((float)$netTotal, 2, '.',',') ?></td>
                                    </tr>
                                    </tfoot>

                                </table>

                                <button id="btn-proceed" class="proceed-btn my-3">Proceed</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>



        <?php footer(); ?>
    </main>
</div>
</div>

<?php body_script_tag_content(); ?>
<script type="module" src="/assets/js/proceed.js"></script>

</body>
</html>