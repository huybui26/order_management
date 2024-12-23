<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $discount_code = $_POST['discount_code'];
    $response = array('success' => false, 'message' => 'Mã giảm giá không hợp lệ!', 'discount_value' => 0);

    // Kiểm tra mã giảm giá
    $discount_sql = "SELECT * FROM discount_codes WHERE code = '$discount_code' AND expiry_date >= CURDATE()";
    $discount_result = $conn->query($discount_sql);

    if ($discount_result->num_rows > 0) {
        $discount = $discount_result->fetch_assoc();
        $response = array(
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'discount_value' => $discount['value']
        );
    }

    echo json_encode($response);
}
