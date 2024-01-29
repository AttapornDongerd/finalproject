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
    <title>จัดการข้อมูลสั่งตัดชุด</title>
    <link rel="shortcut icon" type="image/x-icon" href="../../assets/images/PLogo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="../../assets/css/adminlte.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <style>
        .button-container {
            display: flex;
            justify-content: space-between;
        }

        .btn {
            display: inline-block;
        }
    </style>
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
                                        ข้อมูลสั่งตัดชุด
                                    </h4>
                                    <div class="card-header d-flex justify-content-end">
                                        <a href="form-create.php" class="btn btn-success mt-3">
                                            <i class="fas fa-plus"></i>
                                            เพิ่มข้อมูล
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="logs" class="table table-hover" width="100%">
                                    </table>
                                </div>
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
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(function() {
            $.ajax({
                type: "GET",
                url: "../../service/order_sewing/index.php"
            }).done(function(data) {
                if (data.status) {}
                let tableData = [];
                data.response.forEach(function(item, index) {
                    tableData.unshift([
                        item.ord_id,
                        item.ord_date,
                        item.emp_id,
                        item.design_id,
                        `<span class="badge badge-danger">${item.ord_status}</span>`,
                        `<div class="btn-group" role="group">
                            <a href="info.php?ord_id=${item.ord_id}" type="button" class="btn btn-info">
                                <i class="far fa-edit"></i> รายละเอียดเพิ่มเติม
                            </a>
                            <a href="form-edit.php?id=${item.ord_id}" type="button" class="btn btn-warning text-white">
                                <i class="far fa-edit"></i> แก้ไข
                            </a>
                            <button type="button" class="btn btn-danger" id="delete" data-id="${item.ord_id}" data-index="${index}">
                                <i class="far fa-trash-alt"></i> ลบ
                            </button>
                        </div>`
                    ])
                })

                initDataTables(tableData)
            }).fail(function() {
                Swal.fire({
                    text: 'ไม่สามารถเรียกดูข้อมูลได้',
                    icon: 'error',
                    confirmButtonText: 'ตกลง',
                }).then(function() {
                    location.assign('../dashboard')
                })
            })

            function initDataTables(tableData) {
                $('#logs').DataTable({
                    data: tableData,
                    columns: [{
                            title: "รหัสสั่งตัดชุด",
                            className: "align-middle"
                        },

                        {
                            title: "วันที่สั่งตัด",
                            className: "align-middle"
                        },

                        {
                            title: "รหัสพนักงาน",
                            className: "align-middle"
                        },

                        {
                            title: "รหัสแบบชุด",
                            className: "align-middle"
                        },

                        {
                            title: "สถานะ",
                            className: "align-middle"
                        },

                        {
                            title: "Actions",
                            className: "align-middle"
                        }
                    ],
                    initComplete: function() {
                        $(document).on('click', '#delete', function() {
                            let ord_id = $(this).data('id');
                            let index = $(this).data('index');
                            Swal.fire({
                                text: "คุณแน่ใจหรือไม่...ที่จะลบรายการนี้?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'ใช่! ลบเลย',
                                cancelButtonText: 'ยกเลิก'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        type: "DELETE",
                                        url: "../../service/order_sewing/delete.php",
                                        data: JSON.stringify({
                                            ord_id: ord_id
                                        }),
                                        contentType: "application/json; charset=utf-8",
                                        dataType: "json"
                                    }).done(function(data) {
                                        Swal.fire({
                                            text: 'รายการของคุณถูกลบเรียบร้อย',
                                            icon: 'success',
                                            confirmButtonText: 'ตกลง',
                                        }).then((result) => {
                                            location.reload();
                                        })
                                    }).fail(function(jqXHR, textStatus, errorThrown) {
                                        Swal.fire({
                                            text: 'ไม่สามารถลบข้อมูลรายการนี้ได้',
                                            icon: 'info',
                                            confirmButtonText: 'ตกลง',
                                        })
                                        console.log("AJAX Error: " + textStatus + ' - ' + errorThrown);
                                    })
                                }

                            })
                        })
                    },

                })
            }
        })
    </script>
</body>

</html>