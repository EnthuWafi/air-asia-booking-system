<?php
require_once("functions.inc.php");
// SEARCH FUNCTIONS
function search_flightDetails($flights, $flightCategory, $ageCategoryArr, $travelClass)
{
    echo "<h2>{$flightCategory} Flight to {$flights[0]["destination_airport_state"]}</h2>";
    foreach ($flights as $flight) {
        $categorySmall = strtolower($flightCategory);
        $duration = date_create($flight["duration"]);
        $durationHours = date_format($duration, "G")."h ".date_format($duration, "i")."m";

        $price = calculateSearchFlightPrice($flight["flight_base_price"], $flight["flight_discount"], $ageCategoryArr,  $travelClass, "XSM");
        $price = number_format((float)$price, 2, '.', '');
        echo "
    <div class='shadow p-5 bg-body rounded row'>
        <div class='col-2'>
            <img width='60' src='{$flight["airline_image"]}'>
        </div>
        <div class='col row'>
            <div class='col-sm-2 order-first'>
                <div class='row'>{$flight["departure_time"]}</div>
                <div class='row'>{$flight["origin_airport_name"]}</div>
            </div>
            <div class='col-sm-1 align-middle'><i class='bi bi-arrow-right'></i></div>
            <div class='col-sm-2'>
                <div class='row'>{$flight["arrival_time"]}</div>
                <div class='row'>{$flight["destination_airport_name"]}</div>
            </div>
            <div class='col-4'>
                <div class='row'>{$durationHours}</div>
                <div class='row'><span class='text-muted'>5 kg Baggage</span></div>
            </div>
            <div class='col ms-auto order-last'>
                <div class='col pb-3'>
                    <span class='h5'>RM {$price}</span>
                </div>
                <div class='col'>
                    <button class='btn btn-danger w-100' name='{$categorySmall}_flight_id' value='{$flight["flight_id"]}'>
                        Book
                    </button>
                </div>
                
            </div>
        </div>
    </div>";
    }
}

