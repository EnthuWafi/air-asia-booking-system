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

    header("Location: /flight/booking-guest.php");
    die();
}





try{
    $flightInfo = $_SESSION["book"]["flightInfo"];
    $baggageOptions = baggageOptionsAssocAll();

    $departureFlight = retrieveFlight($flightInfo["departure_flight_id"]);
    $departureFlightAddons = retrieveFlightAddon($departureFlight["flight_id"], $flightInfo["travel_class"]);

    $returnFlight = null;
    $returnFlightAddons = null;
    //flights
    //ok first retrieve from flights again
    $departureFlight = retrieveFlight($flightInfo["departure_flight_id"]);
    $returnFlight = null;
    if (isset($flightInfo["return_flight_id"])) {
        $returnFlight = retrieveFlight($flightInfo["return_flight_id"]);
        $returnFlightAddons = retrieveFlightAddon($returnFlight["flight_id"], $flightInfo["travel_class"]);
    }

    $travelClass = travelClassAssoc($flightInfo["travel_class"]);

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
catch (exception $e){
    makeToast("error", $e->getMessage(), "Error");
    header("Location: /flight/booking-addon.php");
    die();
}


displayToast();
$token = getToken();
?>
<html>
<head>
    <?php head_tag_content(); ?>
    <link rel="stylesheet" href="/assets/css/plane.css">
    <link rel="stylesheet" href="/assets/css/progress.css">
    <title><?= config("name") ?> | Booking Flight Add-ons</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <div class="bg-light">
                <?php header_bar("Flight Add-ons") ?>

                <div class="container py-2 px-4">
                    <div class="row my-4">
                        <div class="position-relative">
                            <div id="msform">
                                <!-- progressbar -->
                                <ul id="progressbar">
                                    <li class="active"><strong>Guest</strong></li>
                                    <li class="active"><strong>Add-ons</strong></li>
                                    <li><strong>Payment</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 ms-3">
                        <div class="col-lg-7 col-sm-auto">
                            <div class="shadow p-3 mb-5 bg-body rounded-4 row">
                                <form id="addon" method="post" action="<?php current_page(); ?>">
                                    <div class="row mt-3">
                                        <h2 class="fs-2 mb-3">Passengers Baggage</h2>
                                    </div>
                                    <div class="row mt-2 container">
                                        <div class='col-6'>
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
                                            echo "<div class='col-6'>";
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
                                    <div class="row mt-3 align-content-stretch justify-content-evenly container">
                                        <div class="col-5">
                                            <?php
                                            echo "
                                            <button type='button' class='btn btn-danger btn-red p-3' data-bs-toggle='modal' data-bs-target='#staticDeparture'>
                                                Departure Flight Seating
                                            </button>";
                                            ?>
                                        </div>
                                        <?php
                                        if (isset($returnFlight)){
                                            echo "<div class='col-5'>";

                                            echo "
                                            <button type='button' class='btn btn-danger btn-red p-3' data-bs-toggle='modal' data-bs-target='#staticReturn'>
                                            Return Flight Seating
                                            </button>";

                                            echo "</div>";
                                        }
                                        ?>
                                    </div>
                                    <div class="row mt-5">
                                        <input type="hidden" name="token" value="<?= $token ?>">
                                        <div class="text-end">
                                            <a type="button" class="btn btn-danger btn-red mt-3" href="/flight/booking-guest.php">Back</a>
                                            <button type="submit" id="btn-submit" class="btn btn-danger btn-red mt-3" onclick="return validateInputs();">Next</button>
                                        </div>

                                    </div>



                                    <!-- Modal departure -->
                                    <div class="modal fade" id="staticDeparture" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3 class="modal-title">Aircraft > <span class="text-danger"><?= $departureFlight["aircraft_name"] ?></span></h3>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row justify-content-end">
                                                        <div class="col-auto">
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
                                                        <div class="col-4">
                                                            <div class="shadow px-2 py-5 rounded-4">
                                                                <h3 class="text-center text-decoration-underline">Seat Guide</h3>
                                                                <div class="row align-items-center mt-3">
                                                                    <div class="col-2">
                                                                        <img src="/assets/img/taken-seat.png">
                                                                    </div>
                                                                    <div class="col">
                                                                        <span>Unavailable/Booked Seat</span>
                                                                    </div>
                                                                </div>
                                                                <div class="row align-items-center">
                                                                    <div class="col-2">
                                                                        <img src="/assets/img/available-seat.png">
                                                                    </div>
                                                                    <div class="col">
                                                                        <span>Available Seat</span>
                                                                    </div>
                                                                </div>
                                                                <div class="row align-items-center">
                                                                    <div class="col-2">
                                                                        <img src="/assets/img/selected-seat.png">
                                                                    </div>
                                                                    <div class="col">
                                                                        <span>Selected Seat</span>
                                                                    </div>
                                                                </div>
                                                            </div>
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
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title">Aircraft > <span class="text-danger"><?= $returnFlight["aircraft_name"] ?></span></h3>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row justify-content-end">
                                                            <div class="col-auto">
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
                                                            <div class="col-4">
                                                                <div class="shadow px-2 py-5 rounded-4">
                                                                    <h3 class="text-center text-decoration-underline">Seat Guide</h3>
                                                                    <div class="row align-items-center mt-3">
                                                                        <div class="col-2">
                                                                            <img src="/assets/img/taken-seat.png">
                                                                        </div>
                                                                        <div class="col">
                                                                            <span>Unavailable/Booked Seat</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row align-items-center">
                                                                        <div class="col-2">
                                                                            <img src="/assets/img/available-seat.png">
                                                                        </div>
                                                                        <div class="col">
                                                                            <span>Available Seat</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row align-items-center">
                                                                        <div class="col-2">
                                                                            <img src="/assets/img/selected-seat.png">
                                                                        </div>
                                                                        <div class="col">
                                                                            <span>Selected Seat</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
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
                        <div class="col-lg-5 col-sm-auto text-end">
                            <div class="shadow p-3 bg-body rounded-4 sticky-top">
                                <div class="card">
                                    <div class="card-body">
                                        <h2 class="card-title text-center mb-3 icon-red fw-bolder">Price Details</h2>
                                        <div class="row mx-2">
                                            <table class="table table-sm text-end">
                                                <thead class="table-light">
                                                <tr>
                                                    <th class="text-start">Item</th>
                                                    <th>Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td class="text-start">
                                                        <div class="fw-bold">
                                                            Depart
                                                        </div>
                                                        <div class="baggage mt-3">
                                                            <p class="small">
                                                                <?= "Adult x{$ageCategoryArr["adult"]}, Child x{$ageCategoryArr["child"]},
                                                Senior x{$ageCategoryArr["senior"]}, Infant x{$ageCategoryArr["infant"]}" ?>
                                                            </p>
                                                            <p class="small">
                                                                <?= "XSM x{$flightInfo["passenger_count"]}, SML x0, STD x0, LRG x0, XLG x0" ?>
                                                            </p>
                                                        </div>

                                                    </td>
                                                    <td id="departureCost">RM<?= number_format($departureFlightCost, 2); ?></td>
                                                </tr>
                                                <?php if (isset($returnFlight)) {?>
                                                    <tr>
                                                        <td class="text-start">
                                                            <span class="fw-bold">
                                                                Return
                                                            </span>
                                                            <div class="baggage mt-3">
                                                                <p class="small">
                                                                    <?= "Adult x{$ageCategoryArr["adult"]}, Child x{$ageCategoryArr["child"]},
                                                Senior x{$ageCategoryArr["senior"]}, Infant x{$ageCategoryArr["infant"]}" ?>
                                                                </p>
                                                                <p class="small">
                                                                    <?= "XSM x{$flightInfo["passenger_count"]}, SML x0, STD x0, LRG x0, XLG x0" ?>
                                                                </p>
                                                            </div>
                                                        </td>
                                                        <td id="returnCost">RM<?= number_format($returnFlightCost, 2); ?></td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                                <tfoot class="table-light">
                                                <tr>
                                                    <td class="fw-bold text-start">Subtotal</td>
                                                    <td id="subtotal">RM<?= number_format($total, 2) ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold text-start">Discount</td>
                                                    <td id="discount" class="text-nowrap">-RM<?= number_format($discountTotal, 2) ?></td>
                                                </tr>
                                                <tr class="highlight-top-border">
                                                    <td class="fw-bold text-start">Total Price</td>
                                                    <td id="total">RM<?= number_format($netTotal, 2) ?></td>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
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
<script>

</script>
<script>

    $(document).ready(function() {

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

    var maxSeats = <?= $flightInfo["passenger_count"] ?>; // Maximum number of seats allowed
    const baggageArr = <?= json_encode($baggageOptions); ?>;

    var baseDeparturePrice = <?php echo ($departureFlightCost - ($baggageOptions["XSM"]["cost"] * $flightInfo["passenger_count"])) ?>;
    <?php if ($returnFlight){ ?>
    var baseReturnPrice = <?php echo ($returnFlightCost - ($baggageOptions["XSM"]["cost"] * $flightInfo["passenger_count"])) ?>;
    <?php } ?>

    const departureDiv = document.getElementById("departureCost");
    const returnDiv = document.getElementById("returnCost");
    const subtotal = document.getElementById("subtotal");
    const discount = document.getElementById("discount");
    const total = document.getElementById("total");


    function updateCost(baggageSelect) {
        var selectedValue = baggageSelect.value;

        var total = 0;
        var subtotal = 0;
        var discount = 0;
        var net = 0;

        var xsm = 0, sml = 0, std = 0, lrg = 0, xlg = 0;

        var baggageElements = document.querySelectorAll("[name^='baggages']");

        baggageElements.forEach(function(element) {
            var baggageOption = baggageArr.find(option => option.code === element.value);
            if (baggageOption) {
                if (baggageOption.code == "XSM"){xsm++;}
                else if (baggageOption.code == "SML"){sml++;}
                else if (baggageOption.code == "STD"){std++;}
                else if (baggageOption.code == "LRG"){lrg++;}
                else if (baggageOption.code == "XLG"){xlg++;}

                total += baggageOption.cost;
            }
        });

        var departureCost = baseDeparturePrice + total;
        <?php
        if ($returnFlight) {
            echo "var returnCost = baseReturnPrice + total;";
        }
        ?>

        var baggageAge = document.getElementsByClassName('baggageAge');
        for (var i = 0; i < baggageAge.length; i++) {
            baggageAge[i].innerHTML =
                `<p class="small">
                <?= "Adult x{$ageCategoryArr["adult"]}, Child x{$ageCategoryArr["child"]},
    Senior x{$ageCategoryArr["senior"]}, Infant x{$ageCategoryArr["infant"]}" ?>
            </p>
            <p class="small">
               XSM x${xsm}, SML x${sml}, STD x${std}, LRG x${lrg}, XLG x${xlg}
            </p>`;
        }
    }

</script>
</body>
</html>
