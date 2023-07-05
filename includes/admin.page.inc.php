<?php
require_once("functions.inc.php");

function admin_displayBookings($bookings) {
    $bookingStatus = retrieveBookingStatus();
    $optionContent = "";
    foreach ($bookingStatus as $status) {
        $statusUC = ucfirst(strtolower($status["booking_status"]));
        $optionContent .= "<option value='{$status["booking_status"]}'>{$statusUC}</option>";
    }
    if ($bookings != null) {
        $count = 1;
        foreach ($bookings as $booking) {
            $tripType = $booking["trip_type"];
            $tripTypeStr = $tripType == "ONE-WAY" ? "One-way Trip" :
                ($tripType == "RETURN" ? "Round-trip" : "null");

            $status = ["status"=>ucfirst(strtolower($booking["booking_status"])), "class"=>strtolower($booking["booking_status"])];
            $bookingCost = number_format((float)$booking["booking_cost"], 2, '.', '');

            echo
            "<tr class='align-middle'>
                <th scope='row'>$count</th>
                <td><a class='text-decoration-none fw-bold' href='/admin/view-booking.php?booking_id={$booking["booking_id"]}'>
                {$booking["booking_reference"]}</a></td>
                <td>{$booking["username"]}</td>
                <td>{$tripTypeStr}</td>
                <td>RM{$bookingCost}</td>
                <td><span class='{$status["class"]}'>{$status["status"]}</span></td>
                <form action='/admin/manage-bookings.php' id='{$booking["booking_id"]}' method='post'>
                <input type='hidden' name='booking_id' value='{$booking["booking_id"]}'>
                <input type='hidden' name='token' value='{$_SESSION["token"]}'>
                <td class='align-middle'>
                    <div class='row pt-2'>   
                        <div class='col-auto'>
                            <select class='form-select' name='status' id='select{$booking["booking_id"]}'>
                                {$optionContent}
                            </select>
                            <script>
                                document.getElementById('select{$booking["booking_id"]}').value = \"{$booking["booking_status"]}\";                   
                            </script>
                        </div>
                        <div class='col'>
                            <button type='button' class='btn btn-danger mb-3' data-bs-toggle='modal' data-bs-target='#updateStatic' 
                            onclick='updateModal({$booking["booking_id"]}, \"modal-btn-update\");'>Update</button>
                        </div>
                    </div>
                </td>
                <td class='text-center'>
                    <a data-bs-toggle='modal' data-bs-target='#deleteStatic' onclick='updateModal({$booking["booking_id"]}, \"modal-btn-delete\");' class='h4'>
                    <i class='bi bi-trash'></i></a>
                </td>
                </form> 
            </tr>";
            $count++;
        }
    }
}

function admin_displayBookingsLite($bookings) {
    ?>
    <table class="table table-responsive table-hover">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Booking Reference</th>
            <th scope="col">Customer</th>
            <th scope="col">Trip Type</th>
            <th scope="col">Status</th>
            <th scope="col">Cost</th>
            <th scope="col" class="text-center">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $bookingStatus = retrieveBookingStatus();
        $optionContent = "";
        foreach ($bookingStatus as $status) {
            $statusUC = ucfirst(strtolower($status["booking_status"]));
            $optionContent .= "<option value='{$status["booking_status"]}'>{$statusUC}</option>";
        }

        if ($bookings != null) {
            $count = 1;
            foreach ($bookings as $booking) {
                $tripType = $booking["trip_type"];
                $tripTypeStr = $tripType == "ONE-WAY" ? "One-way Trip" :
                    ($tripType == "RETURN" ? "Round-trip" : "null");

                $status = ["status"=>ucfirst(strtolower($booking["booking_status"])), "class"=>strtolower($booking["booking_status"])];
                $bookingCost = number_format((float)$booking["booking_cost"], 2, '.', '');


                echo "
<tr class='align-middle'>
    <th scope='row'>$count</th>
    <td><a class='text-decoration-none fw-bold' href='/admin/view-booking.php?booking_id={$booking["booking_id"]}'>
{$booking["booking_reference"]}</a></td>
    <td>{$booking["username"]}</td>
    <td>{$tripTypeStr}</td>
    <td>RM{$bookingCost}</td>
    <td><span class='{$status["class"]}'>{$status["status"]}</span></td>
    <td class='text-center'>
        <a type='button' class='btn btn-outline-primary' href='/admin/manage-bookings.php/#{$booking["booking_id"]}'>
            <i class='bi bi-three-dots'></i> See More
        </a>
    </td>
</tr>";
                $count++;
            }
            $count--;
            echo "<script>$('#booking-count').html(\"{$count}\");</script>";
        } else {
            echo "<tr><td colspan='12' class='text-center'>No bookings found</td></tr>";
        }
        ?>
        </tbody>
    </table><?php
}

function admin_displayFlights($flights) {
    if ($flights != null) {
        $count = 1;
        foreach ($flights as $flight) {

            //status = upcoming, departed, in progress
            $today = date_create();

            $departureDate = $flight["departure_time"];
            $arrivalDate = $flight["arrival_time"];

            $departureUnformatted = date_create($departureDate);
            $arrivalUnformatted = date_create($arrivalDate);

            $departureFormatted = date_format($departureUnformatted, "d M Y H:iA");

            $duration = date_create($flight["duration"]);
            $durationHours = date_format($duration, "G")."h ".date_format($duration, "i")."m";

            $flightBaseCost = number_format((float)$flight["flight_base_price"], 2, '.', '');

            $discount = $flight["flight_discount"];
            $discountPercentage = $discount * 100;

            $status = [];
            if ($departureUnformatted > $today) {
                $status = ["status"=>"Upcoming", "css"=>"upcoming"];
            }
            else if ($arrivalUnformatted > $today) {
                $status = ["status"=>"In Progress", "css"=>"in-progress"];
            }
            else {
                $status = ["status"=>"Departed", "css"=>"departed"];
            }

            echo
            "<tr class='align-middle' id='{$flight["flight_id"]}'>
                <th scope='row'>
                    <a href='/admin/view-flight.php?flight_id={$flight["flight_id"]}' class='text-decoration-none fw-bold'>$count</a>
                </th>
                <td><img src='{$flight["airline_image"]}' width='50' height='40'></td>
                <td>{$flight["origin_airport_code"]}</td>
                <td>{$flight["destination_airport_code"]}</td>
                <td>{$departureFormatted}</td>
                <td>{$durationHours}</td>
                <td>RM{$flightBaseCost}</td>
                <td class='text-center'>{$discountPercentage}%</td>
                <td><span class='{$status["css"]}'>{$status["status"]}</span></td>
                
                <td>{$flight["aircraft_name"]}</td>
                <td class='text-muted'>{$flight["username"]}</td>
                <td class='text-center'>
                    <a type='button' data-bs-toggle='modal' data-bs-target='#updateStatic' 
                    onclick='updateElement({$flight["flight_id"]}, \"update\",\"flight_id\");' class='h4'>
                    <i class='bi bi-pencil-square'></i></a>
                    <a type='button' data-bs-toggle='modal' data-bs-target='#deleteStatic' 
                    onclick='updateElement({$flight["flight_id"]}, \"delete\",\"flight_id\");' class='h4'>
                    <i class='bi bi-trash'></i></a>
                </td>
                
            </tr>";
            $count++;
        }
    }
}

function admin_displayAdminUsers($adminUsers) {
    if ($adminUsers != null) {
        $count = 1;
        // OKAY, FOR DELETING, I need to use a modal so the user can be sure to remove it
        foreach ($adminUsers as $user) {
            $fullName = $user["user_fname"] . " " . $user["user_lname"];
            $date = date_create($user["registration_date"]);
            $dateFormatted = date_format($date, "d M Y");
            echo
            "<tr class='align-middle' id='{$user["user_id"]}'>
                <th scope='row'>$count</th>
                <td>{$user["username"]}</td>
                <td>{$fullName}</td>
                <td>{$user["email"]}</td>
                <td>{$user["admin_code"]}</td>
                <td>{$dateFormatted}</td>
                <td class='text-center'>
                    <a type='button' data-bs-toggle='modal' data-bs-target='#updateAdminStatic' 
                    onclick='updateElement(\"{$user["user_id"]}\", \"update\", \"user_id\");' class='h4'>
                    <i class='bi bi-pencil-square'></i></a>
                    <a type='button' data-bs-toggle='modal' data-bs-target='#static' 
                    onclick='updateElement(\"{$user["user_id"]}\", \"delete\", \"user_id\");' class='h4'>
                    <i class='bi bi-trash'></i></a>
                </td>
            </tr>";
            $count++;
        }
    }
}

function admin_displayAdminUserLite($adminUsers) { ?>
    <table class="table table-responsive table-hover">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Username</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Code</th>
            <th scope="col">Registration</th>
            <th scope="col">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($adminUsers != null) {
            $count = 1;
            foreach ($adminUsers as $user) {
                $fullName = $user["user_fname"] . " " . $user["user_lname"];
                $date = date_create($user["registration_date"]);
                $dateFormatted = date_format($date, "d M Y");
                echo
                "<tr class='align-middle'>
    <th scope='row'>$count</th>
    <td>{$user["username"]}</td>
    <td>{$fullName}</td>
    <td>{$user["email"]}</td>
    <td>{$user["admin_code"]}</td>
    <td>{$dateFormatted}</td>
    <td class='text-center'>
        <a type='button' class='btn btn-outline-primary' href='/admin/manage-users.php/#{$user["user_id"]}'>
        <i class='bi bi-three-dots'></i> See More
        </a>
    </td>
    </tr>";
                $count++;
            }
            $count--;
            echo "<script>$('#admin-count').html(\"{$count}\");</script>";
        }
        else {
            echo "<tr><td colspan='7' class='text-center'>No user found</td></tr>";
        }

        ?>
        </tbody>
    </table><?php
}

function admin_displayCustomerUsers($customerUsers) {
    if ($customerUsers != null) {
        $count = 1;
        // OKAY, FOR DELETING, I need to use a modal so the user can be sure to remove it
        foreach ($customerUsers as $user) {
            $fullName = $user["user_fname"] . " " . $user["user_lname"];
            $date = date_create($user["registration_date"]);
            $dateFormatted = date_format($date, "d M Y");

            $dob = $user["customer_dob"];
            $dobFormatted = $dob ? date_format(date_create($dob), "d M Y") : "-";

            $phone = $user["customer_phone"] ?? "-";
            echo
            "<tr class='align-middle' id='{$user["user_id"]}'>
                <th scope='row'>$count</th>
                <td>{$user["username"]}</td>
                <td>{$fullName}</td>
                <td>{$user["email"]}</td>
                <td>{$dobFormatted}</td>
                <td>{$phone}</td>
                <td>{$dateFormatted}</td>
                <td class='text-center'>
                    <a type='button' data-bs-toggle='modal' data-bs-target='#static' 
                    onclick='updateElement(\"{$user["user_id"]}\", \"delete\", \"user_id\");' class='h4'>
                    <i class='bi bi-trash'></i></a>  
                </td> 
            </tr>";
            $count++;
        }
    }
}

function admin_displayCustomerUserLite($customerUsers) { ?>
    <table class="table table-responsive table-hover">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Username</th>
        <th scope="col">Name</th>
        <th scope="col">Email</th>
        <th scope="col">DOB</th>
        <th scope="col">Phone</th>
        <th scope="col">Registration</th>
        <th scope="col">Action</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($customerUsers != null) {
        $count = 1;
        // OKAY, FOR DELETING, I need to use a modal so the user can be sure to remove it
        foreach ($customerUsers as $user) {
            $fullName = $user["user_fname"] . " " . $user["user_lname"];
            $date = date_create($user["registration_date"]);
            $dateFormatted = date_format($date, "d M Y");

            $dob = $user["customer_dob"];
            $dobFormatted = $dob ? date_format(date_create($dob), "d M Y") : "-";

            $phone = $user["customer_phone"] ?? "-";
            echo
            "<tr class='align-middle'>
                <th scope='row'>$count</th>
                <td>{$user["username"]}</td>
                <td>{$fullName}</td>
                <td>{$user["email"]}</td>
                <td>{$dobFormatted}</td>
                <td>{$phone}</td>
                <td>{$dateFormatted}</td>
                <td class='text-center'>
                    <a type='button' class='btn btn-outline-primary' href='/admin/manage-users.php/#{$user["user_id"]}'>
                        <i class='bi bi-three-dots'></i> See More
                    </a> 
                </td> 
            </tr>";
            $count++;
        }
        $count--;
        echo "<script>$('#customer-count').html(\"{$count}\");</script>";
    }
    else {
        echo "<tr><td colspan='8' class='text-center'>No user found</td></tr>";
    }
    ?>
    </tbody>
    </table><?php
}

function admin_bookingFlightsDisplay($flights) {
    $flightDiv = "";
    foreach ($flights as $flight) {


        $departure = date_create($flight["departure_time"]);
        $arrival = date_create($flight["arrival_time"]);

        $departureFormat = date_format($departure, "d M Y");
        $arrivalFormat = date_format($arrival, "d M Y");

        $hourDeparture = date_format($departure, "H:i A");
        $hourArrival = date_format($arrival, "H:i A");

        $today = date_create("now");
        $status = "";
        if ($departure > $today) {
            $status = "Upcoming";
        }
        else if ($today < $arrival) {
            $status = "In Progress";
        }
        else {
            $status = "Departed";
        }

        $hour = formatDuration($flight["duration"]);

        $flightDiv .= "
<div class='card mb-1'>
  <div class='card-body'>
      <div class='row align-items-center'>
      
    <div class='col-1'>
        <img class='img-fluid' width='60' height='60' src='{$flight["airline_image"]}'>
    </div>
    <div class='col-2 mt-3'>
        <div class='row'>
            <h3 class='text-nowrap'>{$flight["airline_name"]}</h3>
        </div>
        <div class='row'>
            <p class='text-muted'>{$flight["aircraft_name"]}</p>
        </div>
    </div>
    <div class='col-4 mt-3'>
        <div class='row'>
           <div class='col text-center'>
                <div class='row'>
                    <h3><i class='bx bxs-plane-take-off icon-red'></i></h3>     
                </div>
                <div class='row'>
                    <span class='text-muted text-nowrap'>$departureFormat</span><br>
                    <span class='text-muted text-nowrap'>$hourDeparture</span>
                </div>
            </div>
            <div class='col text-center'>
                <div class='row'>
                    <h3><i class='bx bxs-plane-land icon-red'></i></h3>     
                </div>
                <div class='row'>
                    <span class='text-muted text-nowrap'>$arrivalFormat</span><br>
                    <span class='text-muted text-nowrap'>$hourArrival</span>
                </div>
            </div>     
        </div>   
    </div>
    <div class='col-auto ms-1'>
        <span class='h4 text-secondary text-center'> 
            {$flight["origin_airport_code"]} <i class='bi bi-arrow-right'></i> {$flight["destination_airport_code"]}
        </span>
    </div>
    <div class='col text-center'>
        <span class='text-muted'>{$hour}</span>
    </div>
    
      </div>
  </div>
</div>";
    }
    return $flightDiv;
}

function admin_displayFlightLite($flights) { ?>
    <table class="table table-responsive table-hover">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Airline</th>
        <th scope="col">Origin</th>
        <th scope="col">Dest.</th>
        <th scope="col">Departure</th>
        <th scope="col">Duration</th>
        <th scope="col">Base Price</th>
        <th scope="col">Discount</th>
        <th scope="col">Action</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($flights != null) {
        $count = 1;
        foreach ($flights as $flight) {

            //status = upcoming, departed, in progress
            $today = date_create();

            $departureDate = $flight["departure_time"];
            $arrivalDate = $flight["arrival_time"];

            $departureUnformatted = date_create($departureDate);
            $arrivalUnformatted = date_create($arrivalDate);

            $departureFormatted = date_format($departureUnformatted, "d M Y H:iA");

            $duration = date_create($flight["duration"]);
            $durationHours = date_format($duration, "G")."h ".date_format($duration, "i")."m";

            $flightBaseCost = number_format((float)$flight["flight_base_price"], 2, '.', '');

            $discount = $flight["flight_discount"];
            $discountPercentage = $discount * 100;

            $status = [];
            if ($departureUnformatted > $today) {
                $status = ["status"=>"Upcoming", "css"=>"upcoming"];
            }
            else if ($arrivalUnformatted > $today) {
                $status = ["status"=>"In Progress", "css"=>"in-progress"];
            }
            else {
                $status = ["status"=>"Departed", "css"=>"departed"];
            }

            echo
            "<tr class='align-middle'>
                <th scope='row'>
                    <a href='/admin/view-flight.php?flight_id={$flight["flight_id"]}' class='text-decoration-none fw-bold'>$count</a>
                </th>
                <td><img src='{$flight["airline_image"]}' width='50' height='40'></td>
                <td>{$flight["origin_airport_code"]}</td>
                <td>{$flight["destination_airport_code"]}</td>
                <td>{$departureFormatted}</td>
                <td>{$durationHours}</td>
                <td>RM{$flightBaseCost}</td>
                <td class='text-center'>{$discountPercentage}%</td>
                <td><span class='{$status["css"]}'>{$status["status"]}</span></td>
                
                <td>{$flight["aircraft_name"]}</td>
                <td class='text-center'>
                    <a type='button' class='btn btn-outline-primary' href='/admin/manage-flights.php/#{$flight["flight_id"]}'>
                    <i class='bi bi-three-dots'></i> See More
                    </a>
                </td>
                
            </tr>";
            $count++;
        }
        $count--;
        echo "<script>$('#flight-count').html(\"{$count}\");</script>";
    }
    else {
        echo "<tr><td colspan='9' class='text-center'>No flight found</td></tr>";
    }
    ?>
    </tbody>
    </table><?php
}

function admin_displayAircraft($aircrafts) {
    ?>
    <table class="table table-hover table-responsive">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Registration</th>
            <th scope="col">Economy</th>
            <th scope="col">Premium Economy</th>
            <th scope="col">Business</th>
            <th scope="col">First Class</th>
            <th scope="col" class="text-center">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($aircrafts != null) {
            $count = 1;
            // OKAY, FOR DELETING, I need to use a modal so the user can be sure to remove it
            foreach ($aircrafts as $aircraft) {

                $date = date_create($aircraft["registration_date"]);
                $dateFormatted = date_format($date, "d M Y");
                echo
                "<tr class='align-middle' id='{$aircraft["aircraft_id"]}'>
                <th scope='row'>$count</th>
                <td>{$aircraft["aircraft_name"]}</td>
                <td>{$dateFormatted}</td>
                <td class='text-center'>{$aircraft["economy_capacity"]}</td>
                <td class='text-center'>{$aircraft["premium_economy_capacity"]}</td>
                <td class='text-center'>{$aircraft["business_capacity"]}</td>
                <td class='text-center'>{$aircraft["first_class_capacity"]}</td>
                <td class='text-center'>
                    <a type='button' data-bs-toggle='modal' data-bs-target='#updateAircraftStatic' 
                    onclick='updateElement(\"{$aircraft["aircraft_id"]}\", \"update\", \"aircraft_id\"); 
updateElement(\"{$aircraft["aircraft_name"]}\", \"update\", \"name\");
updateElement(\"{$aircraft["economy_capacity"]}\", \"update\", \"economy\");
updateElement(\"{$aircraft["premium_economy_capacity"]}\", \"update\", \"premium-economy\");
updateElement(\"{$aircraft["business_capacity"]}\", \"update\", \"business\");
updateElement(\"{$aircraft["first_class_capacity"]}\", \"update\", \"first-class\");' class='h4'>
                    <i class='bi bi-pencil-square'></i></a>
                    <a type='button' data-bs-toggle='modal' data-bs-target='#deleteStatic' 
                    onclick='updateElement({$aircraft["aircraft_id"]}, \"delete\", \"aircraft_id\");' class='h4'>
                    <i class='bi bi-trash'></i></a>
                </td>
            </tr>";
                $count++;
            }
        }
        else {
            echo "<tr><td colspan='8' class='text-center'>No aircraft found</td></tr>";
        }
        ?>
        </tbody>
    </table>
<?php
}

function admin_displayAircraftLite($aircrafts) {
    ?>
    <table class="table table-hover table-responsive">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Registration</th>
            <th scope="col">Economy</th>
            <th scope="col">Premium Economy</th>
            <th scope="col">Business</th>
            <th scope="col">First Class</th>
            <th scope="col" class="text-center">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($aircrafts != null) {
            $count = 1;
            // OKAY, FOR DELETING, I need to use a modal so the user can be sure to remove it
            foreach ($aircrafts as $aircraft) {

                $date = date_create($aircraft["registration_date"]);
                $dateFormatted = date_format($date, "d M Y");
                echo
                "<tr class='align-middle'>
                <th scope='row'>$count</th>
                <td>{$aircraft["aircraft_name"]}</td>
                <td>{$dateFormatted}</td>
                <td class='text-center'>{$aircraft["economy_capacity"]}</td>
                <td class='text-center'>{$aircraft["premium_economy_capacity"]}</td>
                <td class='text-center'>{$aircraft["business_capacity"]}</td>
                <td class='text-center'>{$aircraft["first_class_capacity"]}</td>
                <td class='text-center'>
                    <a type='button' class='btn btn-outline-primary' href='/admin/manage-aircrafts.php/#{$aircraft["aircraft_id"]}'>
                    <i class='bi bi-three-dots'></i> See More
                    </a> 
                </td>
            </tr>";
                $count++;
            }
            $count--;
            echo "<script>$('#aircraft-count').html(\"{$count}\");</script>";
        }
        else {
            echo "<tr><td colspan='8' class='text-center'>No aircraft found</td></tr>";
        }
        ?>
        </tbody>
    </table>
    <?php
}

function admin_displayCustomerUserDashboard($users) {
    ?>
    <table class="table table-responsive table-hover">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Customer</th>
            <th scope="col" class="text-center">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($users != null) {
            $count = 1;
            foreach ($users as $usersEntry) {
                echo "<tr class='align-middle'>
                <th scope='row'>$count</th>
                <td>{$usersEntry["username"]}</td>
                <td class='text-center'><a class='btn btn-outline-primary' href='/admin/manage-users.php/#{$usersEntry["user_id"]}'>
                    <i class='bi bi-three-dots'></i> See More                                    
                </a></td>
                </tr>";
                $count++;
            }
            $count--;
        } else {
            echo "<tr><td colspan='3' class='text-center'>No customers found</td></tr>";
        }
        ?>
        </tbody>
    </table>

<?php
}