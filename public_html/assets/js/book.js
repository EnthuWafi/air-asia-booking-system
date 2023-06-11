//         const departureFlight = <?= json_encode($departureFlight) ?>;
//         const departureFlightAddon = <?= json_encode($departureFlightAddons) ?>;
//         const returnFlight = <?= json_encode($returnFlight) ?>;
//         const returnFlightAddon = <?= json_encode($returnFlightAddons) ?>;
//         const flightInfo = <?= json_encode($flightInfo) ?>;
//
//         const ageCategories = <?= json_encode($ageCategories) ?>;
//         const baggagePrices = <?= json_encode($baggagePrices) ?>;
//         const travelClasses = <?= json_encode($travelClasses) ?>;
// const departureBaggage = document.getElementById("depart-baggage-option");
// const returnBaggage = document.getElementById("return-baggage-option");
//
// let baggageSelect = document.createElement("select");
// for (const baggage of baggagePrices){
//     let option = document.createElement("option");
//     option.textContent = baggage["baggage_name"];
//     option.value = baggage["baggage_price_code"];
//
//     baggageSelect.appendChild(option);
// }
// let departureBaggageSelect = baggageSelect.cloneNode(true);
// departureBaggageSelect.name = "departure_baggage";
//
// departureBaggage.appendChild(document.createTextNode("Departure Baggage: "));
// departureBaggage.appendChild(departureBaggageSelect);
//
// let returnBaggageSelect = baggageSelect.cloneNode(true);
// returnBaggageSelect.name = "return_baggage";
//
// returnBaggage.appendChild(document.createTextNode("Return Baggage: "));
// returnBaggage.appendChild(returnBaggageSelect);
//
// //seats
// const departureSeat = document.getElementById("depart-flight-seat");
// const returnSeat = document.getElementById("return-flight-seat");
//
// let travelClass = flightInfo["travel_class"].toUpperCase();
// let flightTravelCapacity =
//     travelClass === "FST" ? "first_class_capacity" :
//         travelClass === "BST" ? "business_capacity" :
//             travelClass === "PRE" ? "premium_capacity" :
//                 travelClass === "ECO" ? "economy_capacity" : throw new Error("Something went badly wrong!");
//
// function seatRadio(div, flight, flightAddon, flightTravelCapacity, name) {
//     div.appendChild(document.createTextNode("Seat: " + flightTravelCapacity));
//     for (let i = 0; i < flight[flightTravelCapacity]; i++) {
//         let radio = document.createElement("input");
//         let label = document.createElement("label");
//         radio.type = "radio";
//         radio.name = `${name}`;
//         radio.value = (i + 1).toString()
//         for (const addon of flightAddon) {
//             if ((radio.value).includes(addon["seat_number"])){
//                 radio.disabled = true;
//                 break;
//             }
//         }
//         div.appendChild(radio);
//     }
// }
//
// seatRadio(departureSeat, departureFlight, departureFlightAddon, flightTravelCapacity, "departure_seat");
// seatRadio(returnSeat, returnFlight, returnFlightAddon, flightTravelCapacity, "return_seat");
//
// //guests
// const guestDetailsDiv = document.getElementById("guest-details");
// //numbers
// let adultCount = parseInt(flightInfo["adult"]);
// let childCount = parseInt(flightInfo["child"]);
// let infantCount = parseInt(flightInfo["infant"]);
// let seniorCount = parseInt(flightInfo["senior"]);
//
// function guestDetailsLoop(ageCategory, div, count){
//     div.appendChild(document.createTextNode("Adult"));
//     for (let i = 0; i < count; i++){
//         let firstName = document.createElement("input");
//         firstName.type = "text";
//         firstName.name = `passengers[${ageCategory}][${i}][first_name]`;
//         firstName.placeholder = `First Name`;
//
//         let lastName = document.createElement("input");
//         lastName.type = "text";
//         lastName.name = `passengers[${ageCategory}][${i}][last_name]`;
//         lastName.placeholder = `Last Name`;
//
//         let dob = document.createElement("input");
//         dob.type = "date";
//         dob.name = `passengers[${ageCategory}][${i}][dob]`;
//
//         let maleRadio = document.createElement("input");
//         maleRadio.type = "radio";
//         maleRadio.name = `passengers[${ageCategory}][${i}][gender]`;
//         maleRadio.value = 'male';
//
//         let femaleRadio = document.createElement("input");
//         femaleRadio.type = "radio";
//         femaleRadio.name = `passengers[${ageCategory}][${i}][gender]`;
//         femaleRadio.value = 'female';
//
//         let hiddenSpecial = document.createElement("input");
//         hiddenSpecial.type = "hidden";
//         hiddenSpecial.name = `passengers[${ageCategory}][${i}][special_assistance]`;
//         hiddenSpecial.value = "0";
//
//         let specialAssistance = document.createElement("input");
//         specialAssistance.type = "checkbox";
//         specialAssistance.name = `passengers[${ageCategory}][${i}][special_assistance]`;
//         specialAssistance.value = "1";
//
//
//         div.appendChild(firstName);
//         div.appendChild(lastName);
//         div.appendChild(dob);
//         div.appendChild(maleRadio);
//         div.appendChild(femaleRadio);
//         div.appendChild(specialAssistance);
//         div.appendChild(document.createElement("br"));
//     }
// }
//
// let adultDiv = document.createElement("div");
// guestDetailsLoop("adult", adultDiv, adultCount);
//
// let childDiv = document.createElement("div");
// guestDetailsLoop("child", childDiv, childCount);
//
// let infantDiv = document.createElement("div");
// guestDetailsLoop("infant", infantDiv, infantCount);
//
// let seniorDiv = document.createElement("div");
// guestDetailsLoop("senior", seniorDiv, seniorCount);
//
