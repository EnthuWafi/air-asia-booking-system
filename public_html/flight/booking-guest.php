<?php

session_start();
require("../../includes/functions.inc.php");

customer_login_required();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){
                //passenger session
                if (array_keys_isset(["passengers", "phone", "email", "consent"], $_POST)){
                    $contactInfo = ["email"=>htmlspecialchars($_POST["email"]), "phone"=>htmlspecialchars($_POST["phone"]),
                        "consent"=>htmlspecialchars($_POST["consent"])];

                    if ($contactInfo["consent"] != 1) {
                        throw new Exception("You can't proceed without giving your consent!");
                    }

                    $_SESSION["book"]["contactInfo"] = $contactInfo;
                    $_SESSION["book"]["passengers"] = $_POST["passengers"];

                    header("Location: /flight/booking-addon.php");
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

if (!isset($_SESSION["book"]["flightInfo"])) {
    makeToast("error", "Flight info was not found. Please try searching flight again!", "Error");
    header("Location: /index.php");
    die();
}

try{
    $flightInfo = $_SESSION["book"]["flightInfo"];
    //flights
    //ok first retrieve from flights again
    $departureFlight = retrieveFlight($flightInfo["departure_flight_id"]);
    $returnFlight = null;

    if ($flightInfo["trip_type"] == "RETURN") {
        $returnFlight = retrieveFlight($flightInfo["return_flight_id"]);
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
catch (exception $e){
    makeToast("error", $e->getMessage(), "Error");
    header("Location: /flight/search.php");
    die();
}


$flightInfo = $_SESSION["book"]["flightInfo"];

displayToast();
$token = getToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php head_tag_content(); ?>
    <link rel="stylesheet" href="/assets/css/progress.css">
    <style>
        .highlight-top-border {
            border-top: 2px solid #000000; /* Customize the color as needed */
        }
    </style>
    <title><?= config("name") ?> | Booking Guest Details</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">

            <div class="bg-light">
                <?php header_bar("Passenger Details") ?>
                <div class="container px-4 mt-3">
                    <div class="row my-4">
                        <div class="position-relative">
                            <div id="msform">
                                <!-- progressbar -->
                                <ul id="progressbar">
                                    <li class="active"><strong>Guest</strong></li>
                                    <li ><strong>Add-ons</strong></li>
                                    <li><strong>Payment</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 ms-3">
                        <div class="col">
                            <div class="shadow p-3 mb-5 px-5 bg-body rounded-4">
                                <form method="post" action="<?php current_page(); ?>">
                                    <div class="row mt-2">
                                        <h2 class="fs-2 mb-3">Guest Details</h2>
                                        <?php
                                        book_guestDetails($flightInfo);
                                        ?>
                                    </div>
                                    <div class="row mt-3">
                                        <h2 class="fs-2 mb-3">Contact Details</h2>
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label for="phone" class="form-label">Phone Number:</label>
                                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone number" required>
                                                <div class="invalid-feedback">Please provide a valid phone number.</div>
                                            </div>
                                            <div class="col">
                                                <label for="email" class="form-label">Email:</label>
                                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                                <div class="invalid-feedback">Please provide a valid email address.</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <input type="hidden" name="consent" value="0">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="consent" name="consent" value="1" required>
                                                    <label class="form-check-label" for="consent">
                                                        I agree to AirAsia's terms and conditions.
                                                    </label>
                                                    <div class="invalid-feedback">You must agree to the terms and conditions.</div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="token" value="<?= $token ?>">
                                        <button type="submit" class="btn btn-danger btn-red mt-3 w-25 ms-auto float-end">Next</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="shadow p-3 bg-body rounded-4 sticky-top">
                                <div class="card">
                                    <div class="card-body">
                                        <h2 class="card-title text-center mb-3 icon-red fw-bolder">Price Details</h2>
                                        <div class="row mx-2">
                                            <table class="table table-sm text-end ">
                                                <thead class="table-light">
                                                <tr>
                                                    <th class="text-start">Item</th>
                                                    <th>Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td class="fw-bold text-start">Depart</td>
                                                    <td>RM<?= number_format($departureFlightCost, 2); ?></td>
                                                </tr>
                                                <?php if (isset($returnFlight)) {?>
                                                <tr>
                                                    <td class="fw-bold text-start">Return</td>
                                                    <td>RM<?= number_format($returnFlightCost, 2); ?></td>
                                                </tr>
                                                <?php } ?>
                                                </tbody>
                                                <tfoot class="table-light">
                                                <tr>
                                                    <td  class="fw-bold text-start">Subtotal</td>
                                                    <td>RM<?= number_format($total, 2) ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold text-start">Discount</td>
                                                    <td>-RM<?= number_format($discountTotal, 2) ?></td>
                                                </tr>
                                                <tr class="highlight-top-border">
                                                    <td class="fw-bold text-start">Total Price</td>
                                                    <td>RM<?= number_format($netTotal, 2) ?></td>
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
</body>
</html>
