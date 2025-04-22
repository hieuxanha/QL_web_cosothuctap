<?php
// Kết nối CSDL
require_once '../db.php';

// Lấy tham số khoa từ URL
$khoa = isset($_GET['khoa']) ? $_GET['khoa'] : '';

// Ánh xạ tên khoa từ URL sang giá trị trong CSDL
$khoa_mapping = [
    'kinh_te' => 'kinh_te',
    'moi_truong' => 'moi_truong',
    'quan_ly_dat_dai' => 'quan_ly_dat_dai',
    'khi_tuong_thuy_van' => 'khi_tuong_thuy_van',
    'trac_dia_ban_do' => 'trac_dia_ban_do',
    'dia_chat' => 'dia_chat',
    'tai_nguyen_nuoc' => 'tai_nguyen_nuoc',
    'cong_nghe_thong_tin' => 'cntt',
    'ly_luan_chinh_tri' => 'ly_luan_chinh_tri',
    'khoa_hoc_bien_hai_dao' => 'bien_hai_dao',
    'khoa_hoc_dai_cuong' => 'khoa_hoc_dai_cuong',
    'giao_duc_the_chat_quoc_phong' => 'the_chat_quoc_phong',
    'bo_mon_luat' => 'bo_mon_luat',
    'bien_doi_khi_hau' => 'bien_doi_khi_hau',
    'ngoai_ngu' => 'ngoai_ngu'
];

// Chuyển đổi tham số khoa từ URL sang giá trị trong CSDL
$khoa_value = isset($khoa_mapping[$khoa]) ? $khoa_mapping[$khoa] : '';

// Truy vấn CSDL để lấy tin tuyển dụng theo khoa và thông tin công ty
$sql = "SELECT t.*, c.ten_cong_ty, c.logo 
        FROM tuyen_dung t 
        JOIN cong_ty c ON t.stt_cty = c.stt_cty 
        WHERE t.khoa = ? AND t.trang_thai = 'Đã duyệt'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $khoa_value);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Danh sách tin tuyển dụng - <?php echo htmlspecialchars($khoa); ?></title>
  <link rel="stylesheet" href="../sinh_vien/chi_tiet_khoa.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
  <!-- Header -->
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
        <a href="#">Hồ sơ & CV</a>
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

  <div class="container">
    <div class="main-content">
      <!-- Sidebar Filters -->
      <aside class="sidebar">
        <h3>Bộ lọc</h3>
        <div class="filter-group">
          <h4>Danh mục</h4>
          <label><input type="checkbox"> Sales Bán lẻ</label>
          <label><input type="checkbox"> Kinh doanh khác</label>
          <label><input type="checkbox"> Sales Admin</label>
        </div>
        <div class="filter-group">
          <h4>Hình thức kinh doanh</h4>
          <label><input type="radio" name="business-type"> Tất cả</label>
          <label><input type="radio" name="business-type"> Telesales</label>
        </div>
      </aside>

      <!-- Job Listings -->
      <section class="job-listings">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Truy vấn số lượng ứng tuyển cho tin này
                $ma_tuyen_dung = $row['ma_tuyen_dung'];
                $sql_ung_tuyen = "SELECT COUNT(*) as so_ung_tuyen FROM ung_tuyen WHERE ma_tuyen_dung = ?";
                $stmt_ung_tuyen = $conn->prepare($sql_ung_tuyen);
                $stmt_ung_tuyen->bind_param("s", $ma_tuyen_dung);
                $stmt_ung_tuyen->execute();
                $result_ung_tuyen = $stmt_ung_tuyen->get_result();
                $so_ung_tuyen = $result_ung_tuyen->fetch_assoc()['so_ung_tuyen'];

                echo '<div class="job-card">';
                echo '<img src="../upload/' . htmlspecialchars($row['logo']) . '" alt="Company Logo" class="job-image">';
                echo '<div class="job-info">';
                echo '<h4>' . htmlspecialchars($row['tieu_de']) . '</h4>';
                echo '<p>' . htmlspecialchars($row['ten_cong_ty']) . '</p>';
                echo '<p>' . htmlspecialchars($row['dia_chi']) . ' | Hạn nộp: ' . htmlspecialchars($row['han_nop']) . '</p>';
                echo '<p>Số lượng tuyển: ' . htmlspecialchars($row['so_luong']) . ' | Đã ứng tuyển: ' . $so_ung_tuyen . '</p>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>Không có tin tuyển dụng nào cho khoa này.</p>';
        }
        ?>
      </section>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-section">
        <img src="logo.png" alt="TopCV Logo" class="footer-logo" />
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

<?php
// Đóng kết nối
$stmt->close();
$conn->close();
?>