document.addEventListener("DOMContentLoaded", function() {
    const body = document.body;
    const button = document.getElementById("toggleButton");

    // Sayfa yüklendiğinde localStorage kontrolü yaparak class ekleyin.
    if (localStorage.getItem("hacker-mode") === "true") {
        body.classList.add("hacker-mode");
    }

    // Butona tıklanınca class'ı ekleyip kaldıran fonksiyon.
    button.addEventListener("click", function() {
        if (body.classList.contains("hacker-mode")) {
            body.classList.remove("hacker-mode");
            localStorage.setItem("hacker-mode", "false");
        } else {
            body.classList.add("hacker-mode");
            localStorage.setItem("hacker-mode", "true");
        }
    });
});