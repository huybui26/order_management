<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra thông tin tài khoản trong cơ sở dữ liệu
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: ../index.php');
        exit;
    } else {
        $error = "Tên đăng nhập hoặc mật khẩu không đúng.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            /* background: linear-gradient(to bottom right, #6a11cb, #2575fc); */
            color: white;
        }
        .login-container {
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-container h1 {
            font-weight: bold;
            color: #000;
            margin-bottom: 1rem;
            text-align: center;
        }
        .btn-primary {
            border: none;
        }
        .btn-primary:hover {
            background-color: #4b0ba7;
        }
        .form-label {
            color: #333;
            font-weight: 600;
        }
        .form-control {
            border-radius: 8px;
        }
        .alert {
            font-size: 0.9rem;
            padding: 0.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Đăng Nhập</h1>
        <form method="POST" action="login.php">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="username" class="form-label">Tên Đăng Nhập</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mật Khẩu</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Đăng Nhập</button>
        </form>
    </div>
</body>
</html>
