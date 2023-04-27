const TopBlackBox = document.getElementById('black-box');
const header = document.getElementById('header');
const sizeIncrement = 20;
let squareSize = (sizeIncrement+60);
const letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
const blackboxes = [];
let revealedSquares = [];

function createSquare(letter) {
    const box = document.createElement('div');
    box.classList.add('box');
    box.style.width = squareSize + 'px';
    box.style.height = squareSize + 'px';
    box.style.backgroundColor = 'black';
    box.style.marginRight = 50 + 'px';
    box.style.marginTop = 20 + 'px';
    box.style.marginLeft = 70 + 'px';
    box.style.fontSize = (squareSize - 37 + 'px');
    box.style.display = 'flex';
    box.style.justifyContent = 'center';
    box.style.alignItems = 'center';
    box.innerText = letter;
    box.isRevealed = false;
    box.isMatched = false;
    box.onclick = function () {
        revealSquare(box);
    };
    return box;
}

function revealSquare(box) {
    if (revealedSquares.length < 2 && !box.isRevealed && !box.isMatched) {
        box.isRevealed = true;
        box.style.color = 'white';
        revealedSquares.push(box);
        if (revealedSquares.length === 2) {
            if (revealedSquares[0].innerText === revealedSquares[1].innerText) {
                revealedSquares[0].style.backgroundColor = '#97cba9';
                revealedSquares[0].isMatched = true;
                revealedSquares[1].style.backgroundColor = '#97cba9';
                revealedSquares[1].isMatched = true;
                revealedSquares = [];
            }
            else {
                setTimeout(hideSquares, 800);
            }
        }
    }
}
function hideSquares() {
    console.log(revealedSquares.length);
    revealedSquares.forEach(function (box) {
        box.isRevealed = false;
        box.style.color = 'black';
    });
    revealedSquares = [];
}

const Desktop = document.querySelector('#wrapper3');
const DeskWid = parseInt(getComputedStyle(Desktop).width);
TopBlackBox.onclick = function () {
    const MidCont = document.querySelector('#MidContainer');
    const yellow = document.querySelector('.bottom-yellow');
    const blue = document.querySelector('.right-blue');
    const grey = document.querySelector('.left-grey');
    const footer = document.querySelector('#footer3');
    if(DeskWid >= 1400){
        let yellowPosition = 1708;
        let blueHeight = 1708;
        let greyHeight = 1708;
        let footerPosition = 2300;
        footer.style.top = footerPosition + 'px';
        yellow.style.top = yellowPosition + 'px';
        blue.style.height = blueHeight + 'px';
        grey.style.height = greyHeight + 'px';
    }
    else if (DeskWid < 1400) { // check if the desktop width is greater than 1400px
        yellowPosition = 1800; // change the values of yellowPosition, blueHeight, greyHeight, and footerPosition
        footerPosition = 2300;
        footer.style.top = footerPosition+1000 + 'px';
        yellow.style.top = yellowPosition +1000 + 'px';
    }
    if (blackboxes.length >= 12) {
        alert('You have reached the maximum number of boxes.');
        return;
    }
    for (let i = 0; i < 3; i++) {
        const letter = letters[Math.floor(Math.random() * letters.length)];
        const box = createSquare(letter);
        blackboxes.push(box);
        MidCont.appendChild(box);
        squareSize += sizeIncrement;
    }
};










