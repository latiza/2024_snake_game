<?php /** @noinspection ALL */
// Include database connection
include '../connect.php';
session_start();
// Példa: Bejelentkezés után
$_SESSION['user_id'] = $userId;
// Function to sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}

// Start session
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $action = isset($_POST['action']) ? $_POST['action'] : 'new';

    // Validate input data
    if (empty($email) || empty($password)) {
        die("Minden mezőt ki kell tölteni!");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Érvénytelen e-mail cím!");
    }

    // Prepare and execute SQL statement
    $stmt = $dbconn->prepare("SELECT id, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $password_hash);

    if ($stmt->num_rows === 1) {
        $stmt->fetch();
        if (password_verify($password, $password_hash)) {
            // Start session and store user ID
            $_SESSION['user_id'] = $user_id;
            $_SESSION['new_game'] = ($action === 'new'); // Set new game flag
            header("Location: ../game.php");
        } else {
            echo "Hibás jelszó!";
        }
    } else {
        echo "Nincs ilyen felhasználó!";
    }

    // Close statement and connection
    $stmt->close();
    $dbconn->close();
} else {
    die("Hibás kérés!");
}
