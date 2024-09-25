<?php /** @noinspection ALL */
/** @noinspection ALL */
/** @noinspection ALL */
/** @noinspection ALL */
session_start(); // Ülés kezelés indítása

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    /** @noinspection PhpComposerExtensionStubsInspection */
    echo json_encode(['success' => false, 'message' => 'Felhasználó nincs bejelentkezve.']);
    exit;
}

// Kérjük az adatokat a POST kérésből
/** @noinspection PhpComposerExtensionStubsInspection */
$data = json_decode(file_get_contents('php://input'), true);

// Ellenőrizzük, hogy az adatokat megkaptuk-e
if (isset($data['score']) && isset($data['snake']) && isset($data['foods'])) {
    // Itt lehet adatbázisba menteni az állapotot
    // Például használhatunk egy adatbázis lekérdezést:
    /*
    $stmt = $pdo->prepare('INSERT INTO game_states (user_id, score, snake, foods) VALUES (?, ?, ?, ?)');
    $stmt->execute([$_SESSION['user_id'], $data['score'], json_encode($data['snake']), json_encode($data['foods'])]);
    */

    /** @noinspection PhpComposerExtensionStubsInspection */
    echo json_encode(['success' => true, 'message' => 'Játék állapot mentve.']);
} else {
    /** @noinspection PhpComposerExtensionStubsInspection */
    echo json_encode(['success' => false, 'message' => 'Hiányzó adatok.']);
}

