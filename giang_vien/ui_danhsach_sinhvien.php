<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require '../db.php'; // Kết nối cơ sở dữ liệu

// Mapping array for khoa
$khoa_display_names = [
    'kinh_te' => 'Kinh tế',
    'moi_truong' => 'Môi trường',
    'quan_ly_dat_dai' => 'Quản lý đất đai',
    'khi_tuong_thuy_van' => 'Khí tượng thủy văn',
    'trac_dia_ban_do' => 'Trắc địa bản đồ',
    'dia_chat' => 'Địa chất',
    'tai_nguyen_nuoc' => 'Tài nguyên nước',
    'cntt' => 'Công nghệ thông tin',
    'ly_luan_chinh_tri' => 'Lý luận chính trị',
    'bien_hai_dao' => 'Biển - Hải đảo',
    'khoa_hoc_dai_cuong' => 'Khoa học đại cương',
    'the_chat_quoc_phong' => 'Thể chất quốc phòng',
    'bo_mon_luat' => 'Bộ môn Luật',
    'bien_doi_khi_hau' => 'Biến đổi khí hậu',
    'ngoai_ngu' => 'Ngoại ngữ'
];

// Khởi tạo biến
$selected_lop = isset($_POST['lop']) ? $_POST['lop'] : '';
$students = [];
$classes = [];

// Lấy số hiệu giảng viên từ session
$so_hieu_giang_vien = isset($_SESSION['so_hieu_giang_vien']) ? $_SESSION['so_hieu_giang_vien'] : null;

if (!$so_hieu_giang_vien) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Không tìm thấy thông tin số hiệu của giảng viên. Vui lòng đăng nhập lại.']);
    exit;
}

// Xử lý yêu cầu tìm kiếm qua AJAX
if (isset($_GET['action']) && $_GET['action'] === 'search') {
    header('Content-Type: application/json');
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
    $lop = isset($_GET['lop']) ? $_GET['lop'] : '';

    $sql = "SELECT ma_sinh_vien, ho_ten, email, lop, khoa, so_dien_thoai 
            FROM sinh_vien 
            WHERE so_hieu = ?";
    $params = [$so_hieu_giang_vien];

    if ($lop) {
        $sql .= " AND lop = ?";
        $params[] = $lop;
    }
    if ($keyword) {
        $sql .= " AND (ma_sinh_vien LIKE ? OR ho_ten LIKE ? OR email LIKE ?)";
        $likeKeyword = "%$keyword%";
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
    }
    $sql .= " ORDER BY ho_ten LIMIT 10";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Lỗi prepare: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $row['khoa_display'] = $khoa_display_names[$row['khoa']] ?? $row['khoa'] ?? 'N/A'; // Thêm tên hiển thị
        $students[] = $row;
    }
    $stmt->close();
    echo json_encode(['success' => true, 'data' => ['students' => $students]]);
    exit;
}

// Lấy danh sách lớp của sinh viên thuộc giảng viên (dựa trên so_hieu)
$sql_classes = "SELECT DISTINCT lop 
                FROM sinh_vien 
                WHERE lop IS NOT NULL AND so_hieu = ? 
                ORDER BY lop";
$stmt_classes = $conn->prepare($sql_classes);
if ($stmt_classes === false) {
    die("Lỗi prepare classes: " . $conn->error);
}
$stmt_classes->bind_param("s", $so_hieu_giang_vien);
$stmt_classes->execute();
$result_classes = $stmt_classes->get_result();
while ($row = $result_classes->fetch_assoc()) {
    $classes[] = $row['lop'];
}
$stmt_classes->close();

// Lấy danh sách sinh viên thuộc giảng viên (dựa trên so_hieu)
$sql_students = "SELECT ma_sinh_vien, ho_ten, email, lop, khoa, so_dien_thoai 
                FROM sinh_vien 
                WHERE so_hieu = ?";
$params = [$so_hieu_giang_vien];
if (!empty($selected_lop)) {
    $sql_students .= " AND lop = ?";
    $params[] = $selected_lop;
}
$sql_students .= " ORDER BY ho_ten";

$stmt_students = $conn->prepare($sql_students);
if ($stmt_students === false) {
    die("Lỗi prepare students: " . $conn->error);
}
$stmt_students->bind_param(str_repeat("s", count($params)), ...$params);
$stmt_students->execute();
$result_students = $stmt_students->get_result();
while ($row = $result_students->fetch_assoc()) {
    $students[] = $row;
}
$stmt_students->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản Lý Sinh Viên</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="ui_danhsach_sinhvien.css" />
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

        /* CSS cho tìm kiếm */
        .search-bar {
            position: relative;
            display: flex;
            align-items: center;
        }

        #searchInput {
            padding: 8px;
            width: 300px;
            /* border: 1px solid #ddd; */
            border-radius: 4px;
        }

        #searchLoading {
            margin-left: 10px;
            display: none;
        }

        #searchResults {
            position: absolute;
            top: 40px;
            width: 300px;
            max-height: 300px;
            overflow-y: auto;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            z-index: 1000;
            display: none;
        }

        #searchResults.active {
            display: block;
        }

        #searchResults ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #searchResults li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }

        #searchResults li:hover {
            background-color: #f5f5f5;
        }

        #searchResults li:last-child {
            border-bottom: none;
        }

        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .message.success {
            color: green;
            background: #e0ffe0;
        }

        .message.error {
            color: red;
            background: #ffe0e0;
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
                <li><i class="fa-solid fa-house"></i><a href="./ui_giangvien.php">Trang chủ giảng viên</a></li>
                <li><i class="fa-solid fa-users"></i><a href="./ui_danhsach_sinhvien.php">Danh sách sinh viên</a></li>
                <li><i class="fa-solid fa-user-graduate"></i><a href="./ui_danhsach_thuctap.php">Danh Sách Sinh Viên Đang Thực Tập</a></li>
                <li><i class="fa-solid fa-chart-line"></i><a href="./ui_theo_doi_thuc_tap.php">Theo dõi và đánh giá qtrinh tt của tts</a></li>
                <li><i class="fa-solid fa-file-pdf"></i><a href="./ui_nhan_pdf.php">Chấm Điểm</a></li>
                <li><i class="fa-solid fa-check-circle"></i><a href="./ui_completed_internships.php">Xác nhận hoàn thành thực tập</a></li>
            </ul>
        </div>
    </div>

    <div class="content" id="content">
        <div class="header">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Tìm theo mã SV, họ tên, email..." />
                <span id="searchLoading"><i class="fas fa-spinner fa-spin"></i></span>
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
                <div id="searchResults"></div>
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
                    Danh sách sinh viên
                    <span class="youtube-icon">▶</span>
                </div>
                <!-- <div class="button-group">
                    <button class="btn">Xuất Excel</button>
                </div> -->
            </div>

            <div class="filter-section">
                <div class="filter-title">Hướng dẫn/ Ghi chú: Xem danh sách sinh viên thuộc quyền quản lý.</div>
                <div class="filter-row">
                    <div class="filter-item">
                        <div class="filter-label">Lớp:</div>
                        <form method="POST" id="filterForm">
                            <select class="filter-select" name="lop" onchange="this.form.submit()">
                                <option value="">-- Tất cả lớp --</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo htmlspecialchars($class); ?>" <?php echo $selected_lop === $class ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($class); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="keyword" id="keywordInput" value="">
                        </form>
                    </div>
                </div>
            </div>

            <?php if (!empty($students)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 20px;">STT</th>
                            <th style="width: 150px;">Mã sinh viên</th>
                            <th style="width: 200px;">Họ tên</th>
                            <th style="width: 200px;">Email</th>
                            <th style="width: 100px;">Lớp</th>
                            <th style="width: 100px;">Khoa</th>
                            <th style="width: 120px;">Số điện thoại</th>
                        </tr>
                    </thead>
                    <tbody id="studentList">
                        <?php $stt = 1; ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="center-text"><?php echo $stt++; ?></td>
                                <td><?php echo htmlspecialchars($student['ma_sinh_vien']); ?></td>
                                <td><?php echo htmlspecialchars($student['ho_ten']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['lop']); ?></td>
                                <td><?php echo htmlspecialchars($khoa_display_names[$student['khoa']] ?? $student['khoa'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($student['so_dien_thoai'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Không có sinh viên nào thuộc quyền quản lý của bạn.</p>
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

        // Chức năng tìm kiếm
        let debounceTimer;
        document.getElementById("searchInput").addEventListener("keyup", function() {
            clearTimeout(debounceTimer);
            const keyword = this.value.trim();
            const resultsContainer = document.getElementById("searchResults");
            const loadingSpinner = document.getElementById("searchLoading");
            const lop = document.querySelector('select[name="lop"]').value;

            if (keyword === "") {
                resultsContainer.classList.remove("active");
                updateFilterForm("");
                return;
            }

            loadingSpinner.style.display = 'inline-block';
            debounceTimer = setTimeout(() => {
                const url = `?action=search&lop=${encodeURIComponent(lop)}&keyword=${encodeURIComponent(keyword)}`;

                fetch(url)
                    .then(response => {
                        loadingSpinner.style.display = 'none';
                        if (!response.ok) throw new Error("HTTP status " + response.status);
                        return response.json();
                    })
                    .then(data => {
                        resultsContainer.innerHTML = "";
                        if (data.success && data.data.students.length > 0) {
                            const khoaDisplayNames = <?php echo json_encode($khoa_display_names); ?>;
                            const resultList = document.createElement("ul");
                            data.data.students.slice(0, 10).forEach(student => {
                                const listItem = document.createElement("li");
                                listItem.innerHTML = `
                                    <div>
                                        <strong>${escapeHTML(student.ho_ten)}</strong> (${student.ma_sinh_vien})
                                        <p style="margin: 0; font-size: 12px;">${escapeHTML(student.khoa_display)} - ${escapeHTML(student.email)}</p>
                                    </div>
                                `;
                                listItem.addEventListener("click", () => {
                                    updateFilterForm(student.ho_ten);
                                    resultsContainer.classList.remove("active");
                                });
                                resultList.appendChild(listItem);
                            });
                            resultsContainer.appendChild(resultList);
                            resultsContainer.classList.add("active");
                        } else {
                            resultsContainer.innerHTML = "<p>Không tìm thấy sinh viên phù hợp.</p>";
                            resultsContainer.classList.add("active");
                        }
                    })
                    .catch(error => {
                        loadingSpinner.style.display = 'none';
                        showMessage('error', 'Có lỗi xảy ra khi tìm kiếm.');
                        console.error("Lỗi tìm kiếm:", error);
                    });
            }, 300);
        });

        document.addEventListener("click", function(event) {
            const resultsContainer = document.getElementById("searchResults");
            const searchInput = document.getElementById("searchInput");
            if (!resultsContainer.contains(event.target) && !searchInput.contains(event.target)) {
                resultsContainer.classList.remove("active");
            }
        });

        function escapeHTML(str) {
            return str.replace(/[&<>"']/g, match => ({
                '&': '&',
                '<': '<',
                '>': '>',
                '"': '"',
                "'": "'"

            })[match]);
        }

        function updateFilterForm(keyword) {
            const form = document.getElementById("filterForm");
            document.getElementById("keywordInput").value = keyword;
            form.submit();
        }

        function showMessage(type, message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.textContent = message;
            document.getElementById('content').insertBefore(messageDiv, content.children[1]);
            setTimeout(() => messageDiv.remove(), 3000);
        }
    </script>
</body>

</html>