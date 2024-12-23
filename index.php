<?php
session_start();

// Kiểm tra nếu chưa đăng nhập, chuyển hướng về trang login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: orders/login.php');
    exit;
}
// Kết nối cơ sở dữ liệu
include 'includes/db.php';

// Xử lý tìm kiếm và lọc đơn hàng
$search = '';
$filter_status = '';

// Kiểm tra nếu có tìm kiếm
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

// Kiểm tra nếu có lọc trạng thái
if (isset($_POST['status'])) {
    $filter_status = $_POST['status'];
}

// Lấy trang hiện tại
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Hiển thị 10 đơn hàng mỗi trang
$offset = ($page - 1) * $limit;

// Truy vấn đơn hàng từ cơ sở dữ liệu (không tính đơn đã hủy)
$sql = "SELECT * FROM orders WHERE recipient_name LIKE '%$search%' AND status != 'Cancelled'";

if (!empty($filter_status)) {
    $sql .= " AND status = '$filter_status'";
}

// Thêm phân trang
$sql .= " LIMIT $limit OFFSET $offset";

// Kiểm tra kết quả truy vấn
$result = $conn->query($sql);

if ($result === false) {
    echo "Lỗi truy vấn: " . $conn->error;
    exit;
}

$sql_pending = "SELECT COUNT(*) as pending FROM orders WHERE status = 'Pending' AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = MONTH(CURRENT_DATE())";
$sql_completed = "SELECT COUNT(*) as completed FROM orders WHERE status = 'Completed' AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = MONTH(CURRENT_DATE())";
$sql_cancelled = "SELECT COUNT(*) as cancelled FROM orders WHERE status = 'Cancelled' AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = MONTH(CURRENT_DATE())";
$sql_total_month_sales = "SELECT SUM(total_amount) as total_month_sales FROM orders WHERE status != 'Cancelled' AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = MONTH(CURRENT_DATE())";

// Thực hiện các truy vấn
$result_pending = $conn->query($sql_pending)->fetch_assoc();
$result_completed = $conn->query($sql_completed)->fetch_assoc();
$result_cancelled = $conn->query($sql_cancelled)->fetch_assoc();
$result_total_month_sales = $conn->query($sql_total_month_sales)->fetch_assoc();

// Truy vấn tổng số đơn hàng
$sql_total_orders = "SELECT COUNT(*) as total FROM orders WHERE recipient_name LIKE '%$search%' AND status != 'Cancelled'";
if (!empty($filter_status)) {
    $sql_total_orders .= " AND status = '$filter_status'";
}
$result_total_orders = $conn->query($sql_total_orders);
$total_orders = $result_total_orders->fetch_assoc()['total'];
$total_pages = ceil($total_orders / $limit); // Số trang
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <div class="sidebar">
        <!-- <img src="path_to_shop_image.jpg" alt="Shop Logo"> -->
        <h1>Gateaux</h1>
        <div class="nav-items">
            <div class="nav-item">
                <a href="#">Quản lý khách hàng</a>
            </div>
            <div class="nav-item">
                <a href="#">Quản lý sản phẩm</a>
            </div>

            <!-- Quản lý đơn hàng với các mục con -->
            <div class="nav-item">
                <a href="#" data-bs-toggle="collapse" data-bs-target="#orderManagement" aria-expanded="false" aria-controls="orderManagement">Quản lý đơn hàng</a>
                <div class="collapse" id="orderManagement">
                    <div class="nav-item pl-4">
                        <a href="#">Đơn hàng</a>
                    </div>
                    <div class="nav-item pl-4">
                        <a href="orders/add_order.php">Tạo đơn hàng</a>
                    </div>
                </div>
            </div>
            <div class="nav-item">
                <a href="#">Quản lý doanh thu</a>
            </div>
            <div class="nav-item">
                <a href="#">Cài đặt hệ thống</a>
            </div>
        </div>
        <button class="logout-btn">
            <a href="http://localhost/order_management/orders/login.php" style="text-decoration: none; color: white;">Đăng Xuất</a>
        </button>

    </div>


    <div class="content">
            <div class="stats-section">
                <div class="stats-card">
                    <h6>Số Đơn Hàng Chưa Xử Lý</h6>
                    <p class="value"><?php echo $result_pending['pending']; ?></p>
                </div>
                <div class="stats-card">
                    <h6>Số Đơn Hàng Hoàn Thành</h6>
                    <p class="value"><?php echo $result_completed['completed']; ?></p>
                </div>
                <div class="stats-card">
                    <h6>Số Đơn Hàng Đã Hủy</h6>
                    <p class="value"><?php echo $result_cancelled['cancelled']; ?></p>
                </div>
                <div class="stats-card">
                    <h6>Tổng Doanh Thu Tháng Này</h6>
                    <p class="value"><?php echo number_format($result_total_month_sales['total_month_sales'], 2); ?> VND</p>
                </div>
            </div>

        <!-- Form Tìm kiếm và Lọc -->
        <form action="index.php" method="POST" class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên người nhận" value="<?php echo $search; ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">Tất cả trạng thái</option>
                        <option value="Pending" <?php echo ($filter_status == 'Pending') ? 'selected' : ''; ?>>Chờ xử lý</option>
                        <option value="Completed" <?php echo ($filter_status == 'Completed') ? 'selected' : ''; ?>>Hoàn thành</option>
                        <option value="Cancelled" <?php echo ($filter_status == 'Cancelled') ? 'selected' : ''; ?>>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tìm kiếm và Lọc</button>
                </div>
                <div class="col-md-2">
            <a href="export_orders.php" class="btn btn-success w-100">Xuất File Excel</a>
         </div>
                 <!-- Nút Tạo Đơn Hàng -->
                 <div class="col-md-2 ">
                        <a href="orders/add_order.php" class="btn btn-custom w-100 btn-success">Tạo Đơn Hàng Mới</a>
                    </div>
            </div>
        </form>


            <!-- Bảng danh sách đơn hàng -->
            <table class="table table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Đơn Hàng</th>
                        <th>Tên Người Nhận</th>
                        <th>Địa Chỉ</th>
                        <th>Phương Thức Vận Chuyển</th>
                        <th>Phương Thức Thanh Toán</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['order_id']; ?></td>
                            <td><?php echo $row['recipient_name']; ?></td>
                            <td><?php echo $row['shipping_address']; ?></td>
                            <td><?php echo $row['shipping_method']; ?></td>
                            <td><?php echo $row['payment_method']; ?></td>
                            <td><?php echo number_format($row['total_amount'], 2); ?> VND</td>
                            <td>
                                <span class="badge 
                                    <?php 
                                        if ($row['status'] == 'Pending') echo 'bg-warning';
                                        if ($row['status'] == 'Completed') echo 'bg-success';
                                        if ($row['status'] == 'Cancelled') echo 'bg-danger';
                                    ?>">
                                    <?php 
                                        if ($row['status'] == 'Pending') echo 'Chờ xử lý';
                                        if ($row['status'] == 'Completed') echo 'Hoàn thành';
                                        if ($row['status'] == 'Cancelled') echo 'Đã hủy';
                                    ?>
                                </span>
                            </td>
                            <td>
                                <!-- Nút sửa đơn hàng -->
                                <a href="javascript:void(0);" class="btn btn-warning btn-sm editBtn" data-order-id="<?php echo $row['order_id']; ?>"
                                data-recipient-name="<?php echo $row['recipient_name']; ?>" 
                                data-status="<?php echo $row['status']; ?>">Sửa</a>
                                <a href="orders/delete_order.php?order_id=<?php echo $row['order_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này không?')">Xóa</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Phân Trang -->
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Trước</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Sau</a>
                    </li>
                </ul>
            </nav>

        </div>
        <!-- Modal Chỉnh sửa Trạng Thái Đơn Hàng -->
            <div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editOrderModalLabel">Chỉnh Sửa Trạng Thái Đơn Hàng</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Form chỉnh sửa trạng thái đơn hàng -->
                            <form id="editOrderForm">
                                <!-- Hiển thị ID đơn hàng -->
                                <div class="mb-3">
                                    <label for="order_id" class="form-label">ID Đơn Hàng</label>
                                    <input type="text" class="form-control" name="order_id" id="order_id" readonly> <!-- Chỉ đọc -->
                                </div>
                                
                                <!-- Hiển thị tên người nhận -->
                                <div class="mb-3">
                                    <label for="recipient_name" class="form-label">Tên Người Nhận</label>
                                    <input type="text" class="form-control" name="recipient_name" id="recipient_name" readonly> <!-- Chỉ đọc -->
                                </div>

                                <!-- Trạng Thái -->
                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng Thái</label>
                                    <select class="form-control" name="status" id="status" required>
                                        <option value="Pending">Đang xử lý</option>
                                        <option value="Completed">Hoàn thành</option>
                                        <option value="Cancelled">Đã hủy</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Cập nhật Trạng Thái</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Khi nhấn nút sửa trạng thái
    $('.editBtn').click(function() {
        var orderId = $(this).data('order-id');
        var recipientName = $(this).data('recipient-name');
        var status = $(this).data('status');

        // Điền thông tin vào form trong modal
        $('#order_id').val(orderId); // Điền ID đơn hàng vào input
        $('#recipient_name').val(recipientName); // Điền tên người nhận vào input
        $('#status').val(status); // Điền trạng thái hiện tại vào dropdown

        // Hiển thị modal
        $('#editOrderModal').modal('show');
    });

    // Gửi form cập nhật trạng thái qua AJAX
    $('#editOrderForm').submit(function(e) {
        e.preventDefault(); // Ngừng form gửi theo cách thông thường

        var formData = $(this).serialize(); // Thu thập dữ liệu từ form

        $.ajax({
            url: 'orders/edit_order.php', // File PHP xử lý
            type: 'POST',
            data: formData,
            success: function(response) {
                alert('Cập nhật trạng thái đơn hàng thành công!');
                $('#editOrderModal').modal('hide');
                location.reload(); // Tải lại trang để cập nhật dữ liệu
            },
            error: function() {
                alert('Có lỗi xảy ra khi cập nhật trạng thái đơn hàng!');
            }
        });
    });
});

</script>


</body>
</html>