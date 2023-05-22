<?php

session_start();
require("../includes/functions.inc.php");


login_required();

if (isset($_SESSION['user_id'])) {
    $user = $_SESSION['user_id'];
}

$airports = retrieveAirports();
$flights = null;

if ($_GET) {
    $origin = filter_var($_GET["origin"], FILTER_SANITIZE_SPECIAL_CHARS);
    $destination = filter_var($_GET["destination"], FILTER_SANITIZE_SPECIAL_CHARS);
    $departure = filter_var($_GET["departure"], FILTER_SANITIZE_SPECIAL_CHARS);

    $flights = retrieveFlights($origin, $destination, $departure);
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Flight Search</title>
</head>

<body>
    <div class="d-flex flex-row">
        <a class="navbar-brand" href="/index.php">
            <img class="img-fluid w-50" src="/assets/img/airasiacom_logo.svg">
        </a>
    </div>
    <h1>Flight Search</h1>
    <hr>
    <form action="<?php current_page(); ?>" method="get">
        <select id="trip-type" name="trip-type">
            <option value="round">Round-trip</option>
            <option value="one">One-way Trip</option>
        </select>
        <select id="travel-class" name="travel-class">
            <option value="business">Business</option>
            <option value="economy">Economy</option>
            <option value="first">First Class</option>
        </select>
        <br>
        <select name="origin" id="origin-select" onchange="updateSelect(this,'destination-select');">
            <option>
        </select>

        <select name="destination" id="destination-select" onchange="updateSelect(this,'origin-select');">
            <option>
        </select>

        <input type="date" id="departure" name="departure" min="">
        <input type="date" id="return" name="return" min="">
        <br>
        <input type="submit">
    </form>

    <ul>
        <ol id="flight-result">

        </ol> 
    </ul>
</body>
<script>
    var jsonAirports = <?php echo json_encode($airports) ?>;
    var jsonFlights = <?php echo json_encode($flights) ?>;

    function updateSelect(changedSelect, selectId) {
        var otherSelect = document.getElementById(selectId);
        for (var i = 0; i < otherSelect.options.length; ++i) {
            otherSelect.options[i].disabled = false;
        }
        if (changedSelect.selectedIndex == 0) {
            return;
        }
        otherSelect.options[changedSelect.selectedIndex].disabled = true;
    }

        /**
     * sends a request to the specified url from a form. this will change the window location.
     * @param {string} path the path to send the post request to
     * @param {object} params the parameters to add to the url
     * @param {string} [method=post] the method to use on the form
     */

    function post(path, params, method='post') {

        // The rest of this code assumes you are not using a library.
        // It can be made less verbose if you use one.
        const form = document.createElement('form');
        form.method = method;
        form.action = path;

        for (const key in params) {
        if (params.hasOwnProperty(key)) {
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = key;
            hiddenField.value = params[key];

            form.appendChild(hiddenField);
        }
        }

        document.body.appendChild(form);
        form.submit();
    }


    var origins = document.getElementById("origin-select");
    var destinations = document.getElementById("destination-select");

    for (const airport of jsonAirports) {
        var option = document.createElement("option");
        option.textContent = airport["airport_name"] + " (" + airport["airport_code"] + ")";
        option.value = airport["airport_code"];

        origins.appendChild(option);
        destinations.appendChild(option.cloneNode(true));
    }

    var flightResult = document.getElementById("flight-result");

    if (jsonFlights != null){
        for (const flight of jsonFlights) {
            var li = document.createElement("li");
            var p = document.createElement("p");
            var button = document.createElement("button");
            p.textContent = flight["departure_time"] + "   " + flight["origin_airport_name"] +
                " to " + flight["destination_airport_name"];

            button.textContent = "Select"
            // button.setAttribute("data-flight-id", flight["flight_id"]);
            button.onclick = function() {
                post("/content/flightcheckout.php", {departure_flight_id: flight["flight_id"]})
            }
            li.appendChild(p);
            li.appendChild(button);

            flightResult.appendChild(li);
        }
    }

    var minDate = new Date().toLocaleDateString('fr-ca');

    var departure = document.getElementById("departure");
    var returnDate = document.getElementById("return");
    departure.setAttribute("min", minDate);
    returnDate.setAttribute("min", minDate);

</script>

</html>