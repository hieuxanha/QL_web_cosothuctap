<?php




?>

<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>

    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    />

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
  display: none; /* Ẩn menu thả xuống mặc định */
  position: absolute;
  background-color: #ffffff;
  min-width: 150px;
  box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
  z-index: 1;
  margin-top: 8px;
  padding: 8px;
  border-radius: 15px;
  top: 20px;
  
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

    <link rel="stylesheet" href="./ui_capnhat.css">
  </head>
  <body>
    <?php
    session_start();
    require_once '../db.php'; // Kết nối CSDL

    // Lấy danh sách công ty đã được duyệt
    $sql = "SELECT stt_cty, ten_cong_ty FROM cong_ty WHERE trang_thai = 'Đã duyệt'";
    $result = $conn->query($sql);

    if (!$result) {
        die("Lỗi truy vấn: " . $conn->error);
    }
    ?>

    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <div class="icons">
            <i class="fa-solid fa-circle-user"></i>
        </div>
        <div class="menu">
        <hr />
        <ul>
         <h2>Quản lý</h2>
      
        
         <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_cstt.php">cstttt..</a></li>
         <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_capnhat_cty.php">Đăng ký thông tin cty</a></li>
         <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_capnhat_tt.php">Cập nhật thông tin tuyển dụng</a></li>
         <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_duyet_cv.php">-	Xét duyệt hồ sơ ứng tuyển</a></li>
         <li><i class="fa-brands fa-windows"></i> <a href="#">Theo dõi & đánh giá quá trình TT</a></li>
         <li><i class="fa-brands fa-windows"></i> <a href="#">Xác nhận hoàn thành TT</a></li>
      
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
                    viewBox="0 0 24 24"
                >
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <div class="account">
                <?php
                if (isset($_SESSION['name']) && !empty($_SESSION['name'])) {
                    echo '<div class="dropdown">';
                    echo '<span class="user-name">Xin chào, ' . htmlspecialchars($_SESSION['name']) . '</span>';
                    echo '<div class="dropdown-content">';
                    echo '<a href="../dang_nhap_dang_ki/logic_dangxuat.php">Đăng xuất</a>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo '<div class="dropdown">';
                    echo '<span class="user-name">Xin chào, Khách</span>';
                    echo '<div class="dropdown-content">';
                    echo '<a href="../dang_nhap_dang_ki/dang_nhap.php">Đăng nhập</a>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

       

        <div class="tuyendung">
        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='message success'>" . htmlspecialchars($_SESSION['message']) . "</div>";
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            echo "<div class='message error'>" . htmlspecialchars($_SESSION['error']) . "</div>";
            unset($_SESSION['error']);
        }
        ?>
            <form id="formTuyenDung" action="../logic_cstt/logic_tuyendung.php" method="post">
                <h2>Tin tuyển dụng</h2>

                <label for="stt_cty">Công ty:</label>
                <select name="stt_cty" required>
                    <option value="">Chọn công ty</option>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row['stt_cty']) . "'>" . htmlspecialchars($row['ten_cong_ty']) . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>Không có công ty nào được duyệt</option>";
                    }
                    ?>
                </select>

                <label for="tieu_de">Tiêu đề:</label>
                <input type="text" name="tieu_de_tuyen_dung" required>

                <label for="dia_chi">Địa chỉ:</label>
                <input type="text" name="dia_chi" required>

                <label for="hinh_thuc">Hình thức làm việc:</label>
                <select name="hinh_thuc" required>
                    <option value="Full-time">Full-time</option>
                    <option value="Part-time">Part-time</option>
                </select>

                <label for="gioi_tinh">Giới tính:</label>
                <select name="gioi_tinh" required>
                    <option value="Nam">Nam</option>
                    <option value="Nữ">Nữ</option>
                    <option value="Không giới hạn">Không giới hạn</option>
                </select>

                <label for="mo_ta">Mô tả:</label>
                <textarea name="mo_ta"></textarea>

                <label for="so_luong">Số lượng tuyển:</label>
                <input type="number" name="so_luong" required>

                <label for="yeu_cau">Yêu cầu:</label>
                <textarea name="yeu_cau"></textarea>
                
                <label for="noi_bat">Nổi bật:</label>
                <input type="checkbox" name="noi_bat" value="1"> <!-- Thêm checkbox cho nổi bật -->


                <label for="han_nop">Hạn nộp:</label>
                <input type="date" name="han_nop" required>

              
                <input type="hidden" name="them_tuyen_dung" value="1">
                <button type="submit" class="submit-btn">Đăng tin</button>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const content = document.getElementById("content");
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("collapsed");
        }
    </script>
</body>
</html>