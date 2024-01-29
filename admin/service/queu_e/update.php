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
        'message' => 'Missing ord_id in the request'
    ];
    http_response_code(400);
    echo json_encode($response);
    exit();
}

$queue_datebegin = $data['queue_datebegin'];
$queue_datefinish = $data['queue_datefinish'];
$emp_id = $data['emp_id'];
$ord_id = $data['ord_id'];

$checkQueueStmt = $conn->prepare("SELECT COUNT(*) FROM queu_e WHERE ord_id = :ord_id");
$checkQueueStmt->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);
$checkQueueStmt->execute();
$countQueue = $checkQueueStmt->fetchColumn();

if ($countQueue == 0) {
    $response = [
        'status' => false,
        'message' => 'Invalid ord_id'
    ];
    http_response_code(400);
    echo json_encode($response);
    exit();
}

$stmt = $conn->prepare("UPDATE queu_e SET queue_datebegin = :queue_datebegin, queue_datefinish = :queue_datefinish, emp_id = :emp_id WHERE ord_id = :ord_id");
$stmt->bindParam(":queue_datebegin", $queue_datebegin, PDO::PARAM_STR);
$stmt->bindParam(":queue_datefinish", $queue_datefinish, PDO::PARAM_STR);
$stmt->bindParam(":emp_id", $emp_id, PDO::PARAM_STR);
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
?>
