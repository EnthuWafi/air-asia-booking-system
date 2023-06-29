<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

displayToast();

if (!$_GET) {
    header("Location: /admin/manage-flights.php");
    die();
}

$flightID = htmlspecialchars($_GET["flight_id"]);
$flight = retrieveFlight($flightID) or header("Location: /admin/manage-flights.php");

$token = getToken();
//flights details
$flightCode = sprintf("%05d",$flight["flight_id"]);

$flight_id = $flight['flight_id'];
$user_id = $flight['user_id'];
$username = $flight['username'];
$password = $flight['password'];
$email = $flight['email'];
$user_fname = $flight['user_fname'];
$user_lname = $flight['user_lname'];
$registration_date = $flight['registration_date'];
$user_type = $flight['user_type'];

$origin_airport_code = $flight['origin_airport_code'];
$origin_airport_name = $flight['origin_airport_name'];
$origin_airport_country = $flight['origin_airport_country'];
$origin_airport_state = $flight['origin_airport_state'];

$destination_airport_code = $flight['destination_airport_code'];
$destination_airport_name = $flight['destination_airport_name'];
$destination_airport_country = $flight['destination_airport_country'];
$destination_airport_state = $flight['destination_airport_state'];
$departure_time = $flight['departure_time'];
$duration = $flight['duration'];
$flight_base_price = $flight['flight_base_price'];
$flight_discount = $flight['flight_discount'];
$aircraft_name = $flight['aircraft_name'];
$airline_id = $flight['airline_id'];
$arrival_time = $flight['arrival_time'];
$economy_capacity = $flight['economy_capacity'];
$business_capacity = $flight['business_capacity'];
$premium_economy_capacity = $flight['premium_economy_capacity'];
$first_class_capacity = $flight['first_class_capacity'];
$airline_name = $flight['airline_name'];
$airline_image = $flight['airline_image'];


?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
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
                    <div class="shadow-sm p-3 mb-3 bg-body rounded row gx-3 justify-content-end align-middle">
                        <div class="col">
                            <a type="button" class="btn btn-outline-danger float-end" data-bs-toggle="modal" data-bs-target="#deleteStatic"><i class='bi bi-trash'></i> Delete</a>
                        </div>
                    </div>
                    <div class="shadow p-3 mb-3 bg-body rounded row gx-3">
                        <div class="row">
                            <span class="fs-1" style="font-family: Roboto">Flight <span class="text-primary">#<?= $flightCode ?></span></span>
                        </div>
                        <div class="row mt-4">
                            <div class="col-7">
                                <div class="container">
                                    <h4>Flight Details</h4><hr>
                                    <p><strong>Created by:</strong> <?php echo $username; ?></p>
                                    <p><strong>Origin Airport:</strong> <?php echo "$origin_airport_name ($origin_airport_code)"; ?></p>
                                    <p><strong>Destination Airport:</strong> <?php echo "$destination_airport_name ($destination_airport_code)"; ?></p>
                                    <p><strong>Departure:</strong> <?php echo formatDateTime($departure_time); ?></p>
                                    <p><strong>Duration:</strong> <?php echo formatDuration($duration); ?></p>
                                    <p><strong>Arrival:</strong> <?php echo formatDateTime($arrival_time); ?></p>
                                    <p><strong>Airline:</strong> <?php echo $airline_name; ?></p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="container">
                                    <h4>Aircraft Details</h4><hr>
                                    <p><strong>Aircraft Name:</strong> <?php echo $aircraft_name; ?></p>
                                    <p><strong>Economy Capacity:</strong> <?php echo $economy_capacity; ?></p>
                                    <p><strong>Business Capacity:</strong> <?php echo $business_capacity; ?></p>
                                    <p><strong>Premium Economy Capacity:</strong> <?php echo $premium_economy_capacity; ?></p>
                                    <p><strong>First Class Capacity:</strong> <?php echo $first_class_capacity; ?></p>
                                </div>
                            </div>
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
                                    <form id="delete" method="post" action="/admin/manage-flights.php">
                                        <input type="hidden" name="flight_id" value="<?= $flight["flight_id"] ?>">
                                        <input type="hidden" name="token" value="<?= $token ?>">
                                    </form>
                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' id="modal-btn-delete" form="delete" name="delete" value="1" class='btn btn-danger'>I understand</button>
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