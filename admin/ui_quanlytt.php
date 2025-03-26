<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý Tuyển Dụng</title>

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

    <link rel="stylesheet" href="./giaodien_quanlytt.css" />
    <style>
      .pending-list table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
      }

      .pending-list th, 
      .pending-list td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
        word-wrap: break-word;
        white-space: normal;
      }

      /* Phân bổ chiều rộng cột */
      .pending-list th:nth-child(1), .pending-list td:nth-child(1) { width: 5%; }
      .pending-list th:nth-child(2), .pending-list td:nth-child(2) { width: 25%; }
      .pending-list th:nth-child(3), .pending-list td:nth-child(3) { width: 35%; }
      .pending-list th:nth-child(4), .pending-list td:nth-child(4) { width: 15%; }
      .pending-list th:nth-child(5), .pending-list td:nth-child(5) { width: 20%; }
    </style>
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

    // Lấy danh sách tin tuyển dụng có trạng thái "Đang chờ"
    $sql = "SELECT td.ma_tuyen_dung, td.tieu_de, td.trang_thai, ct.ten_cong_ty
            FROM tuyen_dung td
            JOIN cong_ty ct ON td.stt_cty = ct.stt_cty
            WHERE td.trang_thai = 'Đang chờ'";
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
          <li>
            <i class="fa-brands fa-windows"></i
            ><a href="">Quản lý tk người dùng</a>
          </li>
          <li>
            <i class="fa-brands fa-windows"></i
            ><a href="">Kiểm tra và phê duyệt</a>
          </li>
          <li>
            <i class="fa-brands fa-windows"></i
            ><a href=""> Quản lý thông báo</a>
          </li>
          <li>
            <i class="fa-brands fa-windows"></i
            ><a href=""> Cập nhật, bảo trì hệ thống</a>
          </li>
          <li>
            <i class="fa-brands fa-windows"></i
            ><a href="">Thêm thông tin các cơ sở</a>
          </li>
          <li><i class="fa-brands fa-windows"></i><a href=""></a></li>
        </ul>
      </div>
    </div>

    <div class="content" id="content">
      <div class="header">
        <div class="search-bar">
          <input type="text" placeholder="Tìm kiếm..." />
          <i class="fas fa-search"></i>
        </div>
        <div class="profile">
          <span>Nguyễn Thị My</span>
          <img src="profile.jpg" alt="Ảnh đại diện" />
        </div>
      </div>

      <div class="pending-list">
        <h3>Danh sách chờ duyệt tuyển dụng</h3>
        <table>
          <thead>
            <tr>
              <th>STT</th>
              <th>Tên công ty</th>
              <th>Tiêu đề tuyển dụng</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result->num_rows > 0) {
                $stt = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $stt++ . "</td>";
                    echo "<td data-tooltip='" . htmlspecialchars($row['ten_cong_ty']) . "'>" . htmlspecialchars($row['ten_cong_ty']) . "</td>";
                    echo "<td data-tooltip='" . htmlspecialchars($row['tieu_de']) . "'>" . htmlspecialchars($row['tieu_de']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['trang_thai']) . "</td>";
                    echo "<td>";
                    echo "<form action='../logic_admin/logic_duyet_tuyendung.php' method='post' style='display:inline;'>";
                    echo "<input type='hidden' name='ma_tuyen_dung' value='" . htmlspecialchars($row['ma_tuyen_dung']) . "'>";
                    echo "<button type='submit' name='action' value='approve' class='approve'>Duyệt</button>";
                    echo "</form>";
                    echo "<form action='../logic_admin/logic_duyet_tuyendung.php' method='post' style='display:inline;'>";
                    echo "<input type='hidden' name='ma_tuyen_dung' value='" . htmlspecialchars($row['ma_tuyen_dung']) . "'>";
                    echo "<button type='submit' name='action' value='reject' class='reject'>Từ chối</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Không có tin tuyển dụng nào đang chờ duyệt.</td></tr>";
            }
            ?>
          </tbody>
        </table>
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