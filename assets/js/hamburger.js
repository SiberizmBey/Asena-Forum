const hamburgerMenu = document.getElementById('hamburger-menu');
const menu = document.getElementById('menu');
const hamburgerIcon = document.getElementById('hamburger-icon');

hamburgerMenu.addEventListener('click', () => {
    menu.classList.toggle('open');
    if (menu.classList.contains('open')) {
        hamburgerIcon.classList.remove('fa-bars');
        hamburgerIcon.classList.add('fa-times');
    } else {
        hamburgerIcon.classList.remove('fa-times');
        hamburgerIcon.classList.add('fa-bars');
    }
});

document.addEventListener('click', (event) => {
    const targetElement = event.target;
    if (!targetElement.closest('.menu') && !targetElement.closest('.hamburger-menu')) {
        menu.classList.remove('open');
        hamburgerIcon.classList.remove('fa-times');
        hamburgerIcon.classList.add('fa-bars');
    }
});