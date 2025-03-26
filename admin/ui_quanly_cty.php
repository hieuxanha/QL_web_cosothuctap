<?php
include '../db.php'; // Kết nối CSDL

$sql = "SELECT * FROM cong_ty"; // Lấy tất cả công ty
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý công ty</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
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
                <li><i class="fa-brands fa-windows"></i><a href="">Quản lý tk người dùng</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="">Kiểm tra và phê duyệt</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="">Quản lý thông báo</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="">Cập nhật, bảo trì hệ thống</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="">Thêm thông tin các cơ sở</a></li>
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
            <div class="profile">
                <span>Nguyễn công hiếu</span>
                <img src="profile.jpg" alt="Ảnh đại diện" />
            </div>
        </div>

        <?php
        if (isset($_SESSION['message'])) {
            echo "<div style='padding: 10px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin: 10px; border-radius: 5px;'>";
            echo $_SESSION['message'];
            echo "</div>";
            unset($_SESSION['message']);
        }
        ?>

        <div class="pending-list">
            <h2>Danh sách công ty</h2>
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
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr data-stt-cty="<?php echo $row['stt_cty']; ?>">
                                <td><?php echo $row['stt_cty']; ?></td>
                                <td><?php echo $row['ten_cong_ty']; ?></td>
                                <td><?php echo $row['dia_chi']; ?></td>
                                <td class="trang-thai"><?php echo $row['trang_thai']; ?></td>
                                <td class="action-buttons">
                                    <?php if ($row['trang_thai'] == 'Chờ duyệt'): ?>
                                        <button class="approve" onclick="updateStatus(<?php echo $row['stt_cty']; ?>, 'approve')">Duyệt</button>
                                        <button class="reject" onclick="updateStatus(<?php echo $row['stt_cty']; ?>, 'reject')">Từ chối</button>
                                    <?php elseif ($row['trang_thai'] == 'Đã duyệt'): ?>
                                        <button class="cancel" onclick="updateStatus(<?php echo $row['stt_cty']; ?>, 'cancel')">Hủy duyệt</button>
                                        <button class="reject" onclick="updateStatus(<?php echo $row['stt_cty']; ?>, 'reject')">Từ chối</button>
                                    <?php elseif ($row['trang_thai'] == 'Bị từ chối'): ?>
                                        <button class="restore" onclick="updateStatus(<?php echo $row['stt_cty']; ?>, 'restore')">Khôi phục</button>
                                        <button class="approve" onclick="updateStatus(<?php echo $row['stt_cty']; ?>, 'approve')">Duyệt</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">Không có công ty nào</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("collapsed");
            document.getElementById("content").classList.toggle("collapsed");
        }

        function updateStatus(stt_cty, action) {
            const row = document.querySelector(`tr[data-stt-cty="${stt_cty}"]`);
            const statusCell = row.querySelector('.trang-thai');
            const buttonsCell = row.querySelector('.action-buttons');

            // Gửi yêu cầu AJAX
            fetch('../logic_admin/logic_duyet_cty.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `stt_cty=${stt_cty}&action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật cột trạng thái
                    statusCell.textContent = data.trang_thai;

                    // Cập nhật các nút dựa trên trạng thái mới
                    if (data.trang_thai === 'Chờ duyệt') {
                        buttonsCell.innerHTML = `
                            <button class="approve" onclick="updateStatus(${stt_cty}, 'approve')">Duyệt</button>
                            <button class="reject" onclick="updateStatus(${stt_cty}, 'reject')">Từ chối</button>
                        `;
                    } else if (data.trang_thai === 'Đã duyệt') {
                        buttonsCell.innerHTML = `
                            <button class="cancel" onclick="updateStatus(${stt_cty}, 'cancel')">Hủy duyệt</button>
                            <button class="reject" onclick="updateStatus(${stt_cty}, 'reject')">Từ chối</button>
                        `;
                    } else if (data.trang_thai === 'Bị từ chối') {
                        buttonsCell.innerHTML = `
                            <button class="restore" onclick="updateStatus(${stt_cty}, 'restore')">Khôi phục</button>
                            <button class="approve" onclick="updateStatus(${stt_cty}, 'approve')">Duyệt</button>
                        `;
                    }
                } else {
                    alert('Lỗi: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                alert('Đã có lỗi xảy ra!');
            });
        }
    </script>
</body>
</html>