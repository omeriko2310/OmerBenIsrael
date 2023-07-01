<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form fields and remove whitespace.
        $name = strip_tags(trim($_POST["name"]));
        $email = strip_tags(trim($_POST["email"]));
        $message = strip_tags(trim($_POST["message"]));

        // Check that data was sent to the mailer.
        if ( empty($name) OR empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Set a 400 (bad request) response code and exit.
            http_response_code(400);
            echo "Please complete the form and try again.";
            exit;
        }

        // Here you can write the logic for saving these details in your database or sending an email alert
        // For simplicity, just print the details here
        echo "Name: $name<br>";
        echo "Email: $email<br>";
        echo "Message: $message<br>";
        echo "Your details have been received!";
    } 
    else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }
?>
