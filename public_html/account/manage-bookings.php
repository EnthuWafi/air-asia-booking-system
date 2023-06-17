<?php

session_start();
require("../../includes/functions.inc.php");

customer_login_required();

$bookings = retrieveAllUserBookings($_SESSION["user_data"]["user_id"]);
?>
<html>
<head>
    <?php head_tag_content(); ?>
    <title>My Bookings</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php header_bar("Manage My Bookings") ?>

            <!--  BOOKINGS HERE todo -->
            <div class="row mx-3 my-5">
                <span class="fs-1">My Bookings</span>
            </div>
            <hr>
            <?php
            if ($bookings != null) {
                foreach ($bookings as $booking) {
                    $flight = retrieveBookingFlights($booking["booking_id"]);

                    if (empty($flight)) {
                        continue;
                    }

                    //status = upcoming, departed, in progress
                    $today = date("Y-m-d H:i:s");
                    $departureDate = $flight[0]["departure_time"];
                    $arrivalDate = $flight[0]["arrival_time"];

                    $departureUnformatted = date_create($departureDate);
                    $arrivalUnformatted = date_create($arrivalDate);
                    $departureFormatted = date_format($departureUnformatted, "d M Y");

                    $duration = date_create($flight[0]["duration"]);
                    $durationHours = date_format($duration, "G")."h ".date_format($duration, "i")."m";

                    $departureHourSimple = date_format($departureUnformatted, "H:i");
                    $arrivalHourSimple = date_format($arrivalUnformatted , "H:i");

                    $isReturnTicket = $booking["trip_type"] === "RETURN";
                    if ($isReturnTicket) {
                        $departureReturnDate = $flight[1]["departure_time"];
                        $arrivalReturnDate = $flight[1]["arrival_time"];

                        $departureReturnUnformatted = date_create($departureReturnDate);
                        $arrivalReturnUnformatted = date_create($arrivalReturnDate);

                        $departureReturnFormatted = date_format($departureReturnUnformatted, "d M Y");

                        $durationReturn = date_create($flight[0]["duration"]);
                        $durationHoursReturn = date_format($durationReturn, "G")."h ".date_format($durationReturn, "i")."m";

                        $departureHourReturnSimple = date_format($departureReturnUnformatted, "H:i");
                        $arrivalHourReturnSimple = date_format($arrivalReturnUnformatted, "H:i");
                    }

                    $status = "";
                    if ($departureDate > $today) {
                        $status = "Upcoming";
                    }
                    else if ($today < $arrivalDate) {
                        $status = "In Progress";
                    }
                    else {
                        $status = "Departed";
                    }

                    //elements
                    $arrow = $isReturnTicket ? "<->" : "-->";
                    $returnDepartureText = $isReturnTicket ? "Return: {$departureReturnFormatted}" : "";
                    $returnFlightDiv = $isReturnTicket ? "<div class='row'>
            <div class='col-sm-2 order-first'>
                <div class='row'>{$departureHourReturnSimple}</div>
                <div class='row'>{$flight[1]["origin_airport_code"]}</div>
            </div>
            <div class='col-sm-1 align-middle'>-----</div>
            <div class='col-sm-2'>
                <div class='row'>{$arrivalHourReturnSimple}</div>
                <div class='row'>{$flight[1]["destination_airport_code"]}</div>
            </div>
            <div class='col ms-auto me-auto'>
                <span class='text-muted text-center'>{$durationHoursReturn}</span>
            </div>
        </div>" : "";

                    echo "
<div class='shadow p-5 bg-body rounded'>
    <div class='row'>
        <div class='col ms-auto'>
            <strong>{$status}</strong>
        </div>
    </div>
    <div class='row'>
        <div class='row'>
            <div class='col-sm-2 order-first'>
                <div class='row fs-3'>
                    {$flight[0]["origin_airport_code"]} {$arrow} {$flight[0]["destination_airport_code"]}
                </div>
                <div class='row'>
                    <span class='text-muted'> Departure: {$departureFormatted}</span>
                    <span class='text-muted'> {$returnDepartureText}</span>
                </div>
            </div>
            <div class='col ms-auto'>
                <div class='row'>
                    <span class='text-middle'>Booking Reference Number</span>
                </div>
                <div class='row'>
                    <span class='text-middle'>{$flight[0]["booking_reference"]}</span>
                </div> 
            </div>
        </div>
        <div class='row'>
            <div class='col-sm-2 order-first'>
                <div class='row'>{$departureHourSimple}</div>
                <div class='row'>{$flight[0]["origin_airport_code"]}</div>
            </div>
            <div class='col-sm-1 align-middle'>-----</div>
            <div class='col-sm-2'>
                <div class='row'>{$arrivalHourSimple}</div>
                <div class='row'>{$flight[0]["destination_airport_code"]}</div>
            </div>
            <div class='col ms-auto me-auto'>
                <span class='text-muted text-center'>{$durationHours}</span>
            </div>
        </div>
        <hr>
        {$returnFlightDiv}
    </div>
</div>";
                }
            }

            ?>

            <?php footer(); ?>
        </main>

    </div>
</div>

<?php body_script_tag_content();?>
<script>

</script>
</body>
</html>
