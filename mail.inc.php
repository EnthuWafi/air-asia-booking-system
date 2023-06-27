<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include library files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
function sendMail($recipientEmail, $subject, $body)
{
// Create an instance; Pass `true` to enable exceptions
    $mail = new PHPMailer(true);

// Server settings
//$mail->SMTPDebug = SMTP::DEBUG_SERVER;    //Enable verbose debug output
    $mail->isSMTP();                            // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';           // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                     // Enable SMTP authentication
    $mail->Username = 'kerepekfunz5@gmail.com';       // SMTP username
    $mail->Password = 'jlmcoseoyexindtz';         // SMTP password
    $mail->SMTPSecure = 'ssl';                  // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 465;                          // TCP port to connect to

// Sender info
    $mail->setFrom('airasia@gmail.com', 'Air Asia Information System');

// Add a recipient
    $mail->addAddress($recipientEmail);

//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

// Set email format to HTML
    $mail->isHTML(true);

// Mail subject
    $mail->Subject = $subject;

// Mail body content
    $bodyContent = $body;
    $mail->Body = $bodyContent;

// Send email
    if (!$mail->send()) {
        return false;
    } else {
        return true;
    }
}
