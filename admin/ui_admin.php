<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}



// Database connection
require_once '../db.php';

try {
  // Companies: Total approved and by field
  /** @var mysqli_result $stmt */
  $stmt = $conn->query("SELECT COUNT(*) as total FROM cong_ty WHERE trang_thai = 'Đã duyệt'");
  if ($stmt === false) {
    throw new Exception("Query failed for company total: " . $conn->error);
  }
  $company_total = $stmt->fetch_assoc()['total'];

  /** @var mysqli_result $stmt */
  $stmt = $conn->query("SELECT linh_vuc, COUNT(*) as count FROM cong_ty WHERE trang_thai = 'Đã duyệt' GROUP BY linh_vuc");
  if ($stmt === false) {
    throw new Exception("Query failed for company by field: " . $conn->error);
  }
  $company_by_field = $stmt->fetch_all(MYSQLI_ASSOC);
  $company_labels = array_column($company_by_field, 'linh_vuc');
  $company_data = array_column($company_by_field, 'count');
  $company_details = implode(' | ', array_map(fn($row) => "{$row['linh_vuc']}: {$row['count']}", $company_by_field));

  // Students: Total and by faculty
  /** @var mysqli_result $stmt */
  $stmt = $conn->query("SELECT COUNT(*) as total FROM sinh_vien");
  if ($stmt === false) {
    throw new Exception("Query failed for student total: " . $conn->error);
  }
  $student_total = $stmt->fetch_assoc()['total'];

  /** @var mysqli_result $stmt */
  $stmt = $conn->query("SELECT khoa, COUNT(*) as count FROM sinh_vien GROUP BY khoa");
  if ($stmt === false) {
    throw new Exception("Query failed for student by faculty: " . $conn->error);
  }
  $student_by_faculty = $stmt->fetch_all(MYSQLI_ASSOC);
  $student_labels = array_column($student_by_faculty, 'khoa');
  $student_data = array_column($student_by_faculty, 'count');
  $student_details = implode(' | ', array_map(fn($row) => "{$row['khoa']}: {$row['count']}", $student_by_faculty));

  // Applications: Total and by status
  /** @var mysqli_result $stmt */
  $stmt = $conn->query("SELECT COUNT(*) as total FROM ung_tuyen");
  if ($stmt === false) {
    throw new Exception("Query failed for application total: " . $conn->error);
  }
  $application_total = $stmt->fetch_assoc()['total'];

  /** @var mysqli_result $stmt */
  $stmt = $conn->query("SELECT trang_thai, COUNT(*) as count FROM ung_tuyen GROUP BY trang_thai");
  if ($stmt === false) {
    throw new Exception("Query failed for application by status: " . $conn->error);
  }
  $application_by_status = $stmt->fetch_all(MYSQLI_ASSOC);
  $application_labels = array_column($application_by_status, 'trang_thai');
  $application_data = array_column($application_by_status, 'count');
  $application_details = implode(' | ', array_map(fn($row) => "{$row['trang_thai']}: {$row['count']}", $application_by_status));

  // Job Postings: Total approved and by faculty
  /** @var mysqli_result $stmt */
  $stmt = $conn->query("SELECT COUNT(*) as total FROM tuyen_dung WHERE trang_thai = 'Đã duyệt'");
  if ($stmt === false) {
    throw new Exception("Query failed for job total: " . $conn->error);
  }
  $job_total = $stmt->fetch_assoc()['total'];

  /** @var mysqli_result $stmt */
  $stmt = $conn->query("SELECT khoa, COUNT(*) as count FROM tuyen_dung WHERE trang_thai = 'Đã duyệt' GROUP BY khoa");
  if ($stmt === false) {
    throw new Exception("Query failed for job by faculty: " . $conn->error);
  }
  $job_by_faculty = $stmt->fetch_all(MYSQLI_ASSOC);
  $job_labels = array_column($job_by_faculty, 'khoa');
  $job_data = array_column($job_by_faculty, 'count');
  $job_details = implode(' | ', array_map(fn($row) => "{$row['khoa']}: {$row['count']}", $job_by_faculty));
} catch (Exception $e) {
  $error_message = "Lỗi truy vấn cơ sở dữ liệu: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Quản Trị</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="ui_admin.css" />
  <style>
    .dashboard-content {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      padding: 20px;
    }

    .card {
      background: #fff;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .card h3 {
      margin: 0 0 10px;
      font-size: 1.5em;
    }

    .card p {
      margin: 0 0 10px;
      color: #666;
    }

    .card canvas {
      max-height: 200px;
    }

    .error-message {
      color: red;
      padding: 10px;
      background: #ffe6e6;
      border-radius: 4px;
      margin-bottom: 10px;
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
        <h2>Quản lý</h2>
        <li><i class="fa-brands fa-windows"></i><a href="ui_admin.php">Tổng quan</a></li>
        <li><i class="fa-brands fa-windows"></i><a href="ui_tk_nguoidung.php">Quản lý tài khoản người dùng</a></li>
        <li><i class="fa-brands fa-windows"></i><a href="ui_quanly_cty.php">Phê duyệt công ty</a></li>
        <li><i class="fa-brands fa-windows"></i><a href="ui_quanlytt.php">Phê duyệt tuyển dụng</a></li>
        <li><i class="fa-brands fa-windows"></i><a href="ui_timkiem_gv_phutrach.php">Tìm kiếm giáo viên phụ trách</a></li>
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
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
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
      <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
      <?php endif; ?>

      <div class="card">
        <h3>Công ty: <?php echo htmlspecialchars($company_total); ?></h3>
        <p><?php echo htmlspecialchars($company_details ?: 'Không có dữ liệu'); ?></p>
        <canvas id="companyChart"></canvas>
      </div>
      <div class="card">
        <h3>Sinh viên: <?php echo htmlspecialchars($student_total); ?></h3>
        <p><?php echo htmlspecialchars($student_details ?: 'Không có dữ liệu'); ?></p>
        <canvas id="studentChart"></canvas>
      </div>
      <div class="card">
        <h3>Ứng tuyển: <?php echo htmlspecialchars($application_total); ?></h3>
        <p><?php echo htmlspecialchars($application_details ?: 'Không có dữ liệu'); ?></p>
        <canvas id="applicationChart"></canvas>
      </div>
      <div class="card">
        <h3>Tuyển dụng: <?php echo htmlspecialchars($job_total); ?></h3>
        <p><?php echo htmlspecialchars($job_details ?: 'Không có dữ liệu'); ?></p>
        <canvas id="jobChart"></canvas>
      </div>
      <div class="card" style="grid-column: span 2">
        <h3>Bảng tin</h3>
        <p>Thông báo hệ thống cập nhật vào ngày 22/04/2025</p>
      </div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById("sidebar");
      const content = document.getElementById("content");
      sidebar.classList.toggle("collapsed");
      content.classList.toggle("collapsed");
    }

    // Company Chart (Doughnut)
    new Chart(document.getElementById("companyChart"), {
      type: "doughnut",
      data: {
        labels: <?php echo json_encode($company_labels); ?>,
        datasets: [{
          data: <?php echo json_encode($company_data); ?>,
          backgroundColor: ["#3498db", "#e74c3c", "#2ecc71", "#f1c40f", "#9b59b6"]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top'
          }
        }
      }
    });

    // Student Chart (Bar)
    new Chart(document.getElementById("studentChart"), {
      type: "bar",
      data: {
        labels: <?php echo json_encode($student_labels); ?>,
        datasets: [{
          label: "Số sinh viên",
          data: <?php echo json_encode($student_data); ?>,
          backgroundColor: "#f39c12"
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Application Chart (Pie)
    new Chart(document.getElementById("applicationChart"), {
      type: "pie",
      data: {
        labels: <?php echo json_encode($application_labels); ?>,
        datasets: [{
          data: <?php echo json_encode($application_data); ?>,
          backgroundColor: ["#1abc9c", "#9b59b6", "#e67e22"]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top'
          }
        }
      }
    });

    // Job Chart (Pie)
    new Chart(document.getElementById("jobChart"), {
      type: "pie",
      data: {
        labels: <?php echo json_encode($job_labels); ?>,
        datasets: [{
          data: <?php echo json_encode($job_data); ?>,
          backgroundColor: ["#1abc9c", "#9b59b6", "#e67e22", "#3498db"]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top'
          }
        }
      }
    });
  </script>
</body>

</html>