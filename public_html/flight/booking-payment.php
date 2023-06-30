<?php

session_start();
require("../../includes/functions.inc.php");

customer_login_required();

if (!array_keys_isset(["flightInfo", "passengers", "contactInfo", "baggages", "seats"], $_SESSION["book"])) {
    makeToast("error", "Important information was not found. Please try searching flight again!", "Error");
    header("Location: /index.php");
    die();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try {
        if (!empty($postedToken)) {
            if (isTokenValid($postedToken)) {
                //passenger session
                if (isset($_POST["confirm"])) {

                    //true booking process here
                    if (!array_keys_isset(["payment_file"], $_FILES)) {
                        throw new Exception("File not found!");
                    }

                    //cehck fiel
                    //send payment file
                    $file = $_FILES['payment_file'];

                    $fileName = $file['name'];
                    $fileTmpName = $file['tmp_name'];
                    $fileSize = $file['size'];

                    $fileExt = explode('.', $fileName);
                    $fileActualExt = strtolower(end($fileExt));

                    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

                    if ($file["error"]) {
                        throw new Exception($file["error"]);
                    }
                    if (!in_array($fileActualExt, $allowed)) {
                        throw new Exception("Filetype not allowed");
                    }//file size < 10MB
                    if ($fileSize > 10485760) {
                        throw new Exception("File too big");
                    }


                    $contactInfo = $_SESSION["book"]["contactInfo"];
                    //flight info
                    $flightInfo = $_SESSION["book"]["flightInfo"];
                    //retrieve from user
                    $userData = $_SESSION["user_data"];
                    //retrieve passengers
                    $passengers = $_SESSION["book"]["passengers"];

                    //add adddon to passengers :)
                    $baggages = $_SESSION["book"]["baggages"];
                    $seats = $_SESSION["book"]["seats"];

                    //special index for seats
                    $indexSpecial = 0;
                    //passenger[adult][1][first_name]
                    foreach ($passengers as $ageCategoryKey => $ageCategoryValue) {
                        $index = 0;
                        foreach ($ageCategoryValue as $passenger) {
                            // Add the baggage field to the passenger array
                            $passengers[$ageCategoryKey][$index]['departure_baggage'] = $baggages[$ageCategoryKey][$index];

                            $passengers[$ageCategoryKey][$index]['departure_seat'] = $seats['depart'][$indexSpecial];

                            if ($flightInfo["trip_type"] === "RETURN") {
                                $passengers[$ageCategoryKey][$index]['return_baggage'] = $baggages[$ageCategoryKey][$index];

                                $passengers[$ageCategoryKey][$index]['return_seat'] = $seats['return'][$indexSpecial];
                            }

                            $index++;
                            $indexSpecial++;
                        }
                    }

                    //check if similar booking exists in user booking
                    $userBookings = retrieveAllUserBookings($userData["user_id"]);
                    //flights
                    //ok first retrieve from flights again (last time)
                    $departureFlight = retrieveFlightSearch($flightInfo["departure_flight_id"], $flightInfo["travel_class"],
                        $flightInfo["passenger_count"]);
                    $returnFlight = null;
                    if ($flightInfo["trip_type"] == "RETURN") {
                        $returnFlight = retrieveFlightSearch($flightInfo["return_flight_id"], $flightInfo["travel_class"],
                            $flightInfo["passenger_count"]);
                    }

                    foreach ($userBookings as $booking) {
                        $bookingFlights = retrieveBookingFlights($booking["booking_id"]);
                        foreach ($bookingFlights as $flight) {
                            if ($flight["flight_id"] == $departureFlight["flight_id"]) {
                                throw new Exception("Flight has already been booked by user before!<br>Please book another flight!");
                            }
                            if (!empty($returnFlight)) {
                                if ($flight["flight_id"] == $returnFlight["flight_id"]) {
                                    throw new Exception("Flight has already been booked by user before!<br>Please book another flight!");
                                }
                            }
                        }
                    }


                    //create booking
                    $bookingAssoc = createBooking(["userData" => $userData, "passengers" => $passengers,
                        "flightInfo" => $flightInfo, "contactInfo" => $contactInfo, "flights" => [$departureFlight, $returnFlight]]) or throw new Exception("Booking was a failure!");
                    $bookingID = $bookingAssoc["booking_id"];


                    $bookingReference = $bookingID . $flightInfo["travel_class"] .
                        mb_substr($flightInfo["trip_type"], 0, 1) . "-" . $departureFlight["origin_airport_code"]
                        . $departureFlight["destination_airport_code"];
                    $fileNameNew = "#" . $bookingReference . "." . $fileActualExt;
                    $fileDestination = $_SERVER['DOCUMENT_ROOT'] . '/payments/' . $fileNameNew;

                    move_uploaded_file($fileTmpName, $fileDestination);

                    if (!updateBookingDetails($bookingReference, $fileDestination, $bookingID)) {
                        throw new Exception("Unable to save booking reference..");
                    }

                    unset($_SESSION["book"]);
                    $_SESSION["booking_id"] = $bookingID;
                    makeToast("success", "The booking was a success!", "Success");
                    header("Location: /flight/booking-confirm.php");
                    die();
                }
            } else {
                makeToast("warning", "Please refrain from attempting to resubmit previous form", "Warning");
            }
        } else {
            throw new exception("Token not found");
        }
    } catch (exception $e) {
        makeToast("error", $e->getMessage(), "Error");
    }

    header("Location: /flight/booking-payment.php");
    die();
}


displayToast();
$token = getToken();
?>
<html>
<head>
    <?php head_tag_content(); ?>
    <link rel="stylesheet" href="/assets/css/plane.css">
    <title><?= config("name") ?> | Booking Payment</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php header_bar("Booking Payment") ?>

            <div class="container py-2 px-4 pb-5 mt-3 border rounded-4">
                <div class="row mt-4 ms-3">
                    <div class="col-lg-8 mx-auto shadow p-3 mb-5 bg-body rounded container-md">
                        <form method="post" action="<?php current_page(); ?>" enctype="multipart/form-data">
                            <div class="row text-center">
                                <strong class="fs-3">PROOF OF PAYMENT</strong>
                            </div>
                            <hr>
                            <div class="row mt-2 justify-content-center">
                                <img class="img-fluid img-thumbnail" src="/assets/img/qr.jpeg"
                                     style="width: 300px; height: auto;">
                            </div>
                            <div class="row mt-3 container">
                                <div class="col row mt-2">
                                    <p class="text-muted">Transfer to: <br>ABDUL WAFI BIN CHE AB.RAHIM<br>12177029717158
                                    </p>
                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <label for="paymentProof" class="form-label">Proof of Payment:</label>
                                        <input type="file" class="form-control" id="paymentProof" name="payment_file"
                                               accept=".pdf, .jpg, .png, .jpeg" required>
                                    </div>
                                </div>
                            </div>


                            <div class="row mt-4">
                                <input type="hidden" name="token" value="<?= $token ?>">
                                <div class="col mt-4 ms-4">
                                    <span class="text-muted">File types allowed: .pdf, .jpg, .png, .jpeg & under 10 MB</span>
                                </div>
                                <div class="col text-end">
                                    <a type="button" class="btn btn-outline-primary mt-3"
                                       href="/flight/booking-addon.php">Back</a>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#confirmStatic" class="btn btn-outline-primary mt-3">Confirm</button>

                                </div>
                            </div>

                            <!-- modalconfrim -->
                            <div class='modal fade' id='confirmStatic' data-bs-backdrop='static'
                                 data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel'
                                 aria-hidden='true'>
                                <div class='modal-dialog'>
                                    <div class="modal-content">
                                        <div class="modal-header bg-light-subtle">
                                            <h5 class="modal-title">Final Confirmation</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body bg-warning-subtle">
                                            <div class="px-3">
                                                <div class="mb-1">
                                                    <span class="fw-bolder">Last Chance to Confirm</span>
                                                </div>
                                                <p class="text-black mt-3">Are you sure you want to proceed with booking
                                                    this flight?</p>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light-subtle">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Close
                                            </button>
                                            <button type="submit" name="confirm" value="1" class="btn btn-danger">
                                                Proceed
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <?php footer(); ?>
        </main>

    </div>
</div>

<?php body_script_tag_content(); ?>
<script type="text/javascript" src="/assets/js/modal.js"></script>
</body>
</html>
