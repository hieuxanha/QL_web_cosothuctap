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

    <link rel="stylesheet" href="./ui_capnhat.css" />
  </head>
  <body>
    <?php
    session_start();
    require_once '../db.php'; // Kết nối CSDL

    // Hiển thị thông báo nếu có
    if (isset($_SESSION['message'])) {
        echo "<script>alert('" . $_SESSION['message'] . "');</script>";
        unset($_SESSION['message']);
    }
    if (isset($_SESSION['error'])) {
        echo "<script>alert('" . $_SESSION['error'] . "');</script>";
        unset($_SESSION['error']);
    }

    // Lấy danh sách công ty đã được duyệt
    $sql = "SELECT stt_cty, ten_cong_ty FROM cong_ty WHERE trang_thai = 'Đã duyệt'";
    $result = $conn->query($sql);
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
          <li>
            <i class="fa-brands fa-windows"></i
            ><a href="">Cập nhật thông tin</a>
          </li>
          <li>
            <i class="fa-brands fa-windows"></i
            ><a href="">Duyệt đơn đăng ký của sv</a>
          </li>
          <li>
            <i class="fa-brands fa-windows"></i
            ><a href="">Quản lý ds tts tại công ty</a>
          </li>
          <li>
            <i class="fa-brands fa-windows"></i
            ><a href="">Theo dõi và đánh giá qtrinh tt của tts</a>
          </li>
          <li>
            <i class="fa-brands fa-windows"></i
            ><a href=""> Xác nhận ht thực tập cho tts</a>
          </li>
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
        <div class="profile">
          <span>Nguyễn.....</span>
          <img src="profile.jpg" alt="Ảnh đại diện" />
        </div>
      </div>

      <div class="tuyendung">
        <form id="formTuyenDung" action="../logic_cstt/logic_tuyendung.php" method="post">
          <h2>Tin tuyển dụng</h2>

          <label for="stt_cty">Công ty:</label>
          <select name="stt_cty" required>
            <option value="">Chọn công ty</option>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['stt_cty'] . "'>" . $row['ten_cong_ty'] . "</option>";
                }
            } else {
                echo "<option value='' disabled>Không có công ty nào được duyệt</option>";
            }
            ?>
          </select><br>

          <label for="tieu_de">Tiêu đề:</label>
          <input type="text" name="tieu_de_tuyen_dung" required><br>

          <label for="dia_chi">Địa chỉ:</label>
          <input type="text" name="dia_chi" required><br>

          <label for="hinh_thuc">Hình thức làm việc:</label>
          <select name="hinh_thuc" required>
            <option value="fulltime">Full-time</option>
            <option value="parttime">Part-time</option>
          </select><br>

          <label for="gioi_tinh">Giới tính:</label>
          <select name="gioi_tinh" required>
            <option value="nam">Nam</option>
            <option value="nu">Nữ</option>
            <option value="Không giới hạn">Không giới hạn</option>
          </select><br>

          <label for="mo_ta">Mô tả:</label>
          <textarea name="mo_ta"></textarea><br>

          <label for="so_luong">Số lượng tuyển:</label>
          <input type="number" name="so_luong" required><br>

          <label for="yeu_cau">Yêu cầu:</label>
          <textarea name="yeu_cau"></textarea><br>

          <label for="han_nop">Hạn nộp:</label>
          <input type="date" name="han_nop" required><br>

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