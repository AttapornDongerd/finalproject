<?php
require_once('../authen.php');
if (!isset($_SESSION['EMP_STATUS']) || $_SESSION['EMP_STATUS'] !== 'พนักงาน') {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการข้อมูลตัดชุด</title>
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
                                        เพิ่มข้อมูลตัดชุด
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
                                            <div class="col-md-12 px-1 px-md-5">
                                                <div class="form-group">
                                                    <label for="ord_id">รหัสสั่งตัดชุด</label>
                                                    <select class="form-control" name="ord_id" id="ord_id" required>
                                                        <option value disabled selected>เลือกรหัสสั่งตัดชุด</option>
                                                        <?php
                                                        $OrderSewingQuery = $conn->query("SELECT ord_id FROM order_sewing");
                                                        $OrderSewing = $OrderSewingQuery->fetchAll(PDO::FETCH_ASSOC);
                                                        foreach ($OrderSewing as $OrderSewing) {
                                                            echo "<option value='" . $OrderSewing['ord_id'] . "'>" . $OrderSewing['ord_id'] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="tailor_date">วันที่</label>
                                                    <input type="date" class="form-control" name="tailor_date" id="tailor_date" value="<?= $result['tailor_date']; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary btn-block mx-auto w-50" name="submit">บันทึกข้อมูล</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- SCRIPTS -->
    <script src="../../plugins/jquery/jquery.min.js"></script>
    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="../../assets/js/adminlte.min.js"></script>

    <script>
        $(function() {
            $('#formData').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '../../service/tailored_suit/create.php',
                    data: $('#formData').serialize()
                }).done(function(resp) {
                    console.log(resp);

                    if (resp.status) {
                        Swal.fire({
                            text: 'เพิ่มข้อมูลเรียบร้อย',
                            icon: 'success',
                            confirmButtonText: 'ตกลง',
                        }).
                        then((result) => {
                            location.assign('./');
                        });
                    } else {
                        Swal.fire({
                            text: resp.message,
                            icon: 'error',
                            confirmButtonText: 'ตกลง',
                        });
                    }
                }).fail(function(xhr, status, error) {
                    Swal.fire({
                        text: 'เกิดข้อผิดพลาดในการส่งข้อมูล: ' + error,
                        icon: 'error',
                        confirmButtonText: 'ตกลง',
                    });
                });
            })
        });
    </script>

</body>

</html>