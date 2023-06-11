<?php

token_csrf();

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
$bookingID = createBooking(["userData" => $userData, "passengers" => $passengers,
    "flightInfo" => $flightInfo, "contactInfo"=>$contactInfo]);

if ($bookingID == null){
    die("Error: booking failed! (somehow)");
}
//send payment file
$file = $_FILES['payment_file'];

$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];
$fileType = $file['type'];

$fileExt = explode('.', $fileName);
$fileActualExt = strtolower(end($fileExt));

$allowed = array('jpg','jpeg','png','pdf');

try {
    if (in_array($fileActualExt, $allowed)) {
        //file size < 10MB
        if ($fileSize < 10485760) {
            $bookingReference = date("Y") . $bookingID . $flightInfo["travel_class"] . mb_substr($flightInfo["trip_type"], 0, 1);
            $fileNameNew = "#" . $bookingReference . "." . $fileActualExt;
            $fileDestination = '/payments/' . $fileNameNew;

            move_uploaded_file($fileTmpName, $fileDestination); //to upload file to a specific folder

            updateBookingDetails($bookingReference, $fileDestination, $bookingID);
            $_SESSION["alert"] = ["title" => "Wohoo", "message" => "Booking was a success!", "type" => "success"];
            header("Location: /index.php");
            exit();
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
    $_SESSION["alert"] = ["title"=>"Forbidden", "message" => "Payment was not a success! \n
    {$e->getMessage()}\n
    Please contact the administrators to proceed!", "type" => "error"];
    header("Location: /index.php");
    exit();
}






