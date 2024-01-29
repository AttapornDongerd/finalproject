<?php
header('Content-Type: application/json');
require_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['ord_id'])) {
    $response = [
        'status' => false,
        'message' => 'Missing branch_id in the request'
    ];
    http_response_code(400);
    echo json_encode($response);
    exit();
}

$solv_detail = $data['solv_detail'];
$solv_date = $data['solv_date'];
$ord_id = $data['ord_id'];

$checkSolveStmt = $conn->prepare("SELECT COUNT(*) FROM solve WHERE ord_id = :ord_id");
$checkSolveStmt->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);
$checkSolveStmt->execute();
$countSolve = $checkSolveStmt->fetchColumn();

if ($countSolve == 0) {
    $response = [
        'status' => false,
        'message' => 'Invalid faculty_id or cus_id'
    ];
    http_response_code(400);
    echo json_encode($response);
    exit();
}

$stmt = $conn->prepare("UPDATE solve SET solv_detail = :solv_detail, solv_date = :solv_date 
    WHERE ord_id = :ord_id");

$stmt->bindParam(":solv_detail", $solv_detail, PDO::PARAM_STR);
$stmt->bindParam(":solv_date", $solv_date, PDO::PARAM_STR);
$stmt->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);


if ($stmt->execute()) {
    $response = [
        'status' => true,
        'message' => 'Update Success'
    ];
    http_response_code(200);
    echo json_encode($response);
} else {
    $response = [
        'status' => false,
        'message' => 'Update Failed'
    ];
    http_response_code(500);
    echo json_encode($response);
}
