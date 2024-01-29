<?php
    header('Content-Type: application/json');
    require_once '../connect.php';
?>

<?php
    #process
    $sql = "SELECT queu_e.*, customer.cus_name,order_sewing.ord_date FROM queu_e
    LEFT JOIN customer ON queu_e.cus_id = customer.cus_id
    LEFT JOIN order_sewing ON queu_e.ord_id = order_sewing.ord_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $response = [
        'status' => true,
        'message' => 'Get Data Manager Success'
    ];

    // ดึงข้อมูลจาก $response ไปแสดงผล
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        $response['response'][] = $row;
    }

    http_response_code(200);
    echo json_encode($response);
?>