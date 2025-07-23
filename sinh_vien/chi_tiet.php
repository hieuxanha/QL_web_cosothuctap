<?php
session_start();
require_once '../db.php';

// Lấy ma_tuyen_dung từ URL
$ma_tuyen_dung = isset($_GET['ma_tuyen_dung']) ? trim($_GET['ma_tuyen_dung']) : null;

if (!$ma_tuyen_dung) {
    echo "<p>Không tìm thấy mã tuyển dụng!</p>";
    exit;
}

// Truy vấn thông tin tin tuyển dụng và công ty
$sql = "SELECT td.ma_tuyen_dung, td.tieu_de, td.dia_chi, td.han_nop, td.mo_ta, 
               td.trinh_do, td.so_luong, td.hinh_thuc, td.gioi_tinh, td.stt_cty, 
               ct.ten_cong_ty, ct.dia_chi AS dia_chi_cty, ct.quy_mo, ct.linh_vuc, ct.logo
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

// Truy vấn thông tin sinh viên (nếu đã đăng nhập)
$sinh_vien = null;
if (isset($_SESSION['name'])) {
    $ma_sinh_vien = isset($_SESSION['ma_sinh_vien']) ? $_SESSION['ma_sinh_vien'] : null;
    if ($ma_sinh_vien) {
        $sql = "SELECT * FROM sinh_vien WHERE ma_sinh_vien = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $ma_sinh_vien);
    } else {
        $name = $_SESSION['name'];
        $sql = "SELECT * FROM sinh_vien WHERE ho_ten = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $sinh_vien = $result->fetch_assoc();
        $_SESSION['ma_sinh_vien'] = $sinh_vien['ma_sinh_vien'];
    }
    $stmt->close();
}

// Truy vấn 3 tin tuyển dụng ngẫu nhiên (không bao gồm tin hiện tại)
$sql = "SELECT td.ma_tuyen_dung, td.tieu_de, td.dia_chi, ct.ten_cong_ty, ct.logo
        FROM tuyen_dung td
        JOIN cong_ty ct ON td.stt_cty = ct.stt_cty
        WHERE td.trang_thai = 'Đã duyệt' AND td.ma_tuyen_dung != ?
        ORDER BY RAND() LIMIT 3";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $ma_tuyen_dung);
$stmt->execute();
$result = $stmt->get_result();
$random_jobs = [];
while ($row = $result->fetch_assoc()) {
    $random_jobs[] = $row;
}
$stmt->close();

// Xử lý ứng tuyển
$application_error = $application_success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_application'])) {
    if (!isset($_SESSION['name'])) {
        $application_error = "Vui lòng đăng nhập để ứng tuyển!";
    } elseif (!isset($_SESSION['ma_sinh_vien'])) {
        $application_error = "Không tìm thấy mã sinh viên. Vui lòng đăng nhập lại!";
    } else {
        $ho_ten = trim($_POST['ho_ten'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $so_dien_thoai = trim($_POST['so_dien_thoai'] ?? '');
        $thu_gioi_thieu = trim($_POST['thu_gioi_thieu'] ?? '');
        $cv_file = $_FILES['cv_file'] ?? null;

        if (empty($ho_ten) || empty($email) || empty($so_dien_thoai)) {
            $application_error = "Vui lòng nhập đầy đủ thông tin bắt buộc!";
        } elseif (!$cv_file || $cv_file['size'] == 0) {
            $application_error = "Vui lòng tải lên CV!";
        } else {
            $ma_sinh_vien = $_SESSION['ma_sinh_vien'];
            $sql_sv = "SELECT stt_sv FROM sinh_vien WHERE ma_sinh_vien = ?";
            $stmt_sv = $conn->prepare($sql_sv);
            $stmt_sv->bind_param("s", $ma_sinh_vien);
            $stmt_sv->execute();
            $result_sv = $stmt_sv->get_result();

            if ($result_sv->num_rows === 0) {
                $application_error = "Không tìm thấy sinh viên với mã sinh viên này!";
            } else {
                $sinh_vien_data = $result_sv->fetch_assoc();
                $stt_sv = $sinh_vien_data['stt_sv'];

                $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                $max_size = 5 * 1024 * 1024; // 5MB
                $upload_dir = '../Uploads/cv/';

                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $cv_name = time() . '_' . preg_replace('/[^A-Za-z0-9\-\.]/', '_', $cv_file['name']);
                $cv_path = $upload_dir . $cv_name;

                if (!in_array($cv_file['type'], $allowed_types)) {
                    $application_error = "CV phải là file .doc, .docx hoặc .pdf!";
                } elseif ($cv_file['size'] > $max_size) {
                    $application_error = "CV phải dưới 5MB!";
                } elseif (!move_uploaded_file($cv_file['tmp_name'], $cv_path)) {
                    $application_error = "Lỗi khi tải lên CV! Kiểm tra quyền thư mục uploads/cv/.";
                } else {
                    $sql = "INSERT INTO ung_tuyen (ma_tuyen_dung, stt_sv, ho_ten, email, so_dien_thoai, thu_gioi_thieu, cv_path, ngay_ung_tuyen) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sisssss", $ma_tuyen_dung, $stt_sv, $ho_ten, $email, $so_dien_thoai, $thu_gioi_thieu, $cv_path);
                    if ($stmt->execute()) {
                        $application_success = "Ứng tuyển thành công!";
                    } else {
                        $application_error = "Có lỗi xảy ra khi gửi hồ sơ: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
            $stmt_sv->close();
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
    <link rel="stylesheet" href="../sinh_vien/footer.css">
</head>
<style>

</style>

<body>
    <div class="header">
        <div class="left-section">
            <div class="logo">
                <img alt="TopCV Logo" height="40" src="../img/logo.png" width="100%" />
            </div>
            <div class="ten_trg">
                <h3>ĐẠI HỌC TÀI NGUYÊN & MÔI TRƯỜNG HÀ NỘI</h3>
                <p>Hanoi University of Natural Resources and Environment</p>
            </div>
        </div>
        <div class="nav">
            <div class="account">
                <?php
                if (isset($_SESSION['name'])) {
                    echo '<div class="dropdown">';
                    echo '<span class="user-name">Xin chào, ' . htmlspecialchars($_SESSION['name']) . '</span>';
                    echo '<div class="dropdown-content">';
                    echo '<a href="../dang_nhap_dang_ki/logic_dangxuat.php">Đăng xuất</a>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
            <a href="./giaodien_sinhvien.php">Trang chủ</a>
            <?php
            if (isset($_SESSION['name'])) {
                echo '<a href="./profile22.php"><i class="fa-solid fa-user"></i></a>';
            } else {
                echo '<a href="../dang_nhap_dang_ki/form_dn.php"><i class="fa-solid fa-user"></i></a>';
            }
            ?>
        </div>
    </div>

    <div class="timkiem-job">
        <div class="search-bar">
            <input id="searchInput" placeholder="Tìm theo tiêu đề, công ty..." type="text" />
            <select id="locationFilter">
                <option value="">Địa điểm</option>
                <option value="Ba Đình">Ba Đình</option>
                <option value="Hoàn Kiếm">Hoàn Kiếm</option>
                <option value="Tây Hồ">Tây Hồ</option>
                <option value="Cầu Giấy">Cầu Giấy</option>
                <option value="Đống Đa">Đống Đa</option>
                <option value="Hai Bà Trưng">Hai Bà Trưng</option>
                <option value="Hoàng Mai">Hoàng Mai</option>
                <option value="Long Biên">Long Biên</option>
                <option value="Nam Từ Liêm">Nam Từ Liêm</option>
                <option value="Bắc Từ Liêm">Bắc Từ Liêm</option>
                <option value="Thanh Xuân">Thanh Xuân</option>
                <option value="Sơn Tây">Sơn Tây</option>
                <option value="Ba Vì">Ba Vì</option>
                <option value="Chương Mỹ">Chương Mỹ</option>
                <option value="Đan Phượng">Đan Phượng</option>
                <option value="Đông Anh">Đông Anh</option>
                <option value="Gia Lâm">Gia Lâm</option>
                <option value="Hoài Đức">Hoài Đức</option>
                <option value="Mỹ Đức">Mỹ Đức</option>
                <option value="Phú Xuyên">Phú Xuyên</option>
                <option value="Quốc Oai">Quốc Oai</option>
                <option value="Thạch Thất">Thạch Thất</option>
                <option value="Thái Nguyên">Thái Nguyên</option>
                <option value="Thường Tín">Thường Tín</option>
                <option value="Ứng Hòa">Ứng Hòa</option>
                <option value="Phúc Thọ">Phúc Thọ</option>
                <option value="Hà Nội (ngoại thành)">Hà Nội (ngoại thành)</option>
            </select>
            <button onclick="searchJobs()">Tìm kiếm</button>
            <span id="searchLoading" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
            <div id="searchResults"></div>
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
                    <h3>Mô tả công việc:</h3>
                    <div><?php echo nl2br(htmlspecialchars($job['mo_ta'])); ?></div>
                    <p><strong>Địa điểm làm việc:</strong> <?php echo htmlspecialchars($job['dia_chi']); ?></p>
                </div>
            </div>
            <div class="chitietcv-right">
                <div class="section">
                    <h2>Thông tin chung</h2>
                    <h3>Trình độ:</h3>
                    <p>
                        <strong>Trình độ yêu cầu:</strong>
                        <?php
                        $valid_trinh_do = ['Không yêu cầu', 'Trung cấp', 'Cao đẳng', 'Đại học', 'Thạc sĩ', 'Tiến sĩ'];
                        $trinh_do_display = isset($job['trinh_do']) ? htmlspecialchars($job['trinh_do']) : 'Không xác định';
                        echo in_array($trinh_do_display, $valid_trinh_do) ? $trinh_do_display : 'Không xác định';
                        ?>
                    </p>
                    <p><strong>Số lượng tuyển:</strong> <?php echo htmlspecialchars($job['so_luong']); ?> người</p>
                    <p><strong>Hình thức làm việc:</strong> <?php echo htmlspecialchars($job['hinh_thuc']); ?></p>
                    <p><strong>Giới tính:</strong> <?php echo htmlspecialchars($job['gioi_tinh']); ?></p>
                </div>
                <div class="section">
                    <h2>Thông tin công ty</h2>
                    <p><strong>Tên công ty:</strong> <?php echo htmlspecialchars($job['ten_cong_ty']); ?></p>
                    <p><strong>Quy mô:</strong> <?php echo isset($job['quy_mo']) ? htmlspecialchars($job['quy_mo']) : 'Không xác định'; ?></p>
                    <p><strong>Lĩnh vực:</strong> <?php echo isset($job['linh_vuc']) ? htmlspecialchars($job['linh_vuc']) : 'Không xác định'; ?></p>
                    <p><strong>Địa điểm:</strong> <?php echo htmlspecialchars($job['dia_chi_cty']); ?></p>
                    <a href="giaodien_thongtincty.php?stt_cty=<?php echo htmlspecialchars($job['stt_cty']); ?>" class="button">Xem trang công ty</a>
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
                    <div class="message success"><?php echo htmlspecialchars($application_success); ?></div>
                <?php elseif (!empty($application_error)): ?>
                    <div class="message error"><?php echo htmlspecialchars($application_error); ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
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

                    <div class="info-section">
                        <div class="info-header">
                            Thông tin ứng tuyển <span class="required-notice">(*) Bắt buộc</span>
                        </div>
                        <div class="form-group">
                            <label>Họ và tên <span class="required">*</span></label>
                            <input type="text" name="ho_ten" class="form-control" value="<?php echo isset($sinh_vien['ho_ten']) ? htmlspecialchars($sinh_vien['ho_ten']) : ''; ?>" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" name="email" class="form-control" value="<?php echo isset($sinh_vien['email']) ? htmlspecialchars($sinh_vien['email']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Số điện thoại <span class="required">*</span></label>
                                <input type="tel" name="so_dien_thoai" class="form-control" value="<?php echo isset($sinh_vien['so_dien_thoai']) ? htmlspecialchars($sinh_vien['so_dien_thoai']) : ''; ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="intro-section">
                        <div class="intro-header">
                            <span class="intro-icon">🍃</span>
                            <h2>Thư giới thiệu</h2>
                        </div>
                        <div class="intro-text">Giới thiệu ngắn gọn để gây ấn tượng với nhà tuyển dụng.</div>
                        <textarea name="thu_gioi_thieu" class="intro-textarea" placeholder="Viết giới thiệu ngắn gọn về bản thân (điểm mạnh, kinh nghiệm) và lý do ứng tuyển."></textarea>
                    </div> -->

                    <div class="warning-section">
                        <div class="warning-header">
                            <span class="warning-icon">⚠️</span> Lưu ý
                        </div>
                        <p>Nghiên cứu kỹ thông tin công ty trước khi ứng tuyển. </p>
                    </div>

                    <div class="button-row">
                        <button type="button" class="cancel-btn" onclick="closeApplicationModal()">Hủy</button>
                        <button type="submit" name="submit_application" class="submit-btn">Nộp hồ sơ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <section class="random-jobs-section">
        <h2>Việc làm nổi bật</h2>
        <div class="random-jobs-container">
            <?php if (empty($random_jobs)): ?>
                <p>Không có tin tuyển dụng nào khác để hiển thị.</p>
            <?php else: ?>
                <?php foreach ($random_jobs as $random_job): ?>
                    <div class="job-card">
                        <img src="<?php echo !empty($random_job['logo']) ? '../sinh_vien/uploads/' . htmlspecialchars($random_job['logo']) : '../sinh_vien/uploads/logo.png'; ?>" alt="Company Logo" />
                        <h3><?php echo htmlspecialchars($random_job['tieu_de']); ?></h3>
                        <p><strong>Công ty:</strong> <?php echo htmlspecialchars($random_job['ten_cong_ty']); ?></p>
                        <p><strong>Địa điểm:</strong> <?php echo htmlspecialchars($random_job['dia_chi']); ?></p>
                        <a href="chi_tiet.php?ma_tuyen_dung=<?php echo htmlspecialchars($random_job['ma_tuyen_dung']); ?>">Xem chi tiết</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <img src="../img/logo.png" alt="TopCV Logo" class="footer-logo" />
                <p>Tiếp lợi thế - Nối thành công</p>
                <img src="../img/google_for_startup.webp" alt="Google for Startups" />
                <p>Liên hệ</p>
                <p>Hotline: <a href="tel:02466805958"> 0902.130.130</a> (Giờ hành chính)</p>
                <p>Email: <a href="mailto:hotro@topcv.vn">DHTNMT@hunre.edu.vn</a></p>
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

        function searchJobs() {
            const keyword = document.getElementById('searchInput').value;
            const location = document.getElementById('locationFilter').value;
            const khoa = document.getElementById('searchInput').value; // Use same input for khoa
            const resultsDiv = document.getElementById('searchResults');
            const loadingSpan = document.getElementById('searchLoading');

            resultsDiv.innerHTML = '';
            resultsDiv.style.display = 'none';
            loadingSpan.style.display = 'inline-block';

            fetch(`../logic_sinhvien/logic_search_jobs.php?action=search&khoa=${encodeURIComponent(khoa)}&keyword=${encodeURIComponent(keyword)}&location=${encodeURIComponent(location)}`)
                .then(response => response.json())
                .then(data => {
                    loadingSpan.style.display = 'none';
                    if (data.success && data.data.length > 0) {
                        resultsDiv.style.display = 'block';
                        data.data.forEach(job => {
                            const jobDiv = document.createElement('div');
                            jobDiv.className = 'search-result-item';
                            jobDiv.innerHTML = `
                            <img src="${job.logo}" alt="Company Logo" />
                            <div>
                                <h4><a href="chi_tiet.php?ma_tuyen_dung=${job.ma_tuyen_dung}">${job.tieu_de}</a></h4>
                                <p>${job.ten_cong_ty} - ${job.dia_chi}</p>
                            </div>
                        `;
                            resultsDiv.appendChild(jobDiv);
                        });
                    } else {
                        resultsDiv.style.display = 'block';
                        resultsDiv.innerHTML = '<p>Không tìm thấy công việc phù hợp.</p>';
                    }
                })
                .catch(error => {
                    loadingSpan.style.display = 'none';
                    resultsDiv.style.display = 'block';
                    resultsDiv.innerHTML = '<p>Lỗi khi tìm kiếm: ' + error.message + '</p>';
                });
        }

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchJobs();
            }
        });
    </script>
</body>

</html>