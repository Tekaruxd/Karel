<?php
session_start();

if (!isset($_SESSION['field']) || !isset($_SESSION['karel'])) {
    init_game();
}

if (isset($_POST['command'])) {
    $command = strtoupper(trim($_POST['command']));
    if (!empty($command)) {
        process_command($command);
    }
}

function init_game() {
    $_SESSION['field'] = array_fill(0, 8, array_fill(0, 8, ''));
    $_SESSION['karel'] = [
        'x' => 0,
        'y' => 0,
        'direction' => 'DOWN'
    ];
    $_SESSION['commands'] = [];
}

function process_command($command) {
    $parts = explode(' ', $command);
    $cmd = $parts[0];
    $param = isset($parts[1]) ? $parts[1] : '';
    
    $_SESSION['commands'][] = $command;

    switch ($cmd) {
        case 'MOVE':
            $steps = empty($param) ? 1 : intval($param);
            move($steps);
            break;
        case 'TURNLEFT':
            $times = empty($param) ? 1 : intval($param);
            turn_left($times);
            break;
        case 'PLACE':
            place($param);
            break;
        case 'RESET':
            init_game();
            break;
    }
}

function move($steps) {
    for ($i = 0; $i < $steps; $i++) {
        switch ($_SESSION['karel']['direction']) {
            case 'RIGHT':
                if ($_SESSION['karel']['x'] < 7) $_SESSION['karel']['x']++;
                break;
            case 'LEFT':
                if ($_SESSION['karel']['x'] > 0) $_SESSION['karel']['x']--;
                break;
            case 'UP':
                if ($_SESSION['karel']['y'] > 0) $_SESSION['karel']['y']--;
                break;
            case 'DOWN':
                if ($_SESSION['karel']['y'] < 7) $_SESSION['karel']['y']++;
                break;
        }
    }
}

function turn_left($times = 1) {
    $directions = ['UP', 'LEFT', 'DOWN', 'RIGHT'];
    $currentIndex = array_search($_SESSION['karel']['direction'], $directions);
    
    for ($i = 0; $i < $times; $i++) {
        $currentIndex = ($currentIndex + 1) % 4;
    }
    
    $_SESSION['karel']['direction'] = $directions[$currentIndex];
}

function place($color) {
    $_SESSION['field'][$_SESSION['karel']['y']][$_SESSION['karel']['x']] = $color;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karel</title>
    <style>
        .grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            grid-template-rows: repeat(8, 1fr);
            width: 512px;
            height: 512px;
            border: 1px solid black;
            background-color: white;
        }
        button {
            padding: 10px 20px;
            margin: 5px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        input {
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: calc(100% - 22px);
        }
        form {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            background: white;
        }
        .cell {
            border: 1px solid black;
            background-color: white;
            padding: 10px;
        }
        .user {
            width: 512px;
            height: 512px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid black;
        }
        .container {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }
        body {
            background: #aaaaaa;
            font-family: sans-serif;
        }
        .karel {
            background: red;
        }
        .tooltip {
            position: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            border: 1px solid black;
            margin: 1rem;
            padding: 1rem;
        }
        .chat {
            flex-grow: 1;
            overflow-y: auto;
            padding: 10px;
        }
        .print {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="user">
            <div class="chat">
                <?php foreach ($_SESSION['commands'] as $cmd): ?>
                    <p class="print"><?php echo htmlspecialchars($cmd); ?></p>
                <?php endforeach; ?>
            </div>
            <div class="input">
                <form method="post">
                    <input type="text" name="command" id="input" autofocus>
                    <button type="submit" id="send">Send</button>
                    <button type="submit" name="execute" value="1" id="execute">Execute</button>
                    <button type="submit" name="command" value="RESET" id="reset">Reset</button>
                </form>
            </div>
        </div>
        <div class="window">
            <div class="grid">
                <?php for ($y = 0; $y < 8; $y++): ?>
                    <?php for ($x = 0; $x < 8; $x++): ?>
                        <div class="cell<?php 
                            echo ($x == $_SESSION['karel']['x'] && $y == $_SESSION['karel']['y']) ? ' karel' : '';
                        ?>" id="cell<?php echo $y * 8 + $x; ?>" 
                        <?php if (!empty($_SESSION['field'][$y][$x])): ?>
                            style="background-color: <?php echo htmlspecialchars($_SESSION['field'][$y][$x]); ?>"
                        <?php endif; ?>
                        ></div>
                    <?php endfor; ?>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <div class="tooltip">
        <p>
            MOVE - Karel se v poli posune o tolik míst ve svém směru natočení, kolik
            mu je za příkazem určeno (např: MOVE 4). V případě, že se parametr
            neuvede, Karel provede jeden krok.
        </p>
        <p>
            TURNLEFT - Karel se otočí vlevo. Opět můžeme zadat parametrem, kolikrát
            se tato operace provede.
        </p>
        <p>
            PLACE - Na pozici Karla se položí parametr příkazu (např. PLACE RED).
        </p>
        <p>RESET - nastaví Karla do levého horního místa.</p>
        <p>
            Příkazy je možné zadávat jak velkými, tak i malými písmeny abecedy.
            Každý příkaz musí být na nové řádce.
        </p>
    </div>
</body>
</html>