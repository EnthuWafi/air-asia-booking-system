<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){
                //delete flight todo
                if (isset($_POST["delete"])) {
                    $flightID = htmlspecialchars($_POST["flight_id"]);

                    deleteFlight($flightID) or throw new Exception("Couldn't delete flight");
                    makeToast("success", "Flight successfully deleted!", "Success");
                }
                //create flight todo
                else if (isset($_POST["flight"])) {
                    // Retrieve and sanitize form values
                    $originAirport = htmlspecialchars($_POST['originAirport']);
                    $destinationAirport = htmlspecialchars($_POST['destinationAirport']);
                    $departureTime = htmlspecialchars($_POST['departureTime']);
                    $duration = htmlspecialchars($_POST['duration']);
                    $flightBasePrice = htmlspecialchars($_POST['flightBasePrice']);
                    $aircraft = htmlspecialchars($_POST['aircraft']);
                    $airline = htmlspecialchars($_POST['airline']);


                    $userID = $_SESSION["user_data"]["user_id"];

                    createFlight($userID, $originAirport, $destinationAirport, $departureTime,
                    $duration, $flightBasePrice, $aircraft, $airline) or throw new Exception("Couldn't create flight");

                    makeToast("success", "FLight successfully created!", "Success");
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

    header("Location: /admin/manage-flights.php");
    die();
}
displayToast();

$flightsCount = retrieveCountFlights()["count"] ?? 0;
$flights = retrieveAllFlights();

$airports = retrieveAirports();
$airlines = retrieveAirlines();
$aircrafts = retrieveAircrafts();

$airportsOption = "";
foreach ($airports as $airport) {
    $airportsOption .= "<option value='{$airport["airport_code"]}'>{$airport["airport_state"]} - {$airport["airport_name"]}</option>";
}

$airlinesOption = "";
foreach ($airlines as $airline) {
    $airlinesOption .= "<option value='{$airline["airline_id"]}'>{$airline["airline_name"]}</option>";
}

$aircraftsOption = "";
foreach ($aircrafts as $aircraft) {
    $aircraftsOption .= "<option value='{$aircraft["aircraft_id"]}'>{$aircraft["aircraft_name"]}</option>";
}


$token = getToken();
?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <title><?= config("name") ?> | Manage Flights</title>
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
                <div class="row mt-4 ms-3">
                    <div class="shadow-sm p-3 px-4 mb-5 bg-body rounded row gx-3">
                        <div class="row mb-4">
                            <span class="h2"><?= $flightsCount ?> flights found</span>
                        </div>
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                            <div class="col">
                                <span class="fs-1 mb-3">Flights</span>
                            </div>
                            <div class="col text-end">
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#flightStatic">
                                    <span class="h5"><i class="bi bi-plus-circle"> </i>Add</span>
                                </button>
                            </div>

                            <div class="row mt-3">
                                <table class="table table-hover table-responsive">
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
                                        <th scope="col">Creator</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    admin_displayFlights($flights);
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- modal create product -->
                    <div class='modal fade' id='flightStatic' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header bg-light-subtle'>
                                    <h5 class='modal-title' id='staticBackdropLabel'>Create new Flight</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body'>
                                    <form id="flight" action="/admin/manage-flights.php" method="post">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="originAirport" class="form-label">Origin Airport:</label>
                                                <select id="originAirport" name="originAirport" class="form-select" required>
                                                    <!-- Options for origin airport -->
                                                    <?= $airportsOption; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="destinationAirport" class="form-label">Destination Airport:</label>
                                                <select id="destinationAirport" name="destinationAirport" class="form-select" required>
                                                    <!-- Options for destination airport -->
                                                    <?= $airportsOption; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="departureTime" class="form-label">Departure Time:</label>
                                                <input type="datetime-local" id="departureTime" name="departureTime" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="duration" class="form-label">Duration:</label>
                                                <input type="text" id="duration" name="duration" class="form-control" placeholder="HH:MM" required>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="airline" class="form-label">Airline:</label>
                                                <select id="airline" name="airline" class="form-select" required>
                                                    <!-- Options for airline -->
                                                    <?= $airlinesOption; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="aircraft" class="form-label">Aircraft:</label>
                                                <select id="aircraft" name="aircraft" class="form-select" required>
                                                    <?= $aircraftsOption; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="flightBasePrice" class="form-label">Flight Base Price (RM):</label>
                                                <input type="text" id="flightBasePrice" name="flightBasePrice" class="form-control" required>
                                            </div>
                                        </div>
                                        <input type="hidden" name="token" value="<?= $token ?>">
                                    </form>

                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' form="flight" name="flight" value="1" class='btn btn-danger'>Create Flight</button>
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

                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' id="modal-btn-delete" form="" name="delete" value="1" class='btn btn-danger'>I understand</button>
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
<script>
    const durationInput = document.getElementById('duration');

    durationInput.addEventListener('input', function(e) {
        const input = e.target.value;

        // Regex pattern for matching the format HH:MM
        const regex = /^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$/;

        if (!regex.test(input)) {
            durationInput.setCustomValidity('Please enter duration in HH:MM format');
        } else {
            durationInput.setCustomValidity('');
        }
    });
</script>
</body>

</html>