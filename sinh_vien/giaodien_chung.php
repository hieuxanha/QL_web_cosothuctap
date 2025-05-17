<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Ql_csthcsth</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet" />


  <link rel="stylesheet" href="../sinh_vien/giaodien_chinh.css?v=1.0">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.css" />
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick-theme.css" />
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.min.js"></script>

  <!-- Thêm CSS -->


  <style>
    /* style.css */


    .industry-card {
      background-color: #fff;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      text-align: center;
      /* box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); */
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      padding: 10px;
      margin: 0 10px;
      display: flex;
      flex-direction: column;
      /* Sắp xếp nội dung theo chiều dọc */
      align-items: center;
      /* Căn giữa theo trục ngang */
      justify-content: space-between;
      /* Căn đều giữa các thành phần */
      min-height: 180px;
      /* Chiều cao tối thiểu */
      height: auto;
      /* Cho phép tự động giãn theo nội dung */
    }


    .industry-card img {
      margin-bottom: 40px;
      /* Tạo khoảng cách giữa ảnh và nhóm văn bản */
    }

    .text-group h3 a:hover {
      text-decoration: none;
    }


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
        if (session_status() == PHP_SESSION_NONE) {
          session_start();
        }

        if (isset($_SESSION['name'])) {

          echo '<div class="dropdown">';
          echo '<span class="user-name">Xin chào, ' . htmlspecialchars($_SESSION['name']) . '</span>';
          echo '<div class="dropdown-content">';
          echo '<a href="../dang_nhap_dang_ki/logic_dangxuat.php">Đăng xuất</a>';
          echo '</div>';
          echo '</div>';
        } else {

          //  echo '<a href="./formdangnhapky.php">Tài khoản</a>';
        }

        ?>


      </div>
      <a href="#">Việc làm</a>
      <a href="#">Hồ sơ &amp; CV</a>
      <a class="btn" href="../dang_nhap_dang_ki/form_dn.php">Đăng nhập</a>
      <a class="btn" href="../dang_nhap_dang_ki/form_dk.php">Đăng ký</a>
      <?php
      if (isset($_SESSION['name'])) {
        echo '<a href="./profile.php"><i class="fa-solid fa-user"></i></a>';
      } else {
        echo '<a href="../dang_nhap_dang_ki/form_dn.php"><i class="fa-solid fa-user"></i></a>';
      }
      ?>
    </div>
  </div>





  <!-- #b3b8bd -->

  <div class="search-section">
    <div class="search-section1">
      <h1>Tìm cơ sở thực tập cho sinh viên Trường Đại Học Tài nguyên và Môi trường Hà Nội</h1>
      <p>Tiếp cận 40,000+ tin tuyển dụng việc làm mỗi ngày từ hàng nghìn doanh nghiệp uy tín tại Việt Nam</p>
    </div>

    <div class="aa">
      <div class="search-bar">
        <input type="text" placeholder="Tất cả Tỉnh/Thành phố" />
        <button>Tìm kiếm</button>
      </div>

      <div class="danhmuc-container">
        <div class="danhmuc">
          <div class="danhmuc_1">
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
              <span class="danhmuc_test">Khoa Công nghệ thông tin</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=cong_nghe_thong_tin"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Lý luận chính trị</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=ly_luan_chinh_tri"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Khoa học Biển và Hải đảo</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=khoa_hoc_bien_hai_dao"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Khoa học Đại cương</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=khoa_hoc_dai_cuong"><i class="fa-solid fa-angle-right"></i></a>
            </div>
            <div class="danhmuc_1_option">
              <span class="danhmuc_test">Khoa Giáo dục thể chất và Giáo dục quốc phòng</span>
              <a class="cach" href="chi_tiet_khoa.php?khoa=giao_duc_the_chat_quoc_phong"><i class="fa-solid fa-angle-right"></i></a>
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
              <span class="danhmuc_test">Bộ môn </span>
              <a class="cach" href=".php?khoa=ngoai_ngu"><i class="fa-solid fa-angle-right"></i></a>
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
          <img src="../img/469877645_1005404278278078_3153280250481528893_n.jpg" alt="">
        </div>
      </div>


    </div>
  </div>
  </div>


  <div class="main-content">
    <div class="job-list">
      <h2>Việc làm tốt nhất</h2>
      <div class="job-container">
        <?php
        // Kết nối CSDL
        require_once '../db.php';

        // Lấy danh sách tin tuyển dụng có trạng thái 'Đã duyệt' và nổi bật
        $sql = "SELECT td.ma_tuyen_dung, td.tieu_de, td.dia_chi, ct.stt_cty, ct.ten_cong_ty, ct.logo
            FROM tuyen_dung td
            JOIN cong_ty ct ON td.stt_cty = ct.stt_cty
            WHERE td.trang_thai = 'Đã duyệt' AND td.noi_bat = 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo '<div class="job">';
            // Hiển thị logo công ty, nếu không có logo thì dùng logo mặc định
            $logo = !empty($row['logo']) ? 'uploads/' . htmlspecialchars($row['logo']) : 'uploads/logo.png';
            echo '<img alt="Logo" src="' . $logo . '" />';
            echo '<div class="job-content">';
            // Liên kết đến trang chi tiết tin tuyển dụng
            echo '<h3><a href="chi_tiet.php?ma_tuyen_dung=' . htmlspecialchars($row['ma_tuyen_dung']) . '">' . htmlspecialchars($row['tieu_de']) . '</a></h3>';
            // Liên kết đến trang chi tiết công ty, sử dụng stt_cty
            echo '<p><a href="giaodien_thongtincty.php?stt_cty=' . htmlspecialchars($row['stt_cty']) . '">' . htmlspecialchars($row['ten_cong_ty']) . '</a></p>';
            echo '<p class="location">' . htmlspecialchars($row['dia_chi']) . '</p>';
            echo '</div>';
            echo '</div>';
          }
        } else {
          echo '<p>Chưa có tin tuyển dụng nổi bật nào được duyệt.</p>';
        }
        ?>
      </div>

    </div>

  </div>
  </div>
  <!-- a4 -->
  <section class="featured-industries">
    <h2>Các Khoa và bộ môn</h2>
    <p>Bạn muốn tìm việc mới? Xem danh sách việc làm <a href="#">tại đây</a></p>
    <div class="industries-grid responsive">
      <div class="industry-card">
        <img src="icon-finance.png" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Kinh Tế</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>

      <div class="industry-card">
        <img src="icon-finance.png" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Môi Trường </a></h3>
          <p>818 việc làm</p>
        </div>
      </div>

      <div class="industry-card">
        <img src="icon-finance.png" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Quản lý đất đai</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="icon-finance.png" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa khí tượng thủy văn</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="icon-finance.png" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Trắc địa bản đồ và Thông tin địa lý</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="icon-finance.png" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Địa chất</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="icon-finance.png" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Tài nguyên nước</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="icon-finance.png" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Công nghệ thông tin</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="icon-finance.png" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Lý luận chính trị</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="icon-finance.png" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Khoa học biển vè Hải đảo</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="icon-finance.png" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Khoa học Đại cương</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>

    </div>
  </section>





  <!-- Slide chạy   -->
  <div class="slider-container">
    <div class="slider">
      <div class="slides">
        <img class="hoo" src="../img/anh.png" alt="Image 1" />
        <img class="hoo" src="../img/anh_mag.jpg" alt="Image 2" />
        <img class="hoo" src="../img/header-bg.webp" alt="Image 3" />
        <img class="hoo" src="../img/469877645_1005404278278078_3153280250481528893_n.jpg" alt="Image 4" />
      </div>
    </div>

    <!-- git  buttons -->
    <button class="prev" onclick="prevSlide()">&#10094;</button>
    <button class="next" onclick="nextSlide()">&#10095;</button>

    <!-- Dots -->
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
          <span>Welcome to our support chat! How can we help you today?</span>
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


  <!-- <canvas id="snowCanvas"> </canvas> -->
  <script>
    $(document).ready(function() {
      $('.industries-grid').slick({
        slidesToShow: 4,
        /* Số item hiển thị */
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        dots: true,
        /* Hiển thị các dấu chấm */
        arrows: true,
        /* Hiển thị nút điều hướng */
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
  </script>

  <script src="../js/giaodienchinh.js"></script>
  <!-- của slide -->
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

  <script>
    var swiper = new Swiper('.swiper-container', {
      slidesPerView: 4,
      /* Hiển thị 4 ảnh một lúc */
      spaceBetween: 20,
      /* Khoảng cách giữa các ảnh */
      loop: true,
      /* Lặp lại slide */
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
    });




    // phan trâng
    document.addEventListener("DOMContentLoaded", function() {
      const items = document.querySelectorAll(".danhmuc_1_option"); // Lấy danh sách mục
      const itemsPerPage = 4; // Số mục trên mỗi trang
      let currentPage = 1;
      const totalPages = Math.ceil(items.length / itemsPerPage);
      const pageInfo = document.querySelector(".danhmuc_1_heder-text");

      function showPage(page) {
        items.forEach((item, index) => {
          if (index >= (page - 1) * itemsPerPage && index < page * itemsPerPage) {
            item.style.display = "block";
          } else {
            item.style.display = "none";
          }
        });

        // Cập nhật số trang hiển thị (1/4, 2/4, 3/4,...)
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
    });


    //chat boxxx
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const chatToggle = document.getElementById('chatToggle');
      const chatBox = document.getElementById('chatBox');
      const minimizeChat = document.getElementById('minimizeChat');
      const messageInput = document.getElementById('messageInput');
      const sendMessage = document.getElementById('sendMessage');
      const chatMessages = document.getElementById('chatMessages');

      // Toggle chat box
      chatToggle.addEventListener('click', function() {
        chatBox.classList.toggle('show');
      });

      // Minimize chat box
      minimizeChat.addEventListener('click', function() {
        chatBox.classList.remove('show');
      });

      // Send message function
      function sendUserMessage() {
        const message = messageInput.value.trim();
        if (message) {
          // Add user message
          const userMessageDiv = document.createElement('div');
          userMessageDiv.className = 'message user';
          userMessageDiv.innerHTML = `<span>${message}</span>`;
          chatMessages.appendChild(userMessageDiv);

          // Clear input
          messageInput.value = '';

          // Auto-scroll to bottom
          chatMessages.scrollTop = chatMessages.scrollHeight;

          // Simulate response after a delay
          setTimeout(function() {
            const responseDiv = document.createElement('div');
            responseDiv.className = 'message system';
            responseDiv.innerHTML = `<span>Thanks for your message! Our team will get back to you soon.</span>`;
            chatMessages.appendChild(responseDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
          }, 1000);
        }
      }

      // Send message on button click
      sendMessage.addEventListener('click', sendUserMessage);

      // Send message on Enter key
      messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          sendUserMessage();
        }
      });
    });
  </script>
</body>

</html>