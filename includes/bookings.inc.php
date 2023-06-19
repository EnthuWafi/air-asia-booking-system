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

    $departureCost = calculateFlightPrice($departureFlight["flight_base_price"], $passengers, $flightInfo["travel_class"],
    "departure");
    if ($returnFlight) {
        $returnCost = calculateFlightPrice($returnFlight["flight_base_price"], $passengers, $flightInfo["travel_class"],
            "return");
    }
    $netCost = $departureCost + ($returnCost ?? 0);

    $conn = OpenConn();
    //insert in bookings table
    $sqlQueryFirst = "INSERT INTO bookings(user_id, trip_type, booking_phone, booking_email, booking_cost) 
                    VALUES (?, ?, ?, ?, ?)";
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
            $contactInfo["phone"], $contactInfo["email"], $netCost]);
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
                if ($flightInfo["trip_type"] == "RETURN") {
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
        //remove booking first
        $result = $conn->execute_query($sqlSelectBookingID);
        $assoc = mysqli_fetch_assoc($result);
        deleteBooking($assoc["booking_id"]);

        //ok proceed
        createLog($conn->error);
        die("Error: Booking failed. Check logs!");
    }
    return null;
}

//update booking
function updateBookingDetails($bookingReference, $bookingLocation, $bookingID) {
    $sql = "UPDATE bookings SET booking_reference = ?, booking_payment_location = ? 
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
    return false;
}

//just to remove failed bookings
function deleteBooking($bookingID) {
    $sql = "DELETE FROM bookings WHERE booking_id = ?";
    $conn = OpenConn();
    try {
        if ($conn->execute_query($sql, [$bookingID])){
            CloseConn($conn);
            return true;
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: Booking failed. Check logs!");
    }
    return false;
}

//retrieve ALL
function retrieveAllBookings() {
    $sql = "SELECT b.*, c.*, u.* FROM bookings b
            INNER JOIN customers c on b.user_id = c.user_id
            INNER JOIN users u on c.user_id = u.user_id";
    $conn = OpenConn();
    try {
        $result = $conn->execute_query($sql);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve bookings!");
    }

    return null;
}

//retrieve specific
function retrieveBooking($bookingID) {
    $sql = "SELECT b.* FROM bookings b
            WHERE b.booking_id = ?";
    $conn = OpenConn();
    try {
        $result = $conn->execute_query($sql, [$bookingID]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve bookings!");
    }

    return null;
}

//retrieve specific
function retrieveBookingFlights($bookingID) {
    $sql = "SELECT bo.*, fl.*, ADDTIME(fl.departure_time, fl.duration) as 'arrival_time'
FROM bookings bo
INNER JOIN passengers pa on bo.booking_id = pa.booking_id
INNER JOIN flight_addons fa on pa.passenger_id = fa.passenger_id
INNER JOIN flights fl on fa.flight_id = fl.flight_id 
WHERE bo.booking_id = ?
ORDER BY fl.departure_time ASC";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$bookingID]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_BOTH);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve booking!");
    }

    return null;
}

function retrieveBookingPassengers($bookingID) {
    $sql = "SELECT bo.*, pa.*
FROM bookings bo
INNER JOIN passengers pa on bo.booking_id = pa.booking_id
INNER JOIN flight_addons fa on pa.passenger_id = fa.passenger_id
WHERE bo.booking_id = ?";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$bookingID]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve booking passengers!");
    }

    return null;
}

function retrievePassengerAddon($passengerID) {
    $sql = "SELECT pa.*
FROM passengers pa
INNER JOIN flight_addons fa on pa.passenger_id = fa.passenger_id
WHERE pa.passenger_id = ?";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$passengerID]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve passenger!");
    }

    return null;
}

function retrieveBookingStatus() {
    $sql = "SELECT * FROM booking_statuses";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve passenger!");
    }

    return null;
}




