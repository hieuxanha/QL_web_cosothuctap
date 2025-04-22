<?php
session_start();
require_once '../db.php';

// Lấy bộ lọc từ form (nếu có)
$khoa_filter = isset($_GET['khoa']) ? $_GET['khoa'] : 'Tất cả';
$lop_filter = isset($_GET['lop']) ? $_GET['lop'] : 'Tất cả';

// Truy vấn danh sách sinh viên đã ứng tuyển
$sql = "SELECT ut.stt_sv, sv.ma_sinh_vien, sv.ho_ten, sv.email, sv.lop, sv.khoa, sv.so_dien_thoai, ut.ngay_ung_tuyen, ut.ma_tuyen_dung, td.tieu_de, ut.trang_thai, ut.cv_path
        FROM ung_tuyen ut
        JOIN sinh_vien sv ON ut.stt_sv = sv.stt_sv
        JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung";
$params = [];

if ($khoa_filter !== 'Tất cả') {
    $sql .= " WHERE sv.khoa = ?";
    $params[] = $khoa_filter;
}
if ($lop_filter !== 'Tất cả') {
    $sql .= ($params ? " AND" : " WHERE") . " sv.lop = ?";
    $params[] = $lop_filter;
}

$sql .= " ORDER BY ut.ngay_ung_tuyen DESC";

if ($params) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

$sinh_vien_list = $result->fetch_all(MYSQLI_ASSOC);
if ($params) $stmt->close();

// Lấy danh sách khoa và lớp để điền vào bộ lọc
$khoa_list = $conn->query("SELECT DISTINCT khoa FROM sinh_vien WHERE khoa IS NOT NULL")->fetch_all(MYSQLI_ASSOC);
$lop_list = $conn->query("SELECT DISTINCT lop FROM sinh_vien WHERE lop IS NOT NULL")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Danh Sách Tất Cả Ứng Tuyển</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../co_so_thuc_tap/ui_cv.css" />
    <style>
        .container {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 99%;
            margin: 0 auto;
            padding: 20px 0;
        }
        .subnav {
            padding: 10px 8px;
            display: flex;
            align-items: center;
            background-color: white;
            border-bottom: 1px solid #ddd;
        }
        .subnav-title {
            display: flex;
            align-items: center;
            color: #0078d4;
            font-weight: bold;
            margin-right: 20px;
        }
        .subnav-title img {
            width: 24px;
            height: 24px;
            margin-right: 5px;
        }
        .youtube-icon {
            background-color: red;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 12px;
            margin-left: 10px;
        }
        .button-group {
            margin-left: auto;
            display: flex;
        }
        .btn {
            background-color: #0078d4;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            margin-left: 5px;
            cursor: pointer;
        }
        .filter-section {
            padding: 15px 8px;
            background-color: white;
        }
        .filter-title {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .filter-row {
            display: flex;
            margin-bottom: 10px;
        }
        .filter-item {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }
        .filter-label {
            font-weight: bold;
            margin-right: 10px;
        }
        .filter-select {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-width: 150px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        .data-table th {
            background-color: #0078d4;
            color: white;
            text-align: left;
            padding: 10px;
            font-weight: normal;
        }
        .data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        .data-table tr:hover {
            background-color: #f0f0f0;
        }
        .action-icon {
            margin: 0 5px;
            cursor: pointer;
        }
        .center-text {
            text-align: center;
        }
        .status-pending { color: #ff9800; }
        .status-approved { color: #4caf50; }
        .status-rejected { color: #f44336; }
        .action-btn {
            padding: 5px 10px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .approve-btn { background-color: #4caf50; color: white; }
        .reject-btn { background-color: #f44336; color: white; }
        .cancel-btn { background-color: #ff9800; color: white; }
        .restore-btn { background-color: #2196f3; color: white; }
        .cv-link { color: #0078d4; text-decoration: none; cursor: pointer; }
        .cv-link:hover { text-decoration: underline; }
        .loading { color: #666; font-style: italic; margin-left: 10px; }
        .message.success { color: green; padding: 10px; margin: 10px 0; background: #e0ffe0; }
        .message.error { color: red; padding: 10px; margin: 10px 0; background: #ffe0e0; }

        /* CSS cho modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 900px;
            height: 80vh;
            position: relative;
        }
        .modal-content iframe {
            width: 100%;
            height: 90%;
            border: none;
        }
        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
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
                <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_cstt.php">cstttt..</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_capnhat_cty.php">Đăng ký thông tin cty</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_capnhat_tt.php">Cập nhật thông tin tuyển dụng</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_duyet_cv.php">Xét duyệt hồ sơ ứng tuyển</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_quanly_baocao.php">Gửi báo cáo hàng tuần </a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_danh_gia_thuc_tap.php">Theo dõi & đánh giá quá trình TT</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_xac_nhan_hoan_thanh.php">Xác nhận hoàn thành TT</a></li>
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
                <span><?php echo htmlspecialchars($_SESSION['name'] ?? 'Tên người dùng'); ?></span>
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

        <div class="container">
            <div class="subnav">
                <div class="subnav-title">
                    <img src="/api/placeholder/24/24" alt="Icon" />
                    Danh sách tất cả ứng tuyển
                    <span class="youtube-icon">▶</span>
                </div>
                <div class="button-group">
                    <button class="btn">Xuất Excel</button>
                    <button class="btn">Cấu hình cột hiển thị</button>
                </div>
            </div>

            <div class="filter-section">
                <div class="filter-title">Hướng dẫn/ Ghi chú: Xem và xét duyệt tất cả ứng viên đã ứng tuyển.</div>
                <form method="GET" class="filter-row">
                    <div class="filter-item">
                        <div class="filter-label">Khoa:</div>
                        <select name="khoa" class="filter-select" onchange="this.form.submit()">
                            <option value="Tất cả" <?php echo $khoa_filter === 'Tất cả' ? 'selected' : ''; ?>>Tất cả</option>
                            <?php foreach ($khoa_list as $khoa): ?>
                                <option value="<?php echo htmlspecialchars($khoa['khoa']); ?>" <?php echo $khoa_filter === $khoa['khoa'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($khoa['khoa']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-item">
                        <div class="filter-label">Lớp:</div>
                        <select name="lop" class="filter-select" onchange="this.form.submit()">
                            <option value="Tất cả" <?php echo $lop_filter === 'Tất cả' ? 'selected' : ''; ?>>Tất cả</option>
                            <?php foreach ($lop_list as $lop): ?>
                                <option value="<?php echo htmlspecialchars($lop['lop']); ?>" <?php echo $lop_filter === $lop['lop'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($lop['lop']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">STT</th>
                        <th style="width: 60px;">Sửa</th>
                        <th style="width: 150px;">Mã sinh viên</th>
                        <th style="width: 200px;">Họ tên</th>
                        <th style="width: 200px;">Email</th>
                        <th style="width: 100px;">Lớp</th>
                        <th style="width: 100px;">Khoa</th>
                        <th style="width: 120px;">Số điện thoại</th>
                        <th style="width: 150px;">Ngày ứng tuyển</th>
                        <th style="width: 200px;">Tin tuyển dụng</th>
                        <th style="width: 150px;">Trạng thái</th>
                        <th style="width: 200px;">Hành động</th>
                        <th style="width: 150px;">Xem CV</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($sinh_vien_list)): ?>
                    <tr>
                        <td colspan="13" class="center-text">Không có ứng viên nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sinh_vien_list as $index => $sv): ?>
                        <?php $trang_thai = trim($sv['trang_thai']) ?: 'Chờ duyệt'; // Loại bỏ khoảng trắng ?>
                        <tr data-stt-sv="<?php echo htmlspecialchars($sv['stt_sv']); ?>" data-ma-tuyen-dung="<?php echo htmlspecialchars($sv['ma_tuyen_dung']); ?>">
                            <td class="center-text"><?php echo $index + 1; ?></td>
                            <td class="center-text">
                                <a href="edit_sv.php?stt_sv=<?php echo htmlspecialchars($sv['stt_sv']); ?>" class="action-icon">✏️</a>
                            </td>
                            <td><?php echo htmlspecialchars($sv['ma_sinh_vien']); ?></td>
                            <td><?php echo htmlspecialchars($sv['ho_ten']); ?></td>
                            <td><?php echo htmlspecialchars($sv['email']); ?></td>
                            <td><?php echo htmlspecialchars($sv['lop']); ?></td>
                            <td><?php echo htmlspecialchars($sv['khoa']); ?></td>
                            <td><?php echo htmlspecialchars($sv['so_dien_thoai']); ?></td>
                            <td><?php echo htmlspecialchars($sv['ngay_ung_tuyen']); ?></td>
                            <td><?php echo htmlspecialchars($sv['tieu_de']); ?></td>
                            <td class="<?php echo $trang_thai === 'Chờ duyệt' ? 'status-pending' : ($trang_thai === 'Đồng ý' ? 'status-approved' : 'status-rejected'); ?>">
                                <?php echo htmlspecialchars($trang_thai); ?>
                            </td>
                            <td class="action-buttons">
                                <?php if ($trang_thai === 'Chờ duyệt'): ?>
                                    <button class="action-btn approve-btn" onclick="updateStatus('<?php echo htmlspecialchars($sv['stt_sv']); ?>', '<?php echo htmlspecialchars($sv['ma_tuyen_dung']); ?>', 'approve')">Duyệt</button>
                                    <button class="action-btn reject-btn" onclick="updateStatus('<?php echo htmlspecialchars($sv['stt_sv']); ?>', '<?php echo htmlspecialchars($sv['ma_tuyen_dung']); ?>', 'reject')">Từ chối</button>
                                <?php elseif ($trang_thai === 'Đồng ý'): ?>
                                    <button class="action-btn cancel-btn" onclick="updateStatus('<?php echo htmlspecialchars($sv['stt_sv']); ?>', '<?php echo htmlspecialchars($sv['ma_tuyen_dung']); ?>', 'cancel')">Hủy duyệt</button>
                                    <button class="action-btn reject-btn" onclick="updateStatus('<?php echo htmlspecialchars($sv['stt_sv']); ?>', '<?php echo htmlspecialchars($sv['ma_tuyen_dung']); ?>', 'reject')">Từ chối</button>
                                <?php elseif ($trang_thai === 'Không đồng ý'): ?>
                                    <button class="action-btn restore-btn" onclick="updateStatus('<?php echo htmlspecialchars($sv['stt_sv']); ?>', '<?php echo htmlspecialchars($sv['ma_tuyen_dung']); ?>', 'restore')">Khôi phục</button>
                                    <button class="action-btn approve-btn" onclick="updateStatus('<?php echo htmlspecialchars($sv['stt_sv']); ?>', '<?php echo htmlspecialchars($sv['ma_tuyen_dung']); ?>', 'approve')">Duyệt</button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($sv['cv_path'])): ?>
                                    <span class="cv-link" onclick="showCV('<?php echo htmlspecialchars($sv['cv_path']); ?>')">Xem CV</span>
                                <?php else: ?>
                                    Không có CV
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal để hiển thị CV -->
    <div id="cvModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCV()">×</span>
            <iframe id="cvFrame" src=""></iframe>
        </div>
    </div>

    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        const content = document.getElementById("content");
        sidebar.classList.toggle("collapsed");
        content.classList.toggle("collapsed");
    }

    function showCV(cvPath) {
        const modal = document.getElementById("cvModal");
        const cvFrame = document.getElementById("cvFrame");
        cvFrame.src = "../uploads/" + cvPath;
        modal.style.display = "block";
    }

    function closeCV() {
        const modal = document.getElementById("cvModal");
        const cvFrame = document.getElementById("cvFrame");
        cvFrame.src = "";
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        const modal = document.getElementById("cvModal");
        if (event.target == modal) {
            closeCV();
        }
    }

    function updateStatus(stt_sv, ma_tuyen_dung, action) {
        const row = document.querySelector(`tr[data-stt-sv="${stt_sv}"][data-ma-tuyen-dung="${ma_tuyen_dung}"]`);
        const statusCell = row.querySelector('td:nth-child(11)'); // Cột trạng thái
        const buttonsCell = row.querySelector('.action-buttons');

        // Thêm chỉ báo đang xử lý
        buttonsCell.innerHTML += '<span class="loading">Đang xử lý...</span>';

        // Chuẩn bị dữ liệu gửi đi
        const data = new URLSearchParams();
        data.append('stt_sv', stt_sv);
        data.append('ma_tuyen_dung', ma_tuyen_dung);
        data.append('action', action);

        // Gửi yêu cầu AJAX
        fetch('../logic_cstt/logic_duyet_cv.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: data.toString()
        })
        .then(response => {
            if (!response.ok) throw new Error('Phản hồi mạng không ổn định');
            return response.json();
        })
        .then(data => {
            buttonsCell.querySelector('.loading')?.remove();
            if (data.success) {
                // Cập nhật cột trạng thái
                statusCell.textContent = data.trang_thai;
                statusCell.className = data.trang_thai === 'Chờ duyệt' ? 'status-pending' :
                                      data.trang_thai === 'Đồng ý' ? 'status-approved' : 'status-rejected';

                // Cập nhật các nút dựa trên trạng thái mới
                if (data.trang_thai === 'Chờ duyệt') {
                    buttonsCell.innerHTML = `
                        <button class="action-btn approve-btn" onclick="updateStatus('${stt_sv}', '${ma_tuyen_dung}', 'approve')">Duyệt</button>
                        <button class="action-btn reject-btn" onclick="updateStatus('${stt_sv}', '${ma_tuyen_dung}', 'reject')">Từ chối</button>
                    `;
                } else if (data.trang_thai === 'Đồng ý') {
                    buttonsCell.innerHTML = `
                        <button class="action-btn cancel-btn" onclick="updateStatus('${stt_sv}', '${ma_tuyen_dung}', 'cancel')">Hủy duyệt</button>
                        <button class="action-btn reject-btn" onclick="updateStatus('${stt_sv}', '${ma_tuyen_dung}', 'reject')">Từ chối</button>
                    `;
                } else if (data.trang_thai === 'Không đồng ý') {
                    buttonsCell.innerHTML = `
                        <button class="action-btn restore-btn" onclick="updateStatus('${stt_sv}', '${ma_tuyen_dung}', 'restore')">Khôi phục</button>
                        <button class="action-btn approve-btn" onclick="updateStatus('${stt_sv}', '${ma_tuyen_dung}', 'approve')">Duyệt</button>
                    `;
                }

                // Hiển thị thông báo mà không reload
                const content = document.getElementById('content');
                const existingMessage = content.querySelector('.message');
                if (existingMessage) existingMessage.remove();
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message success';
                messageDiv.textContent = data.message || 'Cập nhật trạng thái thành công!';
                content.insertBefore(messageDiv, content.children[1]);
                setTimeout(() => messageDiv.remove(), 3000); // Tự động ẩn sau 3 giây
            } else {
                alert('Lỗi: ' + data.error);
            }
        })
        .catch(error => {
            buttonsCell.querySelector('.loading')?.remove();
            console.error('Lỗi:', error);
            alert('Đã có lỗi xảy ra khi gửi yêu cầu: ' + error.message);
        });
    }
    </script>
</body>
</html>