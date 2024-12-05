<?php 
function display($path){
    $file = fopen($path,"r");
    while (!feof($file)) {
        $line = fgets($file);
        echo $line;
    }
    fclose($file);
}

display("matter/top.htm");

if (sizeof($_GET) == 0) {
    display("matter/main.htm");
}
else {
    switch ($_GET["Page"]) {
        case "Home": 
            display("matter/main.htm");
            break;
        case "Dashboard":
            display("matter/dashboard.htm");
            break;
    }
}

display("matter/bot.htm");

?>