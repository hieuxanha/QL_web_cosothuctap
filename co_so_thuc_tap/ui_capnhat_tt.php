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
      href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap"
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
          <span>Nguyễn Thị My</span>
          <img src="profile.jpg" alt="Ảnh đại diện" />
        </div>
      </div>

      <div class="tuyendung" >
        <form id="formTuyenDung" action="../logic_cstt/logic_tuyendung.php" method="post" enctype="multipart/form-data">

          <h2>Tin tuyển dụng</h2>
          <label for="tieu_de">Tiêu đề:</label>
        <input type="text" name="tieu_de_tuyen_dung" required><br>

        <label for="linh_vuc">Lĩnh vực:</label>
        <input type="text" name="linh_vuc" required><br>

        <label for="quy_mo">Quy mô:</label>
        <input type="number" name="quy_mo" required><br>

        <label for="khoa">Khoa:</label>
        <input type="text" name="khoa"><br>

        <label for="mo_ta">Mô tả:</label>
        <textarea name="mo_ta"></textarea><br>

        <label for="so_luong">Số lượng tuyển:</label>
        <input type="number" name="so_luong" required><br>

        <label for="yeu_cau">Yêu cầu:</label>
        <textarea name="yeu_cau"></textarea><br>

    
        <label for="han_nop">Hạn nộp:</label>
        <input type="date" name="han_nop" required><br>

        <label for="anh">Hình ảnh:</label>
        <input type="file" name="anh" accept="image/*"><br>

        <button type="submit"  class="submit-btn">Đăng tin</button>
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
  
    
  </body>
</html>
