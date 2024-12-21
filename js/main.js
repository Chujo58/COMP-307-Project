// Chloé Legué, Ling Jie Chen, Rachel Shi, Sophia Zhou
//NAVBAR FUNCTIONS
var popup_default_inner = '';
var navbarIds = ['home','signup','login', 'logout', 'dashboard', 'calendar'];


function arrayRemove(array, elem){
	var index = array.indexOf(elem);
	if (index > -1) {
		array.splice(index, 1);
	}
}

function navbarClick(id){
    if (id == 'login' || id == 'signup'){
        var elem = document.getElementById('popup');
        elem.className += ' active';
        
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange= function(){
            if (this.readyState == 4 && this.status == 200){
                document.getElementById('popup').innerHTML = popup_default_inner + this.responseText;
                setEvent(document.getElementById(id+"_form"));
            }
        };
        
        xhttp.open("POST", "load_form.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send('type='+id);
    }
    toggleActive(id, navbarIds);
}
//TOGGLE OF ACTIVE PAGE IN NAVBAR
function toggleActive(id, ids){
    var elemToActivate = document.getElementById(id).classList;
    if (!elemToActivate.contains("active")) { //If the active class tag isn't present, add it. We only remove
        elemToActivate.add("active");
    }

    document.getElementById(id).classList = elemToActivate;

    ids.forEach(element => {
        if (element == id){
            return;
        }
		if (!document.getElementById(element)) {
			return;
		}
        var elemToDeactivate = document.getElementById(element).classList;
        if (elemToDeactivate != null){   
            elemToDeactivate.remove("active");
            document.getElementById(element).classList = elemToDeactivate;
        }
    });
}

function closePopup(){
    var elem = document.getElementById('popup');
    elem.className = 'popup';
    elem.innerHTML = popup_default_inner;
    toggleActive('home',navbarIds);
}

// FUNCTIONS FOR THE LOGIN AND SIGNUP FORMS
let default_exp_cookie = 10;
let redirect_delay = 500;

function getValue(id){
	return document.getElementById(id).value ?? '';
}

function removeClass(id, class_text){
	var elem = document.getElementById(id);
	if (elem.classList.contains(class_text)) elem.classList.remove(class_text);
	if (id.includes("_div")){
		elem.innerHTML = "";
	}
}

// Logic for empty and non empty field
function isFieldEmpty(id) {
	const field = document.getElementById(id);
	const error = field.nextElementSibling;

	if (field.value === '') {
		field.classList.add('missing');
        if (error.tagName.toLowerCase() == 'div'){
            error.classList.add('missing');
            error.innerText = "Required";
        }

		return true;

	} else {
		field.classList.remove('missing');
        if (error.tagName.toLowerCase() == 'div'){
            error.classList.remove('missing');
            error.innerText = "";
        }

		return false;
	}
}

//CHANGE IF YOU WANT
function redirect(page){
	window.location = `./index.php?Page=${page}&reload=true`;
}

function redirectToCalendar(user_id){
    window.location = `./index.php?Page=Calendar&user_id=${user_id}`;
}


function sendLogoutRequest() {
	let utility_navbar = document.getElementById("utility-navbar");
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function(){
		if (this.readyState == 4){
			if (this.status == 200){
				//redirects to home and update the navbar
				redirect("Home");
				if (window.history && window.history.pushState) {
					window.history.pushState(null, null, window.location.href);
					window.onpopstate = function() {
						window.history.pushState(null, null, window.location.href);
					};
				}
				utility_navbar.innerHTML = signup + login;
			} else {
				console.log("Request failed with status: " + this.status);
			}
		}
	}
	xhttp.open("POST", "php/logout.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send();
}

function sendLoginRequest(){
	let site_navbar = document.getElementById("site-navbar");
	let utility_navbar = document.getElementById("utility-navbar");
	let empty = isFieldEmpty('username');
	empty = isFieldEmpty('password') || empty;

	if (empty) {
		return;
	}

	var user = getValue('username');
	var pass = getValue('password');

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function(){
		if (this.readyState == 4){
			if (this.status == 200){
				if (!this.responseText.includes("Invalid")){
					document.getElementById('test').innerHTML = "Logged in!";
					site_navbar.innerHTML = logo + home + dashboard;
					utility_navbar.innerHTML = `<div id='user_display'>${user}</div>` + logout;
					//remove the expired session to get correct navbar after redirection
					window.sessionStorage.removeItem("ticketExpired");
					setTimeout(function() {redirect("Dashboard");}, redirect_delay);
				} else {
					document.getElementById('test').innerHTML = "Invalid User or Password";
				}
			} else {
				console.log("Request failed with status: " + this.status);
			}
		}
	}

	xhttp.open("POST", "php/login.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(`username=${user}&password=${pass}`);
}

function sendSignUpRequest() {
    // Make sure user cannot sign up with empty fields
    let empty = isFieldEmpty('f_name');
    empty = isFieldEmpty('l_name') || empty;
    empty = isFieldEmpty('username') || empty;
    empty = isFieldEmpty('password') || empty;
    empty = isFieldEmpty('confirm_password') || empty;

    if (empty) {
        return;
    }

    var firstName = getValue('f_name');
    var lastName = getValue('l_name');
    var user = getValue('username');
    var pass = getValue('password');
    var c_pass = getValue('confirm_password');

    // Check if it is McGill email
    let email = user.split('@');
    if (email.length != 2 || (email[1] != "mail.mcgill.ca" && email[1] != "mcgill.ca")) {
        document.getElementById('username').classList.add('missing');
        document.getElementById('username_div').classList.add('missing');
        document.getElementById('username_div').innerText = "Please Use a Valid McGill Email as Username";
        return;
    }

    // Check if both passwords match
    if (pass !== c_pass) {
        document.getElementById('confirm_password').classList.add('missing');
        document.getElementById('confirm_password_div').classList.add('missing');
        document.getElementById("confirm_password_div").innerText = "Passwords Must Match";
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4) {
            console.log(this.responseText);
            if (this.status == 200) {
                if (this.responseText === "User Added") {
                    document.getElementById('test').innerHTML = "Signed up!";
                    setTimeout(function () { navbarClick("login") }, redirect_delay);
                } else {
                    document.getElementById('test').innerHTML = this.responseText;
                }
            } else {
                console.log("Request failed with status: " + this.status);
            }
        }
    };

    xhttp.open("POST", "php/signup.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(`f_name=${encodeURIComponent(firstName)}&l_name=${encodeURIComponent(lastName)}&username=${encodeURIComponent(user)}&password=${encodeURIComponent(pass)}&confirm_password=${encodeURIComponent(c_pass)}`);
}

// 1. Load course levels dynamically
function loadLevels() {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var elem = document.getElementById('course-level');
            if (elem != null){
                elem.innerHTML += this.responseText;
            }
        }
    };
    xhttp.open('GET', 'php/dashboard.php?loadLevels=true', true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}

// 2. Filter and load courses dynamically
function filterCourses() {
    const courseName = document.getElementById('course-name').value;
    const courseID = document.getElementById('course-id').value;
    const courseLevel = document.getElementById('course-level').value;

    const patternID = /^\d{3}([A-Z]\d)?$/;
    if (!patternID.test(courseID) && courseID != ''){
        document.getElementById('course-id-error-dashboard').style.color = 'red';
        document.getElementById('course-id-error-dashboard').innerHTML = 'Invalid format. Should match 3 digits, or 3 digits followed by one uppercase letter and one digit.';   
        return;
    }
    document.getElementById('course-id-error-dashboard').innerHTML = '';

    const queryParams = `course-name=${encodeURIComponent(courseName)}&course-id=${encodeURIComponent(courseID)}&course-level=${encodeURIComponent(courseLevel)}`;

    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var elem = document.getElementById('student-course-list');
            if (elem != null){
                elem.innerHTML = this.responseText;
            }
        }
    };
    xhttp.open('POST', `php/dashboard.php`, true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(queryParams);
}


// Load staff for a selected course
function loadStaff() {
    // Check if courseID is defined (from PHP)
    if (typeof courseID === 'undefined' || !courseID) {
        document.getElementById('staff-list').innerHTML = "<p>No course selected!</p>";
        return;
    }

    // Make an AJAX request to fetch staff details
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            // Populate the staff list container with the response
            var elem = document.getElementById('staff-list');
            if (elem != null){
                elem.innerHTML = this.responseText;
            }
        }
    };
    xhttp.open('GET', `php/list_staff.php?course_id=${courseID}`, true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send();
}

function redirectToStaffList(courseID) {
    // Use window.location.href for redirection
    window.location.href = `index.php?Page=StaffList&course_id=${courseID}`;
}

function toggleNavbar(){
	var navbar = document.getElementById('navbar');
	var icon = document.getElementById('navbar-menu-icon');
	if (navbar.classList.contains('hidden')){
		navbar.classList.remove('hidden');
		icon.innerHTML = "<img src='icons/pulsar_color_close_nav.png'>";
		icon.classList.remove('show');
		return;
	}
	else {
		navbar.classList.add('hidden');
		icon.innerHTML = "<img src='icons/pulsar_color_menu_nav.png'>";
		icon.classList.add('show');
		return;
	}
}

/**
 * Setup event listener for enter keypress
 * @param {HTMLElement|null} form 
 */
function setEvent(form){
    var inputs = form.querySelectorAll("input");
    inputs.forEach(input => {
        if (input.type != "button"){
            input.addEventListener("keypress", function(event){
                if (event.key === "Enter"){
                    event.preventDefault();
                    document.getElementById("form_button").click();
                }
            });
        }
    });
}

// Function to fetch and display pending requests for staff
function loadPendingRequests() {
    const container = document.getElementById('pending-requests-container');

    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            container.innerHTML = this.responseText; // Populate pending requests
        } else if (this.readyState == 4) {
            container.innerHTML = "<p>Failed to load pending requests.</p>";
        }
    };

    xhttp.open('GET', 'php/pending.php', true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}

function acceptRequest(eventID) {
    if (!eventID) return;

    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            alert(this.responseText);
            // Reload the pending requests after successful acceptance
            loadPendingRequests();
        }
    };

    xhttp.open("POST", "php/accept_request.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(`event_id=${eventID}`);
}

function denyRequest(eventID) {
    if (!eventID) return;

    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            alert(this.responseText);
            // Reload the pending requests after successful denial
            loadPendingRequests();
        }
    };

    xhttp.open("POST", "php/deny_request.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(`event_id=${eventID}`);
}