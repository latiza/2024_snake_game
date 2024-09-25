// Canvas elem kiválasztása és 2D contextualise megszerzése
const canvas = document.querySelector('canvas');
const ctx = canvas.getContext('2d');

// Beállítjuk a canvas méreteit
canvas.width = 1000;
canvas.height = 500;
canvas.style.width = '1000px';
canvas.style.height = '500px';
canvas.style.marginLeft = '50px';
canvas.style.border = '3px solid #000';
canvas.style.boxShadow = '0 0 30px black';

// A cellák mérete az új canvas méretekhez igazítva
const CELL_SIZE = 25;
const WORLD_WIDTH = Math.floor(canvas.width / CELL_SIZE);
const WORLD_HEIGHT = Math.floor(canvas.height / CELL_SIZE);
const MOVE_INTERVAL = 300;
const FOOD_SPAWN_INTERVAL = 1500;

let input = {};
let snake;
let foods;
let foodSpawnElapsed;
let gameOver;
let score;

// Inicializálja a játékot
function reset() {
    input = {};
    snake = {
        moveElapsed: 0,
        length: 4,
        parts: [{ x: Math.floor(WORLD_WIDTH / 2), y: Math.floor(WORLD_HEIGHT / 2) }],
        dir: null,
        newDir: { x: 1, y: 0 }
    };
    foods = [];
    foodSpawnElapsed = 0;
    gameOver = false;
    score = 0;
    saveGameState(); // Mentse el az állapotot
}

// Ételtípusok és képek betöltése
const foodTypes = [
    { type: 'apple', points: 1, image: new Image() },
    { type: 'banana', points: 2, image: new Image() },
    { type: 'cherry', points: 3, image: new Image() }
];
foodTypes[0].image.src = 'img/apple.png';
foodTypes[1].image.src = 'img/banana.svg';
foodTypes[2].image.src = 'img/cherry.png';

// Kört rajzoló függvény
function fillCircle(x, y, radius) {
    ctx.beginPath();
    ctx.arc(x + radius, y + radius, radius, 0, Math.PI * 2);
    ctx.fill();
}

// Kígyó mozgásának frissítése
function update(delta) {
    if (gameOver) {
        if (input[' '] && !input['keydown']) {
            reset();
        }
        return;
    }

    if (input.ArrowLeft && snake.dir.x !== 1) {
        snake.newDir = { x: -1, y: 0 };
    } else if (input.ArrowUp && snake.dir.y !== 1) {
        snake.newDir = { x: 0, y: -1 };
    } else if (input.ArrowRight && snake.dir.x !== -1) {
        snake.newDir = { x: 1, y: 0 };
    } else if (input.ArrowDown && snake.dir.y !== -1) {
        snake.newDir = { x: 0, y: 1 };
    }

    snake.moveElapsed += delta;
    if (snake.moveElapsed > MOVE_INTERVAL) {
        snake.dir = snake.newDir;
        snake.moveElapsed -= MOVE_INTERVAL;
        const newSnakePart = {
            x: snake.parts[0].x + snake.dir.x,
            y: snake.parts[0].y + snake.dir.y
        };
        snake.parts.unshift(newSnakePart);

        if (snake.parts.length > snake.length) {
            snake.parts.pop();
        }

        const head = snake.parts[0];
        const foodEatenIndex = foods.findIndex(f => f.x === head.x && f.y === head.y);
        if (foodEatenIndex >= 0) {
            const eatenFood = foods[foodEatenIndex];
            snake.length++;
            score += eatenFood.foodType.points;
            document.getElementById("pont").innerHTML = "Eredmény: " + score;
            foods.splice(foodEatenIndex, 1);
        }

        const worldEdgeIntersect = head.x < 0 || head.x >= WORLD_WIDTH || head.y < 0 || head.y >= WORLD_HEIGHT;
        if (worldEdgeIntersect) {
            gameOver = true;
            return;
        }

        const snakeartIntersect = snake.parts.some((part, index) => index !== 0 && head.x === part.x && head.y === part.y);
        if (snakeartIntersect) {
            gameOver = true;
            return;
        }
    }

    foodSpawnElapsed += delta;
    if (foodSpawnElapsed > FOOD_SPAWN_INTERVAL) {
        foodSpawnElapsed -= FOOD_SPAWN_INTERVAL;
        spawnFood();
    }
}

// Új étel megjelenítése
function spawnFood() {
    const randomFood = foodTypes[Math.floor(Math.random() * foodTypes.length)];
    foods.push({
        x: Math.floor(Math.random() * WORLD_WIDTH),
        y: Math.floor(Math.random() * WORLD_HEIGHT),
        foodType: randomFood
    });
}

// Étel kirajzolása
function renderFood(food) {
    if (food && food.foodType && food.foodType.image) {
        ctx.drawImage(food.foodType.image, food.x * CELL_SIZE, food.y * CELL_SIZE, CELL_SIZE, CELL_SIZE);
    } else {
        console.error("Food or food image is undefined!");
    }
}

// Játék jelenlegi állapotának kirajzolása
function render() {
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    snake.parts.forEach(({ x, y }, index) => {
        ctx.fillStyle = index === 0 ? 'limegreen' : 'darkgreen';
        fillCircle(x * CELL_SIZE, y * CELL_SIZE, CELL_SIZE / 2);
    });

    if (snake.parts.length > 0) {
        const head = snake.parts[0];
        ctx.fillStyle = 'black';
        ctx.beginPath();
        ctx.arc(head.x * CELL_SIZE + CELL_SIZE / 4, head.y * CELL_SIZE + CELL_SIZE / 4, CELL_SIZE / 8, 0, Math.PI * 2);
        ctx.fill();
        ctx.beginPath();
        ctx.arc(head.x * CELL_SIZE + 3 * CELL_SIZE / 4, head.y * CELL_SIZE + CELL_SIZE / 4, CELL_SIZE / 8, 0, Math.PI * 2);
        ctx.fill();
    }

    foods.forEach(renderFood);

    ctx.fillStyle = 'green';
    ctx.font = '20px Arial';
    if (gameOver) {
        ctx.fillStyle = 'red';
        ctx.font = '60px Arial';
        ctx.fillText('VÉGE!', canvas.width / 2, canvas.height / 2);
        ctx.fillStyle = 'black';
        ctx.font = '20px Arial';
        ctx.fillText('Nyomd meg a szóközt az újrakezdéshez!', canvas.width / 2, canvas.height / 2 + 40);
    } else {
        ctx.fillStyle = 'black';
        ctx.font = '20px Arial';
        ctx.fillText(`Eredmény: ${score}`, canvas.width / 2, 20);
    }
}

let lastLoopTime = Date.now();

// A játék futtatásáért felelős függvény
function gameLoop() {
    const now = Date.now();
    const delta = now - lastLoopTime;
    lastLoopTime = now;
    update(delta);
    render();
    window.requestAnimationFrame(gameLoop);
}

// Állapot mentése
function saveGameState() {
    const gameState = {
        score: score,
        position: snake.parts,
        direction: snake.dir
    };

    // Mentés a localStorage-ba
    localStorage.setItem('gameState', JSON.stringify(gameState));
    console.log('Game state saved to localStorage:', gameState);

    // Mentés a backendre
    fetch('backend/game_state.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            'data': JSON.stringify(gameState)
        })
    })
    .then(response => response.text())
    .then(result => {
        console.log('Game state saved to backend:', result);
    })
    .catch(error => {
        console.error('Error saving game state to backend:', error);
    });
}

// Állapot betöltése
function loadGameState() {
    // Először próbáljuk meg betölteni az állapotot a localStorage-ból
    const savedState = localStorage.getItem('gameState');
    if (savedState) {
        const gameState = JSON.parse(savedState);
        score = gameState.score;
        snake.parts = gameState.position;
        snake.dir = gameState.direction;
        console.log('Game state loaded from localStorage:', gameState);
        return;
    }

    // Ha nincs állapot a localStorage-ban, próbáljuk meg betölteni a backendről
    fetch('backend/game_state.php')
    .then(response => response.json())
    .then(data => {
        if (data === 'no_data') {
            console.log('No game state data found on the backend.');
            return;
        }
        // Betöltjük az adatokat, ha vannak
        score = data.score;
        snake.parts = JSON.parse(data.position);
        snake.dir = JSON.parse(data.direction);
        console.log('Game state loaded from backend:', data);
    })
    .catch(error => {
        console.error('Error loading game state from backend:', error);
    });
}

window.addEventListener('beforeunload', function (e) {
    // Ellenőrizzük, hogy van-e változás, ami mentésre szorul
    if (hasUnsavedChanges()) {
        // Az előugró párbeszéd csak modern böngészőkben működik
        e.preventDefault();
        e.returnValue = ''; // Chrome, Firefox és egyes böngészők által megkövetelt
        // Megkérdezzük a felhasználót, hogy szeretné-e menteni
        const confirmSave = confirm("A változtatásokat nem mentette el. Biztosan el akarja hagyni az oldalt?");
        if (confirmSave) {
            saveGameData(); // A mentési funkció meghívása
        }
    }
});

// Állapot betöltése az oldal betöltésekor
window.addEventListener('load', loadGameState);


// Eseménykezelők beállítása
window.addEventListener('keydown', (e) => {
    input[e.key] = true;
});
window.addEventListener('keyup', (e) => {
    input[e.key] = false;
});
window.addEventListener('beforeunload', saveGameState);
window.addEventListener('load', loadGameState);

// Játék inicializálása és futtatása
reset();
gameLoop();

/**
 * Saves the game state to the server.
 * 
 * @returns {Promise} - A promise that resolves with the server response or rejects with an error.
 */
