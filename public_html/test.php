<?php

require("../includes/functions.inc.php");

$travelClass = "ECO";
//testing the result
$passengers = [
    "adult" => [
        0 => [
            "departure_baggage" => "XSM",
        ]
    ],
    "child" => [
        0 => [
            "departure_baggage" => "XSM",
        ],
        1 => [
            "departure_baggage" => "XSM",
        ]
    ]
];

$base = 10;

echo "<br>";
echo calculateFlightPrice($base, $passengers, $travelClass, "departure");
echo "<br>";
echo calculateFlightPriceAlternate($base, ["adult"=>1, "child"=>2], $travelClass, ["XSM"=>3]);
