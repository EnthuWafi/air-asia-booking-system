<?php
function head_tag_content(): void
{
    //bootstrap,boxicons, jquery, toastr
    echo "
    <meta charset='UTF-8'>
    <meta content='width=device-width, initial-scale=1, maximum-scale=5,minimum-scale=1, viewport-fit=cover' name='viewport'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <script src='https://unpkg.com/@popperjs/core@2'></script>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ' crossorigin='anonymous'>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe' crossorigin='anonymous'></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css' integrity='sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==' crossorigin='anonymous' referrerpolicy='no-referrer' />
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css'>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='/assets/css/main.css'>
     ";
}

function body_script_tag_content() {
    echo "
    <script src='/assets/js/main.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js' integrity='sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==' crossorigin='anonymous' referrerpolicy='no-referrer'></script>
    <script src='https://code.jquery.com/jquery-3.7.0.min.js' integrity='sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=' crossorigin='anonymous'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js'></script>
    <script src='https://cdn.canvasjs.com/ga/canvasjs.min.js'></script>
    ";
}

function nav_bar(){
    $user = $_SESSION['user_data']['username'] ?? "";

    $navMenu = "";
    $loginMenu = "";
    if (isset($_SESSION["user_data"])) {
        $loginMenu = "<div id='right-most-login' class='navbar-nav ms-auto order-last'>
                    <span class='pt-2 me-3'>Hello there, <strong>{$user}</strong></span>
                    <a class='nav-link me-auto' href='/logout.php'><i class='bi bi-box-arrow-left h5'></i> Log out</a>
                </div>";
    }
    else {
        $loginMenu = "<div id='right-most-no-login' class='navbar-nav ms-auto'>
                    <a class='nav-link me-auto' href='/login.php'><i class='bi bi-box-arrow-in-left'></i> Log in</a>
                    <a class='nav-link' href='/register.php'>Register</a>
                </div>";
    }

    if (empty($_SESSION["user_data"])) {
        $navMenu = "<a class='nav-link' href='/'>Home</a>
                <a class='nav-link' href='/contact-us.php'>Contact</a>
                <a class='nav-link' href='/about-us.php'>About</a>";
    }
    else if ($_SESSION["user_data"]["user_type"] == "customer") {
        $navMenu = "<a class='nav-link' href='/'>Home</a>
                <a class='nav-link' href='/account/dashboard.php'>Dashboard</a>
                <a class='nav-link' href='/flight/search.php'>Search Flight</a>
                <a class='nav-link' href='/account/manage-my-bookings.php'>My Bookings</a>";
    }
    else if ($_SESSION["user_data"]["user_type"] == "admin") {
        $navMenu = "<a class='nav-link' href='/'>Home</a>
                <a class='nav-link' href='/admin/dashboard.php'>Admin Dashboard</a>
                <a class='nav-link' href='/admin/manage-flights.php'>Flights</a>
                <a class='nav-link' href='/admin/manage-bookings.php'>Bookings</a>
                <a class='nav-link' href='/admin/manage-aircrafts.php'>Aircrafts</a>
                <a class='nav-link' href='/admin/manage-users.php'>Users</a>";
    }


    echo "<nav class='navbar navbar-expand-lg shadow p-3 bg-white rounded sticky-top'>
        <div class='container-fluid'>
            <div class='d-flex'>
                <a class='navbar-brand order-first' href='index.php'>
                    <img class='img-fluid mb-2' src='/assets/img/airasiacom_logo.svg' style='width: 35%;'>
                </a>
            </div>
            <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarNavAltMarkup' aria-controls='navbarNavAltMarkup' aria-expanded='false' aria-label='Toggle navigation'>
                <span class='navbar-toggler-icon'></span>
            </button>
            <div class='collapse navbar-collapse fw-semibold' id='navbarNavAltMarkup'>
                <div class='navbar-nav me-auto'>
                    {$navMenu}
                </div>
                {$loginMenu}
            </div>
        </div>
    </nav>";

}


//This header is specifically for side_bar(), so it is  only to be used in conjunction with side_bar
function header_bar($pageName){
    echo "<div class='navbar navbar-expand-lg shadow navbar-white bg-white'>
        <div class='container-fluid w-100'>
            <div class='d-flex'>
                <span class='navbar-brand order-first mb-4'>
                    <a data-bs-target='#sidebar' data-bs-toggle='collapse' class='border rounded-3 p-3 text-decoration-none'><i class='bi bi-list bi-lg py-2 p-1 text-black'></i></a>
                    <span class='fs-2 ms-3'>{$pageName}</span>
                </span>
            </div>
            <div class='navbar-nav ms-auto order-las'>
                <form class='d-flex' role='search' action='/account/search.php'>
                    <input class='form-control me-2' type='search' name='q' placeholder='Search' aria-label='Search'>
                    <button class='btn btn-outline-success' type='submit'>Search</button>
                </form>
            </div>
        </div>
    </div>";
}

function side_bar() {
    $iconSize = "h4";
    echo "<div id='sidebar' class='collapse collapse-horizontal show border-end sticky-top'>
    <div class='d-flex flex-column flex-shrink-0 p-3 bg-light vh-100 sidebar mx-w-100'>
        <div class='row gx-3'>
            <div class='col-5'>
                <a href='/' class='d-flex align-items-center me-md-auto link-dark text-decoration-none'>
                    <img class='me-2' width='150' height='73' src='/assets/img/airasiacom_logo.svg'>
                </a>
            </div>
            <div class='col d-sm-none'>
                <button data-bs-target='#sidebar' data-bs-toggle='collapse' type='button' class='btn-close'></button>
            </div>
        </div>
        
        <hr>
        <ul class='nav nav-pills flex-column mb-auto'>
            <li class='nav-item'>
                <a href='/' class='nav-link link-dark'>
                    <i class='bi bi-house-door me-2 $iconSize'></i>
                    Home
                </a>
            </li>
            <li>
                <a href='/account/dashboard.php' class='nav-link link-dark'>
                    <i class='bi bi-speedometer2 me-2 $iconSize'></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href='/account/manage-my-bookings.php' class='nav-link link-dark'>
                    <i class='bi bi-calendar-week me-2 $iconSize'></i>
                    My Bookings
                </a>
            </li>
            <li>
                <a href='/flight/search.php' class='nav-link link-dark d-flex align-items-center'>
                    <i class='bx bxs-plane-alt me-2 pt-1 $iconSize'></i>
                    Flight Search
                </a>
            </li>
            <li>
                <a href='/account/profile.php' class='nav-link link-dark'>
                    <i class='bi bi-people me-2 $iconSize'></i>
                    Profile
                </a>
            </li>
        </ul>
        <hr>
        <div class='dropdown'>
            <a href='#' class='d-flex align-items-center link-dark text-decoration-none dropdown-toggle' id='dropdownUser2' data-bs-toggle='dropdown' aria-expanded='false'>
                <img src='/assets/img/default-profile.svg' alt='' width='32' height='32' class='rounded-circle me-2'>
                <strong>{$_SESSION["user_data"]["username"]}</strong>
            </a>
            <ul class='dropdown-menu text-small shadow' aria-labelledby='dropdownUser2'>
                <li><a class='dropdown-item' href='/account/profile.php'>Profile</a></li>
                <li><hr class='dropdown-divider'></li>
                <li><a class='dropdown-item' href='/logout.php'>Log out</a></li>
            </ul>
        </div>
    </div>
</div>";
}

//This header is specifically for admin (same thing just for admins)
function admin_header_bar($pageName){
    echo "<div class='navbar navbar-expand-lg shadow navbar-white bg-white sticky-top'>
        <div class='container-fluid w-100'>
            <div class='d-flex align-middle'>
                <span class='navbar-brand order-first mb-4'>
                    <a data-bs-target='#sidebar' data-bs-toggle='collapse' class='border rounded-3 p-3 text-decoration-none'><i class='bi bi-list bi-lg py-2 p-1 text-black'></i></a>
                    <span class='fs-2 ms-3 pt-2'>{$pageName}</span>
                </span>
            </div>
            <div class='navbar-nav ms-auto order-las'>
                <form class='d-flex' role='search' action='/admin/search.php'>
                    <input class='form-control me-2' type='search' name='q' placeholder='Search' aria-label='Search'>
                    <button class='btn btn-outline-success' type='submit'>Search</button>
                </form>
            </div>
        </div>
    </div>";
}

function admin_side_bar() {
    $iconSize = "h4";
    echo "
<div id='sidebar' class='collapse collapse-horizontal show border-end sticky-top'>
    <div class='d-flex flex-column flex-shrink-0 p-3 bg-light vh-100 sidebar mx-w-100'>
        <div class='row gx-3'>
            <div class='col-5'>
                <a href='/' class='d-flex align-items-center me-md-auto link-dark text-decoration-none'>
                    <img class='me-2' width='150' height='73' src='/assets/img/airasiacom_logo.svg'>
                </a>
            </div>
            <div class='col d-sm-none'>
                <button data-bs-target='#sidebar' data-bs-toggle='collapse' type='button' class='btn-close'></button>
            </div>
        </div>
        
        <hr>
        <ul class='nav nav-pills flex-column mb-auto'>
            <li class='nav-item'>
                <a href='/' class='nav-link link-dark'>
                    <i class='bi bi-house-door me-2 $iconSize'></i>
                    Home
                </a>
            </li>
            <li>
                <a href='/admin/dashboard.php' class='nav-link link-dark'>
                    <i class='bi bi-speedometer2 me-2 $iconSize'></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href='/admin/manage-flights.php' class='nav-link link-dark d-flex align-items-center'>
                    <i class='bx bxs-plane-take-off me-2 pt-1 $iconSize'></i>
                    Flight
                </a>
            </li>
            <li>
                <a href='/admin/manage-bookings.php' class='nav-link link-dark'>
                    <i class='bi bi-calendar me-2 $iconSize'></i>
                    Bookings
                </a>
            </li>
            <li>
                <a href='/admin/manage-aircrafts.php' class='nav-link link-dark'>
                    <i class='bi bi-airplane-engines me-2 $iconSize'></i>
                    Aircrafts
                </a>
            </li>
            <li>
                <a href='/admin/manage-users.php' class='nav-link link-dark'>
                    <i class='bi bi-people me-2 $iconSize'></i>
                    Users
                </a>
            </li>
        </ul>
        <hr>
        <div class='dropdown'>
            <a href='#' class='d-flex align-items-center link-dark text-decoration-none dropdown-toggle' id='dropdownUser2' data-bs-toggle='dropdown' aria-expanded='false'>
                <img src='/assets/img/default-profile.svg' alt='' width='32' height='32' class='rounded-circle me-2'>
                <strong>{$_SESSION["user_data"]["username"]}</strong>
            </a>
            <ul class='dropdown-menu text-small shadow' aria-labelledby='dropdownUser2'>
                <li><a class='dropdown-item' href='/logout.php'>Log out</a></li>
            </ul>
        </div>
    </div>
</div>";
}


function footer(){
    $date = date("Y");
    echo "
    <footer class='d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top'>
    <p class='col-md-4 mb-0 ps-3 text-muted'>Copyright &copy; {$date} AirAsia</p>

    <a href='/' class='col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none'>
      <img class='bi me-2' width='80' height='52' src='/assets/img/airasiacom_logo.svg' alt='airasia'>
    </a>

    <ul class='nav col-md-4 justify-content-end'>
      <li class='nav-item'><a href='/' class='nav-link px-2 text-muted'>Home</a></li>
      <li class='nav-item'><a href='/index.php/#features' class='nav-link px-2 text-muted'>Features</a></li>
      <li class='nav-item'><a href='/about-us.php' class='nav-link px-2 text-muted'>About Us</a></li>
      <li class='nav-item'><a href='/contact-us.php' class='nav-link px-2 text-muted'>Contact Us</a></li>
      <li class='nav-item'><a href='/faqs.php' class='nav-link px-2 text-muted'>FAQs</a></li>
    </ul>
  </footer>
    ";
}

