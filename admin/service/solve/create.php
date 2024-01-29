<?php
header('Content-Type: application/json');
require_once '../connect.php';

// ดึงเลขลำดับล่าสุดของ ord_id จากตาราง solve
// $maxSolveStmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(ord_id, 7) AS SIGNED)) FROM solve");
// $maxSolveStmt->execute();
// $maxSolveNumber = $maxSolveStmt->fetchColumn();
// $nextSolveNumber = $maxSolveNumber + 1;
// $ord_id = 'O-01-' . sprintf('%04d', $nextSolveNumber);


$ord_id = $_POST['ord_id'];
$solv_detail = $_POST['solv_detail'];
$solv_date = $_POST['solv_date'];


$checkStmt = $conn->prepare("SELECT COUNT(*) FROM solve WHERE ord_id = :ord_id");
$checkStmt->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);
$checkStmt->execute();
$count = $checkStmt->fetchColumn();

if ($count > 0) {
    $response = [
        'status' => false,
        'message' => 'รหัสแก้ชุดซ้ำ'
    ];
    http_response_code(400);
    echo json_encode($response);
    exit();
}

$sql = "INSERT INTO solve (ord_id, solv_detail, solv_date) 
        VALUES (:ord_id, :solv_detail, :solv_date)";

$stmt = $conn->prepare($sql);

$stmt->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);
$stmt->bindParam(":solv_detail", $solv_detail, PDO::PARAM_STR);
$stmt->bindParam(":solv_date", $solv_date, PDO::PARAM_STR);

try {
    if ($stmt->execute()) {
        $response = [
            'status' => true,
            'message' => 'เพิ่มข้อมูลสำเร็จ'
        ];
        http_response_code(200);
        echo json_encode($response);
    } else {
        $response = [
            'status' => false,
            'message' => 'เพิ่มข้อมูลล้มเหลว'
        ];
        http_response_code(500);
        echo json_encode($response);
    }
} catch (PDOException $e) {
    error_log('PDOException: ' . $e->getMessage());
    $response = [
        'status' => false,
        'message' => 'ข้อผิดพลาด: ' . $e->getMessage()
    ];
    http_response_code(500);
    echo json_encode($response);
}
?>