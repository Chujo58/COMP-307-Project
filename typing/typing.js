const characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
const elem_id = "demo";


function generateString(length) {
    let result = '';
    const charactersLength = characters.length;
    for ( let i = 0; i < length; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }

    return result;
}

function typeText(text, where, len_error){
    let i = 0;
    let j = 0;
    let total = 0;
    let randomCharIndex = 0;
    let speed = 125;
    let delay = 500;
    let randomGenerated = false;
    let fixing = false;
    let beforeFixTotal = 0;
    
    function typing(){
        function pushNextIter(delayVar){
            total = document.getElementById(elem_id).innerHTML.length;
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
                    document.getElementById(elem_id).innerHTML = document.getElementById(elem_id).innerHTML.substring(0,total-1);
                    pushNextIter(delay);
                }
            }
            else{ //If no fixing needed
                if (i == where[randomCharIndex]){
                    var char_gen = generateString(1);
                    document.getElementById(elem_id).innerHTML += char_gen;
                    j++;

                    if (j+1 == len_error){
                        randomCharIndex++;
                        randomGenerated = true;
                    }
                    
                    pushNextIter(speed);
                }
                else if (randomGenerated){
                    document.getElementById(elem_id).innerHTML += ' ';
                    fixing = true;
                    randomGenerated = false;
                    beforeFixTotal = total + 1;
                    pushNextIter(delay);
                }
                else if (i != where[randomCharIndex]){
                    document.getElementById(elem_id).innerHTML += text.charAt(i);
                    i++;
                    pushNextIter(speed);
                }                
            }
        }
    }
    typing();
}