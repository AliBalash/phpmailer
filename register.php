<?php
require 'vendor/autoload.php';

use Spatie\Async\Pool;
use PHPMailer\PHPMailer\PHPMailer;

// Replace these values with your MySQL server credentials
$host = "localhost";
$username = "root";
$password = "alihhhb1999@gmail.com";
$database = "phpmailer";

// Establish the database connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Process the registration form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute the SQL query to insert data into the database
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";

    if (mysqli_query($conn, $sql)) {
        // Registration successful, send email asynchronously
        echo "Registration successful!";

        sendEmailInBackground($email, $username);
        
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);

// Function to send email asynchronously
function sendEmailInBackground($recipient, $username)
{
    $subject = "Welcome to our website";
    $message = "Dear " . $username . ",\n\nThank you for registering on our website.";

    // Create an async pool
    $pool = Pool::create();

    // Add the email sending task to the pool
    $pool->add(function () use ($recipient, $subject, $message) {
        $mail = new PHPMailer(true);

        // Email configuration (adjust this according to your email setup)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server hostname
        $mail->SMTPAuth = true;
        $mail->Username = 'alihhhb1999@gmail.com';
        $mail->Password = 'nzueksqlvtneouhn';
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';

        // Set email parameters
        $mail->setFrom('alihhhb1999@gmail.com', 'Your Website');
        $mail->addAddress($recipient);
        $mail->Subject = $subject;
        $mail->Body = $message;

        // Send the email
        try {
            $mail->send();
        } catch (Exception $e) {
            // Log any errors or handle them as needed
            echo "ERRRRORRRR";
            var_dump($e);
        }
    });

    // Execute the pool asynchronously
    $pool->wait();
}














<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Pheanstalk\Pheanstalk;

// Beanstalkd server configuration
$queueServer = "127.0.0.1"; // Replace with your Beanstalkd server IP/hostname
$queuePort = 11300; // Replace with your Beanstalkd server port

// Connect to Beanstalkd server
$beanstalk = new Pheanstalk($queueServer, $queuePort);

// Function to send email using PHPMailer
function sendEmail($recipient, $subject, $message)
{
    $mail = new PHPMailer(true);

    // Email configuration (adjust this according to your email setup)
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // SMTP server hostname
    $mail->SMTPAuth = true;
    $mail->Username = 'alihhhb1999@gmail.com';
    $mail->Password = 'nzueksqlvtneouhn';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls'; 

    // Set email parameters
    $mail->setFrom('alihhhb1999@gmail.com', 'Your Website');
    $mail->addAddress($recipient);
    $mail->Subject = $subject;
    $mail->Body = $message;

    // Send the email
    try {
        $mail->send();
    } catch (Exception $e) {
        var_dump($e);
        // Log any errors or handle them as needed
    }
}

// Worker loop
while (true) {
    // Reserve a job from the "email" tube (queue)
    $job = $beanstalk->watch('email')->ignore('default')->reserve();

    // Get the email data from the job payload
    $data = json_decode($job->getData(), true);

    // Send the email
    sendEmail($data['recipient'], $data['subject'], $data['message']);

    // Delete the job from the queue
    $beanstalk->delete($job);
}

