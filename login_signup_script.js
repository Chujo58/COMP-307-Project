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
			document.getElementById('test').innerHTML = this.responseText;
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

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function(){
		if (this.readyState == 4 && this.status == 200){
			document.getElementById('test').innerHTML = this.responseText;
		}
	}

	xhttp.open("POST","php/signup.php","true");
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(`username=${user}&password=${pass}&confirm_password=${c_pass}`);
}