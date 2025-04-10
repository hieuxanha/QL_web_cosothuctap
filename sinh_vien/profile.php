<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['name'])) {
    header("Location: ../dang_nhap_dang_ki/form_dn.php");
    exit();
}

// Kết nối cơ sở dữ liệu
require_once '../db.php';

// Lấy mã sinh viên từ session
$ma_sinh_vien = isset($_SESSION['ma_sinh_vien']) ? $_SESSION['ma_sinh_vien'] : null;

// Truy vấn thông tin sinh viên
if (!$ma_sinh_vien) {
    $name = $_SESSION['name'];
    $sql = "SELECT * FROM sinh_vien WHERE ho_ten = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
} else {
    $sql = "SELECT * FROM sinh_vien WHERE ma_sinh_vien = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ma_sinh_vien);
}

$stmt->execute();
$result = $stmt->get_result();
$sinh_vien = $result->fetch_assoc();

// Đóng kết nối
$stmt->close();
$conn->close();

// Kiểm tra nếu không tìm thấy sinh viên
if (!$sinh_vien) {
    $sinh_vien = [
        'ma_sinh_vien' => 'N/A',
        'ho_ten' => 'Không tìm thấy',
        'email' => 'N/A',
        'lop' => 'N/A',
        'khoa' => 'N/A',
        'so_dien_thoai' => 'N/A'
    ];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SMAS - Hệ thống quản lý nhà trường</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../sinh_vien/profile.css">
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
            <button><a href="./giaodien_sinhvien.php">aaaa</a></button>
            <a href="../index.php">Việc làm</a>
            <a href="#">Hồ sơ & CV</a>
            <a href="../dang_nhap_dang_ki/logic_dangxuat.php" class="btn btn-login"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            <a href="#"><i class="fas fa-user"></i></a>
        </div>
    </div>

    <div class="main-content">
        <div class="section">
            <div class="section-header">
                <div>Thông tin sinh viên</div>
                <div>▲</div>
            </div>
            <div class="section-content">
                <div class="student-profile">
                    <div class="student-avatar">
                        <img src="https://via.placeholder.com/200x250" alt="Student Avatar" />
                        <div class="student-id">Mã sinh viên: <strong><?php echo htmlspecialchars($sinh_vien['ma_sinh_vien']); ?></strong></div>
                    </div>
                    <div class="student-info">
                        <div class="info-row">
                            <div class="info-label">Mã sinh viên:</div>
                            <div class="info-value"><?php echo htmlspecialchars($sinh_vien['ma_sinh_vien']); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Họ và tên:</div>
                            <div class="info-value"><?php echo htmlspecialchars($sinh_vien['ho_ten']); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Email:</div>
                            <div class="info-value"><?php echo htmlspecialchars($sinh_vien['email']); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Lớp:</div>
                            <div class="info-value"><?php echo htmlspecialchars($sinh_vien['lop']); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Khoa:</div>
                            <div class="info-value"><?php echo htmlspecialchars($sinh_vien['khoa']); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Số điện thoại:</div>
                            <div class="info-value"><?php echo htmlspecialchars($sinh_vien['so_dien_thoai']); ?></div>
                        </div>
                    </div>
                </div>
                <a href="./sua_profile.php" class="edit-profile-btn"><i class="fas fa-edit"></i> Sửa hồ sơ</a>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <div>Theo dõi quá trình xét duyệt</div>
                <div>▲</div>
            </div>
            <div class="section-content">
                <table class="status-table">
                    <thead>
                        <tr>
                            <th>Đơn đăng ký</th>
                            <th>Trạng thái</th>
                            <th>Ngày cập nhật</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Đơn đăng ký thực tập</td>
                            <td class="status-approved">Đã duyệt</td>
                            <td>08/04/2025</td>
                        </tr>
                    </tbody>
                </table>
                <div class="notifications">
                    <h3>Thông báo từ hệ thống</h3>
                    <div class="notification-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Hồ sơ thực tập của bạn đã được duyệt - 08/04/2025</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>