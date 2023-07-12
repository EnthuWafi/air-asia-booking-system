<?php
require_once("functions.inc.php");
/* BOOKING */
// INSERTION

function createBooking($parameters) {
    $userData = $parameters["userData"];
    $flightInfo = $parameters["flightInfo"];
    $passengers = $parameters["passengers"];
    $contactInfo = $parameters["contactInfo"];
    $flights = $parameters["flights"];

    $departureFlight = $flights[0];
    $returnFlight = $flights[1];

    $departureCost = calculateFlightPrice($departureFlight["flight_base_price"], $passengers, $flightInfo["travel_class"],
    "departure");
    //net cost is departure (full) + return (full) + discount
    $discountDeparture = $departureCost * $departureFlight["flight_discount"];

    if (!empty($returnFlight)) {
        $returnCost = calculateFlightPrice($returnFlight["flight_base_price"], $passengers, $flightInfo["travel_class"],
            "return");
        $discountReturn = $returnCost * $returnFlight["flight_discount"];
    }


    $discount = $discountDeparture + ($discountReturn ?? 0);
    $netCost = $departureCost + ($returnCost ?? 0) - $discount;

    $conn = OpenConn();
    //insert in bookings table
    $sqlQueryFirst = "INSERT INTO bookings(user_id, trip_type, booking_phone, booking_email, booking_cost, booking_discount) 
                    VALUES (?, ?, ?, ?, ?, ?)";
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
            $contactInfo["phone"], $contactInfo["email"], $netCost, $discount]);
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

function updateBooking($bookingID, $bookingStatus) {
    $sql = "UPDATE bookings SET booking_status = ? WHERE booking_id = ?";
    $conn = OpenConn();
    try {
        if ($conn->execute_query($sql, [$bookingStatus, $bookingID])){
            CloseConn($conn);
            return true;
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: Booking update failed. Check logs!");
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
        die("Error: Booking delete failed. Check logs!");
    }
    return false;
}

//retrieve ALL
function retrieveAllBookings() {
    $sql = "SELECT b.*, c.*, u.* FROM bookings b
            INNER JOIN customers c on b.user_id = c.user_id
            INNER JOIN users u on c.user_id = u.user_id
            ORDER BY b.date_created DESC";
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

//retrieve ALL
function retrieveAllBookingsLIMIT5() {
    $sql = "SELECT b.*, c.*, u.* FROM bookings b
            INNER JOIN customers c on b.user_id = c.user_id
            INNER JOIN users u on c.user_id = u.user_id
            ORDER BY b.date_created DESC
            LIMIT 5
            ";
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
    $sql = "SELECT b.*, u.*, c.* FROM bookings b
           INNER JOIN customers c on b.user_id = c.user_id
           INNER JOIN users u on c.user_id = u.user_id
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

function retrieveBookingByUser($bookingID, $userID) {
    $sql = "SELECT b.*, u.*, c.* FROM bookings b
           INNER JOIN customers c on b.user_id = c.user_id
           INNER JOIN users u on c.user_id = u.user_id
            WHERE b.booking_id = ? AND b.user_id = ?";
    $conn = OpenConn();
    try {
        $result = $conn->execute_query($sql, [$bookingID, $userID]);
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
    $sql = "SELECT fl.*, ADDTIME(fl.departure_time, fl.duration) as 'arrival_time', ac.*, al.*, 
       ao.airport_country as 'origin_airport_country', ao.airport_state as 'origin_airport_state',
       ad.airport_name as 'destination_airport_name', ad.airport_country as 'destination_airport_country', ad.airport_state as 'destination_airport_state'
FROM flights fl
INNER JOIN airports ao on fl.origin_airport_code = ao.airport_code
INNER JOIN airports ad on fl.destination_airport_code = ad.airport_code
INNER JOIN aircrafts ac on fl.aircraft_id = ac.aircraft_id
INNER JOIN airlines al on fl.airline_id  = al.airline_id
WHERE fl.flight_id IN (
    SELECT fa.flight_id 
    FROM bookings bo
    INNER JOIN passengers p on bo.booking_id = p.booking_id
    INNER JOIN flight_addons fa on p.passenger_id = fa.passenger_id
    WHERE bo.booking_id = ?
)
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
        die("Error: unable to retrieve booking flights!");
    }

    return null;
}

function retrieveBookingPassengers($bookingID) {
    $sql = "SELECT pa.*
FROM bookings bo
INNER JOIN passengers pa on bo.booking_id = pa.booking_id
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

function retrieveFlightPassengerAddon($flightID, $passengerID) {
    $sql = "SELECT pa.*, fa.*, tcp.*
FROM passengers pa
INNER JOIN flight_addons fa on pa.passenger_id = fa.passenger_id AND fa.flight_id = ?
INNER JOIN travel_class_prices tcp on fa.travel_class_price_code = tcp.travel_class_price_code
WHERE pa.passenger_id = ?";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$flightID, $passengerID]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
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
        die("Error: unable to retrieve booking status!");
    }

    return null;
}

function retrieveBookingAgeCategoryCount($bookingID, $ageCategory) {
    $ageCategoryAssoc = ageCategoryAssoc($ageCategory);

    $sql = "SELECT COUNT(p.passenger_id) as 'count' FROM bookings b
            INNER JOIN passengers p on b.booking_id = p.booking_id
            WHERE p.passenger_id IN (
                SELECT fa.passenger_id FROM flight_addons fa
                WHERE fa.age_category_price_code = ?
            ) AND b.booking_id = ?";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$ageCategoryAssoc["code"], $bookingID]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve booking pass. count!");
    }

    return null;
}

function retrieveBookingBaggageCount($bookingID, $flightID, $baggageCode) {
    $sql = "SELECT COUNT(p.passenger_id) as 'count' FROM bookings b
            INNER JOIN passengers p on b.booking_id = p.booking_id
            INNER JOIN flight_addons fa on p.passenger_id = fa.passenger_id AND fa.baggage_price_code = ? AND fa.flight_id = ?
            WHERE b.booking_id = ?";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$baggageCode, $flightID, $bookingID]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve booking pass. count!");
    }

    return null;
}
function retrieveBookingTravelClass($bookingID) {

    $sql = "SELECT fa.*
            FROM flight_addons fa
            INNER JOIN passengers p on fa.passenger_id = p.passenger_id
            INNER JOIN bookings b on p.booking_id = b.booking_id
            WHERE b.booking_id = ? LIMIT 1";
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
        die("Error: unable to retrieve booking travel class!");
    }

    return null;
}

function retrieveAllBookingLike($query) {
    $query = "%{$query}%";
    $sql = "SELECT bo.*, c.*, u.* FROM bookings bo
            INNER JOIN customers c on bo.user_id = c.user_id
            INNER JOIN users u on c.user_id = u.user_id
            WHERE bo.booking_reference LIKE ? OR bo.booking_status LIKE ?";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$query, $query]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot bookings like query!");
    }

    return null;
}

function retrieveAllBookingUserLike($userID, $query) {
    $query = "%{$query}%";
    $sql = "SELECT bo.*, c.*, u.* FROM bookings bo
            INNER JOIN customers c on bo.user_id = c.user_id
            INNER JOIN users u on c.user_id = u.user_id AND u.user_id = ?
            WHERE bo.booking_reference LIKE ? OR bo.booking_status LIKE ?";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$userID, $query, $query]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot bookings user like query!");
    }

    return null;
}
function retrieveAllAircraftLike($query) {
    $query = "%{$query}%";
    $sql = "SELECT ac.* FROM aircrafts ac
            WHERE ac.aircraft_name LIKE ?";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$query]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot aircrafts like query!");
    }

    return null;
}



