const logo = `<a class="site-navbar" href="./index.php?Page=Home" id="logo" onclick="navbarClick('home');"><img src="./icons/307logo_fixed.svg"></a>`
const home = `<a class="site-navbar nav-item" href="./index.php?Page=Home" id="home" onclick="navbarClick('home');">Home</a>`
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
    var tempPage = window.location.href.split("?")[1];
    let currentPage = "home";
    if (tempPage){
        currentPage = tempPage.split("Page=")[1].toLowerCase();
    }

    let site_navbar = document.getElementById("site-navbar");
    let utility_navbar = document.getElementById("utility-navbar");

    site_navbar.innerHTML = logo + home;

    // check if cookies is valid
    var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function(){
		if (this.readyState == 4 && this.status == 200){
            if (!this.responseText.includes("Expired")){
                console.log('Logged in automatically');
                site_navbar.innerHTML = logo + home + dashboard;
                utility_navbar.innerHTML = `<div id='user_display'>${this.responseText}</div>` + logout;
                reloadActive();                
            } else {
                site_navbar.innerHTML = logo + home;                
                utility_navbar.innerHTML = signup + login;
                reloadActive();
            }
        } else {
            console.log("Request failed with status: " + this.status);
		}
	}

	xhttp.open("GET", "php/login.php", "true");
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send();
}


window.addEventListener("load",loadNavbar);