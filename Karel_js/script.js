var input_number = 0;
var direction = "DOWN";
function GenerateGrid() {
  const grid = document.querySelector(".grid");
  for (let i = 0; i < 64; i++) {
    const cell = document.createElement("div");
    cell.classList.add("cell");
    cell.id = `cell${i}`;
    grid.appendChild(cell);
  }
  document.getElementById("cell0").classList.add("karel");
}

function Move_karel(direction) {
  const karel = document.querySelector(".karel");
  const currentId = Number(karel.id.slice(4));
  const nextId = (() => {
    switch (direction) {
      case "UP":
        return currentId - 8;
      case "DOWN":
        return currentId + 8;
      case "LEFT":
        return currentId - 1;
      case "RIGHT":
        return currentId + 1;
      case "RESET":
        return 0;
      default:
        return currentId;
    }
  })();
  if (nextId < 0 || nextId > 63) return;
  karel.classList.remove("karel");
  document.getElementById(`cell${nextId}`).classList.add("karel");
}

function Turn_karel(turn_direction, value) {
  switch (turn_direction) {
    case "UP":
      direction = "LEFT";
      break;
    case "DOWN":
      direction = "RIGHT";
      break;
    case "LEFT":
      direction = "DOWN";
      break;
    case "RIGHT":
      direction = "UP";
      break;
    case "RESET":
      direction = "DOWN";
      break;
    default:
      break;
  }
}

function Place_karel(color) {
  const cell = document.querySelector(".karel");
  // cell.classList.add("place");
  cell.style.backgroundColor = color;
}

function Execute() {
  const inputs = document.querySelectorAll(".print");
  inputs.forEach((input) => {
    var [command, value] = input.textContent.split(" ");
    command = command.toUpperCase();
    switch (command) {
      case "MOVE":
        if (value == "") {
          value = 1;
        }
        for (let i = 0; i < value; i++) {
          Move_karel(direction);
        }
        break;
      case "PLACE":
        Place_karel(value);
        break;

      case "TURNLEFT":
        if (value == "") {
          value = 1;
        }
        for (let i = 0; i < value; i++) {
          Turn_karel(direction);
        }
        break;

      case "RESET":
        Move_karel("RESET");
        break;
      default:
        break;
    }
  });
}

function Print_input() {
  const input = document.getElementById("input").value;
  const print = document.createElement("p");
  print.textContent = input;
  print.id = "print" + input_number;
  print.classList.add("print");
  document.getElementById("input").value = "";
  input_number++;
  document.querySelector(".chat").appendChild(print);
}

function Reset() {
  input_number = 0;
  Turn_karel("RESET");
  document.getElementById("input").value = "";
  document.querySelector(".grid").innerHTML = "";
  GenerateGrid();
  Move_karel("RESET");
  document.querySelector(".chat").innerHTML = "";
}

GenerateGrid();
document.getElementById("send").addEventListener("click", Print_input);
document.getElementById("execute").addEventListener("click", Execute);
document.getElementById("reset").addEventListener("click", Reset);
