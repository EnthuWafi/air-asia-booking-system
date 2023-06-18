<?php
require_once("functions.inc.php");

function admin_displayBookings($bookings) {
    $bookingStatus = retrieveBookingStatus();
    $optionContent = "<option selected>Select</option>";
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
                <td><a class='text-decoration-none fw-bold' href='/admin/view-booking.php?booking_ref={$booking["booking_reference"]}'>
                {$booking["booking_reference"]}</a></td>
                <td>{$booking["username"]}</td>
                <td>{$tripTypeStr}</td>
                <td>RM{$bookingCost}</td>
                <td><span class='{$status["class"]}'>{$status["status"]}</span></td>
                <td>
                    <form action='manage-bookings.php' method='post'>
                        <div class='row pt-2'>
                            <input type='hidden' name='booking_id' value='{$booking["booking_id"]}'>
                            <div class='col-auto'>
                                <select class='form-select' name='status' id='floatingSelectGrid'>
                                    {$optionContent}
                                </select>
                            </div>
                            <div class='col'>
                                <button type='button' class='btn btn-danger mb-3' data-bs-toggle='modal' data-bs-target='#updateStatic' 
                                onclick='updateModal({$booking["booking_id"]}, \"modal-btn-update\");'>Update</button>
                            </div>
                        </div>
                    </form>
                </td>
                <td class='text-center'>
                    <form action='/admin/manage-bookings.php' method='post'>
                        <input type='hidden' name='booking_id' value='{$booking["booking_id"]}'>
                        <a data-bs-toggle='modal' data-bs-target='#deleteStatic' onclick='updateModal({$booking["booking_id"]}, \"modal-btn-delete\");' class='h4'>
                        <i class='bi bi-trash'></i></a>
                    </form>    
                </td>
                
            </tr>";
            $count++;
        }
    }
}

function admin_displayFlights($flights) {
    if ($flights != null) {
        $count = 1;
        foreach ($flights as $flight) {

            //status = upcoming, departed, in progress
            $today = date("Y-m-d H:i:s");

            $departureDate = $flight["departure_time"];
            $arrivalDate = $flight["arrival_time"];

            $departureUnformatted = date_create($departureDate);
            $arrivalUnformatted = date_create($arrivalDate);
            $departureFormatted = date_format($departureUnformatted, "d M Y");

            $duration = date_create($flight["duration"]);
            $durationHours = date_format($duration, "G")."h ".date_format($duration, "i")."m";

            $flightBaseCost = number_format((float)$flight["flight_base_price"], 2, '.', '');

            $status = [];
            if ($departureUnformatted > $today) {
                $status = ["status"=>"Upcoming", "css"=>"upcoming"];
            }
            else if ($today < $arrivalUnformatted) {
                $status = ["status"=>"In Progress", "css"=>"in-progress"];
            }
            else {
                $status = ["status"=>"Departed", "css"=>"departed"];
            }

            echo
            "<tr class='align-middle'>
                <th scope='row'>$count</th>
                <td><img src='{$flight["airline_image"]}' width='50' height='40'></td>
                <td>{$flight["origin_airport_code"]}</td>
                <td>{$flight["destination_airport_code"]}</td>
                <td>{$departureFormatted}</td>
                <td>{$durationHours}</td>
                <td>RM{$flightBaseCost}</td>
                <td class='{$status["css"]}'>{$status["status"]}</td>
                <td>{$flight["aircraft_name"]}</td>
                <td>
                    <form action='manage-flights.php' id='{$flight["flight_id"]}' method='post'>
                        <input type='hidden' name='booking_id' value='{$flight["flight_id"]}'>
                        <a type='button' data-bs-toggle='modal' data-bs-target='#deleteStatic' onclick='updateModal({$flight["flight_id"]}, \"modal-btn-delete\");' class='h4'>
                        <i class='bi bi-trash'></i></a>
                    </form>    
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
            "<tr class='align-middle'>
                <th scope='row'>$count</th>
                <td>{$user["username"]}</td>
                <td>{$fullName}</td>
                <td>{$user["email"]}</td>
                <td>{$user["admin_code"]}</td>
                <td>{$dateFormatted}</td>
                <td class='text-center'>
                    <form action='manage-users.php' id='{$user["user_id"]}' method='post'>
                        <input type='hidden' name='user_id' value='{$user["user_id"]}'>
                        <a type='button' data-bs-toggle='modal' data-bs-target='#static' onclick='updateModal({$user["user_id"]}, \"modal-btn\");' class='h4'>
                        <i class='bi bi-trash'></i></a>
                    </form> 
                </td>
            </tr>";
            $count++;
        }
    }
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
            "<tr class='align-middle'>
                <th scope='row'>$count</th>
                <td>{$user["username"]}</td>
                <td>{$fullName}</td>
                <td>{$user["email"]}</td>
                <td>{$dobFormatted}</td>
                <td>{$phone}</td>
                <td>{$dateFormatted}</td>
                <td class='text-center'>
                    <form action='manage-users.php' id='{$user["user_id"]}' method='post'>
                        <input type='hidden' name='user_id' value='{$user["user_id"]}'>
                        <a type='button' data-bs-toggle='modal' data-bs-target='#static' onclick='updateModal({$user["user_id"]}, \"modal-btn\");' class='h4'>
                        <i class='bi bi-trash'></i></a>
                    </form>    
                </td>
            </tr>";
            $count++;
        }
    }
}