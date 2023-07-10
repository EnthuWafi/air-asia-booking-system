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
            $adultCount = retrieveBookingAgeCategoryCount($bookingID,  "adult")["count"];
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

            $ageCategoryAll = ageCategoryAssocAll();

            $count = 0;
            foreach ($bookingFlight as $flight) {
                //baggage
                $baggageArr = ["XSM"=>retrieveBookingBaggageCount($bookingID, $flight["flight_id"], "XSM")["count"],
                    "SML"=>retrieveBookingBaggageCount($bookingID, $flight["flight_id"],  "SML")["count"],
                    "STD"=>retrieveBookingBaggageCount($bookingID, $flight["flight_id"], "STD")["count"],
                    "LRG"=>retrieveBookingBaggageCount($bookingID, $flight["flight_id"], "LRG")["count"],
                    "XLG"=>retrieveBookingBaggageCount($bookingID, $flight["flight_id"], "XLG")["count"]];

                $cost = calculateFlightPriceAlternate($flight["flight_base_price"], $ageCategoryArr, $travelClass, $baggageArr);
                $path = "<em>({$flight["origin_airport_code"]} <i class='bi bi-arrow-right'></i> {$flight["destination_airport_code"]})</em>";

                $flightType = $count == 0 ? "Depart $path" : "Return $path";
                $costFormat = number_format((float)$cost, 2, ".", ",");
                //item main
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
            $discount = $booking["booking_discount"];
            $discountFormatted = number_format((float)$discount, 2);

            $netCost = $booking["booking_cost"];
            $netFormatted = number_format((float)$netCost, 2, ".", ",");

            $subTotal = $discount + $netCost;
            $subTotalFormatted = number_format((float)$subTotal, 2);

            ?>

            <tr class="item">
                <td>Subtotal</td>
                <td><?= "RM{$subTotalFormatted}"; ?></td>
            </tr>

            <tr class="item last">
                <td>Discount</td>

                <td><?= "RM{$discountFormatted}"; ?></td>
            </tr>

            <tr class="total">
                <td></td>

                <td>Total: <?= "RM$netFormatted"; ?></td>
            </tr>
        </table>
    </div>
    <?php
}

function book_ticketList($booking) {
    $bookingID = $booking["booking_id"];
    $passengers = retrieveBookingPassengers($bookingID);
    $flights = retrieveBookingFlights($bookingID);

    //flight depart and return
    $count = 0;
    foreach ($flights as $flight) {
        $flightType = $count == 0 ? "Departure Tickets" : "Return Tickets";
        ?>
        <div class="row my-2">
            <h3 class="text-center card-title"><?= $flightType ?></h3>
        </div>
        <hr>
        <div class="row my-5">
            <?php
            foreach ($passengers as $passenger) {
                $flightAddon = retrieveFlightPassengerAddon($flight["flight_id"], $passenger["passenger_id"]);

                $date = date_create($flight["departure_time"]);

                $dateFormatted = date_format($date, "h:iA d F Y");

                $specialAssistance = $flightAddon["special_assistance"] == 1 ? "<span class='special-assistance'><i class='bx bx-handicap'></i></span>" : "" ;
                ?>

                    <div class="position-relative" style="height: 300px">
                        <div class="box">
                            <ul class="left">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>

                            <ul class="right">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <div class="ticket">
                                <span class="airline"><img src="/assets/img/airasiacom_logo.svg" style="width: 80px; height: auto; filter: brightness(0) invert(1);">
                                </span>
                                <span class="airline airlineslip"><img src="/assets/img/airasiacom_logo.svg" style="width: 80px; height: auto; filter: brightness(0) invert(1);"></span>
                                <span class="boarding">Boarding pass</span>
                                <?= $specialAssistance ?>
                                <div class="content">
                                    <img src="<?= $flight["airline_image"] ?>" class="airline-img">
                                    <span class="jfk"><?= $flight["origin_airport_code"] ?></span>
                                    <span class="plane"><svg class="ms-1 mt-1" clip-rule="evenodd" fill-rule="evenodd" height="60" width="60" image-rendering="optimizeQuality" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg"><g stroke="#222"><line fill="none" stroke-linecap="round" stroke-width="30" x1="300" x2="55" y1="390" y2="390"/><path d="M98 325c-9 10 10 16 25 6l311-156c24-17 35-25 42-50 2-15-46-11-78-7-15 1-34 10-42 16l-56 35 1-1-169-31c-14-3-24-5-37-1-10 5-18 10-27 18l122 72c4 3 5 7 1 9l-44 27-75-15c-10-2-18-4-28 0-8 4-14 9-20 15l74 63z" fill="#222" stroke-linejoin="round" stroke-width="10"/></g></svg></span>
                                    <span class="sfo"><?= $flight["destination_airport_code"] ?></span>
                                    <span class="jfk jfkslip"><?= $flight["origin_airport_code"] ?></span>
                                    <span class="plane planeslip"><svg class="ms-1 mt-1" clip-rule="evenodd" fill-rule="evenodd" height="50" width="50" image-rendering="optimizeQuality" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg"><g stroke="#222"><line fill="none" stroke-linecap="round" stroke-width="30" x1="300" x2="55" y1="390" y2="390"/><path d="M98 325c-9 10 10 16 25 6l311-156c24-17 35-25 42-50 2-15-46-11-78-7-15 1-34 10-42 16l-56 35 1-1-169-31c-14-3-24-5-37-1-10 5-18 10-27 18l122 72c4 3 5 7 1 9l-44 27-75-15c-10-2-18-4-28 0-8 4-14 9-20 15l74 63z" fill="#222" stroke-linejoin="round" stroke-width="10"/></g></svg></span>
                                    <span class="sfo sfoslip"><?= $flight["destination_airport_code"] ?></span>


                                    <div class="sub-content">
                                        <span class="watermark">airasia</span>
                                        <span class="name">PASSENGER NAME<br><span><?= "{$flightAddon["passenger_fname"]}, {$flightAddon["passenger_lname"]}" ?></span></span>
                                        <span class="flight">FLIGHT NO<br><span><?= $flight["flight_id"] ?></span></span>
                                        <span class="baggage">BAGGAGE<br><span><?= $flightAddon["baggage_price_code"] ?></span></span>
                                        <span class="seat">SEAT<br><span><?= $flightAddon["seat_number"] ?></span></span>
                                        <span class="boardingtime">BOARDING TIME<br><span><?= $dateFormatted ?></span></span>
                                        <span class="age">AGE GROUP<br><span><?= $flightAddon["age_category_price_code"] ?></span></span>
                                        <span class="travel-class">TRAVEL CLASS<br><span><?php echo $flightAddon["travel_class_name"] != "Premium Economy" ? $flightAddon["travel_class_name"] : "Pre. Economy"; ?></span></span>


                                        <span class="flight flightslip">FLIGHT NO<br><span><?= $flight["flight_id"] ?></span></span>
                                        <span class="seat seatslip">SEAT<br><span><?= $flightAddon["seat_number"] ?></span></span>
                                        <span class="name nameslip">PASSENGER NAME<br><span><?= "{$flightAddon["passenger_fname"]}, {$flightAddon["passenger_lname"]}" ?></span></span>
                                    </div>
                                </div>
                                <div class="barcode"></div>
                                <div class="barcode slip"></div>
                            </div>
                        </div>
                    </div>


                <?php
            }
            ?>

        </div>
        <?php
        $count++;
    }
}