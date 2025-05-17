<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Kết nối cơ sở dữ liệu
require_once '../db.php';

// Lấy số hiệu giảng viên từ session
$so_hieu_giang_vien = isset($_SESSION['so_hieu_giang_vien']) ? $_SESSION['so_hieu_giang_vien'] : null;
if (!$so_hieu_giang_vien) {
    header("Location: ../dang_nhap_dang_ki/dang_nhap.php");
    exit();
}

// Khởi tạo biến
$selected_position = isset($_POST['position']) ? $_POST['position'] : '';
$internships = [];
$positions = [];

// Lấy danh sách vị trí thực tập đã hoàn thành
$sql_positions = "SELECT DISTINCT td.tieu_de 
                 FROM tuyen_dung td
                 JOIN ung_tuyen ut ON td.ma_tuyen_dung = ut.ma_tuyen_dung
                 JOIN sinh_vien sv ON ut.stt_sv = sv.stt_sv
                 WHERE sv.so_hieu = ? AND ut.trang_thai = 'Hoàn thành'
                 ORDER BY td.tieu_de";
$stmt_positions = $conn->prepare($sql_positions);
if ($stmt_positions === false) {
    die("Lỗi prepare positions: " . $conn->error);
}
$stmt_positions->bind_param("s", $so_hieu_giang_vien);
$stmt_positions->execute();
$result_positions = $stmt_positions->get_result();
while ($row = $result_positions->fetch_assoc()) {
    $positions[] = $row['tieu_de'];
}
$stmt_positions->close();

// Lấy danh sách thực tập đã hoàn thành
$sql_internships = "SELECT td.tieu_de, ut.ngay_ung_tuyen, sv.ho_ten, sv.email, sv.lop, sv.khoa, sv.ma_sinh_vien
                    FROM sinh_vien sv
                    JOIN ung_tuyen ut ON sv.stt_sv = ut.stt_sv
                    JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung
                    WHERE sv.so_hieu = ? AND ut.trang_thai = 'Hoàn thành'";
$params = [$so_hieu_giang_vien];
if (!empty($selected_position)) {
    $sql_internships .= " AND td.tieu_de = ?";
    $params[] = $selected_position;
}
$sql_internships .= " ORDER BY sv.ho_ten";

$stmt_internships = $conn->prepare($sql_internships);
if ($stmt_internships === false) {
    die("Lỗi prepare internships: " . $conn->error);
}
$stmt_internships->bind_param(str_repeat("s", count($params)), ...$params);
$stmt_internships->execute();
$result_internships = $stmt_internships->get_result();
while ($row = $result_internships->fetch_assoc()) {
    $internships[] = $row;
}
$stmt_internships->close();

// Đóng kết nối sau khi tất cả truy vấn hoàn tất
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Danh Sách Thực Tập Đã Hoàn Thành</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="./ui_danhsach_sinhvien.css" />
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

        .center-text {
            text-align: center;
        }

        .account .dropdown {
            position: relative;
            display: inline-block;
        }

        .account .user-name {
            cursor: pointer;
            font-weight: 500;
            color: #333;
        }

        .account .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: #fff;
            min-width: 120px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            z-index: 1;
        }

        .account .dropdown:hover .dropdown-content {
            display: block;
        }

        .account .dropdown-content a {
            color: #333;
            padding: 10px;
            text-decoration: none;
            display: block;
            font-size: 14px;
        }

        .account .dropdown-content a:hover {
            background: #f4f4f4;
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

                <li><i class="fa-brands fa-windows"></i><a href="./ui_giangvien.php">Trang chủ giảng viên</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="./ui_danhsach_sinhvien.php">Danh sách sinh viên</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="./ui_danhsach_thuctap.php">Danh Sách Sinh Viên Đang Thực Tập</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="./ui_theo_doi_thuc_tap.php">Theo dõi và đánh giá qtrinh tt của tts</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="./ui_completed_internships.php">Xác nhận hoàn thành thực tập</a></li>
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
                    viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <div class="account">
                <?php
                if (isset($_SESSION['name'])) {
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
                    echo '<a href="../dang_nhap_dang_ki/dang_nhap.php">Đăng nhập</a>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <div class="container">
            <div class="subnav">
                <div class="subnav-title">
                    <img src="/api/placeholder/24/24" alt="Icon" />
                    Danh Sách Thực Tập Đã Hoàn Thành
                    <span class="youtube-icon">▶</span>
                </div>
                <div class="button-group">
                    <button class="btn">Xuất Excel</button>
                </div>
            </div>

            <div class="filter-section">
                <div class="filter-title">Hướng dẫn/ Ghi chú: Xem danh sách sinh viên đã hoàn thành thực tập.</div>
                <div class="filter-row">
                    <div class="filter-item">
                        <div class="filter-label">Vị trí thực tập:</div>
                        <form method="POST" id="filterForm">
                            <select class="filter-select" name="position" onchange="this.form.submit()">
                                <option value="">-- Tất cả vị trí --</option>
                                <?php foreach ($positions as $position): ?>
                                    <option value="<?php echo htmlspecialchars($position); ?>" <?php echo $selected_position === $position ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($position); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <?php if (!empty($internships)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">STT</th>
                            <th style="width: 150px;">Mã sinh viên</th>
                            <th style="width: 200px;">Họ tên</th>
                            <th style="width: 200px;">Email</th>
                            <th style="width: 100px;">Lớp</th>
                            <th style="width: 100px;">Khoa</th>
                            <th style="width: 200px;">Vị trí thực tập</th>
                            <th style="width: 150px;">Ngày bắt đầu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $stt = 1; ?>
                        <?php foreach ($internships as $internship): ?>
                            <tr>
                                <td class="center-text"><?php echo $stt++; ?></td>
                                <td><?php echo htmlspecialchars($internship['ma_sinh_vien']); ?></td>
                                <td><?php echo htmlspecialchars($internship['ho_ten']); ?></td>
                                <td><?php echo htmlspecialchars($internship['email']); ?></td>
                                <td><?php echo htmlspecialchars($internship['lop'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($internship['khoa'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($internship['tieu_de']); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($internship['ngay_ung_tuyen']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Không có sinh viên nào đã hoàn thành thực tập.</p>
            <?php endif; ?>
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