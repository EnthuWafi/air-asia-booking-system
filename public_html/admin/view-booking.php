<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

displayToast();

if (!$_GET || empty($_GET["booking_id"])){
    header("Location: /admin/manage-my-bookings.php");
    die();
}

$booking = retrieveBooking(htmlspecialchars($_GET["booking_id"]));

?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <title><?= config("name") ?> | View Booking</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php admin_side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php admin_header_bar("View Booking") ?>

            <!-- todo view booking here  -->
            <div class="container">
                <div class="row mt-4 gx-4 ms-3">
                    <div class="shadow p-3 mb-3 bg-body rounded row gx-3">
                        <div class="row">
                            <span class="h3">Booking Reference Number <span class="text-info">#<?= $booking["booking_reference"] ?></span>
                            </span>
                        </div>
                        <div class="container">
                            <div class="row mt-5">
                                <div class="col-md-12">
                                    <h2>Booking Details</h2>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Customer Name:</strong> John Doe
                                </div>
                                <div class="col-md-6">
                                    <strong>Booking Reference:</strong> ABC123XYZ
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Origin:</strong> New York (JFK)
                                </div>
                                <div class="col-md-6">
                                    <strong>Destination:</strong> London (LHR)
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Airline:</strong> British Airways
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-md-12">
                                    <h2>Flights Included</h2>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <?php
                                $flights = retrieveBookingFlights($booking["booking_id"]);
                                $flightDiv = admin_bookingFlightsDisplay($flights);
                                echo $flightDiv;
                                ?>
                            </div>
<!--                            <div class="row mt-3">-->
<!--                                <div class="col-md-6">-->
<!--                                    <strong>Flight 1:</strong> BA1234-->
<!--                                </div>-->
<!--                                <div class="col-md-6">-->
<!--                                    <strong>Departure:</strong> New York (JFK)-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="row mt-3 mb-5">-->
<!--                                <div class="col-md-6">-->
<!--                                    <strong>Flight 2:</strong> BA5678-->
<!--                                </div>-->
<!--                                <div class="col-md-6">-->
<!--                                    <strong>Departure:</strong> London (LHR)-->
<!--                                </div>-->
<!--                            </div>-->
                        </div>
                    </div>

                </div>
            </div>




            <?php footer(); ?>
        </main>

    </div>
</div>
<?php body_script_tag_content();?>
</body>

</html>