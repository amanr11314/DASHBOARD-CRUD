<?php
// Include PHPMailer classes
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function send_custom_mail($to_email = 'amanr11314@gmail.com', $to_name = 'Aman Raj', $subject, $body, $isHtml): bool
{

// Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Set the SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587; // or the appropriate port for your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'testmanager.e.123@gmail.com';
        include "password.php";
        $mail->Password = 'iyypuilmkcbgobow';
        // $mail->SMTPSecure = "ssl";
        // $mail->Password = $password;

        // Set the sender and recipient
        $mail->setFrom('testmanager.e.123@gmail.com', 'Test Manager');
        $mail->addAddress($to_email, $to_name);

        // Set the email subject and message
        $mail->Subject = $subject ?? 'Test Email';

        $mail->isHTML($isHtml);

        $mail->Body = $body ?? 'This is a test email sent from localhost using PHPMailer.';

        // Send the email
        return $mail->send();
        echo 'Email sent successfully!';
    } catch (Exception $e) {
        echo 'Failed to send email. Error: ' . $mail->ErrorInfo;
    }
    return false;
}
// $email_body = "<a href='localhost/email_verification.php?key=123&token=" . time() . "'>Click and Verify Email</a>";
// send_custom_mail(to_email:'amanr11314@gmail.com', to_name:'Aman Raj', subject:'Test Verification Email', body:$email_body, isHtml:true);