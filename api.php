<?php
// ===== CORS FIX (VERY IMPORTANT) =====
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){ exit; }


// ===== DATABASE CONFIG =====
$host = "db_server_ip";
$user = "appuser";
$pass = "Password123";
$db   = "expense_app";

// CONNECT DB
$conn = new mysqli($host,$user,$pass,$db);

if($conn->connect_error){
    die(json_encode(["status"=>"error","message"=>"DB Connection Failed"]));
}


// ===== GET ACTION =====
$action = $_GET['action'] ?? '';


// ===== ADD EXPENSE =====
if($action == "add"){
    $data = json_decode(file_get_contents("php://input"), true);

    $title  = $data['title'] ?? '';
    $amount = $data['amount'] ?? '';

    if($title=="" || $amount==""){
        echo json_encode(["status"=>"error","message"=>"Empty fields"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO expenses(title,amount) VALUES(?,?)");
    $stmt->bind_param("sd",$title,$amount);
    $stmt->execute();

    echo json_encode(["status"=>"success"]);
}


// ===== LIST EXPENSES =====
elseif($action == "list"){
    $result = $conn->query("SELECT * FROM expenses ORDER BY id DESC");

    $data = [];
    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }

    echo json_encode(["status"=>"success","data"=>$data]);
}


// ===== DELETE EXPENSE =====
elseif($action == "delete"){
    $id = $_GET['id'] ?? 0;
    $conn->query("DELETE FROM expenses WHERE id=$id");

    echo json_encode(["status"=>"success"]);
}


// ===== INVALID ACTION =====
else{
    echo json_encode(["status"=>"error","message"=>"Invalid action"]);
}
?>
