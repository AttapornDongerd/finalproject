<?php
    require_once('../authen.php');
    if (!isset($_SESSION['EMP_STATUS']) || $_SESSION['EMP_STATUS'] !== 'เจ้าของร้าน') {
        header('Location: ../login.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>จัดการข้อมูลแบบชุด</title>
    <link rel="shortcut icon" type="image/x-icon" href="../../assets/images/PLogo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="../../plugins/summernote/summernote-bs4.css">
    <link rel="stylesheet" href="../../assets/css/adminlte.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include_once('../includes/sidebar_superadmin.php') ?>
        <div class="content-wrapper pt-3">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header border-0 pt-4">
                                    <h4>
                                        <i class="fas fa-shopping-cart"></i>
                                        เพิ่มข้อมูลแบบชุด
                                    </h4>
                                    <div class="card-header d-flex justify-content-end">
                                        <a href="index.php" class="btn btn-success mt-3">
                                            กลับหน้าหลัก
                                        </a>
                                    </div>
                                </div>
                                <form id="formData" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <div class="form-row">
                                        <div class="form-group col-md-6">
                                                <label for="design_id">รหัสแบบชุด</label>
                                                <input type="text" class="form-control" name="design_id" id="design_id" placeholder="รหัสแบบชุด">
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="design_image">ภาพชุด</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="design_image" name="txt_file" onchange="updateFileName()">
                                                    <label class="custom-file-label" for="design_image" id="fileLabel">เลือกรูปภาพ</label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="detail">รายละเอียดแบบชุด</label>
                                                <textarea id="detail" class="textarea" name="detail" placeholder="Place some text here">
                                            </textarea>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="starting_price">ราคา</label>
                                                <input type="text" class="form-control" name="starting_price" id="starting_price" placeholder="ราคา">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="period">ระยะเวลาตัดชุด</label>
                                                <input type="text" class="form-control" name="period" id="period" placeholder="ระยะเวลาตัดชุด">
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary btn-block mx-auto w-75" name="submit">บันทึกข้อมูล</button>
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
    <script src="../../plugins/summernote/summernote-bs4.min.js"></script>
    <script src="../../assets/js/adminlte.min.js"></script>

    <script>
        function updateFileName() {
            var input = document.getElementById('design_image');
            var label = document.getElementById('fileLabel');
            var fileName = input.files[0].name;
            label.innerHTML = fileName;
        }

        $(function() {
            $('#detail').summernote({
                height: 300,
            });
            $('#formData').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: '../../service/design/create.php',
                    data: formData,
                    contentType: false,
                    processData: false
                }).done(function(resp) {
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