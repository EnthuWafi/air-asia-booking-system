<?php

require("../includes/functions.inc.php");

session_start();

displayToast();
setSessionTraffic();

$airports = retrieveAirports();

$userType = $_SESSION["user_data"]["user_type"] ?? "";

?>

<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content() ?>
    <link href="/assets/css/index.css" rel="stylesheet">
    <style>
        .mySlides {
            display: none;
        }
        /* Slideshow container */
        .slideshow-container {
            width: 100%;
            height: 100%;
            max-height: 100vh;
            margin: auto;
            margin-bottom: 5%;
            margin-left: 4%;
        }
        /* The dots/bullets/indicators */
        .dot {
            height: 15px;
            width: 15px;
            margin: 0 3px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            transition: background-color 0.8s ease;
        }
        /* Fading animation */
        .fade {
            -webkit-animation-name: fade;
            -webkit-animation-duration: 1.5s;
            animation-name: fade;
            animation-duration: 1.5s;
        }
        @-webkit-keyframes fade {
            from {
                opacity: .4
            }
            to {
                opacity: 1
            }
        }
        @keyframes fade {
            from {
                opacity: .4
            }
            to {
                opacity: 1
            }
        }

        /* On smaller screens, decrease text size */
        @media only screen and (max-width: 300px) {
            .prev, .next, .text {
                font-size: 11px
            }
        }
        /* Next & previous buttons */
        .prev, .next {
            cursor: pointer;
            position: relative;
            top: 75%;
            width: auto;
            padding: 16px;
            margin-top: -22px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            transition: 0.9s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
        }
        /* Position the "next button" to the right */
        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }
        /* On hover, add a black background color with a little bit see-through */
        .prev:hover, .next:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }
        .image {
            position: relative;
            width: 300px;
            border-radius: 3%;
        }
        .image__img {
            display: table;
            width: 100%;
            border-radius: 3%;
        }
        .image__overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            color: #ffffff;
            font-family: 'Quicksand', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.5s;
            border-radius: 5%;
        }
        .image__overlay--blur {
            backdrop-filter: blur(1px);
        }
        }
        .image__overlay>* {
            transform: translateY(20px);
            transition: transform 0.5s;
        }
        .image__overlay:hover {
            opacity: 1;
        }
        .image__overlay:hover>* {
            transform: translateY(0);
        }
        .image__title {
            font-size: 2em;
            font-weight: bold;
        }
        .image__description {
            font-size: 1.25em;
            margin-top: 0.25em;
        }
    </style>

    <title><?= config("name") ?> | Flights, and More</title>
</head>

<body>
<!-- Navigation -->
<?php nav_bar() ;?>

<!-- Content -->
<?php if ($userType === "admin") { ?>
    <section class="position-relative"  style="height: 320px;">
        <div class="gradient-primary w-100 position-absolute top-0 start-0 end-0 bottom-0" style="z-index: -1; "></div>
        <div class="row container ms-3">
            <div class="mt-5">
                <h1 class="text-white fw-bold">Take Control of the Skies</h1>
                <h5 class="text-white"">Seamlessly Manage Flights for a Smooth Travel Experience!</h5>
            </div>
        </div>
    </section>
<?php
}
else { ?>
    <section class="position-relative">
        <div class="gradient-primary w-100 position-absolute top-0 start-0 end-0 bottom-0" style="height: 320px; z-index: -1; "></div>
        <div class="row container ms-3">
            <div class="mt-5">
                <h1 class="text-white fw-bold">Start Travelling with AirAsia!</h1>
                <h5 class="text-white"">Get flights worldwide for your trip with the best deals</h5>
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-9">
                <div class="bg-white rounded-4 shadow ms-3 p-5">
                    <form action="/flight/search.php" method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="trip-type">Trip-type</label>
                                <select id="trip-type" name="trip_type" class="form-select">
                                    <option value="ONE-WAY">One-way Trip</option>
                                    <option value="RETURN">Return-trip</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="travel-class">Travel Class</label>
                                <select id="travel-class" name="travel_class" class="form-select">
                                    <option value="BUS">Business</option>
                                    <option value="PRE">Premium Economy</option>
                                    <option value="ECO">Economy</option>
                                    <option value="FST">First Class</option>
                                </select>
                            </div>
                            <div class="col-sm-6 mt-4">
                                <div class="dropdown dropend">
                                    <button class="btn btn-danger dropdown-toggle" type="button" id="passenger-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Guests
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="passenger-toggle">
                                        <div class="row mx-3 mt-2">
                                            <h4><strong class="icon-red">Guests Count</strong></h4>
                                            <hr>
                                        </div>

                                        <div class="row mx-3 mb-2">
                                            <div class="col-md-3">
                                                <label for="adult">Adult:</label>
                                                <input type="number" id="adult" name="adult" min="0" max="9" value="1" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="child">Child:</label>
                                                <input type="number" id="child" name="child" min="0" max="9" value="0" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="infant">Infant:</label>
                                                <input type="number" id="infant" name="infant" min="0" max="9" value="0" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="senior">Senior:</label>
                                                <input type="number" id="senior" name="senior" min="0" max="9" value="0" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="origin-select">Origin</label>
                                <select name="origin" id="origin-select" class="form-select">
                                    <option disabled selected>--- Origin ---</option>
                                    <?php
                                    //airports
                                    foreach ($airports as $airport) {
                                        echo "<option value='{$airport["airport_code"]}'>{$airport["airport_state"]} ({$airport["airport_code"]})
                    </option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="destination-select">Destination</label>
                                <select name="destination" id="destination-select" class="form-select">
                                    <option disabled selected>--- Destination ---</option>
                                    <?php
                                    //airports
                                    foreach ($airports as $airport) {
                                        echo "<option value='{$airport["airport_code"]}'>{$airport["airport_state"]} ({$airport["airport_code"]})
                    </option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <label for="departure">Departure Date:</label>
                                    <input type="date" id="departure" name="departure" min="" value="" class="form-control">
                                </div>
                                <div class="row" id="return">
                                    <label for="return">Return Date:</label>
                                    <input type="date" name="return" min="" value="" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <input type="submit" class="btn btn-danger float-end" value="Search">
                        </div>
                    </form>
                </div>


            </div>
        </div>
    </section>
<?php } ?>

<section class="container my-5">
    <div class="slideshow-container">
        <div class="mySlides fade"> <img src="/assets/img/promo1.jpg" style="width: 90%;height: 90%"> </div>
        <div class="mySlides fade"> <img src="/assets/img/promo2.jpg" style="width: 90%;height: 90%"> </div>
        <div class="mySlides fade"> <img src="/assets/img/promo3.jpg" style="width: 90%;height: 90%"> </div>
        <div style="text-align:center; margin:-5vh;"> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> </div>
        <a class="prev text-decoration-none" onclick="prevSlide()">&#10094;</a>
        <a class="next text-decoration-none" onclick="showSlides()">&#10095;</a> </div>
</section>
<section>
    <div class="container">
        <div class="my-5">
            <span class="fs-3 fw-bold">Recommended Flights</span>
        </div>
        <div class="row gx-0 gy-4 ms-4">
            <div class="col-4">
                <div class="img-fluid image">
                    <img class="image__img" src="/assets/img/kualaLumpur.jpg" alt="Kuala Lumpur">
                    <div class="image__overlay image__overlay--blur">
                        <div class="image__title">KUALA LUMPUR</div>
                        <p class="image__description"> Start from RM 99 </p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="image"> <img class="image__img" src="/assets/img/langkawi.jpg" alt="Langkawi">
                    <div class="image__overlay image__overlay--blur">
                        <div class="image__title">LANGKAWI</div>
                        <p class="image__description"> Start from RM 79 </p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="image"> <img class="image__img" src="/assets/img/georgeTown.jpg" alt="George Town">
                    <div class="image__overlay image__overlay--blur">
                        <div class="image__title">GEORGE TOWN</div>
                        <p class="image__description"> Start from RM 79 </p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="image"> <img class="image__img" src="/assets/img/kotaKinabalu.jpg" alt="Kota Kinabalu">
                    <div class="image__overlay image__overlay--blur">
                        <div class="image__title">KOTA KINABALU</div>
                        <p class="image__description"> Start from RM 149 </p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="image"> <img class="image__img" src="/assets/img/kuching.jpg" alt="Kuching">
                    <div class="image__overlay image__overlay--blur">
                        <div class="image__title">KUCHING</div>
                        <p class="image__description"> Start from RM 149 </p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="image"> <img class="image__img" src="/assets/img/portDickson.jpg" alt="Port Dickson">
                    <div class="image__overlay image__overlay--blur">
                        <div class="image__title">PORT DICKSON</div>
                        <p class="image__description"> Start from RM 99 </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
<section class="bg-light py-5 my-5">
    <div class="container text-center">
        <div class="row mb-5">
            <span class="fs-3 fw-bold ">Why you should travel with AirAsia</span>
        </div>
        <div class="row align-items-stretch">
            <div class="col-4">
                <div class="row">
                    <div class="row justify-content-center">
                        <img src="/assets/img/simplifyBooking.svg" class="img-fluid" style="height: 140px; width: auto;">
                    </div>

                    <div class="row mt-3">
                        <h4>Simplify Your Booking Experience</h4>
                    </div>
                </div>

                <div class="row">
                    <p>Feel the flexibility and simplicity throughout your booking process</p>
                </div>

            </div>
            <div class="col">
                <div class="row">
                    <div class="row justify-content-center">
                        <img src="/assets/img/travelProducts.svg" class="img-fluid" style="height: 140px; width: auto;">
                    </div>

                    <div class="row mt-3">
                        <h4>Wide Selections of Travel Product</h4>
                    </div>
                </div>

                <div class="row">
                    <p>Enjoy your memorable moments with millions of favorable flights and accommodations</p>
                </div>
            </div>
            <div class="col">

                <div class="row">
                    <div class="row justify-content-center">
                        <img src="/assets/img/customerSupport.svg" class="img-fluid" style="height: 140px; width: auto;">
                    </div>
                    <div class="row mt-3">
                        <h4>Affectionate Customer Support</h4>
                    </div>
                </div>

                <div class="row" >
                    <p>Giving best assistance, our customer support is available 24/7 with your local language</p>
                </div>
            </div>
        </div>
    </div>
</section>
<div  id="features"></div>
<?php footer() ?>
<?php
if ($userType !== "admin") {
    showWhatsappWidget();
}
?>
<?php body_script_tag_content(); ?>
<script type="text/javascript" src="/assets/js/search.js"></script>
<script>
    var timeOut = 3000;
    var slideIndex = 0;
    var autoOn = true;

    autoSlides();
    showSlides();

    function autoSlides() {
        timeOut = timeOut - 100;

        if (autoOn == true && timeOut < 0) {
            showSlides();
        }
        setTimeout(autoSlides, 43);
    }

    function prevSlide() {

        timeOut = 3000;

        var slides = document.getElementsByClassName("mySlides");
        var dots = document.getElementsByClassName("dot");

        for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
            dots[i].className = dots[i].className.replace(" active", "");
        }
        slideIndex--;

        if (slideIndex > slides.length) {
            slideIndex = 1
        }
        if (slideIndex == 0) {
            slideIndex = 3
        }
        slides[slideIndex - 1].style.display = "block";
        dots[slideIndex - 1].className += " active";
    }

    function showSlides() {

        timeOut = 3000;

        var slides = document.getElementsByClassName("mySlides");
        var dots = document.getElementsByClassName("dot");

        for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
            dots[i].className = dots[i].className.replace(" active", "");
        }
        slideIndex++;

        if (slideIndex > slides.length) {
            slideIndex = 1
        }
        slides[slideIndex - 1].style.display = "block";
        dots[slideIndex - 1].className += " active";
    }
</script>
</body>

</html>