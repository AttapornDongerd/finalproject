<?php
require_once('../authen.php');
if (!isset($_SESSION['EMP_STATUS']) || $_SESSION['EMP_STATUS'] !== 'พนักงาน') {
    header('Location: ../login.php');
    exit();
}

$ord_id  = $_GET['id'];

$sql = "SELECT * FROM queu_e WHERE ord_id = :ord_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':ord_id', $ord_id, PDO::PARAM_STR);
$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);
// print_r($result);
// return;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการข้อมูลคิวช่าง</title>
    <link rel="shortcut icon" type="image/x-icon" href="../../assets/images/PLogo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="../../assets/css/adminlte.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include_once('../includes/sidebar_admin.php') ?>
        <div class="content-wrapper pt-3">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-header border-0 pt-4">
                                    <h4>
                                        <i class="fas fa-user-cog"></i>
                                        แก้ไขข้อมูลคิวช่าง
                                    </h4>
                                    <div class="card-header d-flex justify-content-end">
                                        <a href="index.php" class="btn btn-success mt-3">
                                            กลับหน้าหลัก
                                        </a>
                                    </div>
                                </div>
                                <form id="formData">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 px-1 px-md-5">

                                                <div class="form-group">
                                                    <label for="ord_id">รหัสสั่งตัดชุด</label>
                                                    <input type="text" class="form-control" name="ord_id" id="ord_id" placeholder="รหัสสั่งตัดชุด" value="<?= $result['ord_id']; ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="queue_datebegin">วันที่สั่งตัด</label>
                                                    <input type="date" class="form-control" name="queue_datebegin" id="queue_datebegin" value="<?= $result['queue_datebegin']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="queue_datefinish">วันที่สิ้นสุด</label>
                                                    <input type="date" class="form-control" name="queue_datefinish" id="queue_datefinish" value="<?= $result['queue_datefinish']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="emp_id">รหัสพนักงาน</label>
                                                    <input type="text" class="form-control" name="emp_id" id="emp_id" placeholder="รหัสพนักงาน" value="<?= $result['emp_id']; ?>" readonly>
                                                </div>

                                            </div>
                                        </div>
                                    </div> 
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary btn-block mx-auto w-50" name="submit">บันทึกข้อมูล</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../plugins/jquery/jquery.min.js"></script>
    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="../../assets/js/adminlte.min.js"></script>

    <script>
        $(function() {
            $('#formData').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'PUT',
                    url: '../../service/queu_e/update.php',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        ord_id: $('#ord_id').val(),
                        queue_datebegin: $('#queue_datebegin').val(),
                        queue_datefinish: $('#queue_datefinish').val(),
                        emp_id: $('#emp_id').val()
                    })
                }).done(function(resp) {
                    Swal.fire({
                        text: 'อัพเดทข้อมูลเรียบร้อย',
                        icon: 'success',
                        confirmButtonText: 'ตกลง',
                    }).then((result) => {
                        location.assign('./');
                    });
                });
            });
        });
    </script>

</body>

</html>