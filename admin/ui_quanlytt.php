<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý Tuyển Dụng</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="./giaodien_quanlytt.css" />

    <style>
        .pending-list table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .pending-list th, .pending-list td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            word-wrap: break-word;
            white-space: normal;
        }
        .pending-list th:nth-child(1), .pending-list td:nth-child(1) { width: 3%; }
        .pending-list th:nth-child(2), .pending-list td:nth-child(2) { width: 20%; }
        .pending-list th:nth-child(3), .pending-list td:nth-child(3) { width: 30%; }
        .pending-list th:nth-child(4), .pending-list td:nth-child(4) { width: 15%; }
        .pending-list th:nth-child(5), .pending-list td:nth-child(5) { width: 5%; }
        .pending-list th:nth-child(6), .pending-list td:nth-child(6) { width: 20%; }
        .approve { background-color: #28a745; color: white; padding: 5px 10px; border: none; cursor: pointer; }
        .reject { background-color: #dc3545; color: white; padding: 5px 10px; border: none; cursor: pointer; }
    </style>
</head>

<body>
    <?php
    session_start();
    require_once '../db.php'; // Kết nối CSDL

    // Lấy danh sách tất cả tin tuyển dụng
    $sql = "SELECT td.ma_tuyen_dung, td.tieu_de, td.trang_thai, td.noi_bat, ct.ten_cong_ty
            FROM tuyen_dung td
            JOIN cong_ty ct ON td.stt_cty = ct.stt_cty";
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
                <i class="fas fa-search"></i>
            </div>
            <div class="profile">
                <span>Nguyễn Thị My</span>
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
            <h3>Danh sách tin tuyển dụng</h3>
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên công ty</th>
                        <th>Tiêu đề tuyển dụng</th>
                        <th>Trạng thái</th>
                        <th>Nổi bật</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $stt = 1;
                        while ($row = $result->fetch_assoc()) {
                            $trang_thai = trim($row['trang_thai']) ?: 'Đang chờ';
                            $noi_bat = $row['noi_bat'] ? 'Có' : 'Không';
                            echo "<tr data-ma-tuyen-dung='" . htmlspecialchars($row['ma_tuyen_dung']) . "'>";
                            echo "<td>" . $stt++ . "</td>";
                            echo "<td data-tooltip='" . htmlspecialchars($row['ten_cong_ty']) . "'>" . htmlspecialchars($row['ten_cong_ty']) . "</td>";
                            echo "<td data-tooltip='" . htmlspecialchars($row['tieu_de']) . "'>" . htmlspecialchars($row['tieu_de']) . "</td>";
                            echo "<td class='trang-thai'>" . htmlspecialchars($trang_thai) . "</td>";
                            echo "<td>" . htmlspecialchars($noi_bat) . "</td>";
                            echo "<td class='action-buttons'>";
                            if ($trang_thai == 'Đang chờ') {
                                echo "<button class='approve' onclick=\"updateStatus('" . htmlspecialchars($row['ma_tuyen_dung']) . "', 'approve')\">Duyệt</button>";
                                echo "<button class='reject' onclick=\"updateStatus('" . htmlspecialchars($row['ma_tuyen_dung']) . "', 'reject')\">Từ chối</button>";
                            } elseif ($trang_thai == 'Đã duyệt') {
                                echo "<button class='cancel' onclick=\"updateStatus('" . htmlspecialchars($row['ma_tuyen_dung']) . "', 'cancel')\">Hủy duyệt</button>";
                                echo "<button class='reject' onclick=\"updateStatus('" . htmlspecialchars($row['ma_tuyen_dung']) . "', 'reject')\">Từ chối</button>";
                            } elseif ($trang_thai == 'Bị từ chối') {
                                echo "<button class='restore' onclick=\"updateStatus('" . htmlspecialchars($row['ma_tuyen_dung']) . "', 'restore')\">Khôi phục</button>";
                                echo "<button class='approve' onclick=\"updateStatus('" . htmlspecialchars($row['ma_tuyen_dung']) . "', 'approve')\">Duyệt</button>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>Không có tin tuyển dụng nào.</td></tr>";
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

        function updateStatus(ma_tuyen_dung, action) {
            const row = document.querySelector(`tr[data-ma-tuyen-dung="${ma_tuyen_dung}"]`);
            const statusCell = row.querySelector('.trang-thai');
            const buttonsCell = row.querySelector('.action-buttons');

            buttonsCell.innerHTML += '<span class="loading">Đang xử lý...</span>';

            fetch('../logic_admin/logic_duyet_tuyendung.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `ma_tuyen_dung=${encodeURIComponent(ma_tuyen_dung)}&action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                buttonsCell.querySelector('.loading')?.remove();
                if (data.success) {
                    statusCell.textContent = data.trang_thai;

                    if (data.trang_thai === 'Đang chờ') {
                        buttonsCell.innerHTML = `
                            <button class="approve" onclick="updateStatus('${ma_tuyen_dung}', 'approve')">Duyệt</button>
                            <button class="reject" onclick="updateStatus('${ma_tuyen_dung}', 'reject')">Từ chối</button>
                        `;
                    } else if (data.trang_thai === 'Đã duyệt') {
                        buttonsCell.innerHTML = `
                            <button class="cancel" onclick="updateStatus('${ma_tuyen_dung}', 'cancel')">Hủy duyệt</button>
                            <button class="reject" onclick="updateStatus('${ma_tuyen_dung}', 'reject')">Từ chối</button>
                        `;
                    } else if (data.trang_thai === 'Bị từ chối') {
                        buttonsCell.innerHTML = `
                            <button class="restore" onclick="updateStatus('${ma_tuyen_dung}', 'restore')">Khôi phục</button>
                            <button class="approve" onclick="updateStatus('${ma_tuyen_dung}', 'approve')">Duyệt</button>
                        `;
                    }

                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message success';
                    messageDiv.textContent = data.message;
                    document.querySelector('.content').prepend(messageDiv);
                    setTimeout(() => messageDiv.remove(), 3000);
                } else {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'message error';
                    errorDiv.textContent = data.error;
                    document.querySelector('.content').prepend(errorDiv);
                    setTimeout(() => errorDiv.remove(), 3000);
                }
            })
            .catch(error => {
                buttonsCell.querySelector('.loading')?.remove();
                console.error('Lỗi:', error);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'message error';
                errorDiv.textContent = 'Đã có lỗi xảy ra!';
                document.querySelector('.content').prepend(errorDiv);
                setTimeout(() => errorDiv.remove(), 3000);
            });
        }
    </script>
</body>
</html>