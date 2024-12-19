const menu = `<a class="site-navbar" id="navbar-menu"><img src="./icons/icons8-menu-win.svg"></a>`
const logo = `<a class="site-navbar" href="./index.php?Page=Home" id="logo" onclick="navbarClick('home');"><img src="./icons/307logo_fixed.svg"></a>`
const home = `<a class="site-navbar nav-item" href="./index.php?Page=Home" id="home" onclick="navbarClick('home');">Home</a>`
const calendar = `<a class="site-navbar nav-item" href="./index.php?Page=Calendar" id="calendar" onclick="navbarClick('calendar');">Calendar</a>`
const calendar_logged = `<a class="site-navbar nav-item" href="./index.php?Page=Calendar&session=true" id="calendar" onclick="navbarClick('calendar');">Calendar</a>`
const dashboard = `<a class="site-navbar nav-item" href="./index.php?Page=Dashboard" id="dashboard" onclick="navbarClick('dashboard');">Dashboard</a>`
const signup = `<a class="utility-navbar nav-item"  id="signup" onclick="navbarClick('signup');">Sign Up</a>`
const login = `<a class="utility-navbar nav-item"  id="login" onclick="navbarClick('login');">Login</a>`
const logout = `<a class="utility-navbar nav-item"  id="logout" onclick="sendLogoutRequest(); navbarClick('logout');">Log Out</a>`


function loadNavbar() {
    const publicPages = ["home", "calendar", "event"];
    const currentPage = getCurrentPage();
    const siteNavbar = document.getElementById("site-navbar");
    const utilityNavbar = document.getElementById("utility-navbar");

    // Set default public navbar
    siteNavbar.innerHTML = logo + home + calendar;
    utilityNavbar.innerHTML = signup + login;

    // Reload active state for public pages
    reloadActive(currentPage);

    // Check for valid session
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4) {
            if (this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.status === "success") {
                    // Load authenticated navbar
                    siteNavbar.innerHTML = logo + home + calendar_logged + dashboard;
                    utilityNavbar.innerHTML = `<div id='user_display'>${response.user}</div>` + logout;
                } else if (!publicPages.includes(currentPage)) {
                    // If not authenticated, redirect unless on a public page
                    window.location.href = "./index.php?Page=Home";
                }
                reloadActive(currentPage);
            } else {
                console.error("Failed to validate session:", this.status);
            }
        }
    };

    xhttp.open("GET", "php/auth.php");
    xhttp.send();
}

function getCurrentPage() {
    const tempPage = window.location.href.split("?")[1]?.split("&")[0];
    return tempPage?.split("Page=")[1]?.toLowerCase() || "home";
}

function reloadActive(currentPage) {
    const elements = document.querySelectorAll(".site-navbar");
    elements.forEach((element) => {
        element.classList.remove("active");
        if (element.id === currentPage) {
            element.classList.add("active");
        }
    });
}

window.addEventListener("load", loadNavbar);
