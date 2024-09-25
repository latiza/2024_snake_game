<?php /** @noinspection PhpComposerExtensionStubsInspection */
/** @noinspection ALL */
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
session_start(); // Ülés indítása

if (!isset($_SESSION['user_id'])) {
    // Ha a felhasználó nincs bejelentkezve, átirányítás a bejelentkezési oldalra
    header("Location: login.html");
    exit();
}

// Ellenőrizzük, hogy új játékot kell-e kezdeni vagy a régi adatokat betölteni
$newGame = isset($_SESSION['new_game']) ? $_SESSION['new_game'] : true;

// Töröljük az állapotot az új játék indításához
if ($newGame) {
    unset($_SESSION['snakeGameState']);
    $_SESSION['new_game'] = false; // Reset the new game flag
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title>Etesd meg a kígyót</title>
</head>

<body>
    <div class="wrapper">
        <canvas></canvas>
        <div id="pont">Eredmény: 0</div>
        <button id="saveButton">Eredmény mentése</button>
        <button id="logout">Kilépés</button> <!-- Kilépés gomb -->
    </div>
    <script src="js01.js"></script>
    <script>
        document.getElementById('saveButton').addEventListener('click', function() {
    const gameData = {
        score: getScore(), // A játék aktuális pontszáma
        position: getPosition(), // A játék aktuális pozíciója
        direction: getDirection() // A játék aktuális iránya
    };

    fetch('backend/game_state.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `data=${encodeURIComponent(JSON.stringify(gameData))}`
    }).then(response => response.text())
      .then(result => {
          if (result === 'success') {
              alert('Eredmény sikeresen mentve!');
          } else {
              alert('Hiba történt az eredmény mentése közben.');
          }
      })
      .catch(error => {
          console.error('Error:', error);
          alert('Hiba történt az eredmény mentése közben.');
      });
});

        // Kilépés kezelése
        document.getElementById('logout').addEventListener('click', () => {
            window.location.href = 'login.html'; // Kilépés és visszatérés a bejelentkezési oldalra
        });

        // Betöltjük a játék állapotát, ha nem új játékot kezdünk
        window.addEventListener('load', () => {
            if (!<?php echo json_encode($newGame); ?>) {
                // Ha nem új játékot kezdünk, betöltjük a régi adatokat
                // Itt lehetőséget kell adni a játék scriptjének a régi állapotok betöltésére
            }
        });
    </script>
</body>

</html>
