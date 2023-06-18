<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

displayToast();

$flightsCount = retrieveCountFlights()["count"] ?? 0;
$flights = retrieveAllFlights();
?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <title><?= config("name") ?> | Admin Dashboard</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php admin_side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php admin_header_bar("Manage Flights") ?>

            <!-- todo DASHBOARD here  -->
            <div class="container">
                <div class="row mt-4 gx-4 ms-3">
                    <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                        <div class="row">
                            <span class="h3"><?= $flightsCount ?> flights found</span>
                        </div>
                        <div class="row mt-3">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Airline</th>
                                    <th scope="col">Origin</th>
                                    <th scope="col">Destination</th>
                                    <th scope="col">Departure</th>
                                    <th scope="col">Duration</th>
                                    <th scope="col">Base Price</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Aircraft</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($flights != null) {
                                    $count = 1;
                                    foreach ($flights as $flight) {

                                        //status = upcoming, departed, in progress
                                        $today = date("Y-m-d H:i:s");

                                        $departureDate = $flight["departure_time"];
                                        $arrivalDate = $flight["arrival_time"];

                                        $departureUnformatted = date_create($departureDate);
                                        $arrivalUnformatted = date_create($arrivalDate);
                                        $departureFormatted = date_format($departureUnformatted, "d M Y");

                                        $duration = date_create($flight["duration"]);
                                        $durationHours = date_format($duration, "G")."h ".date_format($duration, "i")."m";

                                        $flightBaseCost = number_format((float)$flight["flight_base_price"], 2, '.', '');

                                        $status = "";
                                        if ($departureUnformatted > $today) {
                                            $status = "Upcoming";
                                        }
                                        else if ($today < $arrivalUnformatted) {
                                            $status = "In Progress";
                                        }
                                        else {
                                            $status = "Departed";
                                        }

                                        echo
                                        "<tr>
                                            <th scope='row'>$count</th>
                                            <td><img src='{$flight["airline_image"]}' width='50' height='73'></td>
                                            <td>{$flight["origin_airport_code"]}</td>
                                            <td>{$flight["destination_airport_code"]}</td>
                                            <td>{$departureFormatted}</td>
                                            <td>{$durationHours}</td>
                                            <td>RM{$flightBaseCost}</td>
                                            <td>{$status}</td>
                                            <td>{$flight["aircraft_name"]}</td>
                                            <td>
                                                <form action='manage-flights.php' id='{$flight["flight_id"]}' method='post'>
                                                    <input type='hidden' name='booking_id' value='{$flight["flight_id"]}'>
                                                    <a type='button' data-bs-toggle='modal' data-bs-target='#deleteStatic' onclick='updateModal({$flight["flight_id"]}, \"modal-btn-delete\");' class='h4'>
                                                    <i class='bi bi-trash'></i></a>
                                                </form>    
                                            </td>
                                            
                                        </tr>";
                                        $count++;
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- modal delete -->
                    <div class='modal fade' id='deleteStatic' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header bg-light-subtle'>
                                    <h5 class='modal-title' id='staticBackdropLabel'>Delete user?</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body bg-danger-subtle'>
                                    <div class="px-3">
                                        <div class="mb-1">
                                            <span class="fw-bolder">Warning</span>
                                        </div>
                                        <span class="text-black mt-3">This action cannot be reversed!<br>Proceed with caution.</span>
                                    </div>

                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' id="modal-btn-delete" form="" class='btn btn-danger'>I understand</button>
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