<?php
class MailingService {
  public function send_email($to, $subject, $message) {
    if (!mail($to, $subject, $message)) {
      return array('success' => false, 'message' => 'Failed to send email');
    }
    // echo "Sending email to: $to\nSubject: $subject\nMessage: $message\n";
    return array('success' => true, 'message' => 'Email sent successfully');
  }
  public function send_OTC($to, $code) {
    $subject = "Your One-Time Code for Resource Planner";
    $message = "Your one-time code is: $code\nThis code will expire in 5 minutes.";
    return $this->send_email($to, $subject, $message);
  }
}
?>