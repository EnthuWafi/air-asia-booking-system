<?php

session_start();
require("../../includes/functions.inc.php");

customer_login_required();

if (!array_keys_isset(["flightInfo", "passengers", "contactInfo"], $_SESSION["book"])) {
    makeToast("error", "Important information was not found. Please try searching flight again!", "Error");
    header("Location: /index.php");
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){
                //passenger session
                if (array_keys_isset(["baggages", "seats"], $_POST)){

                    $baggages = $_POST["baggages"];
                    $seats = $_POST["seats"];

                    $_SESSION["book"]["baggages"] = $baggages;
                    $_SESSION["book"]["seats"] = $seats;

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



$flightInfo = $_SESSION["book"]["flightInfo"];
$baggageOptions = retrieveBaggageOptions();

$departureFlight = retrieveFlight($flightInfo["departure_flight_id"]);
$departureFlightAddons = retrieveFlightAddon($departureFlight["flight_id"], $flightInfo["travel_class"]);

$returnFlight = null;
$returnFlightAddons = null;

if (isset($flightInfo["return_flight_id"])) {
    $returnFlight = retrieveFlight($flightInfo["return_flight_id"]);
    $returnFlightAddons = retrieveFlightAddon($returnFlight["flight_id"], $flightInfo["travel_class"]);
}

$travelClass = travelClassAssoc($flightInfo["travel_class"]);

displayToast();
$token = getToken();
?>
<html>
<head>
    <?php head_tag_content(); ?>
    <link rel="stylesheet" href="/assets/css/plane.css">
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
                        <form id="addon" method="post" action="<?php current_page(); ?>">
                            <div class="row mt-3">
                                <h2 class="fs-2 mb-3">Passengers Baggage</h2>
                            </div>
                            <div class="row mt-2 container">
                                <div class='col'>
                                    <?php
                                    $baggageDepart = book_baggageAddon($flightInfo, $baggageOptions);
                                    echo "<div class='row'>
                                            <h2 class='fs-4 mb-3'>Departure Flight</h2>
                                          </div>
                                          <div class='row'>
                                            {$baggageDepart}                                    
                                          </div>";
                                    ?>
                                </div>
                                <?php
                                if (isset($returnFlight)) {
                                    echo "<div class='col'>";
                                    $baggageReturn = book_baggageAddon($flightInfo, $baggageOptions);
                                    echo "<div class='row'>
                                            <h2 class='fs-4 mb-3'>Return Flight</h2>
                                          </div>
                                          <div class='row'>
                                            {$baggageReturn}                                    
                                          </div>";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                            <div class="row mt-5 container">
                                <h2 class="fs-2 mb-3">Passengers Seating</h2>
                            </div>
                            <div class="row mt-3 container">
                                <div class="col">
                                    <?php
                                    echo "<div class='row'>
                                        <h2 class='fs-4 mb-3'>Departure Flight</h2>
                                    </div>
                                    <div class='row'>
                                        <div class='col'>
                                            <button type='button' class='btn btn-danger p-3' data-bs-toggle='modal' data-bs-target='#staticDeparture'>
                                                Departure Flight Seating
                                            </button>
                                        </div>
                                    </div>";
                                    ?>
                                </div>
                                <?php
                                if (isset($returnFlight)){
                                    echo "<div class='col'>";

                                    echo "<div class='row'>
                                        <h2 class='fs-4 mb-3'>Departure Flight</h2>
                                      </div>
                                      <div class='row'>
                                        <div class='col'>
                                            <button type='button' class='btn btn-danger p-3' data-bs-toggle='modal' data-bs-target='#staticReturn'>
                                            Return Flight Seating
                                            </button>                                  
                                        </div>                                
                                      </div>";

                                    echo "</div>";
                                }
                                ?>
                            </div>
                            <div class="row mt-4">
                                <input type="hidden" name="token" value="<?= $token ?>">
                                    <div class="text-end">
                                        <a type="button" class="btn btn-outline-primary mt-3" href="/flight/booking-guest.php">Back</a>
                                        <button type="submit" id="btn-submit" class="btn btn-outline-primary mt-3" onclick="return validateInputs()">Next</button>
                                    </div>
                                </div>



                            <!-- Modal departure -->
                            <div class="modal fade" id="staticDeparture" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h3 class="modal-title">Aircraft > <span class="text-danger"><?= $departureFlight["aircraft_name"] ?></span></h3>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="plane">

                                                <div class="cockpit">
                                                    <h1 class="text-center pb-3 text-body-emphasis"><?= $travelClass["name"] ?> Class Section</h1>
                                                </div>
                                                <div class="exit exit--front fuselage"></div>

                                                <ol class="cabin fuselage">
                                                <?php
                                                book_cabinSeating($travelClass, $departureFlight, $departureFlightAddons, "depart");
                                                ?>
                                                </ol>
                                                <div class="exit exit--back fuselage">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" id="btn-confirm-depart-seat" class="btn btn-outline-primary">Confirm Selection</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal return -->

                            <?php if (isset($returnFlight)) { ?>

                                <div class="modal fade" id="staticReturn" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h3 class="modal-title">Aircraft > <span class="text-danger"><?= $returnFlight["aircraft_name"] ?></span></h3>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="plane">

                                                    <div class="cockpit">
                                                        <h1 class="text-center pb-3 text-body-emphasis"><?= $travelClass["name"] ?> Class Section</h1>
                                                    </div>
                                                    <div class="exit exit--front fuselage"></div>

                                                    <ol class="cabin fuselage">
                                                        <?php
                                                        book_cabinSeating($travelClass, $returnFlight, $returnFlightAddons, "return");
                                                        ?>
                                                    </ol>
                                                    <div class="exit exit--back fuselage">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="button" id="btn-confirm-return-seat" class="btn btn-outline-danger">Confirm Selection</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                            <div id="hiddenInputs-depart"></div>
                            <div id="hiddenInputs-return"></div>
                        </form>
                    </div>
                </div>
            </div>

            <?php footer(); ?>
        </main>

    </div>
</div>

<?php body_script_tag_content();?>
<script>
    $(document).ready(function() {
        var maxSeats = <?= $flightInfo["passenger_count"]?>; // Maximum number of seats allowed

        // Function to handle seat selection
        function handleSeatSelection(seatSelector, hiddenInputsContainer, modalName) {
            var selectedSeats = []; // Array to store selected seat values

            $(seatSelector + ' input[type="checkbox"]').on('change', function() {
                var seatValue = $(this).val();

                if ($(this).is(':checked')) {
                    if (selectedSeats.length < maxSeats) {
                        selectedSeats.push(seatValue);
                    } else {
                        $(this).prop('checked', false); // Uncheck the checkbox if the limit is reached
                        alert('You have reached the maximum number of selected seats.');
                    }
                } else {
                    var index = selectedSeats.indexOf(seatValue);
                    if (index !== -1) {
                        selectedSeats.splice(index, 1);
                    }
                }
            });

            $(`#btn-confirm-${hiddenInputsContainer}-seat`).on('click', function() {
                var hiddenInputs = '';

                for (var i = 0; i < selectedSeats.length; i++) {
                    hiddenInputs += `<input type="hidden" name="seats[${hiddenInputsContainer}][${i}]" value="${selectedSeats[i]}">`;
                }

                $(`#hiddenInputs-${hiddenInputsContainer}`).html(hiddenInputs);
                $(modalName).modal('hide'); // Close the Bootstrap modal
            });
        }

        // Call the function for departure flight seat selection
        handleSeatSelection('.seat.depart', 'depart', '#staticDeparture');

        <?php
        if (isset($returnFlight)) {
            echo "handleSeatSelection('.seat.return', 'return', '#staticReturn');";
        }
        ?>

        function validateInputs() {
            var selectedSeatsDepart = document.querySelectorAll('.seat.depart input[type="checkbox"]:checked');
            var selectedSeatsReturn = document.querySelectorAll('.seat.return input[type="checkbox"]:checked');

            var passengerCount = maxSeats;

            if (selectedSeatsDepart.length !== passengerCount ) {
                alert('Please select a seat for each passenger for departure flight.');
                return false; // Prevent form submission
            }
            <?php
            //if return flight exist, check that too!
            if (isset($returnFlight)) {
                echo "
                if (selectedSeatsReturn.length !== passengerCount) {
                    alert('Please select a seat for each passenger for return flight.');
                    return false;
                }";
            }
            ?>
            // Additional validation logic can be added here

            return true; // Proceed with form submission
        }
    });
</script>
</body>
</html>
