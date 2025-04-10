<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['name'])) {
    header("Location: ../dang_nhap_dang_ki/form_dn.php");
    exit();
}

require_once '../db.php';
if (!$conn) {
    die("Lỗi kết nối cơ sở dữ liệu: " . mysqli_connect_error());
}

// Lấy thông tin sinh viên dựa trên ho_ten từ session
$name = $_SESSION['name'];
$sql = "SELECT * FROM sinh_vien WHERE ho_ten = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) die("Lỗi chuẩn bị truy vấn: " . $conn->error);
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();
$sinh_vien = $result->fetch_assoc() ?: [
    'ma_sinh_vien' => 'N/A',
    'ho_ten' => 'Không tìm thấy',
    'email' => 'N/A',
    'lop' => 'N/A',
    'khoa' => 'N/A',
    'so_dien_thoai' => 'N/A'
];
$stmt->close();

// Lưu ma_sinh_vien từ kết quả truy vấn (nếu cần thiết)
$ma_sinh_vien = $sinh_vien['ma_sinh_vien'];

$update_success = $update_error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ho_ten = trim($_POST['ho_ten']);
    $email = trim($_POST['email']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);
    $lop = trim($_POST['lop']);
    $khoa = trim($_POST['khoa']);

    if (empty($ho_ten) || empty($email) || empty($so_dien_thoai)) {
        $update_error = "Vui lòng nhập đầy đủ các trường bắt buộc!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $update_error = "Email không hợp lệ!";
    } elseif (!preg_match("/^[0-9]{10,11}$/", $so_dien_thoai)) {
        $update_error = "Số điện thoại không hợp lệ (10-11 số)!";
    } else {
        // Kiểm tra email trùng lặp
        $sql_check = "SELECT 1 FROM sinh_vien WHERE email = ? AND ho_ten != ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ss", $email, $name); // Dùng $name thay vì $ma_sinh_vien
        $stmt_check->execute();
        $stmt_check->store_result();
        if ($stmt_check->num_rows > 0) {
            $update_error = "Email đã được sử dụng bởi sinh viên khác!";
        } else {
            // Cập nhật dựa trên ho_ten
            $sql = "UPDATE sinh_vien SET ho_ten = ?, email = ?, so_dien_thoai = ?, lop = ?, khoa = ? WHERE ho_ten = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $update_error = "Lỗi chuẩn bị truy vấn: " . $conn->error;
            } else {
                $stmt->bind_param("ssssss", $ho_ten, $email, $so_dien_thoai, $lop, $khoa, $name); // Dùng $name thay vì $ma_sinh_vien
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $update_success = "Cập nhật thông tin thành công!";
                        $_SESSION['name'] = $ho_ten; // Cập nhật lại session
                        header("Location: ./profile.php");
                        exit();
                    } else {
                        $update_error = "Không có thay đổi nào được thực hiện hoặc sinh viên không tồn tại!";
                    }
                } else {
                    $update_error = "Lỗi khi cập nhật: " . $stmt->error;
                }
            }
        }
        $stmt_check->close();
        if (isset($stmt)) $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SMAS - Cập nhật thông tin sinh viên</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../sinh_vien/profile.css">
    <style>
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
            color: #333;
        }
        .form-group label .required {
            color: #f44336;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            color: #333;
        }
        .form-control:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }
        .form-actions {
            margin-top: 20px;
            display: flex;
            gap: 15px;
        }
        .save-btn {
            padding: 10px 25px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .save-btn:hover {
            background-color: #218838;
        }
        .cancel-btn {
            padding: 10px 25px;
            background-color: #f44336;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .cancel-btn:hover {
            background-color: #c62828;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .message.success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .message.error {
            background-color: #ffebee;
            color: #c62828;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="left-section">
            <div class="logo">
                <img alt="Logo" src="https://via.placeholder.com/100x50" />
            </div>
            <div class="ten_trg">
                <h3>ĐẠI HỌC TÀI NGUYÊN MÔI TRƯỜNG HÀ NỘI</h3>
                <p>Hanoi University of Natural Resources and Environment</p>
            </div>
        </div>
        <div class="nav">
            <a href="../index.php">Việc làm</a>
            <a href="#">Hồ sơ & CV</a>
            <a href="../dang_nhap_dang_ki/logic_dangxuat.php" class="btn btn-login"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            <a href="./profile.php"><i class="fas fa-user"></i></a>
        </div>
    </div>

    <div class="main-content">
        <div class="section">
            <div class="section-header">
                <div>Cập nhật thông tin sinh viên</div>
                <div>▲</div>
            </div>
            <div class="section-content">
                <?php if (!empty($update_success)): ?>
                    <div class="message success"><?php echo $update_success; ?></div>
                <?php elseif (!empty($update_error)): ?>
                    <div class="message error"><?php echo $update_error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label>Mã sinh viên</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($sinh_vien['ma_sinh_vien']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Họ và tên <span class="required">*</span></label>
                        <input type="text" name="ho_ten" class="form-control" value="<?php echo htmlspecialchars($sinh_vien['ho_ten']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($sinh_vien['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại <span class="required">*</span></label>
                        <input type="tel" name="so_dien_thoai" class="form-control" value="<?php echo htmlspecialchars($sinh_vien['so_dien_thoai']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Lớp</label>
                        <input type="text" name="lop" class="form-control" value="<?php echo htmlspecialchars($sinh_vien['lop']); ?>" >
                    </div>
                    <div class="form-group">
                        <label>Khoa</label>
                        <input type="text" name="khoa" class="form-control" value="<?php echo htmlspecialchars($sinh_vien['khoa']); ?>" >
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="save-btn"><i class="fas fa-save"></i> Lưu thay đổi</button>
                        <a href="./profile.php" class="cancel-btn"><i class="fas fa-times"></i> Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>