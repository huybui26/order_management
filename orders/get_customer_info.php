<?php
include '../includes/db.php';

if (isset($_POST['customer_id'])) {
    $customer_id = $_POST['customer_id'];

    // Truy vấn lấy thông tin khách hàng
    $sql = "SELECT name, address FROM customers WHERE customer_id = '$customer_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        // Trả về thông tin khách hàng dưới dạng JSON
        echo json_encode($customer);
    } else {
        echo json_encode(['error' => 'Không tìm thấy khách hàng']);
    }
}
?>
