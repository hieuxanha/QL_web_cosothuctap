<?php
session_start();
require_once '../db.php';

// Lấy ma_tuyen_dung từ URL
$ma_tuyen_dung = isset($_GET['ma_tuyen_dung']) ? $_GET['ma_tuyen_dung'] : null;

if (!$ma_tuyen_dung) {
    echo "<p>Không tìm thấy mã tuyển dụng!</p>";
    exit;
}

// Truy vấn thông tin tin tuyển dụng và công ty
$sql = "SELECT td.*, ct.ten_cong_ty, ct.dia_chi AS dia_chi_cty, ct.quy_mo, ct.linh_vuc, ct.logo
        FROM tuyen_dung td
        JOIN cong_ty ct ON td.stt_cty = ct.stt_cty
        WHERE td.ma_tuyen_dung = ? AND td.trang_thai = 'Đã duyệt'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $ma_tuyen_dung);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Tin tuyển dụng không tồn tại hoặc chưa được duyệt!</p>";
    exit;
}

$job = $result->fetch_assoc();
$stmt->close();

// Xử lý ứng tuyển
$application_error = $application_success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_application'])) {
    if (!isset($_SESSION['name'])) {
        $application_error = "Vui lòng đăng nhập để ứng tuyển!";
    } else {
        $ho_ten = trim($_POST['ho_ten']);
        $email = trim($_POST['email']);
        $so_dien_thoai = trim($_POST['so_dien_thoai']);
        $thu_gioi_thieu = trim($_POST['thu_gioi_thieu']);
        $cv_file = $_FILES['cv_file'];

        // Kiểm tra thông tin bắt buộc
        if (empty($ho_ten) || empty($email) || empty($so_dien_thoai)) {
            $application_error = "Vui lòng nhập đầy đủ thông tin bắt buộc!";
        } elseif ($cv_file['size'] == 0) {
            $application_error = "Vui lòng tải lên CV!";
        } else {
            // Xử lý upload file CV
            $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $max_size = 5 * 1024 * 1024; // 5MB
            $upload_dir = '../uploads/cv/';
            $cv_name = time() . '_' . basename($cv_file['name']);
            $cv_path = $upload_dir . $cv_name;

            if (in_array($cv_file['type'], $allowed_types) && $cv_file['size'] <= $max_size) {
                if (move_uploaded_file($cv_file['tmp_name'], $cv_path)) {
                    // Lưu thông tin ứng tuyển vào database (giả sử có bảng ung_tuyen)
                    $sql = "INSERT INTO ung_tuyen (ma_tuyen_dung, stt_sv, ho_ten, email, so_dien_thoai, thu_gioi_thieu, cv_path, ngay_ung_tuyen) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssss", $ma_tuyen_dung, $_SESSION['user_id'], $ho_ten, $email, $so_dien_thoai, $thu_gioi_thieu, $cv_path);
                    if ($stmt->execute()) {
                        $application_success = "Ứng tuyển thành công!";
                    } else {
                        $application_error = "Có lỗi xảy ra khi gửi hồ sơ!";
                    }
                    $stmt->close();
                } else {
                    $application_error = "Lỗi khi tải lên CV!";
                }
            } else {
                $application_error = "CV phải là file .doc, .docx, .pdf và dưới 5MB!";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết - <?php echo htmlspecialchars($job['tieu_de']); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="./chi_tiet.css">
    <style>
        /* Modal styles */
        .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}
.modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 800px;
    padding: 20px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
}
.close-modal {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 24px;
    background: none;
    border: none;
    cursor: pointer;
    color: #888;
}
.application-form { padding: 10px; }
.header h1 {
    color: #4CAF50;
    font-size: 22px;
    margin-bottom: 15px;
}
.upload-section h2 {
    display: flex;
    align-items: center;
    font-size: 18px;
    color: #333;
    margin-bottom: 10px;
}
.upload-section h2 .icon {
    background: #4CAF50;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
}
.upload-container {
    border: 1px dashed #ccc;
    border-radius: 4px;
    padding: 15px;
}
.upload-option {
    display: flex;
    align-items: center;
}
.radio-circle {
    width: 20px;
    height: 20px;
    border: 2px solid #4CAF50;
    border-radius: 50%;
    margin-right: 15px;
    position: relative;
}
.radio-circle.selected:after {
    content: "";
    position: absolute;
    top: 3px;
    left: 3px;
    width: 10px;
    height: 10px;
    background: #4CAF50;
    border-radius: 50%;
}
.upload-area {
    flex: 1;
    text-align: center;
    padding: 10px;
    background: #f9f9f9;
    border-radius: 4px;
}
.upload-icon {
    font-size: 30px;
    color: #888;
    margin-bottom: 5px;
}
.file-info {
    color: #888;
    font-size: 12px;
    margin-top: 5px;
}
.info-section { margin-top: 20px; }
.info-header {
    color: #4CAF50;
    font-size: 16px;
    margin-bottom: 10px;
}
.required-notice {
    color: #f44336;
    float: right;
    font-size: 12px;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}
.form-group label .required { color: #f44336; }
.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}
.form-row {
    display: flex;
    gap: 41px;
}
.form-row .form-group { flex: 1; }
.intro-section { margin-top: 20px; }
.intro-header {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}
.intro-icon {
    color: #4CAF50;
    font-size: 24px;
    margin-right: 10px;
}
.intro-text {
    color: #666;
    font-size: 14px;
    margin-bottom: 10px;
}
.intro-textarea {
    width: 100%;
    min-height: 100px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
    font-size: 14px;
}
.warning-section {
    margin-top: 20px;
    background: #fff9f9;
    border: 1px solid #ffebee;
    padding: 10px;
    border-radius: 4px;
}
.warning-header {
    display: flex;
    align-items: center;
    color: #f44336;
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 5px;
}
.warning-icon { margin-right: 5px; }
.warning-link { color: #4CAF50; text-decoration: none; }
.button-row {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}
.cancel-btn {
    padding: 10px 20px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
}
.submit-btn {
    padding: 10px 20px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    flex-grow: 1;
    margin-left: 10px;
}
.message {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
}
.message.success { background: #e8f5e9; color: #2e7d32; }
.message.error { background: #ffebee; color: #c62828; }
</style>
</head>
<body>
    <div class="header">
        <div class="left-section">
            <div class="logo">
                <img alt="TopCV Logo" height="40" src="../img/logo.png" width="100%" />
            </div>
            <div class="ten_trg">
                <h3>ĐẠI HỌC TRƯỜNG NGUYÊN MÔI TRƯỜNG HÀ NỘI</h3>
                <p>Hanoi University of Natural Resources and Environment</p>
            </div>
        </div>
        <div class="nav">
            <div class="account">
                <?php
                if (isset($_SESSION['name']) && !empty($_SESSION['name'])) {
                    echo '<div class="dropdown">';
                    echo '<span class="user-name">Xin chào, ' . htmlspecialchars($_SESSION['name']) . '</span>';
                    echo '<div class="dropdown-content">';
                    // echo '<a href="../Backend_dkdn/dangxuat.php">Đăng xuất</a>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo '<div class="auth-links">';
                    echo '<a class="btn" href="../dang_nhap_dang_ki/form_dn.php">Đăng nhập</a>';
                    echo '<a class="btn" href="../dang_nhap_dang_ki/form_dk.php">Đăng ký</a>';
                    echo '</div>';
                }
                ?>
            </div>
            <a href="#">Việc làm</a>
            <a href="#">Hồ sơ & CV</a>
            <a href="#"><i class="fa-solid fa-user"></i></a>
        </div>
    </div>

    <div class="timkiem-job">
        <div class="search-bar">
            <input placeholder="Khoa ..." type="text" />
            <button>Tìm kiếm</button>
        </div>
    </div>

    <div class="container">
        <div class="chitietcv">
            <div class="chitietcv-left">
                <div class="section">
                    <h2>Tiêu đề và thông tin chính</h2>
                    <p><strong>Tên công việc:</strong> <?php echo htmlspecialchars($job['tieu_de']); ?></p>
                    <p><strong>Địa điểm:</strong> <?php echo htmlspecialchars($job['dia_chi']); ?></p>
                    <p><strong>Kinh nghiệm:</strong> Không yêu cầu kinh nghiệm</p>
                    <p><strong>Hạn nộp hồ sơ:</strong> <?php echo htmlspecialchars($job['han_nop']); ?></p>
                    <button class="button" onclick="showApplicationModal()">Ứng tuyển ngay</button>
                </div>
                <div class="section">
                    <h2>Chi tiết tin tuyển dụng</h2>
                    <p><strong>Chuyên môn:</strong> Chăm sóc khách hàng</p>
                    <h3>Mô tả công việc:</h3>
                    <div><?php echo nl2br(htmlspecialchars($job['mo_ta'])); ?></div>
                    <h3>Yêu cầu công việc:</h3>
                    <div><?php echo nl2br(htmlspecialchars($job['yeu_cau'])); ?></div>
                    <p><strong>Địa điểm làm việc:</strong> <?php echo htmlspecialchars($job['dia_chi']); ?></p>
                    <p><strong>Thời gian làm việc:</strong> <?php echo htmlspecialchars($job['thoi_gian_lam_viec'] ?? 'Không xác định'); ?></p>
                </div>
            </div>
            <div class="chitietcv-right">
                <div class="section">
                    <h2>Thông tin chung</h2>
                    <p><strong>Cấp bậc:</strong> <?php echo htmlspecialchars($job['cap_bac'] ?? 'Nhân viên'); ?></p>
                    <p><strong>Kinh nghiệm:</strong> Không yêu cầu kinh nghiệm</p>
                    <p><strong>Số lượng tuyển:</strong> <?php echo htmlspecialchars($job['so_luong']); ?> người</p>
                    <p><strong>Hình thức làm việc:</strong> <?php echo htmlspecialchars($job['hinh_thuc']); ?></p>
                    <p><strong>Giới tính:</strong> <?php echo htmlspecialchars($job['gioi_tinh']); ?></p>
                </div>
                <div class="section">
                    <h2>Thông tin công ty</h2>
                    <p><strong>Tên công ty:</strong> <?php echo htmlspecialchars($job['ten_cong_ty']); ?></p>
                    <p><strong>Quy mô:</strong> <?php echo htmlspecialchars($job['quy_mo'] ?? 'Không xác định'); ?></p>
                    <p><strong>Lĩnh vực:</strong> <?php echo htmlspecialchars($job['linh_vuc'] ?? 'Không xác định'); ?></p>
                    <p><strong>Địa điểm:</strong> <?php echo htmlspecialchars($job['dia_chi_cty']); ?></p>
                    <a href="#" class="button">Xem trang công ty</a>
                </div>
            </div>
        </div>
    </div>

    <div id="applicationModal" class="modal">
    <div class="modal-content">
        <button class="close-modal" onclick="closeApplicationModal()">×</button>
        <div class="application-form">
            <div class="header">
                <h1>Ứng tuyển: <?php echo htmlspecialchars($job['tieu_de']); ?></h1>
            </div>

            <?php if (!empty($application_success)): ?>
                <div class="message success"><?php echo $application_success; ?></div>
            <?php elseif (!empty($application_error)): ?>
                <div class="message error"><?php echo $application_error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <!-- Upload CV Section -->
                <div class="upload-section">
                    <h2><span class="icon">👤</span> Chọn CV để ứng tuyển</h2>
                    <div class="upload-container">
                        <div class="upload-option">
                            <div class="radio-circle selected"></div>
                            <div class="upload-area">
                                <div class="upload-icon">⬆️</div>
                                <div>Tải lên CV từ máy tính</div>
                                <input type="file" name="cv_file" accept=".doc,.docx,.pdf" required style="margin-top: 10px;">
                                <div class="file-info">Hỗ trợ .doc, .docx, .pdf, dưới 5MB</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="info-section">
                    <div class="info-header">
                        Thông tin ứng tuyển <span class="required-notice">(*) Bắt buộc</span>
                    </div>
                    <div class="form-group">
                        <label>Họ và tên <span class="required">*</span></label>
                        <input type="text" name="ho_ten" class="form-control" value="<?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : ''; ?>" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại <span class="required">*</span></label>
                            <input type="tel" name="so_dien_thoai" class="form-control" required>
                        </div>
                    </div>
                </div>

                <!-- Intro Section -->
                <div class="intro-section">
                    <div class="intro-header">
                        <span class="intro-icon">🍃</span>
                        <h2>Thư giới thiệu</h2>
                    </div>
                    <div class="intro-text">Giới thiệu ngắn gọn để gây ấn tượng với nhà tuyển dụng.</div>
                    <textarea name="thu_gioi_thieu" class="intro-textarea" placeholder="Viết giới thiệu ngắn gọn về bản thân (điểm mạnh, kinh nghiệm) và lý do ứng tuyển."></textarea>
                </div>

                <!-- Warning Section -->
                <div class="warning-section">
                    <div class="warning-header">
                        <span class="warning-icon">⚠️</span> Lưu ý
                    </div>
                    <p>Nghiên cứu kỹ thông tin công ty trước khi ứng tuyển. Báo cáo vấn đề qua <a href="mailto:hotro@topcv.vn" class="warning-link">hotro@topcv.vn</a>.</p>
                </div>

                <!-- Button Row -->
                <div class="button-row">
                    <button type="button" class="cancel-btn" onclick="closeApplicationModal()">Hủy</button>
                    <button type="submit" name="submit_application" class="submit-btn">Nộp hồ sơ</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <footer class="footer">
        <div class="footer-container">
          <div class="footer-section">
            <img src="../img/logo.png" alt="TopCV Logo" class="footer-logo" />
            <p>Tiếp lợi thế - Nối thành công</p>
            <img src="../img/google_for_startup.webp" alt="Google for Startups" />
            <p>Liên hệ</p>
            <p>Hotline: <a href="tel:02466805958">(024) 6680 5958</a> (Giờ hành chính)</p>
            <p>Email: <a href="mailto:hotro@topcv.vn">hotro@topcv.vn</a></p>
            <p>Ứng dụng tải xuống</p>
            <div class="app-links">
              <img src="../img/app_store.webp" alt="App Store" />
              <img src="../img/chplay.webp" alt="Google Play" />
            </div>
            <div class="social-icons">
              <a href="#"><img src="../img/facebook.webp" alt="Facebook" /></a>
              <a href="#"><img src="../img/youtube.webp" alt="YouTube" /></a>
              <a href="#"><img src="../img/linkedin.webp" alt="LinkedIn" /></a>
              <a href="#"><img src="../img/tiktok.webp" alt="TikTok" /></a>
            </div>
          </div>
          <div class="footer-section">
            <h4>Về TopCV</h4>
            <ul>
              <li><a href="#">Giới thiệu</a></li>
              <li><a href="#">Góc báo chí</a></li>
              <li><a href="#">Tuyển dụng</a></li>
              <li><a href="#">Liên hệ</a></li>
              <li><a href="#">Hỏi đáp</a></li>
            </ul>
          </div>
          <div class="footer-section">
            <h4>Hồ sơ và CV</h4>
            <ul>
              <li><a href="#">Quản lý CV của bạn</a></li>
              <li><a href="#">TopCV Profile</a></li>
              <li><a href="#">Hướng dẫn viết CV</a></li>
            </ul>
          </div>
          <div class="footer-section">
            <h4>Khám phá</h4>
            <ul>
              <li><a href="#">Ứng dụng di động TopCV</a></li>
              <li><a href="#">Tính lương Gross - Net</a></li>
              <li><a href="#">Tính lãi suất kép</a></li>
            </ul>
          </div>
          <div class="footer-section">
            <h4>Xây dựng sự nghiệp</h4>
            <ul>
              <li><a href="#">Việc làm tốt nhất</a></li>
              <li><a href="#">Việc làm lương cao</a></li>
              <li><a href="#">Việc làm quản lý</a></li>
            </ul>
          </div>
        </div>
</footer>
    <script>
        function showApplicationModal() {
            document.getElementById('applicationModal').style.display = 'flex';
        }
        function closeApplicationModal() {
            document.getElementById('applicationModal').style.display = 'none';
        }
    </script>
</body>
</html>