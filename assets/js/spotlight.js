const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let spotlightColor = getComputedStyle(document.documentElement).getPropertyValue('--main-color');

class Spotlight {
    constructor(x, y, radius, alpha, targetX, targetY) {
        this.x = x;
        this.y = y;
        this.radius = radius;
        this.alpha = alpha;
        this.targetX = targetX;
        this.targetY = targetY;
        this.gradient = this.createGradient();
        this.speed = 0.5;
    }

    createGradient() {
        const gradient = ctx.createRadialGradient(this.x, this.y, 0, this.x, this.y, this.radius);
        gradient.addColorStop(0, `rgba(${hexToRgb(spotlightColor)}, ${this.alpha})`);
        gradient.addColorStop(1, 'rgba(0, 0, 0, 0)');
        return gradient;
    }

    update() {
        const dx = this.targetX - this.x;
        const dy = this.targetY - this.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        if (distance > this.speed) {
            this.x += (dx / distance) * this.speed;
            this.y += (dy / distance) * this.speed;
        } else {
            this.targetX = Math.random() * canvas.width;
            this.targetY = Math.random() * canvas.height;
        }

        this.alpha = Math.sin(Date.now() / 1000) * 0.05 + 0.1;
        this.gradient = this.createGradient();
    }

    draw() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.radius, 0, 2 * Math.PI);
        ctx.fillStyle = this.gradient;
        ctx.fill();
    }
}

function hexToRgb(hex) {
    const shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, (m, r, g, b) => r + r + g + g + b + b);
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? `${parseInt(result[1], 16)}, ${parseInt(result[2], 16)}, ${parseInt(result[3], 16)}` : null;
}

const spotlights = [];

function createSpotlight() {
    const x = Math.random() * canvas.width;
    const y = Math.random() * canvas.height;
    const radius = Math.random() * 800 + 800;
    const alpha = 1.0;
    const targetX = Math.random() * canvas.width;
    const targetY = Math.random() * canvas.height;
    spotlights.push(new Spotlight(x, y, radius, alpha, targetX, targetY));
}

function drawSpotlights() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    for (const spotlight of spotlights) {
        spotlight.update();
        spotlight.draw();
    }
}

function animate() {
    drawSpotlights();
    requestAnimationFrame(animate);
}

for (let i = 0; i < 2; i++) {
    createSpotlight();
}

animate();

window.addEventListener('resize', () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    spotlights.length = 0;
    for (let i = 0; i < 2; i++) {
        createSpotlight();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const colorOptions = document.querySelectorAll('.color-option');
    const root = document.documentElement;

    const savedColor = localStorage.getItem('main-color');
    if (savedColor) {
        root.style.setProperty('--main-color', savedColor);
        spotlightColor = savedColor;
    }

    colorOptions.forEach(option => {
        option.addEventListener('click', () => {
            const newColor = option.getAttribute('data-color');
            root.style.setProperty('--main-color', newColor);
            localStorage.setItem('main-color', newColor);
            spotlightColor = newColor;
        });
    });
});