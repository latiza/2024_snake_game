<?php
//telepítsük: composer require vlucas/phpdotenv ez létre hoz egy vendor mappát
//majd hozzuk létre a .env fájlt a gyökér könyvtárban
//.gitignore létrehozása .env tartalommal 
/**a .env fájlt a .gitignore fájlhoz. Ez megakadályozza, hogy a .env fájl a Git verziókezelésébe kerüljön.  */
// Load Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables from the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// HTTP fejléc beállítása a megfelelő tartalomtípussal és karakterkódolással
header("Content-Type: text/html; charset=utf-8");

// Adatbázis kapcsolódáshoz szükséges adatok definíciója
define("DBHOST", $_ENV['DBHOST']);
define("DBUSER", $_ENV['DBUSER']);
define("DBPASS", $_ENV['DBPASS']);
define("DBNAME", $_ENV['DBNAME']);

// Adatbázis kapcsolat létrehozása, és az esetleges hibák kezelése
$dbconn = @mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME) or die("Hiba az adatbázis csatlakozásakor!");

// Karakterkódolás beállítása az adatbáziskapcsolaton keresztül
mysqli_query($dbconn, "SET NAMES utf8");
