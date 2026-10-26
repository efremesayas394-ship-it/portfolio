<?php
require_once 'db.php';

// Include PHPMailer classes
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode(["status" => "error", "message" => "Please fill in all fields."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email address."]);
        exit;
    }

    // 1. Save message into MySQL Database
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        
        // 2. Send Real Email Notification via Gmail SMTP
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'efremesayas394@gmail.com'; // Your email address
            $mail->Password   = 'lxysgsbfjlxgapdb'; // Paste your Google App Password here (no spaces)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('efremesayas394@gmail.com', 'Portfolio Contact');
            $mail->addAddress('efremesayas394@gmail.com', 'Efrem Esayas'); // Send notification to yourself
            $mail->addReplyTo($email, $name); // Let you reply directly to the sender

            // Content
            $mail->isHTML(true);
            $mail->Subject = "New Portfolio Message: " . $subject;
            $mail->Body    = "<h3>You have a new message from your portfolio website!</h3>" .
                             "<p><b>Name:</b> {$name}</p>" .
                             "<p><b>Email:</b> {$email}</p>" .
                             "<p><b>Subject:</b> {$subject}</p>" .
                             "<p><b>Message:</b><br>" . nl2br(htmlspecialchars($message)) . "</p>";

            $mail->send();
            
            echo json_encode(["status" => "success", "message" => "Thank you! Your message has been sent and notification dispatched."]);
        } catch (Exception $e) {
            // Message saved in DB, but email failed to dispatch
            echo json_encode(["status" => "success", "message" => "Message saved, but email notification failed: {$mail->ErrorInfo}"]);
        }

    } else {
        echo json_encode(["status" => "error", "message" => "Failed to save message. Please try again."]);
    }

    $stmt->close();
    $conn->close();
}
?>