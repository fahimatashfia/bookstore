<?php
session_start();

// Remove user from session if exists
if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
}

// Redirect back to homepage
header("Location: ../index.html");
exit();