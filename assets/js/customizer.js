document.addEventListener('DOMContentLoaded', function () {
    const opacitySlider = document.getElementById('opacity-slider');
    const blurSlider = document.getElementById('blur-slider');
    const resetButton = document.getElementById('reset-btn');

    function updateStyles() {
        const opacity = opacitySlider.value / 100;
        const blur = blurSlider.value + 'px';
        document.documentElement.style.setProperty('--menu-opacity', opacity);
        document.documentElement.style.setProperty('--menu-blur', blur);
        localStorage.setItem('menu-opacity', opacitySlider.value);
        localStorage.setItem('menu-blur', blurSlider.value);
    }

    function loadSettings() {
        const savedOpacity = localStorage.getItem('menu-opacity');
        const savedBlur = localStorage.getItem('menu-blur');
        if (savedOpacity !== null) {
            opacitySlider.value = savedOpacity;
        }
        if (savedBlur !== null) {
            blurSlider.value = savedBlur;
        }
        updateStyles();
    }

    function resetSettings() {
        opacitySlider.value = 10;
        blurSlider.value = 10;
        updateStyles();
    }

    opacitySlider.addEventListener('input', updateStyles);
    blurSlider.addEventListener('input', updateStyles);
    resetButton.addEventListener('click', resetSettings);

    // Load settings on page load
    loadSettings();
});
