<?php
$servername = "localhost";
$username = "root";  // Tên người dùng MySQL
$password = "";      // Mật khẩu MySQL
$dbname = "order_management";  // Tên cơ sở dữ liệu
$port = 3306;
// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
