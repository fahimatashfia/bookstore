<?php


session_start();
header("Content-Type: application/json");

// Database connection
$conn = new mysqli("localhost", "root", "Fahima@123", "bookstore");

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';


if ($username === '' || $email === '' || $password === '') {
    echo json_encode([
        "success" => false,
        "message" => "All fields are required"
    ]);
    exit;
}

// Prepare SQL (username + email check)
$sql = "SELECT id, username, email, password 
        FROM users 
        WHERE username = ? AND email = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {

        // Store session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "user_id" => $user['id'],
            "username" => $user['username'],
            "email" => $user['email']
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Incorrect password"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "User not found"
    ]);
}

$stmt->close();
$conn->close();