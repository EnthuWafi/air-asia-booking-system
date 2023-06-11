/**
 * sends a request to the specified url from a form. this will change the window location.
 * @param {string} path the path to send the post request to
 * @param {object} params the parameters to add to the url
 * @param {string} [method=post] the method to use on the form
 */
var departureFlightID = null;
var returnFlightID = null;
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


function bookFlight(flight) {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);

    const departureFlightResult = document.getElementById("depart-flight-result");
    const returnFlightResult = document.getElementById("return-flight-result");

    if (departureFlightID == null) {
        departureFlightID = flight.value;
        if (urlParams.get("trip_type") === "ONE-WAY") {
            //proceed
            post("/flight/search.php", {
                "adult": urlParams.get("adult"),
                "child": urlParams.get("child"),
                "infant": urlParams.get("infant"),
                "senior": urlParams.get("senior"),
                "travel_class": urlParams.get("travel_class"),
                "trip_type": urlParams.get("trip_type"),
                "departure_flight_id": departureFlightID,
                "return_flight_id": null
            });
        } else {
            departureFlightResult.classList.add("d-none");
            returnFlightResult.classList.remove("d-none")
            return;
        }
    } else {
        if (urlParams.get("trip_type") === "RETURN") {
            returnFlightID = flight.value;
            //proceed
            post("/flight/search.php", {
                "adult": urlParams.get("adult"),
                "child": urlParams.get("child"),
                "infant": urlParams.get("infant"),
                "senior": urlParams.get("senior"),
                "travel_class": urlParams.get("travel_class"),
                "trip_type": urlParams.get("trip_type"),
                "departure_flight_id": departureFlightID,
                "return_flight_id": returnFlightID
            });
        }
    }
}

function updateSelect(changedSelect, selectId) {
    const otherSelect = document.getElementById(selectId);
    for (let i = 0; i < otherSelect.options.length; ++i) {
        otherSelect.options[i].disabled = false;
    }
    if (changedSelect.selectedIndex == 0) {
        return;
    }
    otherSelect.options[changedSelect.selectedIndex].disabled = true;
}

function updatePassenger(changedInput, otherId1, otherId2, otherId3) {
    const otherInput1 = document.getElementById(otherId1);
    const otherInput2 = document.getElementById(otherId2);
    const otherInput3 = document.getElementById(otherId3);
    let sum = parseInt(changedInput.value) + parseInt(otherInput1.value) + parseInt(otherInput2.value)
        + parseInt(otherInput3.value);
    console.log(sum);
    const limit = 6;
    if (sum >= limit) {
        changedInput.value = limit - (parseInt(otherInput1.value) + parseInt(otherInput2.value)
            + parseInt(otherInput3.value));
    }
}


//age category
const adult = document.getElementById("adult");
const child = document.getElementById("child");
const infant = document.getElementById("infant");
const senior = document.getElementById("senior");
adult.addEventListener('input', function () {
    updatePassenger(this, 'child', 'infant', 'senior')
});
child.addEventListener('input', function () {
    updatePassenger(this, 'adult', 'infant', 'senior')
});
infant.addEventListener('input', function () {
    updatePassenger(this, 'adult', 'child', 'senior')
});
senior.addEventListener('input', function () {
    updatePassenger(this, 'adult', 'infant', 'child')
});


//airport
const origins = document.getElementById("origin-select");
const destinations = document.getElementById("destination-select");

origins.onchange = function () {
    updateSelect(this, 'destination-select');
}
destinations.onchange = function () {
    updateSelect(this, 'origin-select');
}
//
// for (const airport of jsonAirports) {
//     var option = document.createElement("option");
//     option.textContent = airport["airport_country"] + " (" + airport["airport_code"] + ")";
//     option.value = airport["airport_code"];
//
//     origins.appendChild(option);
//     destinations.appendChild(option.cloneNode(true));
// }

//date limit
const minDate = new Date().toLocaleDateString('fr-ca');
const departureDate = document.getElementById("departure");
const returnDate = document.getElementById("return");
departureDate.setAttribute("min", minDate);
returnDate.setAttribute("min", minDate);

//trip type
const tripType = document.getElementById("trip-type");
tripType.onchange = function () {
    if (this.value === "RETURN") {
        returnDate.style.display = "block";
    } else {
        returnDate.style.display = "none";
    }
}
returnDate.selectedIndex = 0;
returnDate.style.display = "none";

const departureFlight = document.getElementsByName("departure_flight_id");
const returnFlight = document.getElementsByName("return_flight_id");

for (let i = 0; i < departureFlight.length; i++) {
    let flight = departureFlight.item(i);
    flight.onclick = function () {
        bookFlight(flight);
    }
}
for (let i = 0; i < returnFlight.length; i++) {
    let flight = returnFlight.item(i);
    flight.onclick = function () {
        bookFlight(flight);
    }
}

// //flight
// var departureFlightResult = document.getElementById("depart-flight-result");
// var returnFlightResult = document.getElementById("return-flight-result");
//
// const queryString = window.location.search;
// const urlParams = new URLSearchParams(queryString);
//
// var postParameter = {adult: urlParams.get('adult'), child: urlParams.get('child'),
// infant: urlParams.get('infant'), senior: urlParams.get('senior'), travel_class: urlParams.get('travel_class'),
// trip_type: urlParams.get('trip_type')};
//
// if (jsonDepartureFlights != null){
//     var text = document.createElement("p");
//     text.textContent = "Departure flight";
//     departureFlightResult.appendChild(text);
//     for (const flight of jsonDepartureFlights) {
//         var li = document.createElement("li");
//         var p = document.createElement("p");
//         var button = document.createElement("button");
//         p.textContent = flight["departure_time"] + "   " + flight["origin_airport_name"] +
//             " to " + flight["destination_airport_name"];
//
//         button.textContent = "Select"
//         // button.setAttribute("data-flight-id", flight["flight_id"]);
//         button.onclick = function() {
//             //post("/flight/proceed.php", {departure_flight_id: flight["flight_id"]})
//             postParameter["departure_flight_id"] = flight["flight_id"];
//
//
//             if (postParameter["trip_type"] === "one") {
//                 postParameter["token"] = token;
//                 post("/flight/proceed.php", postParameter);
//             }
//             else {
//                 departureFlightResult.style.display = "none";
//                 returnFlightResult.style.display = "block";
//             }
//         }
//         li.appendChild(p);
//         li.appendChild(button);
//
//         departureFlightResult.appendChild(li);
//     }
// }
// if (jsonReturnFlights != null) {
//     returnFlightResult.appendChild(document.createElement("p").textContent = "Return flight");
//     for (const flight of jsonReturnFlights) {
//         var li = document.createElement("li");
//         var p = document.createElement("p");
//         var button = document.createElement("button");
//         p.textContent = flight["departure_time"] + "   " + flight["origin_airport_name"] +
//             " to " + flight["destination_airport_name"];
//
//         button.textContent = "Select"
//         // button.setAttribute("data-flight-id", flight["flight_id"]);
//         button.onclick = function() {
//             postParameter["return_flight_id"] = flight["flight_id"];
//
//             postParameter["token"] = token;
//             post("/flight/search.php", postParameter);
//         }
//         li.appendChild(p);
//         li.appendChild(button);
//
//         returnFlightResult.appendChild(li);
//     }
// }

