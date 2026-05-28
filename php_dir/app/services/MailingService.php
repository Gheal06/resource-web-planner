<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';
class MailingService {
  public function send_email($to, $subject, $message) {
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = getenv('MAIL_HOST') ?: 'localhost';
    $mail->SMTPAuth = getenv('MAIL_USER') ? true : false;
    $mail->SMTPDebug = getenv('MAIL_DEBUG') ? intval(getenv('MAIL_DEBUG')) : 0;
    $mail->Port = getenv('MAIL_PORT') ? intval(getenv('MAIL_PORT')) : 25;
    $mail->Username = getenv('MAIL_USER') ?: '';
    $mail->Password = getenv('MAIL_PASS') ?: '';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $from = getenv('MAIL_FROM') ?: 'noreply@resource-planner.local';
    $fromName = getenv('MAIL_FROM_NAME') ?: 'Resource Web Planner';
    $mail->setFrom($from, $fromName);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->addAddress($to);

    if (!$mail->send()) {
      return array('success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo);
    }

    return array('success' => true, 'message' => "Email sent successfully to $to");
  }
  public function send_OTC($to, $code) {
    $subject = "Your One-Time Code for Resource Planner";
    $message = "Your one-time code is: $code\nThis code will expire in 5 minutes.";
    return $this->send_email($to, $subject, $message);
  }

}
?>