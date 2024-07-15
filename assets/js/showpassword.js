document.getElementById("showpass").addEventListener("click", function () {
    var passwordInput = document.getElementById("passwd");
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
    } else {
        passwordInput.type = "password";
    }
});

document.getElementById("togglePassword").addEventListener("click", function () {
    var passwordInput = document.getElementById("password");
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
    } else {
        passwordInput.type = "password";
    }
});