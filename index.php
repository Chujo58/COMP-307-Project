<?php
function display($path){
    $file = fopen($path,"r");
    while (!feof($file)){
        $line = fgets($file);
        echo $line;
    }
}

//Display top of the page
display('matter/top.htm');

if (sizeof($_GET)==0){
    $_GET["Page"] = "Home";
}

switch ($_GET["Page"]) {
    case 'Home':
        display('matter/home.htm');
        break;
    
    default:
        display('matter/home.htm');
        break;
}

//Display bottom of the page
display('matter/bot.html');
?>