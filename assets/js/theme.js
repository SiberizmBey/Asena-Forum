document.addEventListener('DOMContentLoaded', (event) => {
    const applyTheme = (theme) => {
        document.documentElement.classList.toggle('light-theme', theme === 'light');

        // Buton ikonunu güncelle
        const themeButtonIcon = document.querySelector('#toggle-theme-btn i');
        if (theme === 'light') {
            themeButtonIcon.classList.remove('fa-moon');
            themeButtonIcon.classList.add('fa-sun');
        } else {
            themeButtonIcon.classList.remove('fa-sun');
            themeButtonIcon.classList.add('fa-moon');
        }
    };

    const currentTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
    applyTheme(currentTheme);

    // Tema değiştirme butonu
    const toggleThemeBtn = document.getElementById('toggle-theme-btn');
    toggleThemeBtn.addEventListener('click', () => {
        const isLightTheme = document.documentElement.classList.toggle('light-theme');
        const newTheme = isLightTheme ? 'light' : 'dark';
        localStorage.setItem('theme', newTheme);
        applyTheme(newTheme);
    });

    // Sistem temasını takip etme
    window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', (e) => {
        const newTheme = e.matches ? 'light' : 'dark';
        localStorage.setItem('theme', newTheme);
        applyTheme(newTheme);
    });
});
