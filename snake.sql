-- Adatbázis létrehozása (ha még nem létezik)
CREATE DATABASE IF NOT EXISTS snake_game;
USE snake_game;

-- Felhasználók tábla létrehozása (ha még nem létezik)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- Játék állapot tábla létrehozása (ha még nem létezik)
CREATE TABLE IF NOT EXISTS game_state (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  score INT DEFAULT 0,
  position TEXT, -- JSON formátumban tároljuk a kígyó pozícióját
  direction VARCHAR(10) DEFAULT 'right', -- A kígyó aktuális iránya
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;
