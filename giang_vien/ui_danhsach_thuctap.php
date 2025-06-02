<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require '../db.php'; // Kết nối cơ sở dữ liệu

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Ensure the user is a lecturer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'giang_vien' || !isset($_SESSION['so_hieu_giang_vien'])) {
    header("Location: ../dang_nhap_dang_ki/dang_nhap.php");
    exit;
}

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
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$interns = [];
$classes = [];
$so_hieu_giang_vien = $_SESSION['so_hieu_giang_vien'];

// Export to CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="intern_list.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['STT', 'Mã SV', 'Họ tên', 'Email', 'Lớp', 'Khoa', 'Số điện thoại', 'Vị trí thực tập', 'Ngày bắt đầu']);
    foreach ($interns as $index => $intern) {
        fputcsv($output, [
            $index + 1,
            $intern['ma_sinh_vien'],
            $intern['ho_ten'],
            $intern['email'],
            $intern['lop'],
            $khoa_display_names[$intern['khoa']] ?? $intern['khoa'] ?? 'N/A',
            $intern['so_dien_thoai'] ?? 'N/A',
            $intern['tieu_de'] ?? 'N/A',
            $intern['ngay_ung_tuyen']
        ]);
    }
    fclose($output);
    exit;
}

try {
    // Lấy danh sách lớp của sinh viên đang thực tập
    $sql_classes = "SELECT DISTINCT sv.lop 
                    FROM sinh_vien sv
                    JOIN ung_tuyen ut ON sv.stt_sv = ut.stt_sv
                    WHERE sv.lop IS NOT NULL AND sv.so_hieu = ? AND ut.trang_thai = 'Đồng ý'
                    ORDER BY sv.lop";
    $stmt_classes = $conn->prepare($sql_classes);
    if ($stmt_classes === false) {
        throw new Exception("Lỗi prepare classes: " . $conn->error);
    }
    $stmt_classes->bind_param("s", $so_hieu_giang_vien);
    $stmt_classes->execute();
    $result_classes = $stmt_classes->get_result();
    while ($row = $result_classes->fetch_assoc()) {
        $classes[] = $row['lop'];
    }
    $stmt_classes->close();

    // Lấy danh sách sinh viên đang thực tập
    $sql_interns = "SELECT sv.ma_sinh_vien, sv.ho_ten, sv.email, sv.lop, sv.khoa, sv.so_dien_thoai, 
                           td.tieu_de, ut.ngay_ung_tuyen
                    FROM sinh_vien sv
                    JOIN ung_tuyen ut ON sv.stt_sv = ut.stt_sv
                    JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung
                    WHERE sv.so_hieu = ? AND ut.trang_thai = 'Đồng ý'";
    $params = [$so_hieu_giang_vien];
    $conditions = [];

    if (!empty($selected_lop)) {
        $conditions[] = "sv.lop = ?";
        $params[] = $selected_lop;
    }
    if (!empty($keyword)) {
        $conditions[] = "(sv.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR sv.email LIKE ? OR td.tieu_de LIKE ?)";
        $likeKeyword = "%$keyword%";
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
    }

    if ($conditions) {
        $sql_interns .= " AND " . implode(" AND ", $conditions);
    }
    $sql_interns .= " ORDER BY sv.ho_ten";

    $stmt_interns = $conn->prepare($sql_interns);
    if ($stmt_interns === false) {
        throw new Exception("Lỗi prepare interns: " . $conn->error);
    }
    $stmt_interns->bind_param(str_repeat("s", count($params)), ...$params);
    $stmt_interns->execute();
    $result_interns = $stmt_interns->get_result();
    while ($row = $result_interns->fetch_assoc()) {
        $row['khoa_display'] = $khoa_display_names[$row['khoa']] ?? $row['khoa'] ?? 'N/A'; // Thêm tên hiển thị
        $interns[] = $row;
    }
    $stmt_interns->close();
} catch (Exception $e) {
    $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    header("Location: ui_danhsach_thuctap.php");
    exit;
} finally {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Danh Sách Sinh Viên Đang Thực Tập</title>
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

        .center-text {
            text-align: center;
        }

        .message.success {
            color: green;
            padding: 10px;
            margin: 10px 0;
            background: #e0ffe0;
        }

        .message.error {
            color: red;
            padding: 10px;
            margin: 10px 0;
            background: #ffe0e0;
        }

        .search-bar {
            position: relative;
            display: flex;
            align-items: center;
        }

        #searchInput {
            padding: 8px;
            width: 300px;
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
                <input type="text" id="searchInput" placeholder="Tìm theo mã SV, họ tên, email, vị trí..." value="<?php echo htmlspecialchars($keyword); ?>" />
                <span id="searchLoading"><i class="fas fa-spinner fa-spin"></i></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
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
                }
                ?>
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
                    Danh Sách Sinh Viên Đang Thực Tập
                    <span class="youtube-icon">▶</span>
                </div>

            </div>

            <div class="filter-section">
                <div class="filter-title">Hướng dẫn/ Ghi chú: Xem danh sách sinh viên đang thực tập.</div>
                <div class="filter-row">
                    <div class="filter-item">
                        <div class="filter-label">Lớp:</div>
                        <form method="POST" id="filterForm">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            <select class="filter-select" name="lop" onchange="this.form.submit()">
                                <option value="">-- Tất cả lớp --</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo htmlspecialchars($class); ?>" <?php echo $selected_lop === $class ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($class); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="keyword" id="keywordInput" value="<?php echo htmlspecialchars($keyword); ?>">
                        </form>
                    </div>
                </div>
            </div>

            <?php if (!empty($interns)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">STT</th>
                            <th style="width: 150px;">Mã sinh viên</th>
                            <th style="width: 200px;">Họ tên</th>
                            <th style="width: 200px;">Email</th>
                            <th style="width: 100px;">Lớp</th>
                            <th style="width: 100px;">Khoa</th>
                            <th style="width: 120px;">Số điện thoại</th>
                            <th style="width: 200px;">Vị trí thực tập</th>
                            <th style="width: 150px;">Ngày bắt đầu</th>
                        </tr>
                    </thead>
                    <tbody id="internList">
                        <?php $stt = 1; ?>
                        <?php foreach ($interns as $intern): ?>
                            <tr>
                                <td class="center-text"><?php echo $stt++; ?></td>
                                <td><?php echo htmlspecialchars($intern['ma_sinh_vien']); ?></td>
                                <td><?php echo htmlspecialchars($intern['ho_ten']); ?></td>
                                <td><?php echo htmlspecialchars($intern['email']); ?></td>
                                <td><?php echo htmlspecialchars($intern['lop']); ?></td>
                                <td><?php echo htmlspecialchars($intern['khoa_display']); ?></td>
                                <td><?php echo htmlspecialchars($intern['so_dien_thoai'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($intern['tieu_de'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($intern['ngay_ung_tuyen']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Không có sinh viên nào đang thực tập.</p>
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

        let debounceTimer;
        document.getElementById("searchInput").addEventListener("keyup", function() {
            clearTimeout(debounceTimer);
            const keyword = this.value.trim();
            const resultsContainer = document.getElementById("searchResults");
            const loadingSpinner = document.getElementById("searchLoading");

            if (keyword === "") {
                resultsContainer.classList.remove("active");
                updateSearch("");
                return;
            }

            loadingSpinner.style.display = 'inline-block';
            debounceTimer = setTimeout(() => {
                const url = `../logic_giangvien/logic_quanly_baocao_gv.php?action=search&keyword=${encodeURIComponent(keyword)}&csrf_token=<?php echo urlencode($_SESSION['csrf_token']); ?>`;

                fetch(url)
                    .then(response => {
                        loadingSpinner.style.display = 'none';
                        if (!response.ok) throw new Error("HTTP status " + response.status);
                        return response.json();
                    })
                    .then(data => {
                        resultsContainer.innerHTML = "";
                        if (data.success && data.data.applications.length > 0) {
                            const khoaDisplayNames = <?php echo json_encode($khoa_display_names); ?>;
                            const resultList = document.createElement("ul");
                            data.data.applications.slice(0, 10).forEach(app => {
                                const listItem = document.createElement("li");
                                listItem.innerHTML = `
                                    <div>
                                        <strong>${escapeHTML(app.ho_ten)}</strong> (${app.ma_sinh_vien})
                                        <p style="margin: 0; font-size: 12px;">${escapeHTML(khoaDisplayNames[app.khoa] || app.khoa || 'N/A')} - ${escapeHTML(app.tieu_de)}</p>
                                        <small>${escapeHTML(app.email)}</small>
                                    </div>
                                `;
                                listItem.dataset.keyword = app.ma_sinh_vien;
                                listItem.addEventListener("click", () => {
                                    updateSearch(listItem.dataset.keyword);
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
                        showMessage('error', 'Có lỗi xảy ra khi tìm kiếm: ' + error.message);
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

        function updateSearch(keyword) {
            const lop = '<?php echo htmlspecialchars($selected_lop); ?>';
            window.location.href = `?keyword=${encodeURIComponent(keyword)}&lop=${encodeURIComponent(lop)}`;
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