class PufSimpleAuth {
    constructor() {
        this.init();
    }

    init() {
        this.removeQueryParam("was_logged_in");
        this.setupEventListeners();
    }

    removeQueryParam(param) {
        const url = new URL(window.location);
        if (url.searchParams.has(param)) {
            url.searchParams.delete(param);
            window.history.replaceState({}, document.title, url.toString());
        }
    }

    setupEventListeners() {
        const loginForm = document.getElementById("loginForm");
        const logoutButton = document.getElementById("logoutButton");
        const loginButton = document.getElementById("loginButton");
        const loginModalElement = document.getElementById("loginModal");
        this.loginModal = loginModalElement ? new bootstrap.Modal(loginModalElement) : null;

        if (loginButton && this.loginModal) {
            loginButton.addEventListener("click", () => this.loginModal.show());
        }

        if (loginForm) {
            loginForm.addEventListener("submit", (e) => this.handleLogin(e));
        }

        if (logoutButton) {
            logoutButton.addEventListener("click", (e) => this.handleLogout(e));
        }
    }

    handleLogin(event) {
        event.preventDefault();
        const formData = {
            username: document.getElementById("loginUsername").value,
            password: document.getElementById("loginPassword").value
        };

        fetch("/login", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (this.loginModal) this.loginModal.hide();
                setTimeout(() => location.reload(), 300);
            } else {
                alert("Login failed: " + (data.error || "Unknown error"));
            }
        })
        .catch(error => console.error("Error logging in:", error));
    }

    handleLogout(event) {
        event.preventDefault();
        fetch("/logout", { method: "POST" })
            .then(() => {
                window.location.href = window.location.pathname + "?was_logged_in=true";
            })
            .catch(error => console.error("Error logging out:", error));
    }
}

// Initialize PufSimpleAuth when DOM is ready
document.addEventListener("DOMContentLoaded", () => new PufSimpleAuth());
