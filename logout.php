<?php
session_start(); // Start the session

// Clear all session variables
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to login page
header("Location: login_form.html"); // Change to your login page
exit();
?>