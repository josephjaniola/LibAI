<?php
// Simple PHPMailer wrapper. Run `composer require phpmailer/phpmailer` to install.
class Mailer
{
    protected $mail;
    protected $usePhpMail = false;

    public function __construct()
    {
        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            $this->mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $this->mail->isSMTP();
            $this->mail->Host = MAIL_HOST;
            $this->mail->SMTPAuth = true;
            $this->mail->Username = MAIL_USERNAME;
            $this->mail->Password = MAIL_PASSWORD;
            $this->mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = MAIL_PORT;
            $this->mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            return;
        }

        // Fallback to native PHP mail() when PHPMailer is not installed.
        $this->usePhpMail = true;
    }

    public function send($to, $subject, $body)
    {
        if ($this->usePhpMail) {
            $headers = [];
            $headers[] = 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM_ADDRESS . '>';
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=UTF-8';
            return mail($to, $subject, $body, implode("\r\n", $headers));
        }

        $this->mail->addAddress($to);
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body = $body;
        return $this->mail->send();
    }
}
