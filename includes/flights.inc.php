<?php
require_once("functions.inc.php");
/* FLIGHTS RELATED */
//retrieve all airport
function retrieveAirports() {
    $sql = "SELECT * FROM airports";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: unable to retrieve airports!");
    }
    return null;
}

function retrieveAircrafts() {
    $sql = "SELECT * FROM aircrafts";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: unable to retrieve aircraft!");
    }
    return null;
}

function retrieveAirlines() {
    $sql = "SELECT * FROM airlines";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: unable to retrieve airlines!");
    }
    return null;
}


//retrieve flights that matches parameters
function retrieveFlightsSearch($origin, $destination, $departureTime, $travelClass, $passengerCount) {
    $travelClassAssoc = travelClassAssoc($travelClass);

    $sql = "SELECT fl.*, ao.airport_name as 'origin_airport_name', ao.airport_country as 'origin_airport_country', ao.airport_state as 'origin_airport_state',
       ad.airport_name as 'destination_airport_name', ad.airport_country as 'destination_airport_country', ad.airport_state as 'destination_airport_state',
       ADDTIME(fl.departure_time, fl.duration) as 'arrival_time', ac.economy_capacity, ac.business_capacity, ac.premium_economy_capacity, ac.first_class_capacity,
       al.airline_name, al.airline_image
    FROM flights fl
    INNER JOIN airports ao ON fl.origin_airport_code = ao.airport_code AND ao.airport_code = ?
    INNER JOIN airports ad ON fl.destination_airport_code = ad.airport_code AND ad.airport_code = ?
    INNER JOIN airlines al on fl.airline_id = al.airline_id
    INNER JOIN aircrafts ac ON fl.aircraft_id = ac.aircraft_id AND ac.{$travelClassAssoc["class"]}_capacity > 
    (
    (SELECT COUNT(fa.flight_addon_id) FROM flight_addons fa WHERE fa.flight_id = fl.flight_id 
    AND fa.travel_class_price_code = '{$travelClassAssoc["code"]}') + ?
    )
    
    WHERE DATEDIFF(departure_time, ?) = 0 AND departure_time > NOW()";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$origin, $destination, $passengerCount, $departureTime]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: unable to retrieve flights!");
    }

    return null;
}
// retrieve flight with id
function retrieveFlightSearch($flight_id, $travel_class, $passenger_count) {
    $travel_class_assoc = travelClassAssoc($travel_class);
    $sql = "SELECT fl.*, ao.airport_name as 'origin_airport_name', ao.airport_country as 'origin_airport_country', ao.airport_state as 'origin_airport_state',
       ad.airport_name as 'destination_airport_name', ad.airport_country as 'destination_airport_country', ad.airport_state as 'destination_airport_state',
       ADDTIME(fl.departure_time, fl.duration) as 'arrival_time', ac.economy_capacity, ac.business_capacity, ac.premium_economy_capacity, ac.first_class_capacity,
       al.airline_name, al.airline_image
    FROM flights fl
    INNER JOIN airports ao ON fl.origin_airport_code = ao.airport_code
    INNER JOIN airports ad ON fl.destination_airport_code = ad.airport_code
    INNER JOIN aircrafts ac ON fl.aircraft_id = ac.aircraft_id AND ac.{$travel_class_assoc["class"]}_capacity > 
    (
        (SELECT COUNT(fa.flight_addon_id) FROM flight_addons fa 
        INNER JOIN passengers p ON p.passenger_id = fa.passenger_id
        INNER JOIN bookings b ON b.booking_id = p.booking_id AND booking_status = 'COMPLETED'
        WHERE fa.flight_id = fl.flight_id AND fa.travel_class_price_code = '{$travel_class_assoc["code"]}')
    + ?)
    INNER JOIN airlines al on fl.airline_id = al.airline_id
    WHERE fl.flight_id = ? AND departure_time > NOW()";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$passenger_count, $flight_id]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve flight!");
    }

    return null;
}

function retrieveFlightAddon($flight_id, $travel_class) {
    $travelClassArr = travelClassAssoc($travel_class);
    $sql = "SELECT * FROM flight_addons 
         WHERE flight_id = ? AND travel_class_price_code =  '{$travelClassArr["code"]}'
        ORDER BY seat_number ASC";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$flight_id]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve flight addons!");
    }

    return null;
}


function retrieveBaggageOptions() {

    $sql = "SELECT * FROM baggage_prices ORDER BY baggage_weight ASC";
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
        die();
    }

    return null;
}

function retrieveAgeCategory() {

    $sql = "SELECT * FROM age_category_prices";
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
        die();
    }

    return null;
}

function retrieveTravelClass() {

    $sql = "SELECT * FROM travel_class_prices";
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
        die();
    }

    return null;
}

//RETRIEVE ALL FLIGHTS (FOR ADMIN FLIGHTS)
function retrieveAllFlights() {
    $sql = "SELECT f.*, ADDTIME(f.departure_time, f.duration) as 'arrival_time', al.*,
            ac.*, a.*, u.*
            FROM flights f
            INNER JOIN airlines al on f.airline_id = al.airline_id
            INNER JOIN aircrafts ac on f.aircraft_id = ac.aircraft_id
            INNER JOIN admins a on f.user_id = a.user_id
            INNER JOIN users u on a.user_id = u.user_id";

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
        die("Error: unable to retrieve flights!");
    }

    return null;
}

function retrieveFlight($flightID) {
    $sql = "SELECT fl.*, ao.airport_name as 'origin_airport_name', ao.airport_country as 'origin_airport_country', ao.airport_state as 'origin_airport_state',
       ad.airport_name as 'destination_airport_name', ad.airport_country as 'destination_airport_country', ad.airport_state as 'destination_airport_state',
       ADDTIME(fl.departure_time, fl.duration) as 'arrival_time', ac.*, al.*, a.*, u.*
    FROM flights fl
    INNER JOIN airports ao ON fl.origin_airport_code = ao.airport_code
    INNER JOIN airports ad ON fl.destination_airport_code = ad.airport_code
    INNER JOIN aircrafts ac ON fl.aircraft_id = ac.aircraft_id 
    INNER JOIN airlines al on fl.airline_id = al.airline_id
    INNER JOIN admins a on fl.user_id = a.user_id
    INNER JOIN users u on a.user_id = u.user_id
    WHERE fl.flight_id = ?";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$flightID]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve flight!");
    }

    return null;
}

function deleteFlight($flightID) {
    $sql = "DELETE FROM flights WHERE flight_id = ?";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$flightID]);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to delete flight!");
    }

    return false;
}

function createFlight($userID, $originCode, $destinationCode, $departureTime,
                    $duration, $price, $aircraftID, $airlineID, $discount) {
    $sql = "INSERT INTO flights(user_id, origin_airport_code, destination_airport_code, 
                    departure_time, duration, flight_base_price, aircraft_id, airline_id, flight_discount) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$userID, $originCode, $destinationCode, $departureTime,
            $duration, $price, $aircraftID, $airlineID, $discount]);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to create flight!");
    }

    return false;
}

function updateFlight($flightID, $discount) {
    $sql = "UPDATE flights SET flight_discount = ? WHERE flight_id = ?";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$discount, $flightID]);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to update flight!");
    }

    return false;
}

function retrieveCountFlights() {
    $sql = "SELECT COUNT(flight_id) as 'count' FROM flights";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve count flights!");
    }

    return null;
}

function retrieveCountAircrafts() {
    $sql = "SELECT COUNT(aircraft_id) AS 'count' FROM aircrafts";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve count aircraft!");
    }

    return null;
}

function deleteAircraft($aircraftID) {
    $sql = "DELETE FROM aircrafts WHERE aircraft_id = ?";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$aircraftID]);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to delete aircraft!");
    }

    return false;
}

function createAircraft($name, $economy, $premium_economy, $business, $first_class){
    $sql = "INSERT INTO aircrafts(aircraft_name, economy_capacity, premium_economy_capacity, business_capacity, first_class_capacity)
            VALUES (?, ?, ?, ?, ?)";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$name, $economy, $premium_economy, $business, $first_class]);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to create aircraft!");
    }

    return false;
}

function updateAircraft($aircraftID, $name, $economy, $premium_economy, $business, $first_class){
    $sql = "UPDATE aircrafts SET aircraft_name = ?, economy_capacity = ?, premium_economy_capacity = ?, 
                     business_capacity = ?, first_class_capacity = ? WHERE aircraft_id = ?";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$name, $economy, $premium_economy, $business, $first_class, $aircraftID]);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to update aircraft!");
    }

    return false;
}

function retrieveAllFlightLike($query) {
    $query = "%{$query}%";
    $sql = "SELECT fl.*, ADDTIME(fl.departure_time, fl.duration) as 'arrival_time', ac.*, al.* FROM flights fl
            INNER JOIN aircrafts ac on fl.aircraft_id = ac.aircraft_id
            INNER JOIN airlines al on fl.airline_id = al.airline_id
            WHERE fl.origin_airport_code LIKE ? OR fl.destination_airport_code LIKE ?";

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
        die("Error: cannot flights like query!");
    }

    return null;
}

