const navbar = document.querySelector('.navbar');
const hammen = document.querySelector('.tabBar');
const logo = document.querySelector('.logo');
const sbar = document.querySelector('.search-bar');
const cp = document.querySelector('.color-picker');
const opbar = document.querySelector('#toggle-menu-btn');
const mbar = document.querySelector('.message-bar');
const toggleNavbarBtn = document.querySelector('#toggle-navbar-btn');

toggleNavbarBtn.addEventListener('click', () => {
    navbar.classList.toggle('hidden');
    if (navbar.classList.contains('hidden')) {
        toggleNavbarBtn.textContent = "Yüzen Adaları Göster";
    } else {
        toggleNavbarBtn.textContent = "Yüzen Adaları Gizle";
    }

    sbar.classList.toggle('hidden');
    if (sbar.classList.contains('hidden')) {
        toggleNavbarBtn.textContent = "Yüzen Adaları Göster";
    } else {
        toggleNavbarBtn.textContent = "Yüzen Adaları Gizle";
    }

    hammen.classList.toggle('hidden');
    if (hammen.classList.contains('hidden')) {
        toggleNavbarBtn.textContent = "Yüzen Adaları Göster";
    } else {
        toggleNavbarBtn.textContent = "Yüzen Adaları Gizle";
    }

    logo.classList.toggle('hidden');
    if (logo.classList.contains('hidden')) {
        toggleNavbarBtn.textContent = "Yüzen Adaları Göster";
    } else {
        toggleNavbarBtn.textContent = "Yüzen Adaları Gizle";
    }

    opbar.classList.toggle('hidden');
    if (opbar.classList.contains('hidden')) {
        toggleNavbarBtn.textContent = "Yüzen Adaları Göster";
    } else {
        toggleNavbarBtn.textContent = "Yüzen Adaları Gizle";
    }

    cp.classList.toggle('hidden');
    if (cp.classList.contains('hidden')) {
        toggleNavbarBtn.textContent = "Yüzen Adaları Göster";
    } else {
        toggleNavbarBtn.textContent = "Yüzen Adaları Gizle";
    }
    mbar.classList.toggle('hidden');
    if (mbar.classList.contains('hidden')) {
        toggleNavbarBtn.textContent = "Yüzen Adaları Göster";
    } else {
        toggleNavbarBtn.textContent = "Yüzen Adaları Gizle";
    }
});