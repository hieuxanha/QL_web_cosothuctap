<?php
require_once '../db.php';
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$khoa = isset($_GET['khoa']) ? $_GET['khoa'] : '';
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';

$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

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

$khoa_value = isset($khoa_mapping[$khoa]) ? $khoa_mapping[$khoa] : '';
$error_message = '';
if (empty($khoa_value)) {
  $khoa_value = ''; // Hiển thị tất cả tin nếu khoa không hợp lệ
  $error_message = "Tham số 'khoa' không hợp lệ. Hiển thị tất cả tin tuyển dụng.";
}

$sql = "
    SELECT t.*, c.ten_cong_ty, c.logo, c.stt_cty
    FROM tuyen_dung t 
    JOIN cong_ty c ON t.stt_cty = c.stt_cty 
    WHERE t.trang_thai = 'Đã duyệt'";
$params = [];
$conditions = [];

if ($khoa_value) {
  $sql .= " AND t.khoa = ?";
  $params[] = $khoa_value;
}

if ($keyword) {
  $conditions[] = "(t.tieu_de LIKE ? OR c.ten_cong_ty LIKE ?)";
  $likeKeyword = "%$keyword%";
  $params[] = $likeKeyword;
  $params[] = $likeKeyword;
}

if ($location) {
  $conditions[] = "t.dia_chi LIKE ?";
  $likeLocation = "%$location%";
  $params[] = $likeLocation;
}

if ($conditions) {
  $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY t.han_nop DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

$stmt = $conn->prepare($sql);
$param_count = count($params) - 2; // Số lượng tham số chuỗi
$types = ($param_count > 0 ? str_repeat('s', $param_count) : '') . 'ii';
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$total_sql = "
    SELECT COUNT(*) AS total 
    FROM tuyen_dung t 
    JOIN cong_ty c ON t.stt_cty = c.stt_cty 
    WHERE t.trang_thai = 'Đã duyệt'";
$total_params = [];
if ($khoa_value) {
  $total_sql .= " AND t.khoa = ?";
  $total_params[] = $khoa_value;
}
if ($keyword) {
  $total_sql .= " AND (t.tieu_de LIKE ? OR c.ten_cong_ty LIKE ?)";
  $likeKeyword = "%$keyword%";
  $total_params[] = $likeKeyword;
  $total_params[] = $likeKeyword;
}
if ($location) {
  $total_sql .= " AND t.dia_chi LIKE ?";
  $likeLocation = "%$location%";
  $total_params[] = $likeLocation;
}

$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param(str_repeat('s', count($total_params)), ...$total_params);
$total_stmt->execute();
$total_records = $total_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);
$total_stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Danh sách tin tuyển dụng - <?php echo htmlspecialchars($khoa ? $khoa : 'Tất cả'); ?></title>
  <link rel="stylesheet" href="../sinh_vien/chi_tiet_khoa.css">
  <link rel="stylesheet" href="../sinh_vien/footer.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <style>
    .timkiem-job {
      display: flex;
      justify-content: center;
    }

    .search-bar {
      background-color: #fff;
      padding: 12px;
      position: relative;
      display: inline-flex;
      justify-content: center;
      gap: 10px;
      border-radius: 5px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .search-bar input {
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    #clearSearch {
      cursor: pointer;
      color: #555;
      font-size: 18px;
      margin-left: 5px;
      display: none;
    }

    #clearSearch:hover {
      color: #000;
    }

    #searchLoading {
      margin-left: 10px;
      display: none;
    }

    #searchResults {
      position: absolute;
      top: 100%;
      left: 0;
      width: 100%;
      max-height: 300px;
      overflow-y: auto;
      background-color: #fff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 4px;
      z-index: 1000;
      display: none;
    }

    #searchResults.active {
      display: block;
    }

    #searchResults ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    #searchResults li {
      padding: 20px;
      border-bottom: 1px solid #eee;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    #searchResults li:hover {
      background-color: #f5f5f5;
    }

    #searchResults li:last-child {
      border-bottom: none;
    }

    #searchResults img {
      width: 30px;
      height: 30px;
      object-fit: contain;
    }

    .pagination {
      margin: 20px 0;
      text-align: center;
    }

    .pagination a {
      display: inline-block;
      padding: 8px 12px;
      margin: 0 4px;
      text-decoration: none;
      border: 1px solid #ddd;
      border-radius: 4px;
      color: #0078d4;
    }

    .pagination a:hover {
      background-color: #0078d4;
      color: white;
    }

    .pagination a.active {
      background-color: #0078d4;
      color: white;
      font-weight: bold;
    }

    .job-card img {
      width: 60px;
      height: 60px;
      object-fit: contain;
      margin-right: 15px;
    }

    .job-card h3 {
      margin: 0;
      font-size: 18px;
    }

    .job-card h3 a {
      color: #333;
      text-decoration: none;
    }

    .job-card h3 a:hover {
      text-decoration: underline;
    }

    .job-card p a {
      color: #333;
      text-decoration: none;
    }

    .job-card p a:hover {
      text-decoration: underline;
    }
  </style>
</head>

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
      <?php
      if (!isset($_SESSION['name'])) {
        echo '<a class="btn" href="../dang_nhap_dang_ki/form_dn.php">Đăng nhập</a>';
        echo '<a class="btn" href="../dang_nhap_dang_ki/form_dk.php">Đăng ký</a>';
      }
      ?>
      <?php
      if (isset($_SESSION['name'])) {
        echo '<a href="./profile.php"><i class="fa-solid fa-user"></i></a>';
      } else {
        echo '<a href="../dang_nhap_dang_ki/form_dn.php"><i class="fa-solid fa-user"></i></a>';
      }
      ?>
    </div>
  </div>

  <div class="timkiem-job">
    <div class="search-bar">
      <input id="searchInput" placeholder="Tìm theo tiêu đề, công ty..." type="text" value="<?php echo htmlspecialchars($keyword); ?>" />
      <select id="locationFilter">
        <option value="">Địa điểm</option>
        <option value="Ba Đình" <?php echo $location === 'Ba Đình' ? 'selected' : ''; ?>>Ba Đình</option>
        <option value="Hoàn Kiếm" <?php echo $location === 'Hoàn Kiếm' ? 'selected' : ''; ?>>Hoàn Kiếm</option>
        <option value="Tây Hồ" <?php echo $location === 'Tây Hồ' ? 'selected' : ''; ?>>Tây Hồ</option>
        <option value="Cầu Giấy" <?php echo $location === 'Cầu Giấy' ? 'selected' : ''; ?>>Cầu Giấy</option>
        <option value="Đống Đa" <?php echo $location === 'Đống Đa' ? 'selected' : ''; ?>>Đống Đa</option>
        <option value="Hai Bà Trưng" <?php echo $location === 'Hai Bà Trưng' ? 'selected' : ''; ?>>Hai Bà Trưng</option>
        <option value="Hoàng Mai" <?php echo $location === 'Hoàng Mai' ? 'selected' : ''; ?>>Hoàng Mai</option>
        <option value="Long Biên" <?php echo $location === 'Long Biên' ? 'selected' : ''; ?>>Long Biên</option>
        <option value="Nam Từ Liêm" <?php echo $location === 'Nam Từ Liêm' ? 'selected' : ''; ?>>Nam Từ Liêm</option>
        <option value="Bắc Từ Liêm" <?php echo $location === 'Bắc Từ Liêm' ? 'selected' : ''; ?>>Bắc Từ Liêm</option>
        <option value="Thanh Xuân" <?php echo $location === 'Thanh Xuân' ? 'selected' : ''; ?>>Thanh Xuân</option>
        <option value="Sơn Tây" <?php echo $location === 'Sơn Tây' ? 'selected' : ''; ?>>Sơn Tây</option>
        <option value="Ba Vì" <?php echo $location === 'Ba Vì' ? 'selected' : ''; ?>>Ba Vì</option>
        <option value="Chương Mỹ" <?php echo $location === 'Chương Mỹ' ? 'selected' : ''; ?>>Chương Mỹ</option>
        <option value="Đan Phượng" <?php echo $location === 'Đan Phượng' ? 'selected' : ''; ?>>Đan Phượng</option>
        <option value="Đông Anh" <?php echo $location === 'Đông Anh' ? 'selected' : ''; ?>>Đông Anh</option>
        <option value="Gia Lâm" <?php echo $location === 'Gia Lâm' ? 'selected' : ''; ?>>Gia Lâm</option>
        <option value="Hoài Đức" <?php echo $location === 'Hoài Đức' ? 'selected' : ''; ?>>Hoài Đức</option>
        <option value="Mỹ Đức" <?php echo $location === 'Mỹ Đức' ? 'selected' : ''; ?>>Mỹ Đức</option>
        <option value="Phú Xuyên" <?php echo $location === 'Phú Xuyên' ? 'selected' : ''; ?>>Phú Xuyên</option>
        <option value="Quốc Oai" <?php echo $location === 'Quốc Oai' ? 'selected' : ''; ?>>Quốc Oai</option>
        <option value="Thạch Thất" <?php echo $location === 'Thạch Thất' ? 'selected' : ''; ?>>Thạch Thất</option>
        <option value="Thái Nguyên" <?php echo $location === 'Thái Nguyên' ? 'selected' : ''; ?>>Thái Nguyên</option>
        <option value="Thường Tín" <?php echo $location === 'Thường Tín' ? 'selected' : ''; ?>>Thường Tín</option>
        <option value="Ứng Hòa" <?php echo $location === 'Ứng Hòa' ? 'selected' : ''; ?>>Ứng Hòa</option>
        <option value="Phúc Thọ" <?php echo $location === 'Phúc Thọ' ? 'selected' : ''; ?>>Phúc Thọ</option>
        <option value="Hà Nội (ngoại thành)" <?php echo $location === 'Hà Nội (ngoại thành)' ? 'selected' : ''; ?>>Hà Nội (ngoại thành)</option>
      </select>
      <span id="clearSearch" style="display: none;"><i class="fas fa-times"></i></span>
      <button onclick="updateSearch(document.getElementById('searchInput').value, document.getElementById('locationFilter').value)">Tìm kiếm</button>
      <span id="searchLoading"><i class="fas fa-spinner fa-spin"></i></span>
      <div id="searchResults"></div>
    </div>
  </div>

  <div class="container">
    <div class="main-content">
      <?php if (!empty($error_message)): ?>
        <div style="color: red; padding: 10px; margin: 10px 0; background: #ffe0e0;">
          <?php echo htmlspecialchars($error_message); ?>
        </div>
      <?php endif; ?>
      <section class="job-listings">
        <?php
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $ma_tuyen_dung = $row['ma_tuyen_dung'];
            $sql_ung_tuyen = "SELECT COUNT(*) as so_ung_tuyen FROM ung_tuyen WHERE ma_tuyen_dung = ?";
            $stmt_ung_tuyen = $conn->prepare($sql_ung_tuyen);
            $stmt_ung_tuyen->bind_param("s", $ma_tuyen_dung);
            $stmt_ung_tuyen->execute();
            $result_ung_tuyen = $stmt_ung_tuyen->get_result();
            $so_ung_tuyen = $result_ung_tuyen->fetch_assoc()['so_ung_tuyen'];
            $stmt_ung_tuyen->close();

            echo '<div class="job-card">';
            echo '<img src="../sinh_vien/uploads/' . htmlspecialchars($row['logo']) . '" alt="Company Logo" class="job-image">';
            echo '<div class="job-info">';
            echo '<h3><a href="chi_tiet.php?ma_tuyen_dung=' . htmlspecialchars($row['ma_tuyen_dung']) . '">' . htmlspecialchars($row['tieu_de']) . '</a></h3>';
            echo '<p><a href="giaodien_thongtincty.php?stt_cty=' . htmlspecialchars($row['stt_cty']) . '">' . htmlspecialchars($row['ten_cong_ty']) . '</a></p>';
            echo '<p>' . htmlspecialchars($row['dia_chi']) . ' | Hạn nộp: ' . htmlspecialchars($row['han_nop']) . '</p>';
            echo '<p>Số lượng tuyển: ' . htmlspecialchars($row['so_luong']) . ' | Đã ứng tuyển: ' . $so_ung_tuyen . '</p>';
            echo '</div>';
            echo '</div>';
          }
        } else {
          echo '<p>Không có tin tuyển dụng nào cho khoa này. Kiểm tra dữ liệu trong bảng tuyen_dung và cong_ty.</p>';
          if ($khoa_value) {
            echo '<p>Giá trị khoa hiện tại: ' . htmlspecialchars($khoa_value) . '</p>';
          }
        }
        ?>
      </section>

      <?php if ($total_pages > 1): ?>
        <div class="pagination">
          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?khoa=<?php echo urlencode($khoa); ?>&page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>&location=<?php echo urlencode($location); ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

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
    let debounceTimer;
    document.getElementById("searchInput").addEventListener("keyup", function() {
      clearTimeout(debounceTimer);
      const keyword = this.value.trim();
      const location = document.getElementById("locationFilter").value;
      const resultsContainer = document.getElementById("searchResults");
      const loadingSpinner = document.getElementById("searchLoading");
      const clearSearch = document.getElementById("clearSearch");

      if (keyword === "" && location === "") {
        resultsContainer.classList.remove("active");
        clearSearch.style.display = 'none';
        return;
      }

      clearSearch.style.display = 'inline-block';
      loadingSpinner.style.display = 'inline-block';
      debounceTimer = setTimeout(() => {
        const url = `../logic_sinhvien/logic_chi_tiet_khoa.php?action=search&khoa=<?php echo urlencode($khoa); ?>&keyword=${encodeURIComponent(keyword)}&location=${encodeURIComponent(location)}`;

        fetch(url)
          .then(response => {
            loadingSpinner.style.display = 'none';
            if (!response.ok) throw new Error("HTTP status " + response.status);
            return response.json();
          })
          .then(data => {
            resultsContainer.innerHTML = "";
            if (data.success && data.data.jobs.length > 0) {
              const resultList = document.createElement("ul");
              data.data.jobs.slice(0, 10).forEach(job => {
                const listItem = document.createElement("li");
                const logo = job.logo || "../sinh_vien/uploads/logo.png";
                listItem.innerHTML = `
                                    <img src="${escapeHTML(logo)}" alt="Company Logo" />
                                    <div>
                                        <strong>${escapeHTML(job.tieu_de)}</strong>
                                        <p style="margin: 0; font-size: 12px;">${escapeHTML(job.ten_cong_ty)}</p>
                                    </div>
                                `;
                listItem.addEventListener("click", () => {
                  updateSearch(job.tieu_de, location);
                  resultsContainer.classList.remove("active");
                });
                resultList.appendChild(listItem);
              });
              resultsContainer.appendChild(resultList);
              resultsContainer.classList.add("active");
            } else {
              resultsContainer.innerHTML = "<p>Không tìm thấy tin tuyển dụng phù hợp.</p>";
              resultsContainer.classList.add("active");
            }
          })
          .catch(error => {
            loadingSpinner.style.display = 'none';
            alert("Có lỗi xảy ra khi tìm kiếm: " + error.message);
            console.error("Lỗi tìm kiếm:", error);
          });
      }, 300);
    });

    document.getElementById("locationFilter").addEventListener("change", function() {
      const keyword = document.getElementById("searchInput").value.trim();
      const location = this.value;
      updateSearch(keyword, location);
    });

    document.getElementById("clearSearch").addEventListener("click", function() {
      document.getElementById("searchInput").value = "";
      document.getElementById("locationFilter").value = "";
      this.style.display = 'none';
      updateSearch("", "");
    });

    document.addEventListener("click", function(event) {
      const resultsContainer = document.getElementById("searchResults");
      const searchInput = document.getElementById("searchInput");
      if (!resultsContainer.contains(event.target) && !searchInput.contains(event.target)) {
        resultsContainer.classList.remove("active");
      }
    });

    function escapeHTML(str) {
      return str.replace(/[&<>"']/g, match => ({
        '&': '&',
        '<': '<',
        '>': '>',
        '"': '"',
        "'": "'"
      })[match]);
    }

    function updateSearch(keyword, location) {
      const params = new URLSearchParams();
      params.append('khoa', '<?php echo urlencode($khoa); ?>');
      params.append('page', '1');
      if (keyword) params.append('keyword', keyword);
      if (location) params.append('location', location);
      window.location.href = `?${params.toString()}`;
    }

    window.onload = function() {
      const keyword = document.getElementById("searchInput").value.trim();
      const location = document.getElementById("locationFilter").value;
      if (keyword || location) {
        document.getElementById("clearSearch").style.display = 'inline-block';
      }
    };
  </script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>