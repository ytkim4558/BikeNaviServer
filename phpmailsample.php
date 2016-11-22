<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2016-11-18
 * Time: 오후 5:30
 */
require_once './vendor/autoload.php';
class sendMailUsingPhpMailer
{
    function sendMail($title, $message)
    {
        $mail = new PHPMailer;

        $mail->CharSet = "euc-kr";
        $mail->Encoding = "base64";

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'ytkim4558@gmail.com';                 // SMTP username
        $mail->Password = 'abgoogle9789';                           // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to
        $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only

        $mail->setFrom('ytkim4558@gmail.com', 'Mailer');
        $mail->addAddress('ytkim4558@naver.com', '김용탁');     // Add a recipient
        $mail->addAddress('ytkim4558@gmail.com');               // Name is optional
        $mail->addReplyTo('ytkim4558@gmail.com', 'Information');
        $mail->addCC('ytkim4558@gmail.com');
        $mail->addBCC('bcc@example.com');

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = iconv('UTF-8', 'EUC-KR', $title);
        $mail->MsgHTML(iconv('UTF-8', 'EUC-KR', nl2br($message))); // html로 보내는 경우 br태그를 붙여서 보내야 줄바꿈이 된다.
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if (!$mail->send()) {
            error_log('Message could not be sent.');
            error_log('Mailer Error: ' . $mail->ErrorInfo);
        } else {
            echo 'Message has been sent';
        }
    }
}