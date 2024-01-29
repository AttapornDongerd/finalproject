<?php
require_once('../authen.php');
if (!isset($_SESSION['EMP_STATUS']) || $_SESSION['EMP_STATUS'] !== 'พนักงาน') {
    header('Location: ../../login.php');
    exit();
}

if (!isset($_GET['ord_id'])) {
    echo "ord_id is missing in the URL.";
    exit();
}

$ord_id = $_GET['ord_id'];
$stmt = $conn->prepare("SELECT order_sewing.*, employees.emp_id, employees.emp_name, design.detail, customer.cus_name FROM order_sewing 
                                    LEFT JOIN employees ON order_sewing.emp_id = employees.emp_id
                                    LEFT JOIN design ON order_sewing.design_id = design.design_id
                                    LEFT JOIN customer ON order_sewing.cus_id = customer.cus_id
                                    WHERE order_sewing.ord_id = :ord_id");
$stmt->bindParam(":ord_id", $ord_id, PDO::PARAM_STR);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    echo "Order Sewing not found.";
    exit();
}

$order_sewingInfo = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ข้อมูลสั่งตัดชุด</title>
    <!-- stylesheet -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="../../assets/css/adminlte.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- Datatables -->
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include_once('../includes/sidebar_admin.php') ?>
        <div class="content-wrapper pt-4">
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow">
                                <div class="card-header border-0 pt-4">
                                    <h4>
                                        <i class="nav-icon fas fa-puzzle-piece"></i>
                                        ข้อมูลสั่งตัดชุด
                                    </h4>
                                    <div class="card-header d-flex justify-content-end">
                                        <button class="btn btn-info my-3" onclick="goBack()">
                                            <i class="fas fa-arrow-left"></i>
                                            กลับหน้าหลัก
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body px-5">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card shadow-sm">
                                                <div class="card-header pt-4">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-bookmark"></i>
                                                        รายละเอียดเพิ่มเติม
                                                    </h3>
                                                </div>
                                                <div class="card-body px-5">
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">รหัสสั่งตัดชุด :</p>
                                                        <div class="col-xl-9">
                                                            <?= $order_sewingInfo['ord_id']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">รายละเอียดวัดตัว :</p>
                                                        <div class="col-xl-9">
                                                            <?= $order_sewingInfo['detail_measure']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">ขนาดผ้า/เมตร :</p>
                                                        <div class="col-xl-9">
                                                            <?= $order_sewingInfo['ord_size']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted"> ค่าตัดชุด/บาท :</p>
                                                        <div class="col-xl-9">
                                                            <?= $order_sewingInfo['order_price']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">รายละเอียดเพิ่มเติม :</p>
                                                        <div class="col-xl-9">
                                                            <?= $order_sewingInfo['detailmore']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted"> ราคาเพิ่มเติม/บาท :</p>
                                                        <div class="col-xl-9">
                                                            <?= $order_sewingInfo['price_detailmore']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">วันที่สั่งตัด :</p>
                                                        <div class="col-xl-9">
                                                            <?php
                                                            $formattedDate = DateTime::createFromFormat('Y-m-d', $order_sewingInfo['ord_date']);
                                                            $thaiMonthNames = [
                                                                1 => 'มกราคม',
                                                                2 => 'กุมภาพันธ์',
                                                                3 => 'มีนาคม',
                                                                4 => 'เมษายน',
                                                                5 => 'พฤษภาคม',
                                                                6 => 'มิถุนายน',
                                                                7 => 'กรกฎาคม',
                                                                8 => 'สิงหาคม',
                                                                9 => 'กันยายน',
                                                                10 => 'ตุลาคม',
                                                                11 => 'พฤศจิกายน',
                                                                12 => 'ธันวาคม',
                                                            ];
                                                            echo $formattedDate->format('d') . ' ' . $thaiMonthNames[(int)$formattedDate->format('m')] . ' ' . $formattedDate->format('Y');
                                                            ?>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted"> สถานะ :</p>
                                                        <div class="col-xl-9">
                                                            <?= $order_sewingInfo['ord_status']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted"> รหัสพนักงาน :</p>
                                                        <div class="col-xl-9">
                                                            <?= $order_sewingInfo['emp_name']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">รหัสแบบชุด :</p>
                                                        <div class="col-xl-9">
                                                            <?= $order_sewingInfo['design_id']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">รายละเอียดผ้า :</p>
                                                        <div class="col-xl-9">
                                                            <?= $order_sewingInfo['detail']; ?>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">ชื่อลูกค้า :</p>
                                                        <div class="col-xl-9">
                                                            <?= $order_sewingInfo['cus_name']; ?>
                                                        </div>
                                                    </div>
                                                    <!-- <button type="button" class="btn btn-outline-success btn-block mx-auto" style="width: 20%;" onclick="printDocument()">
                                                        Print
                                                    </button> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- scripts -->
    <script src="../../plugins/jquery/jquery.min.js"></script>
    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="../../assets/js/adminlte.min.js"></script>

    <!-- datatables -->
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>


    <script>
    // Function to handle report submission
    function submitReport() {
        // Get the report content from the textarea
        const reportContent = document.getElementById('reportInput').value;

        // You can now send the reportContent to the server using AJAX or perform any other actions
        // ...

        // For demonstration purposes, you can log the report content to the console
        console.log('Report Content:', reportContent);

        // Optionally, you can show a confirmation message to the user
        Swal.fire({
            text: 'รายงานถูกส่งเรียบร้อย',
            icon: 'success',
            confirmButtonText: 'ตกลง',
        });
    }

    // Function to handle printing
    function printDocument() {
        window.print();
    }
</script>

    <script>
        function goBack() {
            window.history.back();
        }


        $(function() {
            $.ajax({
                type: "GET",
                url: "../../service/order_sewing/info.php"
            }).done(function(data) {
                let tableData = []
                data.response.forEach(function(item, index) {
                    tableData.push([
                        item['ord_id'],
                        item['detail_measure'],
                        item['ord_size'],
                        item['order_price'],
                        item['detailmore'],
                        item['ord_status'],
                        item['emp_id'],
                        item['design_id'],
                        item['cus_id']
                    ])
                })
                initDataTables(tableData)
            }).fail(function() {
                Swal.fire({
                    text: 'ไม่สามารถเรียกดูข้อมูลได้',
                    icon: 'error',
                    confirmButtonText: 'ตกลง',
                }).then(function() {
                    location.assign('../dashboard_2')
                })
            })

            function initDataTables(tableData) {
                $('#logs').DataTable({
                    paging: false,
                    ordering: false,
                    info: false,
                    searching: false,
                    data: tableData,
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function(row) {
                                    return 'กิจกรรม'
                                }
                            }),
                            renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                                tableClass: 'table'
                            })
                        }
                    },
                    language: {
                        "lengthMenu": "แสดงข้อมูล MENU แถว",
                        "zeroRecords": "ไม่พบข้อมูลที่ต้องการ",
                        "info": "แสดงหน้า PAGE จาก PAGES",
                        "infoEmpty": "ไม่พบข้อมูลที่ต้องการ",
                        "infoFiltered": "(filtered from MAX total records)",
                        "search": 'ค้นหา',
                        "paginate": {
                            "previous": "ก่อนหน้านี้",
                            "next": "หน้าต่อไป"
                        }
                    },
                });
            }
        });
    </script>
</body>

</html>