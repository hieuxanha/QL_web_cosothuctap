<?php
include '../db.php'; // Kết nối CSDL

// Lấy danh sách công ty có trạng thái 'Chờ duyệt'
$sql = "SELECT * FROM cong_ty WHERE trang_thai = 'Chờ duyệt'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chờ Duyệt</title>
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
    <link rel="stylesheet" href="../admin/ui_quanly_cty.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
          <span>Nguyễn công hiếu</span>
          <img src="profile.jpg" alt="Ảnh đại diện" />
        </div>
      </div>

      <div class="pending-list">
        <h2>Danh sách chờ duyệt Cty</h2>
        <table>
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên cơ sở</th>
                    <th>Địa chỉ</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['stt_cty'] . "</td>";
                        echo "<td>" . $row['ten_cong_ty'] . "</td>";
                        echo "<td>" . $row['dia_chi'] . "</td>";
                        echo "<td>" . $row['trang_thai'] . "</td>";
                        echo "<td>
                                <button class='approve' data-id='" . $row['stt_cty'] . "'>Duyệt</button>
                                <button class='reject' data-id='" . $row['stt_cty'] . "'>Từ chối</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Không có công ty nào chờ duyệt</td></tr>";
                }
                ?>
            </tbody>
        </table>
      </div>
    </div>

    <script>
      function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("collapsed");
        document.getElementById("content").classList.toggle("collapsed");
      }
    </script>

<script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".approve, .reject").forEach(button => {
                button.addEventListener("click", function () {
                    let stt_cty = this.getAttribute("data-id");
                    let action = this.classList.contains("approve") ? "approve" : "reject";

                    // Gửi yêu cầu lên server
                    fetch("./logic_admin/logic_duyet_cty.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `stt_cty=${stt_cty}&action=${action}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Cập nhật thành công!");
                            location.reload();
                        } else {
                            alert("Lỗi: " + data.message);
                        }
                    })
                    .catch(error => console.error("Lỗi:", error));
                });
            });
        });
    </script>
  </body>
</html>
