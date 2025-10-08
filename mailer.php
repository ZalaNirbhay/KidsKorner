<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require('PHPMailer\PHPMailer.php');
require('PHPMailer\SMTP.php');
require('PHPMailer\Exception.php');

function sendEmail($to, $subject, $body, $file = null, $debug = false)
{
	$mail = new PHPMailer(true);

	try {
		// Enable SMTP and configure server
		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 465;
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // implicit TLS on 465
		if ($debug) {
			$mail->SMTPDebug = SMTP::DEBUG_SERVER; // verbose debug output
			$mail->Debugoutput = 'error_log';
		}
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);

		// Credentials
		$mail->Username = 'zalanirbhay21@gmail.com';
		$mail->Password = 'sfss xztg ulcw zrng'; // App Password

		// Sender/recipients
		$mail->setFrom('zalanirbhay21@gmail.com', 'Kids-Korner');
		$mail->addReplyTo('zalanirbhay21@gmail.com', 'Kids-Korner');
		$mail->addAddress($to);

		// Content
		$mail->Subject = $subject;
		$mail->isHTML(true);
		$mail->CharSet = 'UTF-8';
		$mail->Body = $body;
		$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';

		// Optional attachment
		if (!empty($file) && file_exists($file)) {
			$mail->addAttachment($file);
		}

		// Attempt send
		$mail->send();
		return true;
	} catch (Exception $e) {
		return 'Email failed: ' . $mail->ErrorInfo . ' | Exception: ' . $e->getMessage();
	}
}
