<?php 
// connect.php tartalmazza a DB kapcsolati beállításokat
include'../connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Játék állapot lekérése POST adatokból
    $data = json_decode($_POST['data'], true);
    $userId = $_SESSION['user_id'];
    $score = $data['score'];
    $position = json_encode($data['position']); // JSON-ként mentjük a pozíciót
    $direction = $data['direction'];

    // SQL lekérdezés előkészítése
    $stmt = $dbconn->prepare("INSERT INTO game_state (user_id, score, position, direction) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('isss', $userId, $score, $position, $direction);

    // Lekérdezés végrehajtása
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    // Kapcsolat lezárása
    $stmt->close();
    $dbconn->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Legutolsó játék állapot lekérése az utolsó bejegyzés alapján
    $query = "SELECT * FROM game_state WHERE user_id = ? ORDER BY id DESC LIMIT 1";
    $stmt = $dbconn->prepare($query);
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo 'no_data';
    }

    $stmt->close();
    $dbconn->close();
}
