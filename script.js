//NAVBAR FUNCTIONS
function navbarClick(id){
    if (id == 'login' || id == 'signup'){
        var elem = document.getElementById('popup');
        elem.className += ' active';
        
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange= function(){
            if (this.readyState == 4 && this.status == 200){
                document.getElementById('popup').innerHTML += this.responseText;
            }
        };
        
        xhttp.open("POST", "load_form.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send('type='+id);
    }
    toggleActive(id,['home','signup','login']);
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
        elemToDeactivate.remove("active");
        document.getElementById(element).classList = elemToDeactivate;
    });
}

function closePopup(){
    var elem = document.getElementById('popup');
    elem.className = 'popup';
    elem.innerHTML = '<img src="icons\icons8-close-50.png" id="close" onclick="closePopup();">';
}

//TYPING ANIMATION FUNCTIONS
const characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

function generateString(length) {
    let result = '';
    const charactersLength = characters.length;
    for ( let i = 0; i < length; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }

    return result;
}

function typeText(text, where, len_error, elem_id, speed=125, delay=500){
    let i = 0;
    let j = 0;
    let total = 0;
    let randomCharIndex = 0;
    // let speed = 125;
    // let delay = 500;
    let randomGenerated = false;
    let fixing = false;
    let beforeFixTotal = 0;

    let element = document.getElementById(elem_id);

    function addText(textToAdd){
        element.innerHTML += textToAdd;
        if (element.hasAttribute('data-text')){
            var temp = element.getAttribute('data-text');
            temp += textToAdd;
            element.setAttribute('data-text',temp);
        }
    }

    function removeLastChar(){
        var length = element.innerHTML.length;
        element.innerHTML = element.innerHTML.substring(0, length-1);
        if (element.hasAttribute('data-text')){
            var temp = element.getAttribute('data-text');
            temp = temp.substring(0, length-1);
            element.setAttribute('data-text', temp);
        }
    }
    
    function typing(){
        function pushNextIter(delayVar){
            total = element.innerHTML.length;
            setTimeout(typing,delayVar);
        }
        if (i < text.length){
            if (fixing){ //If fixing needed
                if (beforeFixTotal - total == len_error){
                    fixing = false;
                    j = 0;
                    pushNextIter(speed);
                }
                else{
                    // element.innerHTML = element.innerHTML.substring(0,total-1);
                    removeLastChar();
                    pushNextIter(delay);
                }
            }
            else{ //If no fixing needed
                if (i == where[randomCharIndex]){
                    var char_gen = generateString(1);
                    addText(char_gen);
                    j++;

                    if (j+1 == len_error){
                        randomCharIndex++;
                        randomGenerated = true;
                    }
                    
                    pushNextIter(speed);
                }
                else if (randomGenerated){
                    addText(' ');
                    fixing = true;
                    randomGenerated = false;
                    beforeFixTotal = total + 1;
                    pushNextIter(delay);
                }
                else if (i != where[randomCharIndex]){
                    addText(text.charAt(i));
                    i++;
                    pushNextIter(speed);
                }                
            }
        }
    }
    typing();
}