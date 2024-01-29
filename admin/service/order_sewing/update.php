<?php
    header('Content-Type: application/json');
    require_once '../connect.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['status' => false, 'message' => 'Method Not Allowed']);
        exit();
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $ord_id  = $data['ord_id'];
    $detail_measure = $data['detail_measure'];
    $ord_size = $data['ord_size'];
    $order_price = $data['order_price'];
    $detailmore = $data['detailmore'];
    $ord_date = $data['ord_date'];
    $design_id = $data['design_id'];

    // ตรวจสอบว่ามีรายการสั่งตัดชุดที่ตรงกับรหัสที่ระบุหรือไม่
    $checkOrder_sewingStmt = $conn->prepare("SELECT COUNT(*) FROM order_sewing WHERE ord_id = :ord_id");
    $checkOrder_sewingStmt->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);
    $checkOrder_sewingStmt->execute();
    $countOrder_sewing = $checkOrder_sewingStmt->fetchColumn();

    if ($countOrder_sewing == 0) {
        $response = [
            'status' => false,
            'message' => 'รหัสการสั่งตัดชุดหรือรหัสไม่ถูกต้อง'
        ];
        http_response_code(400);
        echo json_encode($response);
        exit();
    }

    // อัปเดตข้อมูลสั่งตัดชุด
    $stmt = $conn->prepare("UPDATE order_sewing SET detail_measure = :detail_measure, ord_size = :ord_size,
                            order_price = :order_price, detailmore = :detailmore, ord_date = :ord_date, design_id = :design_id WHERE ord_id = :ord_id");

    $stmt->bindParam(":detail_measure", $detail_measure, PDO::PARAM_STR);
    $stmt->bindParam(":ord_size", $ord_size, PDO::PARAM_STR);
    $stmt->bindParam(":order_price", $order_price, PDO::PARAM_STR);
    $stmt->bindParam(":detailmore", $detailmore, PDO::PARAM_STR);
    $stmt->bindParam(":ord_date", $ord_date, PDO::PARAM_STR);
    $stmt->bindParam(":design_id", $design_id, PDO::PARAM_STR);
    $stmt->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $response = [
            'status' => true,
            'message' => 'อัปเดตข้อมูลสำเร็จ'
        ];
        http_response_code(200);
        echo json_encode($response);
    } else {
        $response = [
            'status' => false,
            'message' => 'อัปเดตข้อมูลล้มเหลว'
        ];
        http_response_code(500);
        echo json_encode($response);
    }
?>
