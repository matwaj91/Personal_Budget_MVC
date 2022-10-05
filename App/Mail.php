<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Config;

class Mail 
{
    public static function send($to, $subject, $html, $text)
    {
		$mail = new PHPMailer(true);  
		//$mail->SMTPDebug = SMTP::DEBUG_SERVER;                   
		$mail->isSMTP();                                            
		$mail->Host       = 'smtp.gmail.com';                     
		$mail->SMTPAuth   = true;                                   
		$mail->Username   = 'mateusz.wajnberger@gmail.com';                     
		$mail->Password   = Config::MAIL_PASSWORD;                               
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
		$mail->Port       = 465;                                    

		$mail->setFrom('mateusz.wajnberger@gmail.com', 'Personal Budget');
		$mail->addAddress($to);              

		$mail->isHTML(true);                                  
		$mail->Subject = $subject;
		$mail->Body    = $html;
		$mail->AltBody = $text;

		$mail->send();
	}
}