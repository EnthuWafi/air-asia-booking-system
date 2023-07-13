# Air Asia Booking System

![Air Asia Booking System](./public_html/assets/img/airasiacom_logo.svg)


## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Usage](#usage)
- [Contributing](#contributing)

## Introduction

The Air Asia Booking System is a web application built using PHP, CSS, and JavaScript. It allows users to book flights, manage bookings, and make payments for their reservations. This system is designed to provide a user-friendly interface for customers to easily book their flights and manage their travel plans.

## Features

- User registration and login
- Flight search and booking
- Reservation management
- Secure payment processing
- Email notifications
- User profile management

## Prerequisites

Before setting up the Air Asia Booking System, ensure that you have the following prerequisites installed on your system:

- <img src="https://upload.wikimedia.org/wikipedia/en/thumb/7/78/XAMPP_logo.svg/1200px-XAMPP_logo.svg.png" width="5%"> XAMPP v3.30 (with PHP 8.2.4)

- Web browser (e.g., Google Chrome, Mozilla Firefox)

## Installation

To install and set up the Air Asia Booking System, follow these steps:

1. Clone the repository to your local machine:

2. Move the cloned repository to the appropriate XAMPP folder (e.g., `htdocs`).

3. Start XAMPP and ensure that Apache and MySQL services are running.

4. Import the provided database dump (`air-asia-booking-system.sql`) into your MySQL server.

5. Update the database configuration in the `connection.inc.php` file in "includes" folder with your MySQL server credentials:

```php
	$host = "localhost";
    $username = "root"; 				//username
    $password = "";						//password
    $db = "air-asia-booking-system";	//database name
```

6. Go back to the XAMPP Control Panel

7. Navigate to XAMPP Apache "httpd.conf" file

8. Open it, and change DocumentRoot to the location of the air-asia-booking-system "public_html"


## Usage

To use the Air Asia Booking System, follow these steps:

### Customer Routes

1. Register a new user account or log in with your existing account.

2. Search for available flights by providing the origin and destination airports, departure date, and number of passengers.

3. Select a flight from the search results and proceed to the booking page.

4. Provide passenger details and confirm your reservation.

5. Make a payment for your reservation using the provided payment methods.

6. After successful payment, you will receive a confirmation email with your booking details.

7. You can manage your reservations and view your booking history in the user profile section.

### Admin Routes

To access the admin functionalities, use the following credentials:
- Username: "EnthuWafi"
- Password: "wafi"


## Contributing

Contributions to the Air Asia Booking System are welcome! If you encounter any issues or have suggestions for improvements, please submit an issue or pull request on the GitHub repository.

