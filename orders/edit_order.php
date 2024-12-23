<?php
include '../includes/db.php'; // Kết nối cơ sở dữ liệu

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy thông tin từ form
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Cập nhật trạng thái đơn hàng trong cơ sở dữ liệu
    $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $status, $order_id);

    if ($stmt->execute()) {
        echo "Cập nhật trạng thái đơn hàng thành công!";
    } else {
        echo "Có lỗi xảy ra khi cập nhật trạng thái!";
    }

    $stmt->close();
    $conn->close();
}
?>
