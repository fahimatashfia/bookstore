<?php
ini_set('display_errors',1);
error_reporting(E_ALL);


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

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Basic validation
if ($username === '' || $email === '' || $password === '') {
    echo json_encode([
        "success" => false,
        "message" => "All fields are required"
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email format"
    ]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode([
        "success" => false,
        "message" => "Password must be at least 6 characters"
    ]);
    exit;
}

// Check if username or email already exists
$checkSql = "SELECT id FROM users WHERE username = ? OR email = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("ss", $username, $email);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Username or Email already exists"
    ]);
    $checkStmt->close();
    exit;
}
$checkStmt->close();

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$insertSql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param("sss", $username, $email, $hashedPassword);

if ($insertStmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "User registered successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Signup failed. Try again"
    ]);
}

$insertStmt->close();
$conn->close();