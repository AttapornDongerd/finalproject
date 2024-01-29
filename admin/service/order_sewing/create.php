<?php
header('Content-Type: application/json');
require_once '../connect.php';

$response = ['status' => false, 'message' => 'An unexpected error occurred.'];

if (!isset($_SESSION['EMP_ID'])) {
    $response['message'] = 'User not authenticated. EMP_ID not found in the session.';
    http_response_code(401);
    echo json_encode($response);
    exit();
}

$emp_id = $_SESSION['EMP_ID'];

try {
    $conn->beginTransaction();

    $ord_id = $_POST['ord_id'];
    $cus_id = $_POST['cus_id'];
    $design_id = $_POST['design_id'];
    $detail_measure = $_POST['detail_measure'];
    $ord_size = $_POST['ord_size'];
    $order_price = $_POST['order_price'];
    $detailmore = $_POST['detailmore'];
    $ord_date = $_POST['ord_date'];
    $price_detailmore = $_POST['price_detailmore'];
    $ord_status = "กำลังตัดเย็บ";

    // Validate design_id
    $checkDesignStmt = $conn->prepare("SELECT COUNT(*) FROM design WHERE design_id = :design_id");
    $checkDesignStmt->bindParam(":design_id", $design_id, PDO::PARAM_STR);
    $checkDesignStmt->execute();
    $countDesign = $checkDesignStmt->fetchColumn();

    if ($countDesign == 0) {
        $response = ['status' => false, 'message' => 'Invalid design_id'];
        http_response_code(400);
        echo json_encode($response);
        exit();
    }

    // Check for duplicate ord_id
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM order_sewing WHERE ord_id = :ord_id");
    $checkStmt->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    if ($count > 0) {
        $response = ['status' => false, 'message' => 'Duplicate ord_id'];
        http_response_code(400);
        echo json_encode($response);
        exit();
    }

    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM customer WHERE cus_id = :cus_id");
    $checkStmt->bindParam(":cus_id", $cus_id, PDO::PARAM_STR);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    if ($count == 0) {
        $response = ['status' => false, 'message' => 'Duplicate cus_id'];
        http_response_code(400);
        echo json_encode($response);
        exit();
    }

    // Insert into order_sewing
    $sqlOrderSewing = "INSERT INTO order_sewing (ord_id, detail_measure, ord_size, order_price,
            detailmore, ord_date, ord_status, emp_id, design_id, price_detailmore, cus_id) 
            VALUES (:ord_id, :detail_measure, :ord_size, :order_price, :detailmore, :ord_date, :ord_status, :emp_id, :design_id, :price_detailmore, :cus_id)";

    $stmtOrderSewing = $conn->prepare($sqlOrderSewing);
    $stmtOrderSewing->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);
    $stmtOrderSewing->bindParam(":cus_id", $cus_id, PDO::PARAM_STR);
    $stmtOrderSewing->bindParam(":detail_measure", $detail_measure, PDO::PARAM_STR);
    $stmtOrderSewing->bindParam(":ord_size", $ord_size, PDO::PARAM_STR);
    $stmtOrderSewing->bindParam(":order_price", $order_price, PDO::PARAM_STR);
    $stmtOrderSewing->bindParam(":detailmore", $detailmore, PDO::PARAM_STR);
    $stmtOrderSewing->bindParam(":ord_date", $ord_date, PDO::PARAM_STR);
    $stmtOrderSewing->bindParam(":ord_status", $ord_status, PDO::PARAM_STR);
    $stmtOrderSewing->bindParam(":emp_id", $emp_id, PDO::PARAM_STR);
    $stmtOrderSewing->bindParam(":design_id", $design_id, PDO::PARAM_STR);
    $stmtOrderSewing->bindParam(":price_detailmore", $price_detailmore, PDO::PARAM_STR);

    if ($stmtOrderSewing->execute()) {
        // Insert into queue
        $queueSql = "INSERT INTO queu_e (ord_id, ord_date, cus_id) 
             VALUES (:ord_id, :ord_date, :cus_id)";

        $stmtQueue = $conn->prepare($queueSql);
        $stmtQueue->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);
        $stmtQueue->bindParam(":ord_date", $ord_date, PDO::PARAM_STR);
        $stmtQueue->bindParam(":cus_id", $cus_id, PDO::PARAM_STR);

        if ($stmtQueue->execute()) {
            // Insert into tailored_suit
            $tailoredSql = "INSERT INTO tailored_suit (ord_id, emp_id) VALUES (:ord_id, :emp_id)";
            $stmtTailored = $conn->prepare($tailoredSql);
            $stmtTailored->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);
            $stmtTailored->bindParam(":emp_id", $emp_id, PDO::PARAM_STR);

            if ($stmtTailored->execute()) {
                $conn->commit();
                $response = ['status' => true, 'message' => 'Create Success'];
                http_response_code(200);
                echo json_encode($response);
            } else {
                $conn->rollBack();
                $response = ['status' => false, 'message' => 'Create failed in tailored_suit'];
                http_response_code(500);
                echo json_encode($response);
            }
        } else {
            $conn->rollBack();
            $response = ['status' => false, 'message' => 'Create failed in queu_e'];
            http_response_code(500);
            echo json_encode($response);
        }
    } else {
        $conn->rollBack();
        $response = ['status' => false, 'message' => 'Create failed in order_sewing'];
        http_response_code(500);
        echo json_encode($response);
    }
} catch (PDOException $e) {
    $conn->rollBack();
    $response = ['status' => false, 'message' => 'Error: ' . $e->getMessage()];
    http_response_code(500);
    echo json_encode($response);
}
?>