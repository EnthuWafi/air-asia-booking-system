<?php
function head_tag_content(): void
{
    //bootstrap,boxicons, jquery, toastr
    echo "
    <meta charset='UTF-8'>
    <meta content='width=device-width, initial-scale=1, maximum-scale=5,minimum-scale=1, viewport-fit=cover' name='viewport'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ' crossorigin='anonymous'>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe' crossorigin='anonymous'></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css' integrity='sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==' crossorigin='anonymous' referrerpolicy='no-referrer' />
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css'>
    <link rel='stylesheet' href='/assets/css/main.css'>
     ";
}

function body_script_tag_content() {
    echo "
    <script src='/assets/js/main.js'></script>
    <script src='https://code.jquery.com/jquery-3.7.0.min.js' integrity='sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=' crossorigin='anonymous'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js' integrity='sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==' crossorigin='anonymous' referrerpolicy='no-referrer'></script>
    ";
}

function nav_bar(){
    $user = $_SESSION['user_data']['username'] ?? '';

    echo "<nav class='navbar navbar-expand-lg shadow-sm p-3 bg-white rounded'>
        <div class='container-fluid'>
            <div class='d-flex'>
                <a class='navbar-brand order-first' href='index.php'>
                    <img class='img-fluid w-50' src='/assets/img/airasiacom_logo.svg'>
                </a>
            </div>
            <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarNavAltMarkup' aria-controls='navbarNavAltMarkup' aria-expanded='false' aria-label='Toggle navigation'>
                <span class='navbar-toggler-icon'></span>
            </button>
            <div class='collapse navbar-collapse' id='navbarNavAltMarkup'>
                <div class='navbar-nav me-auto'>
                    <a class='nav-link' href='/'>Home</a>
                    <a class='nav-link' href='/flight/search.php'>Search Flight</a>
                    <a class='nav-link' href='/account/manage-bookings.php'>My Bookings</a>
                </div>
                <div id='right-most-no-login' class='navbar-nav ms-auto'>
                    <a class='nav-link me-auto' href='/login.php'>Log in</a>
                    <a class='nav-link' href='/register.php'>Register</a>
                </div>
                <div id='right-most-login' class='navbar-nav ms-auto order-last'>
                    <span class='pt-2 me-2'>Hello there, {$user}</span>
                    <a class='nav-link me-auto' href='/logout.php'>Log out</a>
                </div>
            </div>
        </div>
    </nav>";
}


//This header is specifically for side_bar(), so it is  only to be used in conjunction with side_bar
function header_bar($pageName){
    echo "<div class='navbar navbar-expand-lg shadow navbar-white'>
        <div class='container-fluid w-100'>
            <div class='d-flex'>
                <span class='navbar-brand order-first mb-4'>
                    <a data-bs-target='#sidebar' data-bs-toggle='collapse' class='border rounded-3 p-3 text-decoration-none'><i class='bi bi-list bi-lg py-2 p-1 text-black'></i></a>
                    <span class='fs-2 ms-3'>{$pageName}</span>
                </span>
            </div>
            <div class='navbar-nav ms-auto order-las'>
                <form class='d-flex' role='search'>
                    <input class='form-control me-2' type='search' name='q' placeholder='Search' aria-label='Search'>
                    <button class='btn btn-outline-success' type='submit'>Search</button>
                </form>
            </div>
        </div>
    </div>";
}

function side_bar() {
    $iconSize = "h4";
    echo "
<div id='sidebar' class='collapse collapse-horizontal show border-end'>
    <div class='d-flex flex-column flex-shrink-0 p-3 bg-light vh-100 sidebar mx-w-100'>
        <div class='row gx-3'>
            <div class='col-5'>
                <a href='/' class='d-flex align-items-center me-md-auto link-dark text-decoration-none'>
                    <img class='me-2' width='150' height='73' src='/assets/img/airasiacom_logo.svg'>
                </a>
            </div>
            <div class='col d-sm-none'>
                <a data-bs-target='#sidebar' data-bs-toggle='collapse' class='p-3 text-black'><i class='bi bi-x-lg'></i></a>
            </div>
        </div>
        
        <hr>
        <ul class='nav nav-pills flex-column mb-auto'>
            <li class='nav-item'>
                <a href='/' class='nav-link link-dark'>
                    <i class='bi bi-house-door-fill me-2 $iconSize'></i>
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
                <a href='/flight/search.php' class='nav-link link-dark'>
                    <i class='bi bi-airplane-fill me-2 $iconSize'></i>
                    Flight Search
                </a>
            </li>
            <li>
                <a href='/account/manage-bookings.php' class='nav-link link-dark'>
                    <i class='bi bi-calendar-event-fill me-2 $iconSize'></i>
                    Manage My Bookings
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
                <li><a class='dropdown-item' href='/account/settings.php'>Settings</a></li>
                <li><a class='dropdown-item' href='/account/dashboard.php'>Profile</a></li>
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
            <div class='d-flex align-middle align-items-center'>
                <span class='navbar-brand order-first mb-4'>
                    <a data-bs-target='#sidebar' data-bs-toggle='collapse' class='border rounded-3 p-3 text-decoration-none'><i class='bi bi-list bi-lg py-2 p-1 text-black'></i></a>
                    <span class='fs-2 ms-3 align-items-center'>{$pageName}</span>
                </span>
            </div>
            <div class='navbar-nav ms-auto order-las'>
                <form class='d-flex' role='search'>
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
                <a data-bs-target='#sidebar' data-bs-toggle='collapse' class='p-3 text-black'><i class='bi bi-x-lg'></i></a>
            </div>
        </div>
        
        <hr>
        <ul class='nav nav-pills flex-column mb-auto'>
            <li class='nav-item'>
                <a href='/' class='nav-link link-dark'>
                    <i class='bi bi-house-door-fill me-2 $iconSize'></i>
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
                <a href='/admin/manage-flights.php' class='nav-link link-dark'>
                    <i class='bi bi-airplane-fill me-2 $iconSize'></i>
                    Flight
                </a>
            </li>
            <li>
                <a href='/admin/manage-bookings.php' class='nav-link link-dark'>
                    <i class='bi bi-calendar-fill me-2 $iconSize'></i>
                    Bookings
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
                <li><a class='dropdown-item' href='/admin/profile.php'>Settings</a></li>
                <li><a class='dropdown-item' href='/admin/dashboard.php'>Profile</a></li>
                <li><hr class='dropdown-divider'></li>
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
      <li class='nav-item'><a href='#' class='nav-link px-2 text-muted'>Home</a></li>
      <li class='nav-item'><a href='#' class='nav-link px-2 text-muted'>Features</a></li>
      <li class='nav-item'><a href='#' class='nav-link px-2 text-muted'>Pricing</a></li>
      <li class='nav-item'><a href='#' class='nav-link px-2 text-muted'>FAQs</a></li>
      <li class='nav-item'><a href='#' class='nav-link px-2 text-muted'>About</a></li>
    </ul>
  </footer>
    ";
}

