<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ql_csthcsth</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../sinh_vien/giaodien_chinh.css?v=1.0">
  <link rel="stylesheet" href="../sinh_vien/footer.css">


  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.css" />
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick-theme.css" />
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.min.js"></script>
  <style>
    /* .industry-card {
      background-color: #fff;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      padding: 10px;
      margin: 0 10px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      min-height: 180px;
      height: auto;
    }


    .industry-card img {
      margin-bottom: 40px;
   
      margin: 0 auto;
      width: 50px;
    } */

    .text-group h3 a:hover {
      text-decoration: none;
    }

    /* .search-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #fff;
      border-radius: 20px;
      margin-bottom: 50px;
      padding: 15px 20px;
      width: 100%;
      margin: 25px auto 25px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      gap: 10px;
      position: relative;
    } */

    /* .search-bar input {
      width: 90%;
      padding: 10px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
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
      background-color: #28a745;
    } */

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
    }

    #clearSearch {
      cursor: pointer;
      margin-right: 10px;
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
      <a href="#">Việc làm</a>
      <a href="#">Hồ sơ & CV</a>
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

  <div class="search-section">
    <div class="search-section1">
      <h1>Tìm cơ sở thực tập cho sinh viên Trường Đại Học Tài nguyên và Môi trường Hà Nội</h1>
      <p>Tiếp cận 40,000+ tin tuyển dụng việc làm mỗi ngày từ hàng nghìn doanh nghiệp uy tín tại Việt Nam</p>
    </div>
    <div class="aa">
      <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Tìm theo tên công ty, vị trí tuyển dụng, địa điểm..." />
        <span id="clearSearch" style="cursor: pointer; display: none; margin-right: 10px;">
          <i class="fas fa-times"></i>
        </span>
        <button onclick="triggerSearch()">Tìm kiếm</button>
        <span id="searchLoading" style="display: none; margin-left: 10px;">
          <i class="fas fa-spinner fa-spin"></i>
        </span>
        <div id="searchResults"></div>
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
              <span class="danhmuc_test">Bộ môn</span>
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

  <div class="main-content">
    <div class="job-list">
      <h2>Việc làm tốt nhất</h2>
      <div class="job-container">
        <?php
        require_once '../db.php';
        $sql = "SELECT td.ma_tuyen_dung, td.tieu_de, td.dia_chi, ct.stt_cty, ct.ten_cong_ty, ct.logo
                        FROM tuyen_dung td
                        JOIN cong_ty ct ON td.stt_cty = ct.stt_cty
                        WHERE td.trang_thai = 'Đã duyệt' AND td.noi_bat = 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo '<div class="job">';
            $logo = !empty($row['logo']) ? 'uploads/' . htmlspecialchars($row['logo']) : 'uploads/logo.png';
            echo '<img alt="Logo" src="' . $logo . '" />';
            echo '<div class="job-content">';
            echo '<h3><a href="chi_tiet.php?ma_tuyen_dung=' . htmlspecialchars($row['ma_tuyen_dung']) . '">' . htmlspecialchars($row['tieu_de']) . '</a></h3>';
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

  <section class="featured-industries">
    <h2>Các Khoa và bộ môn</h2>
    <p>Bạn muốn tìm việc mới? Xem danh sách việc làm <a href="#">tại đây</a></p>
    <div class="industries-grid responsive">
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Kinh Tế</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Môi Trường </a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Quản lý đất đai</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa khí tượng thủy văn</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Trắc địa bản đồ và Thông tin địa lý</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Địa chất</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Tài nguyên nước</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Công nghệ thông tin</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Lý luận chính trị</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Khoa học biển vè Hải đảo</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
      <div class="industry-card">
        <img src="https://www.topcv.vn/v4/image/welcome/top-categories/cong-nghe-thong-tin.png?v=2" alt="Tài chính - Ngân hàng">
        <div class="text-group">
          <h3><a href="">Khoa Khoa học Đại cương</a></h3>
          <p>818 việc làm</p>
        </div>
      </div>
    </div>
  </section>

  <div class="slider-container">
    <div class="slider">
      <div class="slides">
        <img class="hoo" src="../img/anh.png" alt="Image 1" />
        <img class="hoo" src="../img/anh_mag.jpg" alt="Image 2" />
        <img class="hoo" src="../img/header-bg.webp" alt="Image 3" />
        <img class="hoo" src="../img/469877645_1005404278278078_3153280250481528893_n.jpg" alt="Image 4" />
      </div>
    </div>
    <button class="prev" onclick="prevSlide()">❮</button>
    <button class="next" onclick="nextSlide()">❯</button>
    <div class="dots" id="dots-container"></div>
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
      const searchInput = document.getElementById("searchInput");
      const searchResults = document.getElementById("searchResults");
      const searchLoading = document.getElementById("searchLoading");
      const clearSearch = document.getElementById("clearSearch");
      let debounceTimer;

      // Trigger search manually (button click)
      window.triggerSearch = function() {
        const keyword = searchInput.value.trim();
        performSearch(keyword);
      };

      // Real-time search on input
      searchInput.addEventListener("keyup", function() {
        clearTimeout(debounceTimer);
        const keyword = this.value.trim();
        clearSearch.style.display = keyword ? "inline-block" : "none";

        if (keyword.length < 2) {
          searchResults.classList.remove("active");
          searchResults.innerHTML = "";
          return;
        }

        searchLoading.style.display = "inline-block";
        debounceTimer = setTimeout(() => {
          fetch(`../logic_sinhvien/search_jobs.php?keyword=${encodeURIComponent(keyword)}`)
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
              let errorMessage = "Đã xảy ra lỗi khi tìm kiếm.";
              if (error.message.includes("404")) errorMessage = "Không tìm thấy file search_jobs.php.";
              else if (error.message.includes("500")) errorMessage = "Lỗi server trong search_jobs.php.";
              else if (error.message.includes("Unexpected token")) errorMessage = "Server trả về dữ liệu không hợp lệ.";
              searchResults.innerHTML = `<p>${errorMessage}</p>`;
              searchResults.classList.add("active");
              console.error("Search error:", error);
            });
        }, 300);
      });

      // Clear search input
      clearSearch.addEventListener("click", function() {
        searchInput.value = "";
        clearSearch.style.display = "none";
        searchResults.classList.remove("active");
        searchResults.innerHTML = "";
      });

      // Render suggestions dropdown with clickable links
      function renderSuggestions(jobs) {
        searchResults.innerHTML = "";
        const ul = document.createElement("ul");
        jobs.forEach(job => {
          const li = document.createElement("li");
          li.innerHTML = `
                <img src="${escapeHTML(job.logo)}" alt="Logo" style="width: 30px; height: 30px; margin-right: 10px; vertical-align: middle;" />
                <strong><a href="chi_tiet.php?ma_tuyen_dung=${encodeURIComponent(job.ma_tuyen_dung)}">${escapeHTML(job.tieu_de)}</a></strong><br>
                <small><a href="giaodien_thongtincty.php?stt_cty=${encodeURIComponent(job.stt_cty)}">${escapeHTML(job.ten_cong_ty)}</a> - ${escapeHTML(job.dia_chi)}</small>
            `;
          ul.appendChild(li);
        });
        searchResults.appendChild(ul);
        searchResults.classList.add("active");
      }

      // Perform search (displays results in searchResults)
      function performSearch(keyword) {
        searchLoading.style.display = "inline-block";
        fetch(`../logic_sinhvien/search_jobs.php?keyword=${encodeURIComponent(keyword)}`)
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
            let errorMessage = "Đã xảy ra lỗi khi tìm kiếm.";
            if (error.message.includes("404")) errorMessage = "Không tìm thấy file search_jobs.php.";
            else if (error.message.includes("500")) errorMessage = "Lỗi server trong search_jobs.php.";
            else if (error.message.includes("Unexpected token")) errorMessage = "Server trả về dữ liệu không hợp lệ.";
            searchResults.innerHTML = `<p>${errorMessage}</p>`;
            searchResults.classList.add("active");
            console.error("Search error:", error);
          });
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
        if (!searchResults.contains(event.target) && !searchInput.contains(event.target)) {
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
          if (index >= (page - 1) * itemsPerPage && index < page * itemsPerPage) {
            item.style.display = "block";
          } else {
            item.style.display = "none";
          }
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
    });
  </script>
</body>