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

                    $_SESSION["contactInfo"] = $contactInfo;
                    $_SESSION["passengers"] = htmlspecialchars($_POST["passengers"]);

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

if (!isset($_SESSION["flightInfo"])) {
    makeToast("error", "Flight info was not found. Please try searching flight again!", "Error");
    header("Location: /index.php");
    die();
}

$flightInfo = $_SESSION["flightInfo"];

displayToast();
$token = getToken();
?>
<html>
<head>
    <?php head_tag_content(); ?>
    <title><?= config("name") ?> | Booking Guest Details</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php header_bar("Passenger Details") ?>

            <div class="container py-2 px-4 pb-5 mt-3 border rounded-4">
                <div class="row mt-4 gx-4 ms-3">
                    <div class="shadow p-3 mb-5 bg-body rounded row">
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
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                        <div class="invalid-feedback">Please provide a valid phone number.</div>
                                    </div>
                                    <div class="col">
                                        <label for="email" class="form-label">Email:</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
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
                                <button type="submit" class="btn btn-outline-primary mt-3 w-25 ms-auto float-end">Next</button>
                            </div>

                        </form>
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
