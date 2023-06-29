<?php
require('connection.inc.php');
require('config.inc.php');
require('boilerplate.inc.php');

require("bookings.inc.php");
require("flights.inc.php");
require("users.inc.php");
require("traffic.inc.php");

require("book.page.inc.php");
require("search.page.inc.php");
require("admin.page.inc.php");
//functions
function current_page(): void
{
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
}


// TOASTS
function makeToast($type, $message, $title) {
    $_SESSION["alert"] = ["type"=>$type, "message"=>$message, "title"=>$title];
}
function displayToast() {
    if (isset($_SESSION["alert"])){
        showToastr($_SESSION["alert"]);
        unset($_SESSION["alert"]);
    }
}

//TOASTS
function showToastr($alert): void
{
    echo ("<script>
window.onload = function() {
    toastr.options = {
      \"closeButton\": false,
      \"debug\": false,
      \"newestOnTop\": false,
      \"progressBar\": false,
      \"positionClass\": \"toast-top-right\",
      \"preventDuplicates\": false,
      \"onclick\": null,
      \"showDuration\": \"300\",
      \"hideDuration\": \"1000\",
      \"timeOut\": \"5000\",
      \"extendedTimeOut\": \"1000\",
      \"showEasing\": \"swing\",
      \"hideEasing\": \"linear\",
      \"showMethod\": \"fadeIn\",
      \"hideMethod\": \"fadeOut\"
    }
    toastr[\"{$alert["type"]}\"](\"{$alert["message"]}\", \"{$alert["title"]}\");
}
</script>");
}



function setSessionTraffic() {
    if (!isset($_SESSION["traffic"])) {
        $_SESSION["traffic"] = 1;
        createTraffic();
    }
}

//Requires login to access the site
function customer_login_required(): void
{
    if (empty($_SESSION["user_data"])){
        header("Location: /login.php");
        die();
    }
    $_SESSION["user_data"] = retrieveUser($_SESSION["user_data"]["user_id"]) or logout();

    if (returnUserType($_SESSION["user_data"]["user_id"]) != "customer"){
        header("Location: /index.php");
        die();
    }
}

function logout() {
    makeToast("error", "User doesn't exist or was removed!", "Error");
    header("Location: /logout.php");
    die();
}

//Requires user to not be logged in to access the site (For instance, like Login page or Register page)
function admin_login_required() {
    if (empty($_SESSION["user_data"])){
        header("Location: /login.php");
        die();
    }
    $_SESSION["user_data"] = retrieveUser($_SESSION["user_data"]["user_id"]) or logout();
    if (returnUserType($_SESSION["user_data"]["user_id"]) != "admin"){
        header("Location: /index.php");
        die();
    }
}

//special function to prevent admin from bookings flights & customer from creating flights
function admin_forbidden(): void
{
    if (isset($_SESSION["user_data"])){
        if (returnUserType($_SESSION["user_data"]["user_id"]) === "admin"){
            header("Location: /admin/dashboard.php");
            die();
        }
    }
}
function customer_forbidden(): void
{
    if (isset($_SESSION["user_data"])){
        if (returnUserType($_SESSION["user_data"]["user_id"]) === "customer"){
            header("Location: /account/dashboard.php");
            die();
        }
    }
}





function getToken(){
    $token = sha1(mt_rand());
    $_SESSION['token'] = $token;

    return $token;
}

function isTokenValid($token){
    if(!empty($_SESSION['token'])){
        if ($_SESSION['token'] === $token){
            unset($_SESSION['token']);
            return true;
        }
    }
    return false;
}

//check array keys is set
function array_keys_isset($keys, $array): bool
{
    foreach ($keys as $key) {
        if (!isset($array[$key])) {
            return false;
        }
    }
    return true;
}


function createLog($data): void
{
    $file = $_SERVER['DOCUMENT_ROOT']."/logs/log_".date("j.n.Y").".txt";
    $fh = fopen($file, 'a');
    fwrite($fh,"\n".$data);
    fclose($fh);
}


// SQL commands


//travel class assoc
function travelClassAssoc($travel_class) {
    $travelClassArr = retrieveTravelClass();
    $travel_class = strtoupper($travel_class);
    foreach ($travelClassArr as $entry){
        if ($travel_class == $entry["travel_class_price_code"]){
            return ["code" => $entry["travel_class_price_code"],
                "class"=>strtolower(str_replace(' ', '_', $entry["travel_class_name"])),
                "name"=>$entry["travel_class_name"], "cost_multiplier"=>$entry["cost_multiplier"]];
        }
    }
    return null;
}

//age category
function ageCategoryAssoc($category) {
    $ageCategoryArr = retrieveAgeCategory();
    $category = strtoupper($category);
    foreach ($ageCategoryArr as $entry){
        if ($category == strtoupper($entry["age_category_name"])){
            return ["code" => $entry["age_category_price_code"],
                "name"=>$entry["age_category_name"], "cost_multiplier"=>$entry["cost_multiplier"]];
        }
    }
    return null;
}
function ageCategoryAssocAll() {
    $ageCategoryAssoc = retrieveAgeCategory();
    $arrAssoc = [];
    foreach ($ageCategoryAssoc as $entry){
        $arrAssoc[strtolower($entry["age_category_name"])] =
                ["code" => $entry["age_category_price_code"],
                "name"=>$entry["age_category_name"],
                "cost_multiplier"=>$entry["cost_multiplier"]];
    }
    return $arrAssoc;
}


//baggage
function baggageOptionsAssoc($baggage) {
    $BaggageOptionsArr = retrieveBaggageOptions();
    $baggage = strtoupper($baggage);
    foreach ($BaggageOptionsArr as $entry){
        if ($baggage == $entry["baggage_price_code"]){
            return [
                "code" => $entry["baggage_price_code"],
                "name"=>$entry["baggage_name"],
                "weight"=>$entry["baggage_weight"],
                "cost"=>$entry["cost"]
            ];
        }
    }
    return null;
}

function baggageOptionsAssocAll() {
    $baggageOptionsArr = retrieveBaggageOptions();
    $arrAssoc = [];
    foreach ($baggageOptionsArr as $entry){
        $arrAssoc[$entry["baggage_price_code"]] =
            [
                "code" => $entry["baggage_price_code"],
                "name"=>$entry["baggage_name"],
                "weight"=>$entry["baggage_weight"],
                "cost"=>$entry["cost"]
            ];
    }
    return $arrAssoc;
}


//non-sql command

function calculateFlightPriceNoBaggage($flightBasePrice, $ageCategoryArr, $travelClassCode) {
    $travelClassAssoc = travelClassAssoc($travelClassCode);
    $ageCategoryAll = ageCategoryAssocAll();

    $finalPrice = 0;
    //maybe here well calculate the cost for adult, senior, infant, child first

    foreach ($ageCategoryAll as $ageCategoryKey => $ageCategoryValue) {
        foreach ($ageCategoryArr as $ageCategoryCountKey => $ageCategoryCountValue) {
            //age category match
            if ($ageCategoryCountKey === $ageCategoryKey) {
                //price is base_price * age_multiplier * travel_multiplier + baggage_cost for each passenger
                $finalPrice += ((($flightBasePrice * $ageCategoryValue["cost_multiplier"]) * $ageCategoryCountValue) *
                    $travelClassAssoc["cost_multiplier"]);
                break;
            }
        }
    }

    return $finalPrice;
}
function calculateFlightPrice($flightBasePrice, $passengers, $travelClassCode, $flightType) {
    $travelClassAssoc = travelClassAssoc($travelClassCode);
    $ageCategoryAll = ageCategoryAssocAll();
    $baggageOptionAll = baggageOptionsAssocAll();
    $finalPrice = 0;

    foreach ($ageCategoryAll as $ageCategoryKey => $ageCategoryValue) {
        foreach ($passengers as $passengerAgeCategoryKey => $passengerAgeCategoryValue) {
            if ($ageCategoryKey === $passengerAgeCategoryKey) {
                foreach ($passengerAgeCategoryValue as $passenger) {
                    $baggage = $baggageOptionAll[$passenger["{$flightType}_baggage"]];
                    $finalPrice += (($flightBasePrice * $ageCategoryValue["cost_multiplier"]) *
                            $travelClassAssoc["cost_multiplier"]) + $baggage["cost"];
                }
            }
        }
    }
    return $finalPrice;
}
//calculate price flights
function calculateSearchFlightPrice($flightBasePrice, $ageCategoryArr, $travelClassCode, $baggageOptionCode) {
    $travelClassAssoc = travelClassAssoc($travelClassCode);
    $ageCategoryAll = ageCategoryAssocAll();
    $baggageOption = baggageOptionsAssoc($baggageOptionCode);

    $finalPrice = 0;
    //maybe here well calculate the cost for adult, senior, infant, child first

    foreach ($ageCategoryAll as $ageCategoryKey => $ageCategoryValue) {
        foreach ($ageCategoryArr as $ageCategoryCountKey => $ageCategoryCountValue) {
            //age category match
            if ($ageCategoryCountKey === $ageCategoryKey) {
                if ($ageCategoryCountValue > 0 || $ageCategoryCountValue == null){
                    $finalPrice += $baggageOption["cost"];
                }
                //price is base_price * age_multiplier * travel_multiplier + baggage_cost for each passenger
                $finalPrice += ((($flightBasePrice * $ageCategoryValue["cost_multiplier"]) * $ageCategoryCountValue) *
                    $travelClassAssoc["cost_multiplier"]);
                break;
            }
        }
    }

    return $finalPrice;
}

function makeChart($dataPoints, $name, $max) {
    $encode = json_encode($dataPoints);
    echo "<script>
window.onload = function () {
 
var chart = new CanvasJS.Chart(\"{$name}\", {
	animationEnabled: true,
	theme: \"light2\",
	title:{
		text: \"Site Traffic\"
	},
	axisX: {
		valueFormatString: \"DD MMM\"
	},
	axisY: {
		title: \"Total Number of Visits\",
		includeZero: true,
		maximum: {$max}
	},
	data: [{
		type: \"splineArea\",
		color: \"#6599FF\",
		xValueType: \"dateTime\",
		xValueFormatString: \"DD MMM\",
		yValueFormatString: \"#,##0 Visits\",
		dataPoints: {$encode}
	}]
});
 
chart.render();
 
}
</script>
<div id=\"$name\" class='w-100' style='height: 380px'></div>
";
}


// Function to format the date and time
function formatDateTime($dateTime)
{
    $dateObj = new DateTime($dateTime);
    return $dateObj->format('d M Y h:iA');
}

// Function to format the duration
function formatDuration($duration)
{
    $time = date_create($duration);
    $durationHours = date_format($time, "G")."h ".date_format($time, "i")."m";

    return $durationHours;
}

// Function to format the discount
function formatDiscount($discount)
{
    return number_format($discount * 100) . '%';
}
