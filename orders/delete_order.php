<?php
include '../includes/db.php';

// Kiểm tra nếu có order_id từ URL
if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Sử dụng prepared statement để xóa đơn hàng
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    
    // Liên kết biến $order_id với câu lệnh chuẩn bị
    $stmt->bind_param("i", $order_id);  // "i" là kiểu dữ liệu integer
    
    // Thực thi câu lệnh
    if ($stmt->execute()) {
        header('Location: ../index.php');  // Chuyển hướng về trang quản lý đơn hàng
    } else {
        echo "Lỗi: " . $stmt->error;
    }
    
    // Đóng statement
    $stmt->close();
} else {
    // Nếu không có order_id hoặc order_id không hợp lệ
    echo "Không có mã đơn hàng hoặc mã đơn hàng không hợp lệ!";
}

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>
