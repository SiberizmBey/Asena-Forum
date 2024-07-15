<style>
    @keyframes shake {
        0% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        50% { transform: translateX(5px); }
        75% { transform: translateX(-5px); }
        100% { transform: translateX(0); }
    }
    .new-message {
        animation: shake 0.5s infinite;
    }

    .new-message i {
        color: var(--main-theme);
    }

</style>


<div class="bottom-bar">
    <a href="./index.php"><img src="assets/img/logo.jpg" alt="Logo" class="logo"/></a>
    <div class="navbar">
        <a href="./index.php" class="bar-button bell"><i class="fas fa-home"></i></a>
        <a href="./notifications.php" class="bar-button bell"><i class="fa-solid fa-bell"></i></a>
        <a href="./view_conversations.php" class="bar-button bell" id="message-icon"><i class="fa-solid fa-message"></i></a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="profile-dropdown">
                <a href="#" class="bar-button bell profile-icon"><i class="fa-regular fa-user"></i></a>
                <div class="dropdown-content">
                    <a href="./profile.php"><i class="fa-regular fa-user"></i> Profil</a>
                    <a href="./new_post.php"><i class="fa-solid fa-notes-medical"></i> Yeni Post</a>
                    <a href="./logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Çıkış</a>
                </div>
            </div>
        <?php else: ?>
            <a href="./login.php" class="bar-button bell"><i class="fa-regular fa-user"></i></a>
        <?php endif; ?>
    </div>
    <button class="tabBar" id="toggle-navbar-btn">Yüzen Adaları Gizle</button>
    <canvas id="canvas"></canvas>
    <script src="./assets/js/drawer.js?v=1.0.1"></script>
    <script src="./assets/js/customizer.js?v=1.0.0"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function checkForNewMessages() {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'check_new_messages.php', true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        var hasNewMessages = response.hasNewMessages;
                        var messageIcon = document.getElementById('message-icon');

                        if (hasNewMessages) {
                            messageIcon.classList.add('new-message');
                        } else {
                            messageIcon.classList.remove('new-message');
                        }
                    }
                };
                xhr.send();
            }
            checkForNewMessages();
            setInterval(checkForNewMessages, 30000);
        });
    </script>
</div>
