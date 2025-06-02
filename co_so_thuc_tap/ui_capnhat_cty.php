<?php

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}


?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>

  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap"
    rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <link rel="stylesheet" href="./ui_capnhat_cty.css" />
  <style>
    /* CSS cho menu thả xuống */
    .dropdown {
      position: relative;
      display: inline-block;

    }

    .user-name {
      cursor: pointer;
      padding: 8px;
      background-color: #f0f0f0;
      border-radius: 4px;
    }

    .dropdown-content {
      display: none;
      /* Ẩn menu thả xuống mặc định */
      position: absolute;
      background-color: #ffffff;
      min-width: 150px;
      box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
      z-index: 1;
      margin-top: 8px;
      padding: 8px;
      border-radius: 15px;
      top: 18px;

    }

    .dropdown-content a {
      color: black;
      text-decoration: none;
      display: block;
      padding: 8px 12px;

    }

    .dropdown-content a:hover {
      background-color: #ddd;
    }

    /* Hiển thị menu thả xuống khi di chuột vào */
    .dropdown:hover .dropdown-content {
      display: block;
    }
  </style>

</head>

<body>
  <div class="sidebar" id="sidebar">
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    <div class="icons">
      <i class="fa-solid fa-circle-user"></i>
    </div>
    <div class="menu">
      <hr />
      <ul>
        <h3>Quản lý</h3>
        <li><i class="fa-solid fa-building"></i> <a href="ui_cstt.php">Cơ sở thực tập</a></li>
        <li><i class="fa-solid fa-briefcase"></i> <a href="ui_capnhat_cty.php">Đăng ký thông tin công ty</a></li>
        <li><i class="fa-solid fa-bullhorn"></i> <a href="ui_capnhat_tt.php">Cập nhật thông tin tuyển dụng</a></li>
        <li><i class="fa-solid fa-file-alt"></i> <a href="ui_duyet_cv.php">Xét duyệt hồ sơ ứng tuyển</a></li>
        <li><i class="fa-solid fa-file-signature"></i> <a href="ui_quanly_baocao.php">Gửi báo cáo hàng tuần</a></li>
        <li><i class="fa-solid fa-star"></i> <a href="ui_danh_gia_thuc_tap.php">Theo dõi & đánh giá thực tập</a></li>
        <li><i class="fa-solid fa-list-check"></i> <a href="ui_quan_ly_danh_gia.php">Quản lý đánh giá thực tập</a></li>
        <li><i class="fa-solid fa-check-circle"></i> <a href="ui_xac_nhan_hoan_thanh.php">Xác nhận hoàn thành thực tập</a></li>
      </ul>
    </div>
  </div>

  <div class="content" id="content">
    <div class="header">
      <div class="search-bar">
        <input type="text" placeholder="Tìm kiếm..." />
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="20"
          height="20"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </div>
      <div class="account">
        <?php
        if (isset($_SESSION['name'])) {
          echo '<div class="dropdown">';
          echo '<span class="user-name">Xin chào, ' . htmlspecialchars($_SESSION['name']) . '</span>';
          echo '<div class="dropdown-content">';
          echo '<a href="../dang_nhap_dang_ki/logic_dangxuat.php">Đăng xuất</a>';
          echo '</div>';
          echo '</div>';
        } else {
        }
        ?>
      </div>
    </div>

    <div class="pending-list">
      <h2>Thêm Công Ty Mới</h2>
      <form id="companyForm" action="../logic_cstt/logic_cty.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="ten_cong_ty">Tên Công Ty:</label>
          <input type="text" id="ten_cong_ty" name="ten_cong_ty" required>
        </div>

        <div class="form-group">
          <label for="dia_chi">Địa Chỉ:</label>
          <input type="text" id="dia_chi" name="dia_chi" required>
        </div>
        <div class="form-group">
          <label for="quy_mo">Quy Mô:</label>
          <input type="text" id="quy_mo" name="quy_mo" placeholder="Ví dụ: 50-100 nhân viên">
        </div>

        <div class="form-group">
          <label for="linh_vuc">Lĩnh Vực:</label>
          <input type="text" id="linh_vuc" name="linh_vuc" placeholder="Ví dụ: Công nghệ thông tin">
        </div>

        <!--     
    <label for="linh_vuc">Lĩnh vực:</label>
    <input type="text" name="linh_vuc" required><br> -->

        <div class="form-group">
          <label for="so_dien_thoai">Số Điện Thoại:</label>
          <input type="text" id="so_dien_thoai" name="so_dien_thoai" required>
        </div>

        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
          <label for="gioi_thieu">Giới Thiệu:</label>
          <textarea id="gioi_thieu" name="gioi_thieu"></textarea>
        </div>

        <div class="form-group">
          <label for="logo">Logo Công Ty:</label>
          <input type="file" id="logo" name="logo" accept="image/*" onchange="previewImage(event, 'previewLogo')">
          <img id="previewLogo" src="" alt="Xem trước logo" class="preview-image">
        </div>

        <div class="form-group">
          <label for="anh_bia">Ảnh Bìa Công Ty:</label>
          <input type="file" id="anh_bia" name="anh_bia" accept="image/*" onchange="previewImage(event, 'previewAnhBia')">
          <img id="previewAnhBia" src="" alt="Xem trước ảnh bìa" class="preview-image">
        </div>

        <button type="submit" class="nut">Thêm Công Ty</button>
      </form>

    </div>





  </div>

  <script>
    function toggleSidebar() {
      let sidebar = document.getElementById("sidebar");
      sidebar.classList.toggle("collapsed");
    }

    function toggleSidebar() {
      const sidebar = document.getElementById("sidebar");
      const content = document.getElementById("content");
      sidebar.classList.toggle("collapsed");
      content.classList.toggle("collapsed");
    }
  </script>

  <script>
    function previewImage(event, previewId) {
      var reader = new FileReader();
      reader.onload = function() {
        var output = document.getElementById(previewId);
        output.src = reader.result;
        output.style.display = "block";
      };
      reader.readAsDataURL(event.target.files[0]);
    }
  </script>



</body>

</html>