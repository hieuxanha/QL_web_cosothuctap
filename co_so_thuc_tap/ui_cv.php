<?php
session_start();
require_once '../db.php';

// Lấy bộ lọc từ form (nếu có)
$khoa_filter = isset($_GET['khoa']) ? $_GET['khoa'] : 'Tất cả';
$lop_filter = isset($_GET['lop']) ? $_GET['lop'] : 'Tất cả';

// Truy vấn danh sách sinh viên đã ứng tuyển (không lọc theo công ty)
$sql = "SELECT ut.stt_sv, sv.ma_sinh_vien, sv.ho_ten, sv.email, sv.lop, sv.khoa, sv.so_dien_thoai, ut.ngay_ung_tuyen, ut.ma_tuyen_dung, td.tieu_de
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

$sql .= " ORDER BY ut.ngay_ung_tuyen DESC"; // Sắp xếp theo ngày ứng tuyển mới nhất

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
                <li><i class="fa-brands fa-windows"></i><a href="#">Cập nhật thông tin</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="#">Duyệt đơn đăng ký của sv</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="#">Quản lý ds tts tại công ty</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="#">Theo dõi và đánh giá qtrinh tt của tts</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="#">Xác nhận ht thực tập cho tts</a></li>
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
                <div class="filter-title">Hướng dẫn/ Ghi chú: Xem tất cả ứng viên đã ứng tuyển vào các tin tuyển dụng.</div>
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
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sinh_vien_list)): ?>
                        <tr>
                            <td colspan="10" class="center-text">Không có ứng viên nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sinh_vien_list as $index => $sv): ?>
                            <tr>
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
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
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