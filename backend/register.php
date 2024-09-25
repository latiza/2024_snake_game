<?php /** @noinspection ALL */
// Include database connection
include '../connect.php';

// Function to sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);

    // Validate input data
    if (empty($name) || empty($email) || empty($password)) {
        die("Minden mezőt ki kell tölteni!");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Érvénytelen e-mail cím!");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute SQL statement
    $stmt = $dbconn->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashedPassword);

    if ($stmt->execute()) {
        header("Location: ../login.html");
        exit();
    } else {
        echo "Hiba történt a regisztráció során. Kérjük, próbálja meg később!";
    }

    // Close statement and connection
    $stmt->close();
    $dbconn->close();
} else {
    die("Hibás kérés!");
}
