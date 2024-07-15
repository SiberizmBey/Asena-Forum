document.getElementById('toggle-menu-btn').addEventListener('click', function() {
    var leftMenu = document.getElementById('left-menu');
    if (leftMenu.style.display === 'none' || leftMenu.style.display === '') {
        leftMenu.style.display = 'flex';
    } else {
        leftMenu.style.display = 'none';
    }
});
