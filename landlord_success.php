<?php
// landlord_success.php

// Get the first name from the URL
$first_name = htmlspecialchars($_GET['fname']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Success</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ECF0F6; /* Lightest shade */
            font-family: Arial, sans-serif;
        }

        .container {
            text-align: center;
            background-color: #BDCCDC; /* Light blue-gray */
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .message-box h1 {
            color: #142F48; /* Darkest navy blue */
            margin-bottom: 10px;
        }

        .message-box p {
            color: #47647F; /* Deep blue-gray */
            margin-bottom: 20px;
        }

        .loading {
            font-size: 48px; /* Increased size for loading dots */
            color: #8199B1; /* Muted medium blue */
        }

        .loading span {
            animation: blink 1s infinite;
        }

        .loading span:nth-child(1) {
            animation-delay: 0s;
        }
        .loading span:nth-child(2) {
            animation-delay: 0.2s;
        }
        .loading span:nth-child(3) {
            animation-delay: 0.4s;
        }
        .loading span:nth-child(4) {
            animation-delay: 0.6s;
        }

        @keyframes blink {
            0%, 20%, 100% {
                opacity: 0;
            }
            10%, 30% {
                opacity: 1;
            }
        }
    </style>
    <script>
        // Redirect to login_form.html after 3 seconds
        setTimeout(function() {
            window.location.href = "login_form.html";
        }, 3000);
    </script>
</head>
<body>
    <div class="container">
        <div class="message-box">
            <h1>Hello Landlord <?php echo $first_name; ?>!</h1>
            <p>You are successfully registered. Redirecting to login...</p>
            <div class="loading">
                <span>.</span>
                <span>.</span>
                <span>.</span>
                <span>.</span>
            </div>
        </div>
    </div>
</body>
</html>