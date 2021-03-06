<?php
/**
 * This example shows making an SMTP connection with authentication.
 */

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

require 'PHPMailerAutoload.php';
require 'class.smtp.php';

//Create a new PHPMailer instance
$mail = new PHPMailer;
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 2;
//Ask for HTML-friendly debug output
$mail->SMTPSecure = 'tls';
$mail->Debugoutput = 'html';
//Set the hostname of the mail server
$mail->Host = "webmail.bungadavi.co.id";
//Set the SMTP port number - likely to be 25, 465 or 587
$mail->Port = 25;
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
$mail->Port = 587;           

//Username to use for SMTP authentication
$mail->Username = "no-reply@bungadavi.co.id";
//Password to use for SMTP authentication
$mail->Password = "P@ssw0rd";
//Set who the message is to be sent from
$mail->setFrom('info@bungadavi.co.id', 'Bunga Davi Indonesia');
//Set an alternative reply-to address
$mail->addReplyTo('info@bungadavi.co.id', 'Info Bunga Davi Indonesia');
//Set who the message is to be sent to
$mail->addAddress('afz60.30@gmail.com', 'Arfan Azhari');
//Set the subject line
$mail->Subject = 'Invoice Penagihan Pembelian Barang';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML(file_get_contents('content.html'), dirname(__FILE__));
//Replace the plain text body with one created manually
$mail->AltBody = 'invoice pembelian barang';
//Attach an image file
// $mail->addAttachment('images/phpmailer_mini.png');

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
