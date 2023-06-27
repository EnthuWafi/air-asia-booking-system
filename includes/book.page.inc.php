<?php
require_once("functions.inc.php");
error_reporting(E_ALL);
ini_set('display_errors', 'On');
function confirm_booking(): void
{

    if (!array_keys_isset_or_not(["passengers", "phone", "email", "consent", "submit"], $_POST)) {
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
            echo "<div class='row'>
<h5>" . (ucfirst($key)) . " " . ($i + 1) . "</h5><br>
<input type='text' name='passengers[{$key}][{$i}][first_name]' placeholder='First Name'>
<input type='text' name='passengers[{$key}][{$i}][last_name]' placeholder='Last Name'>
<select name='passengers[{$key}][{$i}][gender]'>
<option value='Male'>Male</option>
<option value='Female'>Female</option>
</select>
<input type='date' name='passengers[{$key}][{$i}][dob]' placeholder='Date of Birth'>

<input type='hidden' name='passengers[{$key}][{$i}][special_assistance]' value='0'>
<label>
<span>Special Assistance: </span>
<input type='checkbox' name='passengers[{$key}][{$i}][special_assistance]' value='1'>
</label>

</div>
";
        }
    }
}

function book_baggageAddon($flightInfo, $baggageOptions, $name)
{
    $ageCategoriesArr = ["adult", "child", "infant", "senior"];
    //loop here to iterate over passenger
    foreach ($ageCategoriesArr as $key) {
        for ($i = 0, $n = $flightInfo[$key]; $i < $n; $i++) {
            $j = $i + 1;
            echo "<div class='row'>
                <h5>" . (ucfirst($key)) . " {$j}</h5>
                <select name='passengers[{$key}][{$i}][{$name}]' onchange='updateTotalCost();'>";
            foreach ($baggageOptions as $baggage) {
                echo "<option value='{$baggage["baggage_price_code"]}'>{$baggage["baggage_name"]}</option>";
            }
            echo "</select></div>";
        }
    }
}

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



