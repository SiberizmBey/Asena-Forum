document.addEventListener('DOMContentLoaded', function () {
    const colorDropdown = document.getElementById('color-dropdown');
    const selectedColor = colorDropdown.querySelector('.selected-color');
    const colorOptions = colorDropdown.querySelectorAll('.color-option');
    const customColorOption = colorDropdown.querySelector('.custom-color-option');
    const themeColorPicker = document.getElementById('theme-color-picker');
    const root = document.documentElement;

    // Varsayılan tema rengi (kullanmak istediğiniz varsayılan rengi buraya ekleyin)
    const defaultThemeColor = '#d82c54';

    // Renk seçiminden gelen veriyi yükle
    const savedThemeColor = localStorage.getItem('main-theme') || defaultThemeColor;
    root.style.setProperty('--main-theme', savedThemeColor);
    selectedColor.style.backgroundColor = savedThemeColor;

    // Dropdown menüsüne tıklama olayı
    colorDropdown.addEventListener('click', function () {
        colorDropdown.classList.toggle('open');
    });

    // Renk seçeneklerinden biri seçildiğinde
    colorOptions.forEach(function (option) {
        option.addEventListener('click', function (event) {
            const selectedColorValue = option.getAttribute('data-color');
            if (selectedColorValue) {
                root.style.setProperty('--main-theme', selectedColorValue);
                selectedColor.style.backgroundColor = selectedColorValue;
                colorDropdown.classList.remove('open');
                localStorage.setItem('main-theme', selectedColorValue);
                event.stopPropagation();  // Dropdown'ın hemen kapanmasını önler
            }
        });
    });

    // Özel renk seçimi açma
    customColorOption.addEventListener('click', function (event) {
        themeColorPicker.click();
        event.stopPropagation();  // Dropdown'ın hemen kapanmasını önler
    });

    // Sayfanın başka bir yerine tıklandığında dropdown'ı kapat
    document.addEventListener('click', function (event) {
        if (!colorDropdown.contains(event.target)) {
            colorDropdown.classList.remove('open');
        }
    });

    // Özel renk seçimi değiştiğinde
    themeColorPicker.addEventListener('input', function (event) {
        const newColor = event.target.value;
        root.style.setProperty('--main-theme', newColor);
        selectedColor.style.backgroundColor = newColor;
        colorDropdown.classList.remove('open');
        localStorage.setItem('main-theme', newColor);
    });
});
