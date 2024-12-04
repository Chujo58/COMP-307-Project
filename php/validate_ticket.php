<?php 
if ($_SERVER['REQUEST_METHOD'] == "POST"){
    $conn = new mysqli("localhost", "root", "", "comp307project");

    if ($conn->connect_error) {
        die("Internal Server Error: ". $conn->connect_error);
    }

    $query_ticket = "SELECT exp_date FROM tickets WHERE ticket_id='" . $_POST['ticket'] . "'";
    $ticket_res = $conn->query($query_ticket);
    if ($ticket_res->num_rows != 0 && date("Y-m-d") < $ticket_res->fetch_assoc()["exp_date"]) {
        print("Valid");
    }
    else {
        print("Not valid");
    }
}
?>