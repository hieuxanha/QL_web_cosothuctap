<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'giang_vien') {
    header("Location: ../dang_nhap_dang_ki/form_dn.php");
    exit();
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Kết nối cơ sở dữ liệu
require_once '../db.php';
$conn->set_charset("utf8mb4");

// Lấy số hiệu giảng viên từ session
$so_hieu_giang_vien = isset($_SESSION['so_hieu_giang_vien']) ? $_SESSION['so_hieu_giang_vien'] : null;

// Truy vấn thông tin giảng viên
if (!$so_hieu_giang_vien) {
    $name = $_SESSION['name'];
    $sql = "SELECT * FROM giang_vien WHERE ho_ten = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    $stmt->bind_param("s", $name);
} else {
    $sql = "SELECT * FROM giang_vien WHERE so_hieu_giang_vien = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    $stmt->bind_param("s", $so_hieu_giang_vien);
}

$stmt->execute();
$result = $stmt->get_result();
$giang_vien = $result->fetch_assoc();
$stmt->close();

// Kiểm tra nếu không tìm thấy giảng viên
if (!$giang_vien) {
    $giang_vien = [
        'stt_gv' => null,
        'so_hieu_giang_vien' => 'N/A',
        'ho_ten' => 'Không tìm thấy',
        'email' => 'N/A',
        'khoa' => 'N/A',
        'so_dien_thoai' => 'N/A'
    ];
}




// Xử lý duyệt báo cáo hoặc đánh giá

// Đóng kết nối
$conn->close();
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
    <style>

    </style>
</head>

<body>
    <div class="header">
        <div class="left-section">
            <div class="logo">
                <img alt="Logo" src="../img/logo.png" />
            </div>
            <div class="ten_trg">
                <h3>ĐẠI HỌC TÀI NGUYÊN MÔI TRƯỜNG HÀ NỘI</h3>
                <p>Hanoi University of Natural Resources and Environment</p>
            </div>
        </div>
        <div class="nav">
            <a href="ui_giangvien.php">Trang chủ</a>
            <a href="../dang_nhap_dang_ki/logic_dangxuat.php" class="btn btn-login"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>

            <a href="#"><i class="fas fa-user"></i></a>
        </div>
    </div>

    <div class="main-content">
        <div class="section">
            <div class="section-header">
                <div>Thông tin giảng viên</div>
                <div>▲</div>
            </div>
            <div class="section-content">
                <div class="student-profile">

                    <div class="student-info">
                        <div class="info-row">
                            <div class="info-label">Số hiệu:</div>
                            <div class="info-value"><?php echo htmlspecialchars($giang_vien['so_hieu_giang_vien']); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Họ và tên:</div>
                            <div class="info-value"><?php echo htmlspecialchars($giang_vien['ho_ten']); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Email:</div>
                            <div class="info-value"><?php echo htmlspecialchars($giang_vien['email']); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Khoa:</div>
                            <div class="info-value">
                                <?php
                                // Define the khoa_options array
                                $khoa_options = [
                                    'kinh_te' => 'Kinh tế',
                                    'moi_truong' => 'Môi trường',
                                    'quan_ly_dat_dai' => 'Quản lý đất đai',
                                    'khi_tuong_thuy_van' => 'Khí tượng thủy văn',
                                    'trac_dia_ban_do' => 'Trắc địa bản đồ',
                                    'dia_chat' => 'Địa chất',
                                    'tai_nguyen_nuoc' => 'Tài nguyên nước',
                                    'cntt' => 'Công nghệ thông tin',
                                    'ly_luan_chinh_tri' => 'Lý luận chính trị',
                                    'bien_hai_dao' => 'Biển - Hải đảo',
                                    'khoa_hoc_dai_cuong' => 'Khoa học đại cương',
                                    'the_chat_quoc_phong' => 'Thể chất quốc phòng',
                                    'bo_mon_luat' => 'Bộ môn Luật',
                                    'bien_doi_khi_hau' => 'Biến đổi khí hậu',
                                    'ngoai_ngu' => 'Ngoại ngữ'
                                ];

                                // Display the full name of the khoa if it exists in the array, otherwise show 'N/A'
                                echo htmlspecialchars(isset($giang_vien['khoa']) && isset($khoa_options[$giang_vien['khoa']])
                                    ? $khoa_options[$giang_vien['khoa']]
                                    : 'N/A');
                                ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Số điện thoại:</div>
                            <div class="info-value"><?php echo htmlspecialchars($giang_vien['so_dien_thoai'] ?? 'N/A'); ?></div>
                        </div>
                    </div>
                </div>
                <a href="./sua_giang_vien_profile.php" class="edit-profile-btn"><i class="fas fa-edit"></i> Sửa hồ sơ</a>
            </div>
        </div>


    </div>


</body>

</html>