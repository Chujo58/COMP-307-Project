const home = `<a class="site-navbar nav-item active" href="#/!" id="home" onclick="navbarClick('home');">Home</a>`
const dashboard = `<a class="site-navbar nav-item active" href="#/dashboard" id="dashboard" onclick="navbarClick('dashboard');">Dashboard</a>`
const signup = `<a class="utility-navbar nav-item"  id="signup" onclick="navbarClick('signup');">Sign Up</a>`
const login = `<a class="utility-navbar nav-item"  id="login" onclick="navbarClick('login');">Login</a>`



function loadNavbar(){
    let navbar = document.getElementById("navbar");
    let site_navbar = document.getElementById("site-navbar");
    let utility_navbar = document.getElementById("utility-navbar");

    site_navbar.innerHTML = home;
    utility_navbar.innerHTML = signup + login;
}


window.addEventListener("load",loadNavbar);