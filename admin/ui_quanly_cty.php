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
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <div class="profile">
                <span>Nguyễn Công Hiếu</span>
                <img src="profile.jpg" alt="Ảnh đại diện" />
            </div>
        </div>

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
                        <?php $stt = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php $trang_thai = trim($row['trang_thai']) ?: 'Đang chờ'; ?>
                            <tr data-stt-cty="<?php echo htmlspecialchars($row['stt_cty']); ?>">
                                <td><?php echo $stt++; ?></td>
                                <td><?php echo htmlspecialchars($row['ten_cong_ty']); ?></td>
                                <td><?php echo htmlspecialchars($row['dia_chi']); ?></td>
                                <td class="trang-thai"><?php echo htmlspecialchars($trang_thai); ?></td>
                                <td class="action-buttons">
                                    <?php if ($trang_thai == 'Đang chờ'): ?>
                                        <button class="approve" onclick="updateStatus(<?php echo $row['stt_cty']; ?>, 'approve')">Duyệt</button>
                                        <button class="reject" onclick="updateStatus(<?php echo $row['stt_cty']; ?>, 'reject')">Từ chối</button>
                                    <?php elseif ($trang_thai == 'Đã duyệt'): ?>
                                        <button class="cancel" onclick="updateStatus(<?php echo $row['stt_cty']; ?>, 'cancel')">Hủy duyệt</button>
                                        <button class="reject" onclick="updateStatus(<?php echo $row['stt_cty']; ?>, 'reject')">Từ chối</button>
                                    <?php elseif ($trang_thai == 'Bị từ chối'): ?>
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

            // Thêm chỉ báo đang xử lý
            buttonsCell.innerHTML += '<span class="loading">Đang xử lý...</span>';

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
                buttonsCell.querySelector('.loading')?.remove(); // Xóa chỉ báo
                if (data.success) {
                    // Cập nhật cột trạng thái
                    statusCell.textContent = data.trang_thai;

                    // Cập nhật các nút dựa trên trạng thái mới
                    if (data.trang_thai === 'Đang chờ') {
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
                    // Tải lại trang để hiển thị thông báo từ session
                    setTimeout(() => location.reload(), 500);
                } else {
                    alert('Lỗi: ' + data.error);
                }
            })
            .catch(error => {
                buttonsCell.querySelector('.loading')?.remove();
                console.error('Lỗi:', error);
                alert('Đã có lỗi xảy ra!');
            });
        }
    </script>
</body>
</html>