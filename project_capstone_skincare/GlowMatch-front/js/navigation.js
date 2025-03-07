document.addEventListener("DOMContentLoaded", function () {
    const consultLink = document.querySelector("a[href*='consulting.html']");
    const analyzeLink = document.querySelector("a[href*='analysis.html']");
    const loginPopup = document.querySelector(".container"); // Login form container
    const flipCheckbox = document.querySelector("#flip"); // Checkbox for switching between login & signup

    function isUserLoggedIn() {
        return localStorage.getItem("loggedIn") === "true";
    }

    function showLoginPopup() {
        loginPopup.style.display = "block"; // Show login form
        flipCheckbox.checked = false; // Reset to login view
    }

    function requireLogin(event) {
        if (!isUserLoggedIn()) {
            event.preventDefault(); // Prevent navigation
            showLoginPopup(); // Show the login popup
        }
    }

    // ✅ Attach event listeners ONLY if elements exist
    if (consultLink) consultLink.addEventListener("click", requireLogin);
    if (analyzeLink) analyzeLink.addEventListener("click", requireLogin);
});
