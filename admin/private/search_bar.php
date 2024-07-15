<button class="opbar" id="toggle-menu-btn"><i class="fa-solid fa-swatchbook"></i></button>

<!-- Sol Menü -->
<div class="left-menu" id="left-menu">
    <div class="opacity-control">
        <label for="opacity-slider">Saydamlık:</label>
        <input type="range" id="opacity-slider" min="0" max="100" value="10">
    </div>
    <div class="blur-control">
        <label for="blur-slider">Bulanıklık:</label>
        <input type="range" id="blur-slider" min="0" max="100" value="10">
    </div>
    <button id="reset-btn"><i class="fa-solid fa-repeat"></i></button>
    <button id="toggle-theme-btn" class="li-mo"><i class="fa-solid fa-moon"></i></button>
    <button id="toggleButton"><i class="fa-solid fa-hat-cowboy-side"></i><span class="solution" id="solution">HACKER MODU</span></button>
</div>

<div class="search-bar">
    <form action="./search.php" method="get">
        <input type="text" name="q" placeholder="Aramak İçin Buraya Yazın"/>
    </form>
</div>

<!-- Renk Seçici -->
<div class="color-picker">
    <div id="color-dropdown" class="color-dropdown">
        <div class="selected-color" style="background-color: #d82c54;"></div>
        <div class="color-options">
            <div class="color-option" data-color="#ffffff" style="background-color: #ffffff;"></div>
            <div class="color-option" data-color="#d82c54" style="background-color: #d82c54;"></div>
            <div class="color-option" data-color="#ff9800" style="background-color: #ff9800;"></div>
            <div class="color-option" data-color="#4caf50" style="background-color: #4caf50;"></div>
            <div class="color-option" data-color="#2196f3" style="background-color: #2196f3;"></div>
            <div class="color-option" data-color="#9c27b0" style="background-color: #9c27b0;"></div>
            <div class="color-option custom-color-option" title="Özel Renk Seç"
                 style="background-color: transparent; border: 1px dashed white;"></div>
        </div>
    </div>
    <input type="color" id="theme-color-picker" title="Özel Renk Seç" style="display:none;">
</div>


<script src="assets/js/colorpicker.js?v=1.0.0"></script>
<!--<script src="assets/js/snow.js"></script>-->
<script src="assets/js/customizer.js?v=1.0.0"></script>
<script src="assets/js/theme.js?v=1.0.0"></script>
<script src="assets/js/customizermenu.js?v=1.0.0"></script>
<script src="assets/js/hacker-mode.js?v=1.0.0"></script>