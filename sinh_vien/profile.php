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
$stmt->close();

// Kiểm tra nếu không tìm thấy sinh viên
if (!$sinh_vien) {
    $sinh_vien = [
        'stt_sv' => null,
        'ma_sinh_vien' => 'N/A',
        'ho_ten' => 'Không tìm thấy',
        'email' => 'N/A',
        'lop' => 'N/A',
        'khoa' => 'N/A',
        'so_dien_thoai' => 'N/A'
    ];
}

// Truy vấn danh sách đơn đăng ký thực tập của sinh viên
$don_dang_ky_list = [];
if ($sinh_vien['stt_sv']) {
    $sql = "SELECT ut.ngay_ung_tuyen, ut.trang_thai, td.tieu_de
            FROM ung_tuyen ut
            JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung
            WHERE ut.stt_sv = ?
            ORDER BY ut.ngay_ung_tuyen DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sinh_vien['stt_sv']);
    $stmt->execute();
    $result = $stmt->get_result();
    $don_dang_ky_list = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

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
        .status-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .status-table th, .status-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .status-table th {
            background-color: #0078d4;
            color: white;
        }
        .status-pending { color: #ff9800; }
        .status-approved { color: #4caf50; }
        .status-rejected { color: #f44336; }
        .notifications {
            margin-top: 20px;
        }
        .notification-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .notification-item i {
            margin-right: 10px;
            color: #4caf50;
        }
        .notification-item.rejected i {
            color: #f44336;
        }
        .notification-item.pending i {
            color: #ff9800;
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
                            <th>Ngày ứng tuyển</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($don_dang_ky_list)): ?>
                            <tr>
                                <td colspan="3">Bạn chưa đăng ký thực tập.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($don_dang_ky_list as $don): ?>
                                <?php $trang_thai = trim($don['trang_thai']) ?: 'Chờ duyệt'; ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($don['tieu_de']); ?></td>
                                    <td class="<?php echo $trang_thai === 'Chờ duyệt' ? 'status-pending' : ($trang_thai === 'Đồng ý' ? 'status-approved' : 'status-rejected'); ?>">
                                        <?php echo htmlspecialchars($trang_thai); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($don['ngay_ung_tuyen']))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="notifications">
                    <h3>Thông báo từ hệ thống</h3>
                    <?php if (empty($don_dang_ky_list)): ?>
                        <div class="notification-item">
                            <i class="fas fa-info-circle"></i>
                            <span>Bạn chưa đăng ký thực tập.</span>
                        </div>
                    <?php else: ?>
                        <?php foreach ($don_dang_ky_list as $don): ?>
                            <?php $trang_thai = trim($don['trang_thai']) ?: 'Chờ duyệt'; ?>
                            <div class="notification-item <?php echo $trang_thai === 'Chờ duyệt' ? 'pending' : ($trang_thai === 'Đồng ý' ? 'approved' : 'rejected'); ?>">
                                <i class="fas fa-<?php echo $trang_thai === 'Chờ duyệt' ? 'hourglass-half' : ($trang_thai === 'Đồng ý' ? 'check-circle' : 'times-circle'); ?>"></i>
                                <span>
                                    Hồ sơ thực tập của bạn (<?php echo htmlspecialchars($don['tieu_de']); ?>) 
                                    <?php echo $trang_thai === 'Chờ duyệt' ? 'đang chờ duyệt' : ($trang_thai === 'Đồng ý' ? 'đã được duyệt' : 'bị từ chối'); ?> 
                                    - <?php echo htmlspecialchars(date('d/m/Y', strtotime($don['ngay_ung_tuyen']))); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>