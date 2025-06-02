<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản Lý Cơ Sở Thực Tập</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../sinh_vien/giaodien_chinh.css?v=1.1">
  <link rel="stylesheet" href="../sinh_vien/footer.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
  <style>
    .text-group h3 a:hover {
      text-decoration: none;
    }

    .search-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #fff;
      border-radius: 20px;
      margin: 25px auto;
      padding: 15px 20px;
      width: 100%;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      gap: 10px;
      position: relative;
    }

    .search-bar input {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
    }

    .search-bar select {
      padding: 10px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      max-width: 200px;
    }

    .search-bar button {
      width: 90px;
      padding: 10px;
      background-color: #28a745;
      color: #fff;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .search-bar button:hover {
      background-color: #218838;
    }

    #searchResults {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      width: 100%;
      max-height: 200px;
      overflow-y: auto;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      margin-top: 5px;
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
      padding: 10px 15px;
      border-bottom: 1px solid #ececec;
      cursor: pointer;
      font-size: 14px;
      color: #263a4d;
    }

    #searchResults li:hover {
      background-color: #f5f5f5;
    }

    #searchResults li:last-child {
      border-bottom: none;
    }

    #searchResults p {
      padding: 10px 15px;
      margin: 0;
      font-size: 14px;
      color: #263a4d;
      text-align: center;
    }

    #searchLoading {
      color: #28a745;
      display: none;
      margin-right: 10px;
    }

    #clearSearch {
      cursor: pointer;
      margin-right: 10px;
      display: none;
    }

    #searchResults li a {
      text-decoration: none;
      color: #333;
      transition: color 0.3s ease;
    }

    #searchResults li a:hover {
      color: #28a745;
      text-decoration: none;
    }

    /* chat */
    /* Chat Box Styles */
    .chat-container {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 9999;
      font-family: 'Roboto', sans-serif;
    }

    .chat-box {
      width: 320px;
      height: 400px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      transition: all 0.3s ease;
      display: none;
    }

    .chat-header {
      background-color: #4CAF50;
      color: white;
      padding: 12px 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .chat-header h3 {
      margin: 0;
      font-size: 16px;
      font-weight: 500;
    }

    .chat-header button {
      background: none;
      border: none;
      color: white;
      cursor: pointer;
      font-size: 16px;
    }

    .chat-messages {
      flex: 1;
      padding: 15px;
      overflow-y: auto;
      background-color: #f5f5f5;
    }

    .message {
      margin-bottom: 10px;
      max-width: 80%;
      padding: 10px;
      border-radius: 10px;
      word-wrap: break-word;
    }

    .message.user {
      background-color: #E3F2FD;
      color: #333;
      margin-left: auto;
      border-bottom-right-radius: 2px;
    }

    .message.system {
      background-color: #EEEEEE;
      color: #333;
      border-bottom-left-radius: 2px;
    }

    .chat-input {
      display: flex;
      padding: 10px;
      background-color: #fff;
      border-top: 1px solid #e0e0e0;
    }

    .chat-input input {
      flex: 1;
      padding: 10px;
      border: 1px solid #e0e0e0;
      border-radius: 20px;
      outline: none;
      font-size: 14px;
    }

    .chat-input button {
      background-color: #4CAF50;
      color: white;
      border: none;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-left: 10px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .chat-input button:hover {
      background-color: #3d8b40;
    }

    .chat-toggle {
      width: 60px;
      height: 60px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
      font-size: 24px;
      transition: transform 0.3s, background-color 0.3s;
    }

    .chat-toggle:hover {
      background-color: #3d8b40;
      transform: scale(1.05);
    }

    /* Add animation */
    @keyframes bounceIn {
      0% {
        opacity: 0;
        transform: scale(0.3);
      }

      50% {
        opacity: 1;
        transform: scale(1.05);
      }

      70% {
        transform: scale(0.9);
      }

      100% {
        transform: scale(1);
      }
    }

    .chat-box.show {
      display: flex;
      animation: bounceIn 0.5s;
    }
  </style>
</head>

<body>
  <div class="header">
    <div class="left-section">
      <div class="logo">
        <img alt="Logo" height="40" src="../img/logo.png" width="100%" />
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
      <a href="<?php echo isset($_SESSION['name']) ? './profile.php' : '../dang_nhap_dang_ki/form_dn.php'; ?>">
        <i class="fa-solid fa-user"></i>
      </a>
    </div>
  </div>

  <div class="search-section">
    <div class="search-section1">
      <h1>Tìm cơ sở thực tập cho sinh viên Trường Đại Học Tài nguyên và Môi trường Hà Nội</h1>
      <p>Tiếp cận 40,000+ tin tuyển dụng việc làm mỗi ngày từ hàng nghìn doanh nghiệp uy tín tại Việt Nam</p>
    </div>
    <div class="aa">
      <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Tìm theo tên công ty, vị trí tuyển dụng, địa điểm..." />
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
        <span id="clearSearch" style="display: none;"><i class="fas fa-times"></i></span>
        <span id="searchLoading">Đang tìm...</span>
        <button onclick="triggerSearch()">Tìm kiếm</button>
        <div id="searchResults"></div>
      </div>
      <div class="danhmuc-container">
        <div class="danhmuc">
          <div class="danhmuc_1">
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Công nghệ thông tin</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=cong_nghe_thong_tin"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Kinh tế</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=kinh_te"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Môi trường</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=moi_truong"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Quản lý đất đai</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=quan_ly_dat_dai"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Khí tượng thủy văn</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=khi_tuong_thuy_van"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Trắc địa bản đồ và Thông tin địa lý</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=trac_dia_ban_do"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Địa chất</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=dia_chat"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Tài nguyên nước</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=tai_nguyen_nuoc"><i class="fa-solid fa-angle-right"></i></a>
            </div>

            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Lý luận chính trị</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=ly_luan_chinh_tri"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Khoa học Biển và Hải đảo</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=bien_hai_dao"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Khoa học Đại cương</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=khoa_hoc_dai_cuong"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Giáo dục thể chất và Giáo dục quốc phòng</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=the_chat_quoc_phong"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Bộ môn Luật</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=bo_mon_luat"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Bộ môn Biến đổi khí hậu và PT bền vững</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=bien_doi_khi_hau"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Bộ môn Ngoại ngữ</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=ngoai_ngu"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Bộ môn khác</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=bo_mon_khac"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_heder">
              <div class="danhmuc_1_heder-pag">
                <div class="danhmuc_1_heder-text"></div>
                <div class="danhmuc_1_heder-action">
                  <button id="prev-btn">←</button>
                  <button id="next-btn">→</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="a">
          <img src="../img/469877645_1005404278278078_3153280250481528893_n.jpg" alt="Banner">
        </div>
      </div>
    </div>
  </div>

  <div class="main-content">
    <div class="job-list">
      <h2>Việc làm tốt nhất</h2>
      <div class="job-container">
        <?php
        require_once '../db.php';
        $sql = "SELECT td.ma_tuyen_dung, td.tieu_de, td.dia_chi, td.hinh_thuc, ct.stt_cty, ct.ten_cong_ty, ct.logo
                FROM tuyen_dung td
                JOIN cong_ty ct ON td.stt_cty = ct.stt_cty
                WHERE td.trang_thai = 'Đã duyệt' AND td.noi_bat = 1";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo '<div class="job">';
            $logo = !empty($row['logo']) ? 'uploads/' . htmlspecialchars($row['logo']) : 'uploads/logo.png';
            echo '<img alt="Logo" src="' . $logo . '" />';
            echo '<div class="job-content">';
            echo '<h3><a href="chi_tiet.php?ma_tuyen_dung=' . htmlspecialchars($row['ma_tuyen_dung']) . '">' . htmlspecialchars($row['tieu_de']) . '</a></h3>';
            echo '<p><a href="giaodien_thongtincty.php?stt_cty=' . htmlspecialchars($row['stt_cty']) . '">' . htmlspecialchars($row['ten_cong_ty']) . '</a></p>';
            echo '<p class="location">' . htmlspecialchars($row['dia_chi']) . ' - ' . htmlspecialchars($row['hinh_thuc']) . '</p>';
            echo '</div>';
            echo '</div>';
          }
          $result->free();
        } else {
          echo '<p>Chưa có tin tuyển dụng nổi bật nào được duyệt.</p>';
        }
        $conn->close();
        ?>
      </div>
    </div>
  </div>

  <section class="featured-industries">
    <h2>Các Khoa và Bộ môn</h2>
    <p>Bạn muốn tìm việc mới? Xem danh sách việc làm <a href="#">tại đây</a></p>
    <div class="industries-grid responsive">
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Khoa Kinh Tế">
        <div class="text-group">
          <h3><a href="chi_tiet_khoa.php?khoa=kinh_te">Khoa Kinh Tế</a></h3>

        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Khoa Môi Trường">
        <div class="text-group">
          <h3><a href="chi_tiet_khoa.php?khoa=moi_truong">Khoa Môi Trường</a></h3>

        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Khoa Quản lý đất đai">
        <div class="text-group">
          <h3><a href="chi_tiet_khoa.php?khoa=quan_ly_dat_dai">Khoa Quản lý đất đai</a></h3>

        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Khoa Khí tượng thủy văn">
        <div class="text-group">
          <h3><a href="chi_tiet_khoa.php?khoa=khi_tuong_thuy_van">Khoa Khí tượng thủy văn</a></h3>

        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Khoa Trắc địa bản đồ">
        <div class="text-group">
          <h3><a href="chi_tiet_khoa.php?khoa=trac_dia_ban_do">Khoa Trắc địa bản đồ và Thông tin địa lý</a></h3>

        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Khoa Địa chất">
        <div class="text-group">
          <h3><a href="chi_tiet_khoa.php?khoa=dia_chat">Khoa Địa chất</a></h3>

        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Khoa Tài nguyên nước">
        <div class="text-group">
          <h3><a href="chi_tiet_khoa.php?khoa=tai_nguyen_nuoc">Khoa Tài nguyên nước</a></h3>

        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Khoa Công nghệ thông tin">
        <div class="text-group">
          <h3><a href="chi_tiet_khoa.php?khoa=cong_nghe_thong_tin">Khoa Công nghệ thông tin</a></h3>

        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Khoa Lý luận chính trị">
        <div class="text-group">
          <h3><a href="chi_tiet_khoa.php?khoa=ly_luan_chinh_tri">Khoa Lý luận chính trị</a></h3>

        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Khoa Khoa học Biển và Hải đảo">
        <div class="text-group">
          <h3><a href="chi_tiet_khoa.php?khoa=bien_hai_dao">Khoa Khoa học Biển và Hải đảo</a></h3>

        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Khoa Khoa học Đại cương">
        <div class="text-group">
          <h3><a href="chi_tiet_khoa.php?khoa=khoa_hoc_dai_cuong">Khoa Khoa học Đại cương</a></h3>

        </div>
      </div>
    </div>
  </section>

  <div class="slider-container">
    <div class="slider">
      <div class="slides">
        <img class="hoo" src="../img/anh.png" alt="Image 1" />
        <img class="hoo" src="../img/Fanpage_1.jpg" alt="Image 2" />
        <img class="hoo" src="../img/header-bg.webp" alt="Image 3" />
        <img class="hoo" src="../img/a1.jpg" alt="Image 4" />
      </div>
    </div>
    <button class="prev" onclick="prevSlide()">❮</button>
    <button class="next" onclick="nextSlide()">❯</button>
    <div class="dots" id="dots-container"></div>
  </div>
  <div class="chat-container">
    <div class="chat-box" id="chatBox">
      <div class="chat-header">
        <h3>Chat Support</h3>
        <button id="minimizeChat"><i class="fas fa-minus"></i></button>
      </div>
      <div class="chat-messages" id="chatMessages">
        <div class="message system">
        </div>
      </div>
      <div class="chat-input">
        <input type="text" id="messageInput" placeholder="Type your message...">
        <button id="sendMessage"><i class="fas fa-paper-plane"></i></button>
      </div>
    </div>
    <button class="chat-toggle" id="chatToggle">
      <i class="fas fa-comments"></i>
    </button>
  </div>

  <footer class="footer">
    <div class="footer-container">
      <div class="footer-section">
        <img src="../img/logo.png" alt="Logo" class="footer-logo" />
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
        <h4>Về chúng tôi</h4>
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
          <li><a href="#">Hồ sơ cá nhân</a></li>
          <li><a href="#">Hướng dẫn viết CV</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h4>Khám phá</h4>
        <ul>
          <li><a href="#">Ứng dụng di động</a></li>
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

  <script src="../js/giaodienchinh.js"></script>
  <script>
    $(document).ready(function() {
      $('.industries-grid').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        dots: true,
        arrows: true,
        responsive: [{
            breakpoint: 1024,
            settings: {
              slidesToShow: 3
            }
          },
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 2
            }
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 1
            }
          }
        ]
      });
    });

    document.addEventListener("DOMContentLoaded", function() {
      const chatToggle = document.getElementById('chatToggle');
      const chatBox = document.getElementById('chatBox');
      const minimizeChat = document.getElementById('minimizeChat');
      const messageInput = document.getElementById('messageInput');
      const sendMessage = document.getElementById('sendMessage');
      const chatMessages = document.getElementById('chatMessages');

      const searchInput = document.getElementById("searchInput");
      const locationFilter = document.getElementById("locationFilter");
      const searchResults = document.getElementById("searchResults");
      const searchLoading = document.getElementById("searchLoading");
      const clearSearch = document.getElementById("clearSearch");
      let debounceTimer;

      // Trigger search manually (button click)
      window.triggerSearch = function() {
        const keyword = searchInput.value.trim();
        const location = locationFilter.value;
        performSearch(keyword, location);
      };

      // Real-time search on input
      searchInput.addEventListener("input", function() {
        clearTimeout(debounceTimer);
        const keyword = this.value.trim();
        const location = locationFilter.value;
        clearSearch.style.display = keyword ? "inline-block" : "none";

        if (keyword.length < 2 && !location) {
          searchResults.classList.remove("active");
          searchResults.innerHTML = "";
          return;
        }

        searchLoading.style.display = "inline-block";
        debounceTimer = setTimeout(() => {
          performSearch(keyword, location);
        }, 300);
      });

      // Trigger search on location change
      locationFilter.addEventListener("change", function() {
        const keyword = searchInput.value.trim();
        const location = this.value;
        performSearch(keyword, location);
      });

      // Clear search input and filters
      clearSearch.addEventListener("click", function() {
        searchInput.value = "";
        locationFilter.value = "";
        clearSearch.style.display = "none";
        searchResults.classList.remove("active");
        searchResults.innerHTML = "";
      });

      // Perform search
      function performSearch(keyword, location = "") {
        searchLoading.style.display = "inline-block";
        searchResults.classList.remove("active");

        const query = new URLSearchParams();
        if (keyword) query.append("keyword", keyword);
        if (location) query.append("location", location);

        fetch(`../logic_sinhvien/search_jobs.php?${query.toString()}`)
          .then(response => {
            searchLoading.style.display = "none";
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
          })
          .then(data => {
            if (data.success && data.data.jobs.length > 0) {
              renderSuggestions(data.data.jobs);
            } else {
              searchResults.innerHTML = "<p>Không tìm thấy kết quả phù hợp.</p>";
              searchResults.classList.add("active");
            }
          })
          .catch(error => {
            searchLoading.style.display = "none";
            searchResults.innerHTML = "<p>Đã xảy ra lỗi khi tìm kiếm.</p>";
            searchResults.classList.add("active");
            console.error("Search error:", error);
          });
      }

      // Render suggestions dropdown
      function renderSuggestions(jobs) {
        searchResults.innerHTML = "";
        const ul = document.createElement("ul");
        jobs.forEach(job => {
          const li = document.createElement("li");
          const logo = job.logo || "uploads/logo.png";
          li.innerHTML = `
            <img src="${escapeHTML(logo)}" alt="Logo" style="width: 30px; height: 30px; margin-right: 10px; vertical-align: middle;" />
            <strong><a href="chi_tiet.php?ma_tuyen_dung=${encodeURIComponent(job.ma_tuyen_dung)}">${escapeHTML(job.tieu_de)}</a></strong><br>
            <small><a href="giaodien_thongtincty.php?stt_cty=${encodeURIComponent(job.stt_cty)}">${escapeHTML(job.ten_cong_ty)}</a> - ${escapeHTML(job.dia_chi)}</small>
          `;
          ul.appendChild(li);
        });
        searchResults.appendChild(ul);
        searchResults.classList.add("active");
      }

      // Escape HTML to prevent XSS
      function escapeHTML(str) {
        return str.replace(/[&<>"']/g, match => ({
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#39;'
        })[match]);
      }

      // Hide suggestions when clicking outside
      document.addEventListener("click", function(event) {
        if (!searchResults.contains(event.target) && !searchInput.contains(event.target) && !locationFilter.contains(event.target)) {
          searchResults.classList.remove("active");
        }
      });

      // Pagination for danhmuc
      const items = document.querySelectorAll(".danhmuc_1_option");
      const itemsPerPage = 4;
      let currentPage = 1;
      const totalPages = Math.ceil(items.length / itemsPerPage);
      const pageInfo = document.querySelector(".danhmuc_1_heder-text");

      function showPage(page) {
        items.forEach((item, index) => {
          item.style.display = (index >= (page - 1) * itemsPerPage && index < page * itemsPerPage) ? "block" : "none";
        });
        pageInfo.textContent = `${page}/${totalPages}`;
      }

      document.getElementById("prev-btn").addEventListener("click", function() {
        if (currentPage > 1) {
          currentPage--;
          showPage(currentPage);
        }
      });

      document.getElementById("next-btn").addEventListener("click", function() {
        if (currentPage < totalPages) {
          currentPage++;
          showPage(currentPage);
        }
      });

      showPage(currentPage);


      chatToggle.addEventListener('click', () => chatBox.classList.toggle('show'));
      minimizeChat.addEventListener('click', () => chatBox.classList.remove('show'));

      // Hàm gửi tin nhắn
      function sendUserMessage() {
        const message = messageInput.value.trim();
        if (!message) return;

        // Hiển thị tin nhắn người dùng
        const userMessageDiv = document.createElement('div');
        userMessageDiv.className = 'message user';
        userMessageDiv.innerHTML = `<span>${escapeHtml(message)}</span>`;
        chatMessages.appendChild(userMessageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        messageInput.value = '';

        // Hiển thị loading
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'message system loading';
        loadingDiv.innerHTML = `<span>Đang xử lý... <i class="fas fa-spinner fa-spin"></i></span>`;
        chatMessages.appendChild(loadingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Gửi yêu cầu đến server
        fetch('chat_01.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              message
            })
          })
          .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
          })
          .then(data => {
            chatMessages.removeChild(loadingDiv);
            const responseDiv = document.createElement('div');
            responseDiv.className = 'message system';
            responseDiv.innerHTML = `<span>${formatMarkdown(escapeHtml(data.reply))}</span>`;
            chatMessages.appendChild(responseDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
          })
          .catch(error => {
            chatMessages.removeChild(loadingDiv);
            const responseDiv = document.createElement('div');
            responseDiv.className = 'message system error';
            responseDiv.innerHTML = `<span>❌ Lỗi: ${escapeHtml(error.message)}</span>`;
            chatMessages.appendChild(responseDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
          });
      }

      // Hàm xử lý markdown đơn giản
      function formatMarkdown(text) {
        return text
          .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // In đậm
          .replace(/\*(.*?)\*/g, '<em>$1</em>') // Nghiêng
          .replace(/(\n\s*[-•])/g, '<br>&bull;') // Danh sách gạch đầu dòng
          .replace(/\n/g, '<br>'); // Xuống dòng
      }

      // Hàm escape HTML để tránh XSS
      function escapeHtml(unsafe) {
        return unsafe
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;")
          .replace(/'/g, "&#039;");
      }

      // Sự kiện gửi tin nhắn
      sendMessage.addEventListener('click', sendUserMessage);
      messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          sendUserMessage();
        }
      });

      // Tin nhắn chào mừng
      setTimeout(() => {
        const welcomeDiv = document.createElement('div');
        welcomeDiv.className = 'message system';
        welcomeDiv.innerHTML = `
            <span>
                🎓 <strong>Xin chào! Tôi là trợ lý ảo của HUNRE</strong><br>
                Bạn có thể hỏi tôi về:<br>
                &bull; 🏢 Danh sách công ty thực tập<br>
                &bull; 💼 Tin tuyển dụng mới nhất<br>
                &bull; 📋 Thông tin báo cáo thực tập<br>
                &bull; ⭐ Đánh giá thực tập<br><br>
                Hãy thử hỏi: <em>"Có công ty nào thực tập không?"</em> hoặc <em>"Tìm việc làm mới"</em>
            </span>
        `;
        chatMessages.appendChild(welcomeDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
      }, 1000);
    });



    //chat box
  </script>



</body>

</html>