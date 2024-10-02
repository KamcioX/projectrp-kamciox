<footer class="bg-body-tertiary text-center text-lg-start">
    <!-- Social Media Icons -->
    <div class="p-3">
        <a href="https://discord.gg/KamcioX ProjectRP" target="_blank" class="text-dark me-3 social-icon">
            <i class="bi bi-discord" style="font-size: 1.5rem;"></i>
        </a>
        <a href="https://www.tiktok.com/@KamcioX ProjectRP.pl" target="_blank" class="text-dark social-icon">
            <i class="bi bi-tiktok" style="font-size: 1.5rem;"></i>
        </a>
    </div>
    <!-- Copyright -->
    <div class="text-center p-3">
        © 2024 KamcioX ProjectRP - Stworzone przez KamcioX'a
    </div>
    <!-- Copyright -->
</footer>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<style>
/* CSS for social icons */
.social-icon i {
  transition: color 0.3s;
}

.dark-mode .social-icon i {
  color: white !important;
}
</style>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script>
// JavaScript do zarządzania trybem ciemnym
document.addEventListener("DOMContentLoaded", function() {
    const darkModeSwitch = document.getElementById('darkModeSwitch');
    const sunIcon = document.getElementById('sunIcon');
    const darkModeClass = 'dark-mode';
    const localStorageKey = 'darkMode';

    function applyDarkMode() {
        const darkModeEnabled = localStorage.getItem(localStorageKey) === 'enabled';

        if (darkModeEnabled) {
            document.body.classList.add(darkModeClass);
            sunIcon.classList.add('d-none');
        } else {
            document.body.classList.remove(darkModeClass);
            sunIcon.classList.remove('d-none');
        }

        darkModeSwitch.checked = darkModeEnabled;
    }

    darkModeSwitch.addEventListener('change', function() {
        localStorage.setItem(localStorageKey, this.checked ? 'enabled' : 'disabled');
        applyDarkMode();
    });

    // Apply dark mode on page load
    applyDarkMode();
});
</script>

<script>
// JavaScript to show loading dots animation
document.addEventListener('DOMContentLoaded', function () {
    var dotsInterval = window.setInterval(function () {
        var loadingDots = document.getElementById('loadingDots');
        if (loadingDots.innerHTML.length > 2)
            loadingDots.innerHTML = "";
        else
            loadingDots.innerHTML += ".";
    }, 20);

    // Hide loading screen after 5 seconds with a fade out effect
    window.setTimeout(function () {
        clearInterval(dotsInterval); // Stop dots animation
        var loadingScreen = document.getElementById('loadingScreen');
        loadingScreen.style.opacity = '0'; // Start fade out
        setTimeout(function () {
            loadingScreen.style.display = 'none'; // Hide loading screen after fade out
        }, 500); // 0.5s delay after opacity transition
    }, 200); // 5s timeout
});
</script>
