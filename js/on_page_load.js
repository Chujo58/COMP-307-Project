const logo = `<a class="site-navbar" href="./index.php?Page=Home" id="logo" onclick="navbarClick('home');"><img src="./icons/307logo_fixed.svg"></a>`
const home = `<a class="site-navbar nav-item active" href="./index.php?Page=Home" id="home" onclick="navbarClick('home');">Home</a>`
const dashboard = `<a class="site-navbar nav-item active" href="./index.php?Page=Dashboard" id="dashboard" onclick="navbarClick('dashboard');">Dashboard</a>`
const signup = `<a class="utility-navbar nav-item"  id="signup" onclick="navbarClick('signup');">Sign Up</a>`
const login = `<a class="utility-navbar nav-item"  id="login" onclick="navbarClick('login');">Login</a>`



function loadNavbar(){
    // let navbar = document.getElementById("navbar");
    let site_navbar = document.getElementById("site-navbar");
    let utility_navbar = document.getElementById("utility-navbar");

    site_navbar.innerHTML = logo + home;
    utility_navbar.innerHTML = signup + login;
}


window.addEventListener("load",loadNavbar);