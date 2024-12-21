<?php 
// Chloé Legué & Ling Jie Chen
function readHTMLfile($filename) {
    $fp = fopen($filename,"r");
    while (($data = fgets($fp)) !== false) {
        echo $data;
    }
    fclose($fp);
}

function checkMethod($method) {
    switch ($method["type"]){
        case "login":
            readHTMLfile("matter/login.htm");
            break;
        case "signup":
            readHTMLfile("matter/signup.htm");
            break;
        case "":
            echo "";
            break;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    checkMethod($_POST);
}
if ($_SERVER['REQUEST_METHOD'] == "GET"){
    checkMethod($_GET);
}
?>