<?php

use PHPMailer\PHPMailer\PHPMailer;

class mailer
{

    public function sendLetter($ar, $start, $scriptStart)
    {
        $mail = new PHPMailer();

        try {
            //$mail->SMTPDebug = 2;
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = data::getInstance()->getMailerSMTP();  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = data::getInstance()->getMailerUsername();                           // SMTP username
            $mail->Password = data::getInstance()->getMailerPassword();                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = data::getInstance()->getMailerPort();             // TCP port to connect to

            //Recipients
            $mail->setFrom("no-reply@gmail.com", "Parser");
            $mail->addAddress(data::getInstance()->getEmailForResult(), 'USER');     // Add a recipient

            //Attachments
            foreach ($ar as $path) {
                $mail->addAttachment($path);         // Add attachments
            }

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'CARiD suspension systems | ' . date("Y-m-d H:i:s");
            $mail->Body = 'Script started on ' . $scriptStart . '. <br> 
               Script worked for ' . (int)(microtime(true) - $start) . ' seconds.';

            $mail->send();
            echo 'Message has been sent';
        } catch
        (Exception $e) {
            echo 'Message could not be sent. mailer Error: ', $mail->ErrorInfo;
        }
    }
}