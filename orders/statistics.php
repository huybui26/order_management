<?php
include '../includes/db.php';

// Lọc theo năm và tháng
$year_filter = isset($_GET['year_filter']) ? $_GET['year_filter'] : date('Y');
$month_filter = isset($_GET['month_filter']) ? $_GET['month_filter'] : null;

// Dữ liệu tháng
$months_list = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];

// Dữ liệu ngày trong tháng
$days_list = [];
for ($i = 1; $i <= 31; $i++) {
    $days_list[] = 'Ngày ' . $i;
}

if ($month_filter) {
    // Lọc theo tháng
    $sql = "SELECT DAY(created_at) AS day, SUM(total_amount) as total_sales, COUNT(*) as count 
            FROM orders 
            WHERE YEAR(created_at) = '$year_filter' AND MONTH(created_at) = '$month_filter'
            GROUP BY DAY(created_at)";
    
    $result = $conn->query($sql);
    
    $days = [];
    $sales = [];
    $counts = [];
    
    while ($stat = $result->fetch_assoc()) {
        $days[] = $stat['day'];
        $sales[] = $stat['total_sales'];
        $counts[] = $stat['count'];
    }
} else {
    // Lọc theo năm (12 tháng)
    $sql = "SELECT MONTH(created_at) AS month, SUM(total_amount) as total_sales, COUNT(*) as count 
            FROM orders 
            WHERE YEAR(created_at) = '$year_filter'
            GROUP BY MONTH(created_at)";
    
    $result = $conn->query($sql);
    
    $months = [];
    $sales = [];
    $counts = [];
    
    while ($stat = $result->fetch_assoc()) {
        $months[] = $stat['month'];
        $sales[] = $stat['total_sales'];
        $counts[] = $stat['count'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../assets/css/statistics.css">
</head>
<body>

<div class="container mt-5">
    <h3 class="text-center mb-4">Thống Kê Đơn Hàng</h3>

    <!-- Form chọn năm và tháng -->
    <form method="GET">
        <div class="form-row mb-4">
            <div class="col-md-3">
                <select class="form-control" name="year_filter" onchange="this.form.submit()">
                    <option value="2023" <?php echo $year_filter == '2023' ? 'selected' : ''; ?>>2023</option>
                    <option value="2024" <?php echo $year_filter == '2024' ? 'selected' : ''; ?>>2024</option>
                    <!-- Thêm các năm khác nếu cần -->
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" name="month_filter" onchange="this.form.submit()">
                    <option value="" <?php echo $month_filter == null ? 'selected' : ''; ?>>Tất cả tháng</option>
                    <?php foreach ($months_list as $index => $month) { ?>
                        <option value="<?php echo $index + 1; ?>" <?php echo $month_filter == $index + 1 ? 'selected' : ''; ?>>
                            <?php echo $month; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Lọc</button>
            </div>
        </div>
    </form>

    <!-- Biểu đồ cột - Doanh thu theo tháng hoặc ngày -->
    <div class="row mb-5">
        <div class="col-md-12">
            <h4>
                <?php echo $month_filter ? 'Doanh thu theo ngày trong tháng ' . $months_list[$month_filter - 1] . ' ' . $year_filter : 'Doanh thu theo các tháng trong năm ' . $year_filter; ?>
            </h4>
            <canvas id="salesBarChart"></canvas>
        </div>
    </div>

    <!-- Biểu đồ đường - Doanh thu theo tháng hoặc ngày -->
    <div class="row mb-5">
        <div class="col-md-12">
            <h4>Doanh thu theo tháng (Biểu đồ Đường)</h4>
            <canvas id="salesLineChart"></canvas>
        </div>
    </div>

    <!-- Biểu đồ tròn - Phương thức thanh toán -->
    <div class="row mb-5">
        <div class="col-md-12">
            <h4>Phương thức thanh toán (Biểu đồ Tròn)</h4>
            <canvas id="paymentMethodPieChart"></canvas>
        </div>
    </div>

    <!-- Bảng thống kê chi tiết -->
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th><?php echo $month_filter ? 'Ngày' : 'Tháng'; ?></th>
                <th>Số Đơn Hàng</th>
                <th>Tổng Doanh Thu</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($month_filter) {
                // Hiển thị doanh thu theo ngày
                for ($i = 1; $i <= 31; $i++) {
                    $day = $i;
                    $day_name = 'Ngày ' . $i;
                    $sales_day = isset($sales[$i - 1]) ? $sales[$i - 1] : 0;
                    $count_day = isset($counts[$i - 1]) ? $counts[$i - 1] : 0;
                    echo "<tr>
                            <td>{$day_name}</td>
                            <td>{$count_day}</td>
                            <td>" . number_format($sales_day, 2) . " VNĐ</td>
                        </tr>";
                }
            } else {
                // Hiển thị doanh thu theo tháng
                for ($i = 0; $i < 12; $i++) {
                    $month = $i + 1;
                    $month_name = $months_list[$i];
                    $sales_month = isset($sales[$i]) ? $sales[$i] : 0;
                    $count_month = isset($counts[$i]) ? $counts[$i] : 0;
                    echo "<tr>
                            <td>{$month_name}</td>
                            <td>{$count_month}</td>
                            <td>" . number_format($sales_month, 2) . " VNĐ</td>
                        </tr>";
                }
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    // Biểu đồ cột (Bar Chart) cho doanh thu theo tháng hoặc ngày
    var ctx = document.getElementById('salesBarChart').getContext('2d');
    var salesBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($month_filter ? $days_list : $months_list); ?>,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?php echo json_encode($month_filter ? $sales : $sales); ?>,
                backgroundColor: '#007bff',
                borderColor: '#0056b3',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return value.toLocaleString(); }
                    }
                }
            }
        }
    });

    // Biểu đồ đường (Line Chart) cho doanh thu theo tháng hoặc ngày
    var ctxLine = document.getElementById('salesLineChart').getContext('2d');
    var salesLineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($month_filter ? $days_list : $months_list); ?>,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?php echo json_encode($month_filter ? $sales : $sales); ?>,
                fill: false,
                borderColor: '#28a745',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return value.toLocaleString(); }
                    }
                }
            }
        }
    });

    // Biểu đồ tròn (Pie Chart) cho phương thức thanh toán (Giả sử các phương thức thanh toán đã được lưu trong DB)
    var ctxPie = document.getElementById('paymentMethodPieChart').getContext('2d');
    var paymentMethodPieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: ['Tiền mặt', 'Chuyển khoản', 'Ví điện tử'], // Các phương thức thanh toán
            datasets: [{
                label: 'Phương thức thanh toán',
                data: [30, 50, 20], // Các giá trị của mỗi phương thức thanh toán (cần thay đổi theo thực tế)
                backgroundColor: ['#ff6384', '#36a2eb', '#ffcd56'],
                hoverOffset: 4
            }]
        }
    });
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
