<?php
require 'vendor/autoload.php'; // Tải thư viện PhpSpreadsheet
include 'includes/db.php'; // Kết nối cơ sở dữ liệu

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Lấy dữ liệu đơn hàng từ database
$search = isset($_POST['search']) ? $_POST['search'] : '';
$filter_status = isset($_POST['status']) ? $_POST['status'] : '';

$sql = "SELECT * FROM orders WHERE recipient_name LIKE '%$search%' AND status != 'Cancelled'";
if (!empty($filter_status)) {
    $sql .= " AND status = '$filter_status'";
}

$result = $conn->query($sql);

// Tạo file Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Thiết lập tiêu đề cột
$sheet->setCellValue('A1', 'ID Đơn Hàng')
      ->setCellValue('B1', 'Tên Người Nhận')
      ->setCellValue('C1', 'Địa Chỉ')
      ->setCellValue('D1', 'Phương Thức Vận Chuyển')
      ->setCellValue('E1', 'Phương Thức Thanh Toán')
      ->setCellValue('F1', 'Tổng Tiền')
      ->setCellValue('G1', 'Trạng Thái');

// Thêm dữ liệu vào Excel
$rowIndex = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue("A$rowIndex", $row['order_id'])
          ->setCellValue("B$rowIndex", $row['recipient_name'])
          ->setCellValue("C$rowIndex", $row['shipping_address'])
          ->setCellValue("D$rowIndex", $row['shipping_method'])
          ->setCellValue("E$rowIndex", $row['payment_method'])
          ->setCellValue("F$rowIndex", number_format($row['total_amount'], 2) . ' VND')
          ->setCellValue("G$rowIndex", $row['status']);
    $rowIndex++;
}

// Xuất file Excel
$writer = new Xlsx($spreadsheet);

// Gửi header để tải file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="orders.xlsx"');
header('Cache-Control: max-age=0');

// Xuất file
$writer->save('php://output');
exit;
?>
