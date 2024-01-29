<?php
header('Content-Type: application/json');
require_once '../connect.php';

if (!isset($_SESSION['EMP_ID'])) {
    $response = [
        'status' => false,
        'message' => 'User not authenticated. EMP_ID not found in the session.'
    ];
    http_response_code(401);
    echo json_encode($response);
    exit();
}
$emp_id = $_SESSION['EMP_ID'];

$ord_id = $_POST['ord_id'];
$tailor_date = $_POST['tailor_date'];

$checkStmt = $conn->prepare("SELECT COUNT(*) FROM tailored_suit WHERE ord_id = :ord_id");
$checkStmt->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);
$checkStmt->execute();
$count = $checkStmt->fetchColumn();

if ($count > 0) {
    $response = [
        'status' => false,
        'message' => 'รหัสลูกค้าซ้ำ'
    ];
    http_response_code(400);
    echo json_encode($response);
    exit();
}

$conn->beginTransaction();

$sqlInsertTailoredSuit = "INSERT INTO tailored_suit (ord_id, tailor_date, emp_id) 
        VALUES (:ord_id, :tailor_date, :emp_id)";

$sqlUpdateOrderSewing = "UPDATE order_sewing SET ord_status = 'เสร็จสิ้น' WHERE ord_id = :ord_id";

try {
    $stmtInsertTailoredSuit = $conn->prepare($sqlInsertTailoredSuit);
    $stmtInsertTailoredSuit->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);
    $stmtInsertTailoredSuit->bindParam(":tailor_date", $tailor_date, PDO::PARAM_STR);
    $stmtInsertTailoredSuit->bindParam(":emp_id", $emp_id, PDO::PARAM_STR);

    if (!$stmtInsertTailoredSuit->execute()) {
        throw new PDOException('Failed to insert data into tailored_suit');
    }

    $stmtUpdateOrderSewing = $conn->prepare($sqlUpdateOrderSewing);
    $stmtUpdateOrderSewing->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);

    if (!$stmtUpdateOrderSewing->execute()) {
        throw new PDOException('Failed to update ord_status in order_sewing');
    }

    $conn->commit();

    $response = [
        'status' => true,
        'message' => 'เพิ่มข้อมูลสำเร็จ'
    ];
    http_response_code(200);
    echo json_encode($response);
} catch (PDOException $e) {
    $conn->rollBack();

    error_log('PDOException: ' . $e->getMessage());
    $response = [
        'status' => false,
        'message' => 'ข้อผิดพลาด: ' . $e->getMessage()
    ];
    http_response_code(500);
    echo json_encode($response);
}
?>
