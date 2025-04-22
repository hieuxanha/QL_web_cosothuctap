<?php

// Khởi tạo session nếu chưa có
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

    <link rel="stylesheet" href="ui_admin.css" />
  <style>
 

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
        <h2>Quản lý</h2>
             <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_admin.php">admin..</a></li>
             <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_tk_nguoidung.php">Quản lý tài khoản người dùng</a></li>
             <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_quanly_cty.php">Phê duyệt công ty</a></li>
             <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_quanlytt.php">Phê duyệt tuyển dụng</a></li>
             <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_timkiem_gv_phutrach.php">Tìm kiếm giáo viên phụ trách</a></a></li>

             <li><i class="fa-brands fa-windows"></i><a href="#">Thông báo</a></li>
             <li><i class="fa-brands fa-windows"></i><a href="#">Bảo trì hệ thống</a></li>
             <li><i class="fa-brands fa-windows"></i><a href="#">Cơ sở</a></li>

        </ul>
      </div>
    </div>

    <div class="content" id="content">
      <div class="header">
        <div class="search-bar">
          <input type="text" placeholder="Tìm kiếm..." />
          <!-- <i class="fas fa-search"></i>  -->
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
                    echo '<a href="../dang_nhap_dang_ki/form_dn.php">Đăng nhập</a>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
      </div>

      <div class="dashboard-content">
        <div class="card">
          <h3>Nhân sự: 47</h3>
          <p>Giáo viên: 43 | Quản lý: 3 | Nhân viên: 1</p>
          <canvas id="staffChart"></canvas>
        </div>
        <div class="card">
          <h3>Học sinh: 557</h3>
          <p>
            Khối 1: 103 | Khối 2: 131 | Khối 3: 103 | Khối 4: 104 | Khối 5: 111
          </p>
          <canvas id="studentsChart"></canvas>
        </div>
        <div class="card">
          <h3>Lớp học: 15</h3>
          <p>Khối 1 - 3 lớp | Khối 2 - 3 lớp | Khối 3 - 3 lớp</p>
          <canvas id="classesChart"></canvas>
        </div>
        <div class="card">
          <h3>Thời khóa biểu</h3>
          <p>Không có dữ liệu</p>
        </div>
        <div class="card" style="grid-column: span 2">
          <h3>Bảng tin</h3>
          <p>Thông báo hệ thống cập nhật vào ngày 18/09/2024</p>
        </div>
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

      new Chart(document.getElementById("staffChart"), {
        type: "doughnut",
        data: {
          labels: ["Giáo viên", "Quản lý", "Nhân viên"],
          datasets: [
            {
              data: [43, 3, 1],
              backgroundColor: ["#3498db", "#e74c3c", "#2ecc71"],
            },
          ],
        },
      });

      // Vẽ biểu đồ Học sinh
      new Chart(document.getElementById("studentsChart"), {
        type: "bar",
        data: {
          labels: ["Khối 1", "Khối 2", "Khối 3", "Khối 4", "Khối 5"],
          datasets: [
            {
              label: "Số học sinh",
              data: [103, 131, 103, 104, 111],
              backgroundColor: "#f39c12",
            },
          ],
        },
        options: {
          responsive: true,
          scales: {
            y: { beginAtZero: true },
          },
        },
      });

      // Vẽ biểu đồ Lớp học
      new Chart(document.getElementById("classesChart"), {
        type: "pie",
        data: {
          labels: ["Khối 1", "Khối 2", "Khối 3"],
          datasets: [
            {
              data: [3, 3, 3],
              backgroundColor: ["#1abc9c", "#9b59b6", "#e67e22"],
            },
          ],
        },
      });
    </script>
  </body>
</html>
