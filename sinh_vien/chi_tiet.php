
<?php
session_start();


// Kết nối CSDL
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
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet" href="./chi_tiet.css">
    <style>
  





    </style>

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
            <a href="#">Việc làm</a>
            <a href="#">Hồ sơ &amp; CV</a>
            <a class="btn" href="./form_dn.html">Đăng nhập</a>
            <a class="btn" href="./form_dk.html">Đăng ký</a>
            <a href="#"><i class="fa-solid fa-user"></i></a>
        </div>
    </div>
    
    <div class="timkiem-job">
      <div class="search-bar">
          <input placeholder="Khoa ..." type="text" />
          <button>Tìm kiếm</button>
      </div>
    </div>


    <!-- <div class="container">
        <div class="header_1">
            <h1>Customer Service - Flight Ticket App</h1>
            <div class="actions">
                <button class="apply-btn">Ứng tuyển ngay</button>
                <button class="save-btn">Lưu tin</button>
            </div>
        </div>
        <div class="details">
            <h2>Chi tiết tin tuyển dụng</h2>
            <p><strong>Chuyên môn:</strong> Chăm sóc khách hàng</p>
            <p><strong>Mô tả công việc:</strong></p>
            <p>Liaising between guests and partners to resolve light complexity issues via inbound, outbound, email, chat, and messaging...</p>
            <div class="info">
                <div>
                    <p>Mức lương</p>
                    <span>10 - 13 triệu</span>
                </div>
                <div>
                    <p>Địa điểm</p>
                    <span>Hồ Chí Minh</span>
                </div>
                <div>
                    <p>Kinh nghiệm</p>
                    <span>Không yêu cầu</span>
                </div>
            </div>
        </div>
        <div class="company">
            <h3>Thông tin công ty</h3>
            <p><strong>Công ty:</strong> TNHH Concentrix Service Vietnam</p>
            <p><strong>Quy mô:</strong> 1000+ nhân viên</p>
            <p><strong>Lĩnh vực:</strong> Tư vấn</p>
            <p><strong>Địa chỉ:</strong> Tầng 4, QTSC Building...</p>
        </div>
    </div> -->


    <!-- doạn dưới tim kiếm -->

     <!-- job-detail-_wwraper -->
     <div class="container">
        <div class="chitietcv">
            <div class="chitietcv-left">
                <div class="section">
                    <h2>Tiêu đề và thông tin chính</h2>
                    <p><strong>Tên công việc:</strong> <?php echo htmlspecialchars($job['tieu_de']); ?></p>
                    <p><strong>Địa điểm:</strong> <?php echo htmlspecialchars($job['dia_chi']); ?></p>
                    <p><strong>Kinh nghiệm:</strong> Không yêu cầu kinh nghiệm</p>
                    <p><strong>Hạn nộp hồ sơ:</strong> <?php echo htmlspecialchars($job['han_nop']); ?></p>
                    <a href="#" class="button">Ứng tuyển ngay</a>
                </div>

                <div class="section">
                    <h2>Chi tiết tin tuyển dụng</h2>
                    <p><strong>Chuyên môn:</strong> Chăm sóc khách hàng</p>
                    <h3>Mô tả công việc:</h3>
                    <div>
                        <?php echo nl2br(htmlspecialchars($job['mo_ta'])); ?>
                    </div>
                    <h3>Yêu cầu công việc:</h3>
                    <div>
                        <?php echo nl2br(htmlspecialchars($job['yeu_cau'])); ?>
                    </div>
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

    </div>

<!-- endend -->
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
      
    
</body>
</html>