document.addEventListener("DOMContentLoaded", function () {
    fetch('session.php')
        .then(response => response.json())
        .then(data => {
            const usernameDisplay = document.getElementById("username-display");
            const signInLink = document.getElementById("sign-in-link");
            const logoutLink = document.getElementById("logout-link");

            if (data.username) {
                // Show username and logout
                usernameDisplay.textContent = `Hello, ${data.username}`;
                usernameDisplay.style.display = "inline";
                signInLink.style.display = "none";
                logoutLink.style.display = "inline";
            } else {
                // Show sign-in link and hide logout and username
                usernameDisplay.style.display = "none";
                signInLink.style.display = "inline";
                logoutLink.style.display = "none";
            }
        })
        .catch(error => console.error('Error fetching session:', error));
});
