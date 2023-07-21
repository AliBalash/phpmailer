<?php
// Thread.php

class ThreadEmail extends Thread
{
    private $toEmail;
    private $username;

    public function __construct($toEmail, $username)
    {
        $this->toEmail = $toEmail;
        $this->username = $username;
    }

    public function run()
    {
        // Include PHPMailer
        require 'vendor/autoload.php';

        // Function to send the registration email
        function sendRegistrationEmail($toEmail, $username)
        {
            // The same function defined in process_registration.php
            // Copy it here or include the process_registration.php file.

            // For clarity, let's assume you have the function definition here:
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            // Configure PHPMailer settings (SMTP, From, etc.)

            // Rest of the function to send the registration email...
        }

        // Call the function to send the email
        sendRegistrationEmail($this->toEmail, $this->username);
    }
}
?>
