<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'sinh_vien') {
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
    if (!$stmt) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    $stmt->bind_param("s", $name);
} else {
    $sql = "SELECT * FROM sinh_vien WHERE ma_sinh_vien = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
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
    $sql = "SELECT ut.id, ut.ma_tuyen_dung, ut.ngay_ung_tuyen, ut.trang_thai, td.tieu_de
            FROM ung_tuyen ut
            JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung
            WHERE ut.stt_sv = ?
            ORDER BY ut.ngay_ung_tuyen DESC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    $stmt->bind_param("i", $sinh_vien['stt_sv']);
    $stmt->execute();
    $result = $stmt->get_result();
    $don_dang_ky_list = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Truy vấn danh sách báo cáo hằng tuần đã gửi
$bao_cao_list = [];
if ($sinh_vien['stt_sv']) {
    $sql = "SELECT bct.stt_baocao, bct.noi_dung, bct.ngay_gui, bct.file_path, td.tieu_de
            FROM bao_cao_thuc_tap bct
            JOIN ung_tuyen ut ON bct.ma_dang_ky = ut.id
            JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung
            WHERE ut.stt_sv = ?
            ORDER BY bct.ngay_gui DESC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    $stmt->bind_param("i", $sinh_vien['stt_sv']);
    $stmt->execute();
    $result = $stmt->get_result();
    $bao_cao_list = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Xử lý gửi báo cáo hằng tuần
$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gui_bao_cao'])) {
    $ma_dang_ky = filter_input(INPUT_POST, 'ma_dang_ky', FILTER_VALIDATE_INT);
    $ma_tuyen_dung = filter_input(INPUT_POST, 'ma_tuyen_dung', FILTER_SANITIZE_STRING);
    $noi_dung = trim($_POST['noi_dung']);
    $file_path = null;

    // Kiểm tra nội dung báo cáo
    if (empty($noi_dung)) {
        $errors[] = "Nội dung báo cáo không được để trống.";
    }

    // Xử lý file đính kèm
    if (isset($_FILES['file_dinh_kem']) && $_FILES['file_dinh_kem']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file_dinh_kem'];
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // Kiểm tra loại file
        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = "Chỉ hỗ trợ file PDF hoặc DOC/DOCX.";
        }

        // Kiểm tra kích thước file
        if ($file['size'] > $max_size) {
            $errors[] = "File không được lớn hơn 5MB.";
        }

        // Lưu file nếu không có lỗi
        if (empty($errors)) {
            $upload_dir = '../Uploads/';
            $file_name = 'baocao_' . time() . '_' . basename($file['name']);
            $file_path = $upload_dir . $file_name;

            if (!move_uploaded_file($file['tmp_name'], $file_path)) {
                $errors[] = "Không thể tải file lên.";
            }
        }
    }

    // Lưu báo cáo vào cơ sở dữ liệu nếu không có lỗi
    if (empty($errors)) {
        $sql = "INSERT INTO bao_cao_thuc_tap (ma_dang_ky, ma_tuyen_dung, noi_dung, ngay_gui, file_path)
                VALUES (?, ?, ?, CURDATE(), ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $errors[] = "Lỗi chuẩn bị truy vấn: " . $conn->error;
        } else {
            $stmt->bind_param("isss", $ma_dang_ky, $ma_tuyen_dung, $noi_dung, $file_path);
            if ($stmt->execute()) {
                $success = "Báo cáo hằng tuần đã được gửi thành công!";
                // Làm mới danh sách báo cáo
                $sql = "SELECT bct.stt_baocao, bct.noi_dung, bct.ngay_gui, bct.file_path, td.tieu_de
                        FROM bao_cao_thuc_tap bct
                        JOIN ung_tuyen ut ON bct.ma_dang_ky = ut.id
                        JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung
                        WHERE ut.stt_sv = ?
                        ORDER BY bct.ngay_gui DESC";
                $stmt_refresh = $conn->prepare($sql);
                if (!$stmt_refresh) {
                    $errors[] = "Lỗi chuẩn bị truy vấn: " . $conn->error;
                } else {
                    $stmt_refresh->bind_param("i", $sinh_vien['stt_sv']);
                    $stmt_refresh->execute();
                    $result = $stmt_refresh->get_result();
                    $bao_cao_list = $result->fetch_all(MYSQLI_ASSOC);
                    $stmt_refresh->close();
                }
            } else {
                $errors[] = "Có lỗi xảy ra khi gửi báo cáo: " . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Đóng kết nối sau khi tất cả các truy vấn hoàn tất
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

        .status-table th,
        .status-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .status-table th {
            background-color: #0078d4;
            color: white;
        }

        .status-pending {
            color: #ff9800;
        }

        .status-approved {
            color: #4caf50;
        }

        .status-rejected {
            color: #f44336;
        }

        .status-completed {
            color: #2e7d32;
            font-weight: bold;
        }

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

        .notification-item.completed i {
            color: #2e7d32;
        }

        .btn-report {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-report:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .btn-report:hover:not(:disabled) {
            background-color: #45a049;
        }

        .btn-report i {
            margin-right: 5px;
        }

        .bao-cao-form {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .bao-cao-form h4 {
            margin-bottom: 15px;
            color: #0078d4;
        }

        .bao-cao-form textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
            min-height: 100px;
            margin-bottom: 10px;
        }

        .bao-cao-form input[type="file"] {
            margin-bottom: 10px;
        }

        .bao-cao-form button {
            padding: 8px 16px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .bao-cao-form button:hover {
            background-color: #45a049;
        }

        .error,
        .success {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 4px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .file-link {
            color: #0078d4;
            text-decoration: none;
        }

        .file-link:hover {
            text-decoration: underline;
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
            <button style=""><a href="./giaodien_sinhvien.php">Trang Chủ</a></button>
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
                            <div class="info-value"><?php echo htmlspecialchars($sinh_vien['lop'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Khoa:</div>
                            <div class="info-value"><?php echo htmlspecialchars($sinh_vien['khoa'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Số điện thoại:</div>
                            <div class="info-value"><?php echo htmlspecialchars($sinh_vien['so_dien_thoai'] ?? 'N/A'); ?></div>
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
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($don_dang_ky_list)): ?>
                            <tr>
                                <td colspan="4">Bạn chưa đăng ký thực tập.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($don_dang_ky_list as $don): ?>
                                <?php $trang_thai = trim($don['trang_thai']) ?: 'Chờ duyệt'; ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($don['tieu_de']); ?></td>
                                    <td class="<?php
                                                if ($trang_thai === 'Chờ duyệt') echo 'status-pending';
                                                elseif ($trang_thai === 'Đồng ý') echo 'status-approved';
                                                elseif ($trang_thai === 'Không đồng ý') echo 'status-rejected';
                                                else echo 'status-completed';
                                                ?>">
                                        <?php echo htmlspecialchars($trang_thai); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($don['ngay_ung_tuyen']))); ?></td>
                                    <td>
                                        <?php if ($trang_thai === 'Đồng ý'): ?>
                                            <button class="btn-report" onclick="showBaoCaoForm(<?php echo $don['id']; ?>, '<?php echo htmlspecialchars($don['ma_tuyen_dung']); ?>', '<?php echo htmlspecialchars($don['tieu_de']); ?>')">
                                                <i class="fas fa-file-alt"></i> Gửi báo cáo hằng tuần
                                            </button>
                                        <?php else: ?>
                                            <button class="btn-report" disabled><i class="fas fa-file-alt"></i> Gửi báo cáo</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Form gửi báo cáo hằng tuần (ẩn mặc định) -->
                <div id="bao-cao-form" class="bao-cao-form" style="display: none;">
                    <h4 id="bao-cao-title">Gửi báo cáo hằng tuần</h4>
                    <?php if (!empty($errors)): ?>
                        <div class="error">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="success">
                            <p><?php echo htmlspecialchars($success); ?></p>
                        </div>
                    <?php endif; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="ma_dang_ky" id="ma_dang_ky">
                        <input type="hidden" name="ma_tuyen_dung" id="ma_tuyen_dung">
                        <input type="hidden" name="gui_bao_cao" value="1">
                        <textarea name="noi_dung" placeholder="Nhập nội dung báo cáo hằng tuần..." required></textarea>
                        <input type="file" name="file_dinh_kem" accept=".pdf,.doc,.docx">
                        <button type="submit"><i class="fas fa-paper-plane"></i> Gửi báo cáo</button>
                    </form>
                </div>

                <!-- Danh sách báo cáo hằng tuần đã gửi -->
                <div class="bao-cao-list">
                    <h3>Danh sách báo cáo hằng tuần đã gửi</h3>
                    <table class="status-table">
                        <thead>
                            <tr>
                                <th>Đơn đăng ký</th>
                                <th>Nội dung</th>
                                <th>File đính kèm</th>
                                <th>Ngày gửi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bao_cao_list)): ?>
                                <tr>
                                    <td colspan="4">Bạn chưa gửi báo cáo hằng tuần nào.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($bao_cao_list as $bao_cao): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($bao_cao['tieu_de']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($bao_cao['noi_dung'], 0, 50)) . (strlen($bao_cao['noi_dung']) > 50 ? '...' : ''); ?></td>
                                        <td>
                                            <?php if ($bao_cao['file_path']): ?>
                                                <a href="<?php echo htmlspecialchars($bao_cao['file_path']); ?>" class="file-link" target="_blank">Tải xuống</a>
                                            <?php else: ?>
                                                Không có
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($bao_cao['ngay_gui']))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

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
                            <div class="notification-item <?php
                                                            if ($trang_thai === 'Chờ duyệt') echo 'pending';
                                                            elseif ($trang_thai === 'Đồng ý') echo 'approved';
                                                            elseif ($trang_thai === 'Không đồng ý') echo 'rejected';
                                                            else echo 'completed';
                                                            ?>">
                                <i class="fas fa-<?php
                                                    if ($trang_thai === 'Chờ duyệt') echo 'hourglass-half';
                                                    elseif ($trang_thai === 'Đồng ý') echo 'check-circle';
                                                    elseif ($trang_thai === 'Không đồng ý') echo 'times-circle';
                                                    else echo 'certificate';
                                                    ?>"></i>
                                <span>
                                    Hồ sơ thực tập của bạn (<?php echo htmlspecialchars($don['tieu_de']); ?>)
                                    <?php
                                    if ($trang_thai === 'Chờ duyệt') echo 'đang chờ duyệt';
                                    elseif ($trang_thai === 'Đồng ý') echo 'đã được duyệt';
                                    elseif ($trang_thai === 'Không đồng ý') echo 'bị từ chối';
                                    else echo 'đã hoàn thành';
                                    ?>
                                    - <?php echo htmlspecialchars(date('d/m/Y', strtotime($don['ngay_ung_tuyen']))); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showBaoCaoForm(ma_dang_ky, ma_tuyen_dung, tieu_de) {
            document.getElementById('bao-cao-form').style.display = 'block';
            document.getElementById('bao-cao-title').innerText = 'Gửi báo cáo hằng tuần: ' + tieu_de;
            document.getElementById('ma_dang_ky').value = ma_dang_ky;
            document.getElementById('ma_tuyen_dung').value = ma_tuyen_dung;
            window.scrollTo(0, document.getElementById('bao-cao-form').offsetTop);
        }
    </script>
</body>

</html>