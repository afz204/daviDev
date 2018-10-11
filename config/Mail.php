<?php
/**
 * This example shows making an SMTP connection with authentication.
 */

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

require '../../assets/vendors/Mailler/PHPMailerAutoload.php';
require '../../assets/vendors/Mailler/class.smtp.php';

/**
 * 
 */
class Mail
{
	
	function Mailler($receivedEmail, $receivedName, $cc, $subject, $content) {

		//Create a new PHPMailer instance
		$mail = new PHPMailer;
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;
		//Ask for HTML-friendly debug output
		$mail->SMTPSecure = 'tls';
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = "srv25.niagahoster.com";
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = 587;
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		$mail->Port = 587;           
		
		//Username to use for SMTP authentication
		$mail->Username = "info@bungadavi.co.id";
		//Password to use for SMTP authentication
		$mail->Password = "admin123";
		//Set who the message is to be sent from
		$mail->setFrom('info@bungadavi.co.id', 'Bunga Davi Indonesia');
		//Set an alternative reply-to address
		$mail->addReplyTo('no-reply@bungadavi.co.id', 'Info Bunga Davi Indonesia');
		//Set who the message is to be sent to
		$mail->addAddress($receivedEmail, $receivedName);
		//Set the subject line
		$mail->AddCC($cc);
		$mail->AddCC('info@bungadavi.co.id');
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->isHTML(true);       
		$mail->Subject = $subject;               
	    $mail->Body    = $content;
	    $mail->AltBody = 'Order Confirmation';
		// $mail->msgHTML(file_get_contents($content), dirname(__FILE__));
		//Replace the plain text body with one created manually
		//Attach an image file
		// $mail->addAttachment('images/phpmailer_mini.png');

		//send the message, check for errors
		if (!$mail->send()) {
			$response = 'ERROR';
			$msg = $mail->ErrorInfo;
		} else {
			$response = 'OK';
			$msg = 'Success!';
		}
		die(json_encode(['response' => $response, 'msg' => $msg], JSON_FORCE_OBJECT));
	}
}

