<?php
session_start();
session_unset(); // Xóa tất cả các biến session
session_destroy(); // Hủy phiên làm việc
header('Location: orders/login.php'); // Chuyển hướng về trang đăng nhập
exit;
?>
