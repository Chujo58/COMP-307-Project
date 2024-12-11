//NAVBAR FUNCTIONS
var popup_default_inner = '';
var navbarIds = ['home','signup','login','dashboard'];
function navbarClick(id){
    if (id == 'login' || id == 'signup'){
        var elem = document.getElementById('popup');
        elem.className += ' active';
        
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange= function(){
            if (this.readyState == 4 && this.status == 200){
                document.getElementById('popup').innerHTML = popup_default_inner + this.responseText;
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


// COOKIES!!!!!!!!!
function setCookie(cname, cvalue, exp_days){
	const d = new Date();
	d.setTime(d.getTime() + exp_days*24*60*60*1000);
	let expires = `expires=${d.toUTCString()}`;
	document.cookie = `${cname}=${cvalue};${expires};path=/`;
}

function getCookie(cname){
	let name = `${cname}=`;
	let decodedCookie = decodeURIComponent(document.cookie);
	let ca = decodedCookie.split(';');
	for (let i = 0; i < ca.length; i++) {
		let c = ca[i];
		while (c.charAt(0) == " "){
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0){
			return c.substring(name.length, c.length);
		}		
	}
	return "";
}

// FUNCTIONS FOR THE LOGIN AND SIGNUP FORMS
let default_exp_cookie = 10;
let redirect_delay = 1500;

function getValue(id){
	return document.getElementById(id).value ?? '';
}

function addClass(id, text, class_text){
	var elem = document.getElementById(id);
	elem.classList.add(class_text);
	if (id.includes("_div")){
		elem.innerHTML = class_text.substring(0,1).toUpperCase() + class_text.substring(1,class_text.length) + " " + text;
	}
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
		error.classList.add('missing');
		error.innerText = "Required";

		return true;

	} else {
		field.classList.remove('missing');
		error.classList.remove('missing');
		error.innerText = "";

		return false;
	}
}

//CHANGE IF YOU WANT
function encrypt(message, key){
	return CryptoJS.AES.encrypt(message,key);
}

function decrypt(message, key){
	return CryptoJS.AES.decrypt(message, key).toString(CryptoJS.enc.Utf8);
}

function redirect(page){
	window.location = `./index.php?Page=${page}`;
}

function sendLoginRequest(){
	let empty = isFieldEmpty('username');
	empty = isFieldEmpty('password') || empty;

	if (empty) {
		return;
	}

	var user = getValue('username');
	var pass = getValue('password');

	// pass = encrypt(pass, Storage.key);

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function(){
		if (this.readyState == 4){
			console.log(this.responseText);
			if (this.status == 200){
				if (!this.responseText.includes("Invalid")){
					document.getElementById('test').innerHTML = "Logged in!";
					setCookie("user",user,default_exp_cookie);
					setTimeout(function() {redirect("Dashboard");}, redirect_delay);
				} else {
					document.getElementById('test').innerHTML = "Invalid User or Password";
				}
			} else {
				console.log("Request failed with status: " + this.status);
			}
		}
	}

	xhttp.open("POST", "php/login.php", "true");
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(`username=${user}&password=${pass}`);
}

function sendSignUpRequest(event){
	// Make sure user cannot sign up with empty fields
	let empty = isFieldEmpty('username');
	empty = isFieldEmpty('password') || empty;
	empty = isFieldEmpty('confirm_password') || empty;

	if (empty) {
		return
	}

	var user = getValue('username');
	var pass = getValue('password');
	var c_pass = getValue('confirm_password');

	// pass = encrypt(pass, Storage.key);
	// c_pass = encrypt(c_pass, Storage.key);

	// Check if both passwords match
	if (pass !== c_pass) {
		addClass('confirm_password','password','missing');
		addClass('confirm_password_div','password','missing');
		document.getElementById("confirm_password_div").innerText = "Passwords Must Match";
		return;
	}

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function(){
		if (this.readyState == 4){
			console.log(this.responseText);
			if (this.status == 200){
				if (this.responseText === "User added"){
					document.getElementById('test').innerHTML = "Signed up!";
					setTimeout(function(){navbarClick("login")}, redirect_delay);
				} else {
					document.getElementById('test').innerHTML = this.responseText;
				}
			} else {
				console.log("Request failed with status: " + this.status);
			}
		}
	}

	xhttp.open("POST","php/signup.php","true");
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(`username=${user}&password=${pass}&confirm_password=${c_pass}`);
}