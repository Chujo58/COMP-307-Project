const menu = `<a class="site-navbar" id="navbar-menu"><img src="./icons/icons8-menu-win.svg"></a>`
const logo = `<a class="site-navbar" href="./index.php?Page=Home" id="logo" onclick="navbarClick('home');"><img src="./icons/307logo_fixed.svg"></a>`
const home = `<a class="site-navbar nav-item" href="./index.php?Page=Home" id="home" onclick="navbarClick('home');">Home</a>`
const calendar = `<a class="site-navbar nav-item" href="./index.php?Page=Calendar" id="calendar" onclick="navbarClick('calendar');">Calendar</a>`
const dashboard = `<a class="site-navbar nav-item" href="./index.php?Page=Dashboard" id="dashboard" onclick="navbarClick('dashboard');">Dashboard</a>`
const signup = `<a class="utility-navbar nav-item"  id="signup" onclick="navbarClick('signup');">Sign Up</a>`
const login = `<a class="utility-navbar nav-item"  id="login" onclick="navbarClick('login');">Login</a>`
const logout = `<a class="utility-navbar nav-item"  id="logout" onclick="sendLogoutRequest(); navbarClick('logout');">Log Out</a>`


function loadNavbar(){
    function reloadActive(){
        var elems = site_navbar.querySelectorAll(".site-navbar");
        elems.forEach(element => {
            if (element.classList.contains("active")){
                element.classList.remove("active");
            }
            if (element.id == currentPage){
                element.classList.add("active");
            }
        });
    }
    var tempPage = window.location.href.split("?")[1].split("&")[0];
    let currentPage = "home";
    if (tempPage){
        currentPage = tempPage.split("Page=")[1].toLowerCase();
    }

    let site_navbar = document.getElementById("site-navbar");
    let utility_navbar = document.getElementById("utility-navbar");

    site_navbar.innerHTML = logo + home + calendar;
    utility_navbar.innerHTML = signup + login;
    reloadActive();

    if (window.sessionStorage.getItem("ticketExpired")) {
        // Prevent making further requests if already redirected
        window.sessionStorage.removeItem("ticketExpired");
        if (currentPage != "home"){
            window.location.href = "./index.php?Page=Home";
        }
        return;
    }

    // check if cookies is valid
    var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function(){
		if (this.readyState == 4 && this.status == 200){
            let response = JSON.parse(this.responseText);
            if (response.status !== "success"){
                window.sessionStorage.setItem("ticketExpired", true);   
                window.location.href = "./index.php?Page=Home"; 
            } else {
                site_navbar.innerHTML = logo + home + calendar + dashboard;
                utility_navbar.innerHTML = `<div id='user_display'>${response.user}</div>` + logout;
                reloadActive();
            }
        } else {
            console.log("Request failed with status: " + this.status);
		}
	}

	xhttp.open("GET", "php/auth.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send();
}


window.addEventListener("load",loadNavbar);