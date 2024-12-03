function getValue(id){
    return document.getElementById(id).value ?? '';
}

function addMissing(id, text){
    var elem = document.getElementById(id);
    elem.classList.add('missing');
    if (id.includes("_div")) {
        elem.innerHTML = "Missing " + text;
    }
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

function removeMissing(id){
    var elem = document.getElementById(id);
    if (elem.classList.contains('missing')) elem.classList.remove('missing');
    if (id.includes("_div")) {
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

function onLoginClick(){
    var user = "";
    var pass = "";
    data = onLoginKeyPress();
    user = data[0];
    pass = data[1];

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