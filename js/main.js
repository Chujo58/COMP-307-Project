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

function onLoginKeyPress(){
	var user = getValue('username');
	var pass = getValue('password');

	if (user.length == 0){
		addClass('username','username','missing');
		addClass('username_div','username','missing');
	}
	if (pass.length == 0){
		addClass('password','password','missing');
		addClass('password_div','password','missing');
	}

	if (user.length != 0) {
		removeClass('username', 'missing');
		removeClass('username_div','missing');
	}
	if (pass.length != 0) {
		removeClass('password', 'missing');
		removeClass('password_div','missing');
	}
	return [user, pass];
}

function onSignUpKeyPress(){
	var user = getValue('username');
	var pass = getValue('password');
	var c_pass = getValue('confirm_password');

	if (user.length == 0){
		addClass('username','username','missing');
		addClass('username_div','username','missing');
	}
	if (pass.length == 0){
		addClass('password','password','missing');
		addClass('password_div','password','missing');
	}
	if (c_pass.length == 0){
		addClass('confirm_password','password','missing');
		addClass('confirm_password_div','password','missing');
	}

	if (user.length != 0) {
		removeClass('username', 'missing');
		removeClass('username_div','missing');
	}
	if (pass.length != 0) {
		removeClass('password', 'missing');
		removeClass('password_div','missing');
	}
	if (c_pass.length != 0){
		removeClass('confirm_password', 'missing');
		removeClass('confirm_password_div','missing');
	}
	return [user, pass, c_pass];
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
	var user = "";
	var pass = "";
	data = onLoginKeyPress();
	user = data[0];
	pass = data[1];
	// pass = encrypt(pass, Storage.key);

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function(){
		if (this.readyState == 4 && this.status == 200){
			if (!this.responseText.includes("Invalid")){
				document.getElementById('test').innerHTML = "Logged in!";
				setCookie("user",user,default_exp_cookie);
				setTimeout(function() {redirect("Dashboard");}, redirect_delay);
			}
		}
	}

	xhttp.open("POST", "php/login.php", "true");
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(`username=${user}&password=${pass}`);
}

function sendSignUpRequest(){
	var user = "";
	var pass = "";
	var c_pass = "";
	data = onSignUpKeyPress();
	user = data[0];
	pass = data[1];
	c_pass = data[2];

	// pass = encrypt(pass, Storage.key);
	// c_pass = encrypt(c_pass, Storage.key);

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function(){
		if (this.readyState == 4 && this.status == 200){
			if (!this.responseText.includes("Invalid")){
				document.getElementById('test').innerHTML = "Signed up!";
				setTimeout(function(){navbarClick("login")}, redirect_delay);
			}
		}
	}

	xhttp.open("POST","php/signup.php","true");
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(`username=${user}&password=${pass}&confirm_password=${c_pass}`);
}