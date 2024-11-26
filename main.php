<?php 
function display($path){
    $file = fopen($path,'r');
    while (!feof($file)){
        $line = fgets($file);
        echo $line;
    }
}
//Load common top part
display('matter/top.htm');

//Load the changing middle part
if (sizeof($_GET) == 0) $_GET["Page"] = "Home";
switch ($_GET["Page"]) {
    case 'Login':
        display('matter/login.htm');
        break;
    case 'Home':
    default:
        display('matter/main.htm');
        break;
}
//Load common bottom part
display('matter/bot.htm');
?>