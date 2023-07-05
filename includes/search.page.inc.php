<?php
require_once("functions.inc.php");
// SEARCH FUNCTIONS
function search_flightDetails($flights, $flightCategory, $ageCategoryArr, $travelClassCode)
{
    echo "<h2>{$flightCategory} Flight to {$flights[0]["destination_airport_state"]}</h2>";
    foreach ($flights as $flight) {
        $categorySmall = strtolower($flightCategory);
        $duration = $flight["duration"];
        $durationHours = formatDuration($duration);

        $departureTime = formatDateTime($flight['departure_time']);
        $arrivalTime = formatDateTime($flight['arrival_time']);

        $countPassenger = $ageCategoryArr["adult"] + $ageCategoryArr["child"] + $ageCategoryArr["infant"] + $ageCategoryArr["senior"];
        $flightPrice = calculateFlightPriceAlternate($flight["flight_base_price"], $ageCategoryArr, $travelClassCode, ["XSM"=>$countPassenger]);
        $discountedPrice = $flightPrice - ($flightPrice * $flight["flight_discount"]);

        $price = number_format((float)$flightPrice, 2, '.', '');
        $truePrice = number_format((float)$discountedPrice, 2, '.', '');
        ?>
        <div class="card mb-3">
            <div class="row g-0 align-items-center">
                <div class="col-2 p-4">
                    <img src="<?php echo $flight['airline_image']; ?>" class="card-img-top" alt="Airline Image">
                </div>
                <div class="col">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-sm-2">
                                <p class="card-text"><?php echo $departureTime; ?></p>
                                <p class="card-text"><?php echo $flight['origin_airport_state']; ?></p>
                            </div>
                            <div class="col-auto align-self-center">
                                <h4><i class="bi bi-arrow-right"></i></h4>
                            </div>
                            <div class="col-sm-2">
                                <p class="card-text"><?php echo $arrivalTime; ?></p>
                                <p class="card-text"><?php echo $flight['destination_airport_state']; ?></p>
                            </div>
                            <div class="col-4 text-center">
                                <p class="card-text"><?php echo $durationHours; ?></p>
                                <p class="card-text"><i class="bi bi-briefcase"></i><span class="text-muted"> 5 kg Baggage</span></p>
                            </div>
                            <div class="col mt-2">
                                <div class="row pb-1">
                                    <?php
                                    if ($flight["flight_discount"] > 0) {
                                        echo "<h5 class='card-text fs-2 text-decoration-line-through'>RM$price</h5>";
                                    }
                                    ?>
                                    <h5 class="card-text fw-bold fs-2">RM <?php echo $truePrice; ?></h5>
                                </div>
                                <div class="row text-end p-3">
                                    <button class="btn btn-danger" name="<?php echo $categorySmall; ?>_flight_id" value="<?php echo $flight['flight_id']; ?>">
                                        Book
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

function flightProceed_flightDetails($flight, $flightInfo) {
    //departure
    if (isset($flight)){
        $departDate = date_create($flight["departure_time"]);
        $departDate_date = date_format($departDate, 'd M Y');
        $departDate_time = date_format($departDate, 'H:i A');

        $arrivalDate = date_create($flight["arrival_time"]);
        $arrivalDate_date = date_format($arrivalDate, 'd M Y');
        $arrivalDate_time = date_format($arrivalDate, 'H:i A');

        echo "<div>
<p>{$flight["origin_airport_state"]} ({$flight["origin_airport_code"]}) <i class='bi bi-arrow-right'></i>
{$flight["destination_airport_state"]} ({$flight["destination_airport_code"]})</p>
<span>{$flightInfo["passenger_count"]} Passengers</span><br>
<div class='row mt-3'>
    <div class='col-md-4'>
        <span>{$departDate_time}</span><br>
        <small>{$departDate_date}</small>
    </div>
    <div class='col-md-4'>
        <span>{$flight["origin_airport_state"]} ({$flight["origin_airport_code"]})</span><br>
        <small>{$flight["origin_airport_name"]}</small>
    </div>
</div>
<div class='row'>
    <div class='col-md-4'>
        <span>{$arrivalDate_time}</span><br>
        <small>{$arrivalDate_date}</small>
    </div>
    <div class='col-md-4'>
        <span>{$flight["destination_airport_state"]} ({$flight["destination_airport_code"]})</span><br>
        <small>{$flight["destination_airport_name"]}</small>
    </div>
</div>


</div>";
    }
}