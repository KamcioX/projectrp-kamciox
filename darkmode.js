// JavaScript dla zarządzania trybem ciemnym
document.addEventListener("DOMContentLoaded", function() {
    const darkModeSwitch = document.getElementById('darkModeSwitch');
    const sunIcon = document.getElementById('sunIcon');
    const moonIcon = document.getElementById('moonIcon');
    const darkModeClass = 'dark-mode';
    const localStorageKey = 'darkMode';

    function applyDarkMode() {
        const darkModeEnabled = localStorage.getItem(localStorageKey) === 'enabled';

        if (darkModeEnabled) {
            document.body.classList.add(darkModeClass);
            sunIcon.classList.add('d-none');
            moonIcon.classList.remove('d-none');
        } else {
            document.body.classList.remove(darkModeClass);
            sunIcon.classList.remove('d-none');
            moonIcon.classList.add('d-none');
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
