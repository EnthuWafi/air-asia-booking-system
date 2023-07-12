<?php

require("../includes/functions.inc.php");

session_start();

displayToast();

?>

<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content() ?>
    <link href="/assets/css/bootstrap-start.css" rel="stylesheet">
    <style>
        <style>

        .home__img {
            position: relative;
        }

        .home__img img {
            position: absolute;
            top: -255px;
            left: 0;
            z-index: 0;
            width: 650px;
            height: auto;
            object-fit: cover;
        }

    </style>
    <title><?= config("name") ?> | Homepage</title>
</head>

<body>
<!-- Navigation -->
<div class="bg-light">
    <?php nav_bar() ;?>
</div>


<!-- Mashead header-->
<header class="masthead">
    <div class="container px-5">
        <div class="row gx-5 align-items-center" id="home_section">
            <div class="col-lg-6">
                <!-- Mashead text and app badges-->
                <div class="mb-5 mb-lg-0 text-center text-lg-start">
                    <h1 class="display-2 lh-1 mb-3" style="font-weight: bold" id="title-main">Fly with AsiaAsia now!</h1>
                    <p class="lead fw-normal text-muted mb-5" id="title-desc">Experience a seamless travel experience with AirAsia. Book flights, hotels, activities, and more!</p>
                    <div class="d-flex flex-column flex-lg-row align-items-center" id="title-bottom">
                        <a class="me-lg-3 mb-4 py-3 btn btn-danger rounded-pill h3 btn-red shadow" style="width: 150px;" href="/index.php">Get Started!</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 position-relative">
                <div class="home__img">
                    <img src="/assets/img/airplane.png" id="plane">
                </div>
            </div>
        </div>

    </div>
</header>

<!-- App badge section-->
<section class="bg-gradient-primary-to-secondary" id="download">
    <div class="container px-5">
        <h2 class="text-center text-white font-alt mb-4">Get the app now!</h2>
        <div class="d-flex flex-column flex-lg-row align-items-center justify-content-center">
            <a class="me-lg-3 mb-4 mb-lg-0" target="_blank" href="https://play.google.com/store/apps/details?id=com.airasia.mobile&hl=en&gl=US&pli=1"><img class="app-badge" src="assets/img/google-play-badge.svg" alt="..." /></a>
            <a target="_blank" href="https://apps.apple.com/us/app/airasia-superapp-travel-deals/id565050268"><img class="app-badge" src="assets/img/app-store-badge.svg" alt="..." /></a>
        </div>
    </div>
</section>

<?php footer() ?>
<?php body_script_tag_content(); ?>
<!-- ===== GSAP ANIMATION ===== -->
<script language="JavaScript">
    gsap.from('#plane', { opacity: 0, duration: 2, delay: .5, x: 60 })
    gsap.from('#title-main, #title-desc, #title-bottom', { opacity: 0, duration: 2, delay: 1, y: 25, ease: 'expo.out', stagger: .2 })
    gsap.from('#navHeader , .navbar-brand, .nav-link', { opacity: 0, duration: 2, delay: 1.5, y: 25, ease: 'expo.out', stagger: .2 })
</script>
</body>

</html>