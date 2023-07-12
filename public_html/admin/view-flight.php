<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

displayToast();

if (!$_GET || empty($_GET["flight_id"])) {
    header("Location: /admin/manage-flights.php");
    die();
}

$flightID = htmlspecialchars($_GET["flight_id"]);
$flight = retrieveFlight($flightID) or header("Location: /admin/manage-flights.php");
//flight addons
$flightAddonsFirstClass = retrieveFlightAddon($flightID, "FST");
$flightAddonsBusiness = retrieveFlightAddon($flightID, "BUS");
$flightAddonsPremium = retrieveFlightAddon($flightID, "PRE");
$flightAddonsEconomy = retrieveFlightAddon($flightID, "ECO");

//flights details
$flightCode = sprintf("%05d",$flight["flight_id"]);

?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <link rel="stylesheet" href="/assets/css/plane.css">

    <title><?= config("name") ?> | View Flights</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php admin_side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php admin_header_bar("View Flight") ?>

            <!-- todo DASHBOARD here  -->

            <div class="container">
                <div class="row mt-4 gx-4 ms-3">
                    <div class="shadow p-4 mb-3 bg-body rounded-3 row gx-3">
                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <span class="h3">Flight <span class="text-info">#<?= $flightCode ?></span></span>
                            </div>
                            <div class="col-auto">
                                <span class="h5 text-muted ">by <?= $flight["username"] ?> (<?= $flight["admin_code"] ?>)</span>
                            </div>

                        </div>
                        <div class="row">
                            <span class="text-muted">Created on <span class="text-body-secondary"><?= formatDateFriendly($flight["date_created"]) ?></span></span>
                        </div>
                        <div class="container row justify-content-between">

                            <div class="col-lg-8 col-sm-auto">
                                <div class="row mt-5">
                                    <div class="col-md-12">
                                        <h2><img src="<?= "{$flight["airline_image"]}" ?>" class="img-fluid object-fit-cover" style="width: 80px; height: 40px"> Flight Details</h2>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <strong>Origin:</strong> <?= "{$flight["origin_airport_state"]} ({$flight["origin_airport_code"]})" ?>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Destination:</strong> <?= "{$flight["destination_airport_state"]} ({$flight["destination_airport_code"]})" ?>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <strong>Departure:</strong> <?= formatDateTime($flight["departure_time"]) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Arrival:</strong> <?= formatDateTime($flight["arrival_time"]) ?>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <strong>Duration:</strong> <?= formatDuration($flight["duration"]) ?>
                                    </div>
                                </div>

                            </div>
                            <div class="col-lg-3 col-sm-auto">
                                <div class="row mt-5">
                                    <div class="col-md-12">
                                        <h2>Aircraft</h2>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mt-3">
                                    <div class="col">
                                        <strong>Aircraft:</strong> <span class="icon-red"><?= $flight["aircraft_name"] ?></span>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <strong>Economy:</strong> <?= $flight["economy_capacity"] ?>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Premium:</strong>  <?= $flight["premium_economy_capacity"] ?>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <strong>Business:</strong> <?= $flight["business_capacity"] ?>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>First Class:</strong>  <?= $flight["first_class_capacity"] ?>
                                    </div>
                                </div>
                            </div>


                            <div class="row justify-content-center mt-5 mb-2">
                                <div class="col-auto">
                                    <a type="button" class="btn btn-danger btn-block" style="width: 110px;" href="/admin/manage-flights.php">
                                        <i class="bi bi-arrow-left-circle"></i> Back
                                    </a>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-danger btn-block" style="width: 150px;" data-bs-toggle="modal" data-bs-target="#staticSeating">
                                        <i class='bx bx-chair'></i> View Seating
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal airplane -->
                    <div class="modal fade" id="staticSeating" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title">Aircraft > <span class="text-danger"><?= $flight["aircraft_name"] ?></span></h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="pointer-events: none;">
                                    <div class="row justify-content-end">
                                        <div class="col-auto">
                                            <div class="plane">

                                                <div class="cockpit">
                                                    <h1 class="text-center pb-3 text-body-emphasis">Flight <?= $flight["aircraft_name"] ?></h1>
                                                </div>

                                                <div class="exit exit--front fuselage"></div>

                                                <ol class="cabin fuselage">
                                                    <div class='row justify-content-center'>
                                                        <span class='text-center h4'><i class="bi bi-caret-down-fill"></i> First Class <i class="bi bi-caret-down-fill"></i></span>
                                                    </div>
                                                    <?php

                                                    echo admin_cabinSeating($flightAddonsFirstClass, $flight["first_class_capacity"], "FST");

                                                    ?>
                                                </ol>
                                                <div class="exit exit--front fuselage"></div>
                                                <ol class="cabin fuselage">
                                                    <div class='row justify-content-center'>
                                                        <span class='text-center h4'><i class="bi bi-caret-down-fill"></i> Business <i class="bi bi-caret-down-fill"></i></span>
                                                    </div>
                                                    <?php
                                                    echo admin_cabinSeating($flightAddonsBusiness, $flight["business_capacity"], "BUS");

                                                    ?>
                                                </ol>
                                                <div class="exit exit--front fuselage"></div>
                                                <ol class="cabin fuselage">
                                                    <div class='row justify-content-center'>
                                                        <span class='text-center h4'><i class="bi bi-caret-down-fill"></i> Premium Eco <i class="bi bi-caret-down-fill"></i></span>
                                                    </div>
                                                    <?php

                                                    echo admin_cabinSeating($flightAddonsPremium, $flight["premium_economy_capacity"], "PRE");

                                                    ?>
                                                </ol>
                                                <div class="exit exit--front fuselage"></div>
                                                <ol class="cabin fuselage">
                                                    <div class='row justify-content-center'>
                                                        <span class='text-center h4'><i class="bi bi-caret-down-fill"></i> Economy <i class="bi bi-caret-down-fill"></i></span>
                                                    </div>
                                                    <?php

                                                    echo admin_cabinSeating($flightAddonsEconomy, $flight["economy_capacity"], "ECO");
                                                    ?>
                                                </ol>
                                                <div class="exit exit--back fuselage"></div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-auto">
                                            <div class="shadow px-2 py-5 rounded-4">
                                                <h3 class="text-center text-decoration-underline">Seat Guide</h3>
                                                <div class="row align-items-center mt-3">
                                                    <div class="col-2">
                                                        <img src="/assets/img/taken-seat.png">
                                                    </div>
                                                    <div class="col">
                                                        <span>Booked Seat</span>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center">
                                                    <div class="col-2">
                                                        <img src="/assets/img/available-seat.png">
                                                    </div>
                                                    <div class="col">
                                                        <span>Empty Seat</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
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
<?php body_script_tag_content();?>
<script type="text/javascript" src="/assets/js/modal.js"></script>
</body>

</html>