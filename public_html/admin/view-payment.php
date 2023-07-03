<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

if (!$_GET || empty($_GET["booking_id"])){
    makeToast("warning", "Booking doesn't exist!", "Warning");
    header("Location: /admin/manage-my-bookings.php");
    die();
}

$booking = retrieveBooking(htmlspecialchars($_GET["booking_id"]));
$locationFileName = $booking["booking_payment_location"];

$fileExt = explode('.', $locationFileName);
$fileActualExt = strtolower(end($fileExt));

if ($fileActualExt === "pdf") {
    header("Content-type: application/pdf");
    header("Content-Disposition: inline; filename=filename.pdf");
    @readfile($locationFileName);
}
else {
    // Image file
    $imageContent = file_get_contents($locationFileName);
    if ($imageContent !== false) {
        $imageInfo = getimagesize($locationFileName);
        if ($imageInfo !== false) {
            $mimeType = $imageInfo['mime'];
            header("Content-type: $mimeType");
            echo $imageContent;
        } else {
            // Invalid image file
            echo "Invalid image file";
        }
    } else {
        // Error reading image file
        echo "Error reading image file";
    }
}

