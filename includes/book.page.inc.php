<?php
require_once("functions.inc.php");
error_reporting(E_ALL);
ini_set('display_errors', 'On');
function confirm_booking(): void
{

    if (!array_keys_isset(["passengers", "phone", "email", "consent", "submit"], $_POST)) {
        die("Error: false POST values!");
    }
    $contactInfo = ["email"=>htmlspecialchars($_POST["email"]), "phone"=>htmlspecialchars($_POST["phone"]),
        "consent"=>htmlspecialchars($_POST["consent"])];

    if ($contactInfo["consent"] != 1) {
        die("Error: no agreement terms and service!");
    }
    //flight info
    $flightInfo = $_SESSION["flightInfo"];
    //retrieve from user
    $userData = $_SESSION["user_data"];
    //retrieve passengers
    $passengers = $_POST["passengers"];

    //create booking
    $bookingAssoc = createBooking(["userData" => $userData, "passengers" => $passengers,
        "flightInfo" => $flightInfo, "contactInfo"=>$contactInfo]);
    $bookingID = $bookingAssoc["booking_id"];
    $_SESSION["booking_id"] = $bookingID;

    if (empty($bookingID)){
        die("Error: booking failed! (somehow)");
    }
    //send payment file
    $file = $_FILES['payment_file'];

    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = ['jpg','jpeg','png','pdf'];

    try {
        if ($file["error"]) {
            throw new Exception($file["error"]);
        }
        if (in_array($fileActualExt, $allowed)) {
            //file size < 10MB
            if ($fileSize < 10485760) {
                $bookingReference = date("Y") . $bookingID . $flightInfo["travel_class"] . mb_substr($flightInfo["trip_type"], 0, 1);
                $fileNameNew = "#" . $bookingReference . "." . $fileActualExt;
                $fileDestination = $_SERVER['DOCUMENT_ROOT'] . '/payments/' . $fileNameNew;

                move_uploaded_file($fileTmpName, $fileDestination);

                if (!updateBookingDetails($bookingReference, $fileDestination, $bookingID)){
                    throw new Exception("Unable to save booking reference..");
                };

                $_SESSION["alert"] = ["title" => "Wohoo", "message" => "Booking was a success!", "type" => "success"];
            }
            else {
                throw new Exception("File too big");
            }
        }
        else{
            throw new Exception("Filetype not allowed");
        }
    }
    catch (exception $e) {
        $_SESSION["alert"] = ["title"=>"Forbidden", "message" => "Payment was not a success!<br>{$e->getMessage()}. Please contact the administrators to proceed!", "type" => "error"];
        header("Location: /index.php");
        exit();
    }
}


// BOOK FUNCTIONS
function book_guestDetails($flightInfo)
{
    $ageCategoriesArr = ["adult", "child", "infant", "senior"];
    foreach ($ageCategoriesArr as $key) {
        for ($i = 0, $n = $flightInfo[$key]; $i < $n; $i++) {
            echo "
<div class='mb-3'>
    <div class='row'>
        <h5>" . ucfirst($key) . " " . ($i + 1) . "</h5>
    </div>
    <div class='row row-cols-2'>
        <div class='col mt-2'>
            <label for='passengers[{$key}][{$i}][first_name]' class='form-label'>First Name</label>
            <input type='text' class='form-control' id='passengers[{$key}][{$i}][first_name]' name='passengers[{$key}][{$i}][first_name]' placeholder='First Name'>
        </div>
        <div class='col mt-2'>
            <label for='passengers[{$key}][{$i}][last_name]' class='form-label'>Last Name</label>
            <input type='text' class='form-control' id='passengers[{$key}][{$i}][last_name]' name='passengers[{$key}][{$i}][last_name]' placeholder='Last Name'>
        </div>
        <div class='col mt-2'>
            <label for='passengers[{$key}][{$i}][gender]' class='form-label'>Gender</label>
            <select class='form-select' id='passengers[{$key}][{$i}][gender]' name='passengers[{$key}][{$i}][gender]'>
                <option value='Male'>Male</option>
                <option value='Female'>Female</option>
            </select>
        </div>
        <div class='col mt-2'>
            <label for='passengers[{$key}][{$i}][dob]' class='form-label'>Date of Birth</label>
            <input type='date' class='form-control' id='passengers[{$key}][{$i}][dob]' name='passengers[{$key}][{$i}][dob]' placeholder='Date of Birth'>
        </div>
    </div>
    <div class='row mt-2'>
        <div class='col'>
            <input type='hidden' name='passengers[{$key}][{$i}][special_assistance]' value='0'>
            <div class='form-check'>
                <input class='form-check-input' type='checkbox' id='passengers[{$key}][{$i}][special_assistance]' name='passengers[{$key}][{$i}][special_assistance]' value='1'>
                <label class='form-check-label' for='passengers[{$key}][{$i}][special_assistance]'>
                    Special Assistance
                </label>
            </div>
        </div>
    </div>
</div>";
        }
    }
}

function book_baggageAddon($flightInfo, $baggageOptions)
{
    $ageCategoriesArr = ["adult", "child", "infant", "senior"];
    $str = "";
    //loop here to iterate over passenger
    foreach ($ageCategoriesArr as $key) {
        for ($i = 0, $n = $flightInfo[$key]; $i < $n; $i++) {
            $j = $i + 1;
            $str .= "<div class='row mt-3'>
                <div class='row mb-2'>
                    <h5>" . ucfirst($key) . " {$j}</h5>
                </div>
                <div class='col'>
                <select class='form-select' name='baggages[{$key}][{$i}]' onchange='updateCost(this);' required>
                    <option selected disabled>Select baggage option</option>";
            foreach ($baggageOptions as $baggage) {
                $str .= "<option value='{$baggage["code"]}'>{$baggage["name"]}</option>";
            }
            $str .= "</select>
                    </div>
                </div>";
        }
    }
    return $str;
}

//outdated function - just keeping it around for fun
function book_seatingAddon($flightInfo, $flight, $flightAddons, $name)
{
    $ageCategoriesArr = ["adult", "child", "infant", "senior"];

    //loop here to iterate over passenger
    $travelClassArr = travelClassAssoc($flightInfo["travel_class"]);
    foreach ($ageCategoriesArr as $key) {
        for ($i = 0, $n = $flightInfo[$key]; $i < $n; $i++) {
            //seat
            echo "
<div class='row'>
<h5>" . (ucfirst($key)) . " " . ($i + 1) . "</h5>
<button class='btn btn-primary' type='button' data-bs-toggle='collapse' data-bs-target='#collapse{$name}{$key}{$i}' aria-expanded='false' aria-controls='collapse{$name}{$key}{$i}'>Seat Choice</button>
<div class='collapse' id='collapse{$name}{$key}{$i}'>
    <div class='card card-body'>";

            echo "<div class='row d-flex justify-content-center text-center'>";
            $capacity = $travelClassArr["class"] . "_capacity";
            for ($j = 0, $m = $flight[$capacity]; $j < $m; $j++) {
                $disabled = false;
                if ($flightAddons != null) {
                    foreach ($flightAddons as $addon) {
                        if ($addon["seat_number"] == ($j + 1)) {
                            $disabled = true;
                            break;
                        }
                    }
                }
                $disabledStr = $disabled ? "disabled" : "";


                $plusOne = $j + 1;
                //each row how many seats
                if ($j % 10 == 0 && $j != 0) {
                    echo "<div class='w-100'></div>";
                }

                echo "<div class='col text-center'>
                            <input type='radio' name='passengers[{$key}][{$i}][{$name}]' value='{$plusOne}' {$disabledStr}>
                      </div>";
            }
            echo "</div>
            </div>
        </div>
    </div>";
        }
    }
}

//newer
function book_cabinSeating($travelClass, $flight, $flightAddons, $type) {
    $flightCapacityName = $travelClass["class"] . "_capacity";
    $flightCapacity = $flight[$flightCapacityName];

    //seat disabled
    $seatDisabled = [];
    if ($flightAddons != null) {
        foreach ($flightAddons as $flightAddon) {
            $seatNumber = $flightAddon["seat_number"];
            array_push($seatDisabled, $seatNumber);
        }
    }


    $seats = "";
    $maxColumnLength = 6;

    $seatLabels = range('A', 'Z'); // Generate an array of seat labels from A to Z

    $rowCount = 1; //rows
    $colCount = 0; //col
    $i = 0; //for counting till capacity is reached
    while ($i < $flightCapacity){
        if ($i % $maxColumnLength == 0) {
            $seats .= "<li class=''><ol class='seats'>";
        }
        $i++;
        $colCount++;

        $label = $rowCount . $seatLabels[$colCount - 1]; // Generate the seat label
        $disabledCheck = in_array($label, $seatDisabled) ? "disabled" : "";

        $seats .= "<li class='seat $type'>
          <input type='checkbox' id='{$label}{$type}' value='$label' $disabledCheck>
          <label for='{$label}{$type}'>$label</label>
        </li>";

        if ($i % $maxColumnLength == 0) {
            $seats .= "</ol></li>";
            $colCount = 0;
            $rowCount++;
        }
    }
    echo $seats;
}
function book_invoiceBooking($booking) {
    $bookingID = $booking["booking_id"];
    ?>
    <!-- Invoice details and content -->
    <div class="invoice-box my-5">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="/assets/img/airasiacom_logo.svg" style="width: 100%; max-width: 200px" />
                            </td>

                            <td>
                                Booking Ref: <span class="font-lato fw-bold">#<?= $booking["booking_reference"]; ?></span><br />
                                Created: <?= formatDateFriendly($booking["date_created"]); ?> <br />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                AIRASIA BERHAD<br />
                                RedQ Jalan Pekeliling 5<br />
                                Lapangan Terbang Antarabangsa Kuala Lumpur<br/>
                                Selangor, 64000 Malaysia<br />
                                +60-3-86604333
                            </td>

                            <td>
                                <?= "{$booking["user_fname"]} {$booking["user_lname"]}"; ?><br />
                                <?= "{$booking["booking_email"]}"; ?><br />
                                <?= $booking["booking_phone"] ?? "-"; ?><br />
                                <?= "{$booking["username"]}"; ?><br />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Payment Method</td>
                <td></td>
            </tr>

            <tr class="details">
                <td>Direct Bank Transfer</td>
                <td></td>
            </tr>

            <tr class="heading">
                <td>Item</td>

                <td>Price</td>
            </tr>

            <?php

            //tis how to calculate these crap
            $bookingFlight = retrieveBookingFlights($bookingID);

            // Retrieve count for adult age category
            $adultCount = retrieveBookingAgeCategoryCount($bookingID, "adult")["count"];
            // Retrieve count for child age category
            $childCount = retrieveBookingAgeCategoryCount($bookingID, "child")["count"];
            // Retrieve count for senior age category
            $seniorCount = retrieveBookingAgeCategoryCount($bookingID, "senior")["count"];
            // Retrieve count for infant age category
            $infantCount = retrieveBookingAgeCategoryCount($bookingID, "infant")["count"];
            $ageCategoryArr = ["adult"=>$adultCount, "child"=>$childCount, "senior"=>$seniorCount,
                "infant"=>$infantCount];

            $bookingTravelClass = retrieveBookingTravelClass($bookingID);
            $travelClass = $bookingTravelClass["travel_class_price_code"];

            $baggageDepartArr = ["XSM"=>retrieveBookingBaggageCount($bookingID, "XSM")["count"],
                "SML"=>retrieveBookingBaggageCount($bookingID, "SML")["count"],
                "STD"=>retrieveBookingBaggageCount($bookingID, "STD")["count"],
                "LRG"=>retrieveBookingBaggageCount($bookingID, "LRG")["count"],
                "XLG"=>retrieveBookingBaggageCount($bookingID, "XLG")["count"]];

            $ageCategoryAll = ageCategoryAssocAll();

            $count = 0;
            foreach ($bookingFlight as $flight) {
                $cost = calculateFlightPriceAlternate($flight["flight_base_price"], $ageCategoryArr, $travelClass, $baggageArr);
                $path = "<em>({$flight["origin_airport_code"]} <i class='bi bi-arrow-right'></i> {$flight["destination_airport_code"]})</em>";

                $flightType = $count == 0 ? "Depart $path" : "Return $path";
                $costFormat = number_format((float)$cost, 2, ".", ",");
                echo "
                <tr class='item py-2'>
                    <td>$flightType</td>        
                    <td>RM$costFormat</td>
                </tr>";

                echo "
                <tr class='item'>
                    <td class='ps-4'>";
                    foreach ($ageCategoryAll as $ageCategoryKey => $ageCategoryValue) {
                        echo "{$ageCategoryValue["name"]} x{$ageCategoryArr[$ageCategoryKey]}";
                        echo end($ageCategoryAll) == $ageCategoryValue ? "" : ", ";
                    }
                echo "
                    </td>
                    <td></td>
                <tr class='item'>";

                echo "
                <tr class='item'>
                    <td class='ps-4'>";
                foreach ($baggageArr as $baggageArrKey => $baggageArrValue) {
                    echo "{$baggageArrKey} x{$baggageArrValue}";
                    echo "XLG" == $baggageArrKey? "" : ", ";
                }
                echo "
                    </td>
                    <td></td>
                <tr class='item'>";

                $count++;
            }
            $netCost = $booking["booking_cost"];
            $netFormatted = number_format((float)$netCost, 2, ".", ",");

            ?>


            <tr class="item last">
                <td>Discount</td>

                <td><?= "RM{$booking["booking_discount"]}"; ?></td>
            </tr>

            <tr class="total">
                <td></td>

                <td>Total: <?= "RM$netFormatted"; ?></td>
            </tr>
        </table>
    </div>
    <?php
}