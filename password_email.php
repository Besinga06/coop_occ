<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';


function sendMemberEmail($email, $fullname, $plain_password)
{

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        $mail->Username   = 'coop.cooperative.06@gmail.com';
        $mail->Password   = '';

        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;


        $mail->setFrom('coop.cooperative.06@gmail.com', 'Cooperative System');

        $mail->addAddress($email, $fullname);


        $mail->isHTML(true);

        $mail->Subject = 'Your Cooperative Account Login Details';

        $mail->Body = "
            Hello $fullname,<br><br>

            Your cooperative account has been successfully created.<br><br>

            <b>Username:</b> $email<br>
            <b>Password:</b> $plain_password<br><br>

            Please login and change your password immediately.<br><br>

            Thank you.<br>
            Cooperative Management System
        ";

        $mail->send();

        return true;
    } catch (Exception $e) {

        return false;
    }
}
