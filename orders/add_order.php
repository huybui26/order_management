<?php
include '../includes/db.php';

$discount_value = 0; // Mặc định không có giảm giá
$total_amount = 0; // Tổng tiền ban đầu

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy thông tin từ form
    $customer_id = $_POST['customer_id'];
    $recipient_name = $_POST['recipient_name'];
    $shipping_address = $_POST['shipping_address'];
    $shipping_method = $_POST['shipping_method'];
    $payment_method = $_POST['payment_method'];
    $status = $_POST['status'];
    $discount_code = $_POST['discount_code'];
    $total_amount = $_POST['total_amount'];  // Lấy giá trị tổng tiền sau giảm giá từ form

    // Kiểm tra trạng thái hợp lệ
    $valid_statuses = ['Pending', 'Completed', 'Cancelled']; // Các trạng thái hợp lệ
    if (!in_array($status, $valid_statuses)) {
        die("Trạng thái không hợp lệ");
    }



    // Đảm bảo tổng tiền không âm
    $total_amount = max($total_amount, 0);

    // Thêm đơn hàng vào bảng orders
    $sql = "INSERT INTO orders (customer_id, recipient_name, shipping_address, shipping_method, payment_method, total_amount, status)
    VALUES ('$customer_id', '$recipient_name', '$shipping_address', '$shipping_method', '$payment_method', '$total_amount', '$status')";


    if ($conn->query($sql) === TRUE) {
        $order_id = $conn->insert_id;  // Lấy ID đơn hàng

        // Thêm sản phẩm vào bảng order_items
        foreach ($_POST['products'] as $product_id => $quantity) {
            $product_sql = "SELECT price FROM products WHERE product_id = $product_id";
            $product_result = $conn->query($product_sql);
            $product = $product_result->fetch_assoc();
            $price = $product['price'];

            // Thêm vào bảng order_items
            $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price)
                               VALUES ('$order_id', '$product_id', '$quantity', '$price')";
            $conn->query($order_item_sql);
        }

        header('Location: ../index.php');
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

// Lấy danh sách khách hàng
$customers_sql = "SELECT * FROM customers";
$customers_result = $conn->query($customers_sql);

// Lấy danh sách sản phẩm
$products_sql = "SELECT * FROM products";
$products_result = $conn->query($products_sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/add_order.css">

</head>
<body>
<div class="sidebar">
        <img src="path_to_shop_image.jpg" alt="Shop Logo">
        <h1>Gateaux</h1>
        <div class="nav-items">
            <div class="nav-item"><a href="#">Quản lý khách hàng</a></div>
            <div class="nav-item"><a href="#">Quản lý sản phẩm</a></div>
             <!-- Quản lý đơn hàng với các mục con -->
             <div class="nav-item">
                <a href="../index.php" data-bs-toggle="collapse" data-bs-target="#orderManagement" aria-expanded="false" aria-controls="orderManagement">Quản lý đơn hàng</a>
                <div class="collapse" id="orderManagement">
                    <div class="nav-item pl-4">
                        <a href="../index.php">Đơn hàng</a>
                    </div>
                    <div class="nav-item pl-4">
                        <a href="#">Tạo đơn hàng</a>
                    </div>
                    <div class="nav-item pl-4">
                        <a href="statistics.php">Thống kê</a>
                    </div>
                </div>
            </div>
            <div class="nav-item"><a href="#">Quản lý doanh thu</a></div>
            <div class="nav-item"><a href="#">Cài đặt hệ thống</a></div>
        </div>
        <button class="logout-btn">Đăng xuất</button>
    </div>
<div class="container mt-5">
    <h3 class="text-center mb-4">Thêm Đơn Hàng</h3>

    <form action="add_order.php" method="POST">
        <!-- Chọn khách hàng -->
        <div class="form-group">
            <label for="customer_id">Khách Hàng</label>
            <div class="input-group">
                <select class="form-control" name="customer_id" id="customer_id" onchange="fetchCustomerInfo()" required>
                    <option value="">Chọn khách hàng</option>
                    <?php while ($customer = $customers_result->fetch_assoc()) { ?>
                        <option value="<?php echo $customer['customer_id']; ?>"><?php echo $customer['name']; ?></option>
                    <?php } ?>
                </select>
                <button type="button" class="btn btn-info" onclick="showCreateCustomer()">Tạo khách hàng mới</button>
            </div>
        </div>

        <!-- Tên người nhận và địa chỉ -->
        <div class="form-group">
            <label for="recipient_name">Tên Người Nhận</label>
            <input type="text" class="form-control" name="recipient_name" id="recipient_name" required>
        </div>

        <div class="form-group">
            <label for="shipping_address">Địa Chỉ</label>
            <input type="text" class="form-control" name="shipping_address" id="shipping_address" required>
        </div>

        <!-- Phương thức vận chuyển và thanh toán -->
        <div class="form-group">
            <label for="shipping_method">Phương Thức Vận Chuyển & Thanh Toán</label>
            <div class="input-group">
                <select class="form-control" name="shipping_method" id="shipping_method" required>
                    <option value="Nhận tại cửa hàng" selected>Nhận tại cửa hàng</option>
                    <option value="Giao tận nơi">Giao tận nơi</option>
                </select>
                <select class="form-control" name="payment_method" id="payment_method" required>
                    <option value="Thanh toán khi nhận hàng" selected>Thanh toán khi nhận hàng</option>
                    <option value="Chuyển khoản ngân hàng">Chuyển khoản ngân hàng</option>
                </select>
            </div>
        </div>

        <!-- Mã giảm giá -->
        <div class="form-group">
            <label for="discount_code">Mã Giảm Giá</label>
            <div class="d-flex">
                <input type="text" class="form-control" name="discount_code" id="discount_code" placeholder="Nhập mã giảm giá (nếu có)">
                <button type="button" class="btn btn-success ml-2" onclick="applyDiscount()">Áp dụng</button>
            </div>
            <div id="discount-message" class="mt-2"></div>
        </div>

        <input type="hidden" name="discount_value" id="discount_value" value="0">
        <input type="hidden" name="total_amount" id="total_amount" value="0">

        <!-- Thêm sản phẩm -->
        <div class="form-group" id="product-container">
            <label for="products">Sản Phẩm</label>
            <div class="d-flex mb-3">
                <select class="form-control" name="product_id" id="product-select">
                    <option value="">Chọn sản phẩm</option>
                    <?php while ($product = $products_result->fetch_assoc()) { ?>
                        <option value="<?php echo $product['product_id']; ?>" data-name="<?php echo $product['name']; ?>" data-price="<?php echo $product['price']; ?>" data-image="<?php echo $product['image_url']; ?>">
                            <?php echo $product['name']; ?>
                        </option>
                    <?php } ?>
                </select>
                <input type="number" class="form-control ml-2" name="quantity" id="quantity" placeholder="Số lượng" required>
                <button type="button" class="btn btn-primary ml-2" onclick="addProduct()">Thêm</button>
            </div>
        </div>

        <!-- Hiển thị sản phẩm đã chọn -->
        <div id="selected-products" class="mt-4">
            <h5>Sản Phẩm Đã Chọn:</h5>
            <div id="selected-product-list"></div>
        </div>

        <!-- Hiển thị tổng tiền -->
        <div class="mt-4">
            <div>
                <strong>Tổng Tiền: </strong><span id="total-amount">0</span>
            </div>
            <div>
                <strong>Sau giảm giá: </strong><span id="final-amount">0</span>
            </div>
        </div>

        <!-- Trạng thái đơn hàng -->
        <div class="form-group">
            <label for="status">Trạng Thái</label>
            <select class="form-control" name="status" id="status">
                <option value="Pending">Chờ xử lý</option>
                <option value="Completed">Hoàn thành</option>
                <option value="Cancelled">Đã hủy</option>
            </select>
        </div>

        <div class="d-flex justify-content-between mb-4">
            <button type="submit" class="btn btn-success btn-sm">Tạo Đơn Hàng</button>
            <a href="../index.php" class="btn btn-danger btn-sm">Hủy</a>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function fetchCustomerInfo() {
        var customerId = document.getElementById('customer_id').value;

        if (customerId) {
            $.ajax({
                url: 'get_customer_info.php',
                method: 'POST',
                data: { customer_id: customerId },
                success: function(response) {
                    var customer = JSON.parse(response);
                    document.getElementById('recipient_name').value = customer.name;
                    document.getElementById('shipping_address').value = customer.address;
                }
            });
        }
    }
    function showCreateCustomer() {
        // Đây là hàm để hiển thị form tạo khách hàng mới
        alert('Chức năng tạo khách hàng mới chưa được cài đặt!');
    }

    function addProduct() {
        var productSelect = document.getElementById('product-select');
        var selectedProduct = productSelect.options[productSelect.selectedIndex];
        var productId = selectedProduct.value;
        var productName = selectedProduct.dataset.name;
        var productPrice = selectedProduct.dataset.price;
        var productImage = selectedProduct.dataset.image;
        var quantity = document.getElementById('quantity').value;

        if (!productId || !quantity) return;

        // Thêm sản phẩm vào danh sách đã chọn
        var productList = document.getElementById('selected-product-list');
        var productItem = document.createElement('div');
        productItem.classList.add('selected-product-item');
        productItem.dataset.productId = productId; // Lưu ID sản phẩm để dễ dàng xóa
        productItem.innerHTML = `
            <img src="${productImage}" alt="${productName}">
            <div class="product-info">
                <span><strong>${productName}</strong></span>
                <span>Giá: ${productPrice} VND</span>
                <span>Số lượng: ${quantity}</span>
            </div>
            <button type="button" class="btn btn-danger btn-sm ml-2" onclick="removeProduct(this)">Xóa</button>
        `;
        productList.appendChild(productItem);

        updateTotalAmount();
    }

    function removeProduct(button) {
        var productItem = button.closest('.selected-product-item');
        productItem.remove();
        updateTotalAmount();
    }

    function updateTotalAmount() {
    var total = 0;
    var discountValue = parseInt($('#discount_value').val()) || 0;  // Giá trị giảm giá mặc định là 0

    // Tính tổng tiền của các sản phẩm
    $('#selected-product-list .selected-product-item').each(function() {
        var priceText = $(this).find('.product-info span:nth-child(2)').text().split(': ')[1].replace(' VND', '');
        var quantityText = $(this).find('.product-info span:nth-child(3)').text().split(': ')[1];

        var price = parseFloat(priceText) || 0;
        var quantity = parseInt(quantityText) || 0;

        if (price > 0 && quantity > 0) {
            total += price * quantity;  // Cộng tổng tiền của tất cả sản phẩm
        }
    });

    // Cập nhật tổng tiền sau giảm giá
    var finalAmount = total - discountValue;

    $('#total-amount').text(total + ' VND');  // Hiển thị tổng tiền trước giảm giá
    $('#final-amount').text(finalAmount + ' VND');  // Hiển thị tổng tiền sau giảm giá

    // Cập nhật giá trị tổng tiền cuối cùng vào form (để gửi lên server)
    $('#total_amount').val(total);  
}

function applyDiscount() {
    var discountCode = $('#discount_code').val();

    if (discountCode) {
        $.ajax({
            type: 'POST',
            url: 'check_discount_code.php',  // Kiểm tra mã giảm giá
            data: { discount_code: discountCode },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    $('#discount-message').text(data.message).css('color', 'green');
                    
                    // Cập nhật giá trị giảm giá và tổng tiền
                    $('#discount_value').val(data.discount_value);  // Cập nhật giá trị giảm giá vào form

                    // Cập nhật lại tổng tiền sau khi giảm giá
                    updateTotalAmount();
                } else {
                    $('#discount-message').text(data.message).css('color', 'red');
                }
            }
        });
    } else {
        $('#discount-message').text('Vui lòng nhập mã giảm giá').css('color', 'red');
    }
}

</script>

</body>
</html>
