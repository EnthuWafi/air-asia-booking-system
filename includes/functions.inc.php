<?php
require('connection.inc.php');
require('config.inc.php');

require("bookings.inc.php");
require("flights.inc.php");
require("users.inc.php");

//functions
function current_page(): string
{
    return htmlspecialchars($_SERVER["PHP_SELF"]);
}

//Requires login to access the site
function login_required(): void
{
    if (empty($_SESSION["user_data"])){
        header("Location: /index.php");
        die();
    }
    if (!checkUser($_SESSION["user_data"]["username"], "customers")){
        header("Location: /logout.php");
        die();
    }
}

//Requires user to not be logged in to access the site (For instance, like Login page or Register page)
  function login_forbidden(): void
  {
    if (isset($_SESSION["user_data"])){
        header("Location: /index.php");
        die();
    }
}

function token_csrf(): void
{
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_SPECIAL_CHARS);

    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    }
}

//check array keys is set
function array_keys_isset_or_not($keys, $array): bool
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

//travel class assoc
function travelClassAssoc($travel_class) {
    $travelClassArr = retrieveTravelClass();
    $travel_class = strtoupper($travel_class);
    foreach ($travelClassArr as $entry){
        if ($travel_class == $entry["travel_class_price_code"]){
            return ["code" => $entry["travel_class_price_code"],
                "class"=>strtolower(str_replace(' ', '_', $entry["travel_class_name"])),
                "name"=>$entry["travel_class_name"]];
        }
    }
    return null;
}

//age category
function ageCategoryAssoc($category) {
    $AgeCategoryArr = retrieveAgeCategory();
    $category = strtoupper($category);
    foreach ($AgeCategoryArr as $entry){
        if ($category == strtoupper($entry["age_category_name"])){
            return ["code" => $entry["age_category_price_code"],
                "name"=>$entry["age_category_name"]];
        }
    }
    return null;
}

//baggage
function baggageOptionsAssoc($baggage) {
    $BaggageOptionsArr = retrieveBaggageOptions();
    $baggage = strtoupper($baggage);
    foreach ($BaggageOptionsArr as $entry){
        if ($baggage == $entry["baggage_price_code"]){
            return ["code" => $entry["baggage_price_code"],
                "name"=>$entry["baggage_name"]];
        }
    }
    return null;
}


function showToastr($alert): void
{
    echo ("<script>
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
</script>");
}

function head(): void
{
    //bootstrap,boxicons, jquery, toastr
    echo "
    <meta charset='UTF-8'>
    <meta content='width=device-width, initial-scale=1, maximum-scale=5,minimum-scale=1, viewport-fit=cover' name='viewport'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ' crossorigin='anonymous'>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe' crossorigin='anonymous'></script>
    <link href='/assets/css/bootstrap.css' rel='stylesheet'>
    <script src='https://unpkg.com/boxicons@2.1.4/dist/boxicons.js'></script>
    <script src='https://code.jquery.com/jquery-3.7.0.min.js' integrity='sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=' crossorigin='anonymous'></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css' integrity='sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==' crossorigin='anonymous' referrerpolicy='no-referrer' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js' integrity='sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==' crossorigin='anonymous' referrerpolicy='no-referrer'></script>
    ";
}