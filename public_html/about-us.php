<?php

require("../includes/functions.inc.php");

session_start();

displayToast();

?>

<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content() ?>
    <style>
        .table-image {
            width: 500px; /* Set the desired width */
            height: 200px; /* Set the desired height */
            object-fit: cover; /* Maintain aspect ratio and cover the area */
            border-radius: 30%; /* Optional: Apply border radius for circular images */
        }
        .img-top {
            object-position: top;
        }
    </style>
    <title><?= config("name") ?> | About Us</title>
</head>

<body>
<!-- Navigation -->
<?php nav_bar() ;?>

<div class="container py-5">

    <div class="row align-items-center">
        <div class="col">
            <h1>About AirAsia</h1>
        </div>
        <div class="col-2">
            <img src="/assets/img/airasia.png" class="img-fluid">
        </div>
    </div>
    <div class="row">
        <p class="text-body-secondary">AirAsia is a leading low-cost airline in Asia, providing affordable and convenient air travel to millions of passengers. With our extensive network of destinations, we strive to make flying accessible to everyone, opening up new possibilities for exploration and adventure.</p>
        <p class="text-body-secondary">At AirAsia, we are committed to delivering exceptional customer experiences and ensuring the highest standards of safety and reliability. Our dedicated crew and modern fleet of aircraft are designed to offer comfort and convenience throughout your journey.</p>
        <p class="text-body-secondary">Whether you're planning a business trip, a family vacation, or a weekend getaway, AirAsia has a wide range of flights and services to suit your needs. We constantly innovate and improve our offerings to provide you with seamless travel experiences at affordable prices.</p>
        <p class="text-body-secondary">Discover the world with AirAsia and let us take you to your dream destinations with our renowned "Now Everyone Can Fly" spirit. Join millions of satisfied passengers and experience the joy of hassle-free air travel with AirAsia!</p>

    </div>

</div>
<div class="bg-light pt-2">
    <div class="container py-5">
        <h1 class="mb-2">Group Members</h1>
        <table class="table table-hover align-middle">
            <thead>
            <tr>
                <th scope="col">PICTURE</th>
                <th scope="col">NAME</th>
                <th scope="col">MATRIC</th>
                <th scope="col">PHONE</th>
                <th scope="col">PROGRAM</th>
                <th scope="col">GROUP</th>
                <th scope="col">EMAIL</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><img src="/assets/img/wafi.jpg" class="img-fluid table-image"></td>
                <td>ABDUL WAFI BIN CHE AB.RAHIM</td>
                <td>2021828502</td>
                <td>010-885 7639</td>
                <td>CS110 - DIPLOMA IN COMPUTER SCIENCE</td>
                <td>A4CS1104A</td>
                <td>2021828502@student.uitm.edu.my</td>
            </tr>

            <tr>
                <td><img src="/assets/img/aizat.jpg" alt="" class="img-fluid table-image" ></td>
                <td>AIZAT NAZRIN BIN ZULKIFLI</td>
                <td>2021896686</td>
                <td>019-721 3983</td>
                <td>CS110 - DIPLOMA IN COMPUTER SCIENCE</td>
                <td>A4CS1104A</td>
                <td>2021896686@student.uitm.edu.my</td>
            </tr>

            <tr>
                <td><img src="/assets/img/izzah.jpg" alt="" class="img-fluid table-image"></td>
                <td>'IZZAH UQAILAH BINTI SHAMSUDDIN</td>
                <td>2021131365</td>
                <td>011-6097 3045</td>
                <td>CS110 - DIPLOMA IN COMPUTER SCIENCE</td>
                <td>A4CS1104A</td>
                <td>2021131365@student.uitm.edu.my</td>
            </tr>

            <tr>
                <td><img src="/assets/img/aqil.jpg" alt="" class="img-fluid table-image" ></td>
                <td>AQIL IMRAN BIN NORHIDZAM</td>
                <td>2021895046</td>
                <td>017-500 5871</td>
                <td>CS110 - DIPLOMA IN COMPUTER SCIENCE</td>
                <td>A4CS1104A</td>
                <td>2021895046@student.uitm.edu.my</td>
            </tr>
            <tr>
                <td><img src="/assets/img/imran.jpg" alt="" class="img-fluid table-image img-top" ></td>
                <td>MOHD IMRAN BIN MOHD ISA</td>
                <td>2020949497</td>
                <td>013-335 6575</td>
                <td>CS110 - DIPLOMA IN COMPUTER SCIENCE</td>
                <td>A4CS1104A</td>
                <td>2020949497@student.uitm.edu.my</td>
            </tr>
            </tbody>


        </table>
    </div>
</div>

<?php footer() ?>
<?php body_script_tag_content(); ?>
</body>

</html>