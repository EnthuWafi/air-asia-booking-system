<?php
require_once("functions.inc.php");
/* BOOKING */
// INSERTION

function createBooking($parameters) {
    $userData = $parameters["userData"];
    $flightInfo = $parameters["flightInfo"];
    $passengers = $parameters["passengers"];
    $contactInfo = $parameters["contactInfo"];

    //ok first retrieve from flights again (last time)
    $departureFlight = retrieveFlight($flightInfo["departure_flight_id"], $flightInfo["travel_class"],
        $flightInfo["passenger_count"]);
    $returnFlight = null;
    if ($flightInfo["trip_type"] == "RETURN") {
        $returnFlight = retrieveFlight($flightInfo["return_flight_id"], $flightInfo["travel_class"],
            $flightInfo["passenger_count"]);
    }

    $conn = OpenConn();
    //insert in bookings table
    $sqlQueryFirst = "INSERT INTO bookings(user_id, trip_type, booking_phone, booking_email) 
                    VALUES (?, ?, ?, ?)";
    $sqlQueryFirstID = "SET @last_booking_id = LAST_INSERT_ID()";

    //loop
    //insert all passengers in passengers table 
    $sqlQuerySecond = "INSERT INTO passengers(booking_id, passenger_fname, passenger_lname, 
                       passenger_dob, passenger_gender, special_assistance) 
                        VALUES (@last_booking_id, ?, ?, ?, ?, ?)";
    $sqlQuerySecondID = "SET @last_passenger_id = LAST_INSERT_ID()";

    //insert all flight addons in flight_addons table
    $sqlQueryThird = "INSERT INTO flight_addons(flight_id, passenger_id, 
                          age_category_price_code, travel_class_price_code, seat_number, 
                          baggage_price_code) VALUES (?, @last_passenger_id, ?, ?, ?, ?)";
    $sqlSelectBookingID = "SELECT @last_booking_id as 'booking_id'";
    try {
        //first query
        $conn->execute_query($sqlQueryFirst, [$userData["user_id"], $flightInfo["trip_type"],
            $contactInfo["phone"], $contactInfo["email"]]);
        $conn->query($sqlQueryFirstID); //id

        //loop
        foreach ($passengers as $passengerAgeCategoryKey => $passengerAgeCategoryValue){
            $ageCategory = ageCategoryAssoc($passengerAgeCategoryKey);
            foreach ($passengerAgeCategoryValue as $passenger){
                $conn->execute_query($sqlQuerySecond, [$passenger["first_name"], $passenger["last_name"],
                    $passenger["dob"], $passenger["gender"], $passenger["special_assistance"]]);
                $conn->query($sqlQuerySecondID); //id

                //insert flight addon
                $conn->execute_query($sqlQueryThird, [$departureFlight["flight_id"], $ageCategory["code"],
                    $flightInfo["travel_class"], $passenger["departure_seat"], $passenger["departure_baggage"]]);
                if ($flightInfo["trip_type"] == "return") {
                    $conn->execute_query($sqlQueryThird, [$returnFlight["flight_id"], $ageCategory["code"],
                        $flightInfo["travel_class"], $passenger["return_seat"], $passenger["return_baggage"]]);
                }
            }
        }
        //return booking id
        $result = $conn->execute_query($sqlSelectBookingID);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: Booking failed. Check logs!");
    }
    return null;
}

//update booking
function updateBookingDetails($bookingReference, $bookingLocation, $bookingID) {
    $sql = "UPDATE bookings SET booking_reference = ? and booking_payment_location = ? 
            WHERE booking_id = ?";
    $conn = OpenConn();

    try {
        if ($conn->execute_query($sql, [$bookingReference, $bookingLocation, $bookingID])){
            CloseConn($conn);
            return true;
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: Booking failed. Check logs!");
    }
    return null;
}