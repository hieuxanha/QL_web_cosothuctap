<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../db.php';

// Ensure the user is a lecturer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'giang_vien' || !isset($_SESSION['so_hieu_giang_vien'])) {
    header('Location: ../dang_nhap_dang_ki/dang_nhap.php');
    exit;
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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

// Pagination and search parameters
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Query to fetch approved applications and reports
$sql = "
    SELECT 
        sv.stt_sv, 
        sv.ma_sinh_vien, 
        sv.ho_ten, 
        sv.email, 
        sv.lop, 
        sv.khoa, 
        sv.so_dien_thoai, 
        ut.ma_tuyen_dung, 
        ut.ngay_ung_tuyen, 
        ut.trang_thai, 
        ut.cv_path,
        td.tieu_de,
        GROUP_CONCAT(bct.noi_dung SEPARATOR '|||') AS noi_dung_list,
        GROUP_CONCAT(bct.file_path SEPARATOR '|||') AS file_path_list,
        GROUP_CONCAT(bct.ngay_gui SEPARATOR '|||') AS ngay_gui_list
    FROM sinh_vien sv
    LEFT JOIN ung_tuyen ut ON sv.stt_sv = ut.stt_sv
    LEFT JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung
    LEFT JOIN bao_cao_thuc_tap bct ON ut.id = bct.ma_dang_ky
    WHERE ut.id IS NOT NULL 
        AND ut.trang_thai = 'Đồng ý'
        AND sv.so_hieu = ?";
$params = [$_SESSION['so_hieu_giang_vien']];
$conditions = [];

if ($keyword) {
    $enum_keyword = array_search($keyword, $khoa_display_names) ?: $keyword;
    $conditions[] = "(sv.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR sv.email LIKE ? OR sv.khoa LIKE ? OR sv.khoa = ? OR td.tieu_de LIKE ?)";
    $likeKeyword = "%$keyword%";
    $params[] = $likeKeyword;
    $params[] = $likeKeyword;
    $params[] = $likeKeyword;
    $params[] = $likeKeyword;
    $params[] = $enum_keyword;
    $params[] = $likeKeyword;
}

if ($conditions) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " GROUP BY sv.stt_sv, ut.ma_tuyen_dung ORDER BY ut.ngay_ung_tuyen DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    $_SESSION['error'] = "Lỗi prepare: " . $conn->error;
    header("Location: ui_theo_doi_thuc_tap.php");
    exit;
}
$types = str_repeat('s', count($params) - 2) . 'ii';
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$sinh_vien_list = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['khoa_display'] = $khoa_display_names[$row['khoa']] ?? $row['khoa'] ?? 'N/A';
        $row['noi_dung_list'] = !empty($row['noi_dung_list']) ? explode('|||', $row['noi_dung_list']) : [];
        $row['file_path_list'] = !empty($row['file_path_list']) ? explode('|||', $row['file_path_list']) : [];
        $row['ngay_gui_list'] = !empty($row['ngay_gui_list']) ? explode('|||', $row['ngay_gui_list']) : [];
        $sinh_vien_list[] = $row;
    }
}
$stmt->close();

// Calculate total records for pagination
$total_sql = "
    SELECT COUNT(DISTINCT sv.stt_sv) AS total 
    FROM sinh_vien sv 
    JOIN ung_tuyen ut ON sv.stt_sv = ut.stt_sv 
    LEFT JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung 
    WHERE ut.trang_thai = 'Đồng ý' 
        AND sv.so_hieu = ?";
$total_params = [$_SESSION['so_hieu_giang_vien']];

if ($keyword) {
    $enum_keyword = array_search($keyword, $khoa_display_names) ?: $keyword;
    $total_sql .= " AND (sv.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR sv.email LIKE ? OR sv.khoa LIKE ? OR sv.khoa = ? OR td.tieu_de LIKE ?)";
    $likeKeyword = "%$keyword%";
    $total_params[] = $likeKeyword;
    $total_params[] = $likeKeyword;
    $total_params[] = $likeKeyword;
    $total_params[] = $likeKeyword;
    $total_params[] = $enum_keyword;
    $total_params[] = $likeKeyword;
}

$total_stmt = $conn->prepare($total_sql);
if ($total_stmt === false) {
    $_SESSION['error'] = "Lỗi prepare total: " . $conn->error;
    header("Location: ui_theo_doi_thuc_tap.php");
    exit;
}
$total_stmt->bind_param(str_repeat('s', count($total_params)), ...$total_params);
$total_stmt->execute();
$total_records = $total_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);
$total_stmt->close();

// Export to CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="internship_reports.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['STT', 'Mã SV', 'Họ tên', 'Email', 'Lớp', 'Khoa', 'Tin tuyển dụng', 'Trạng thái thực tập']);
    foreach ($sinh_vien_list as $index => $sv) {
        fputcsv($output, [
            $index + 1 + $offset,
            $sv['ma_sinh_vien'],
            $sv['ho_ten'],
            $sv['email'],
            $sv['lop'] ?? 'N/A',
            $sv['khoa_display'],
            $sv['tieu_de'] ?? 'N/A',
            $sv['trang_thai'] === 'Đồng ý' ? 'Đang thực tập' : $sv['trang_thai']
        ]);
    }
    fclose($output);
    exit;
}

// Handle AJAX search
if (isset($_GET['action']) && $_GET['action'] === 'search') {
    header('Content-Type: application/json');
    $keyword = trim($_GET['keyword']);
    $sql_search = "
        SELECT 
            sv.stt_sv, 
            sv.ma_sinh_vien, 
            sv.ho_ten, 
            sv.email, 
            sv.lop, 
            sv.khoa, 
            td.tieu_de
        FROM sinh_vien sv
        LEFT JOIN ung_tuyen ut ON sv.stt_sv = ut.stt_sv
        LEFT JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung
        WHERE ut.id IS NOT NULL 
            AND ut.trang_thai = 'Đồng ý'
            AND sv.so_hieu = ?";
    $search_params = [$_SESSION['so_hieu_giang_vien']];

    if ($keyword) {
        $enum_keyword = array_search($keyword, $khoa_display_names) ?: $keyword;
        $sql_search .= " AND (sv.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR sv.email LIKE ? OR sv.khoa LIKE ? OR sv.khoa = ? OR td.tieu_de LIKE ?)";
        $likeKeyword = "%$keyword%";
        $search_params[] = $likeKeyword;
        $search_params[] = $likeKeyword;
        $search_params[] = $likeKeyword;
        $search_params[] = $likeKeyword;
        $search_params[] = $enum_keyword;
        $search_params[] = $likeKeyword;
    }

    $sql_search .= " GROUP BY sv.stt_sv, ut.ma_tuyen_dung LIMIT 10";

    $stmt_search = $conn->prepare($sql_search);
    if ($stmt_search === false) {
        echo json_encode(['success' => false, 'error' => 'Lỗi prepare search: ' . $conn->error]);
        exit;
    }
    $stmt_search->bind_param(str_repeat('s', count($search_params)), ...$search_params);
    $stmt_search->execute();
    $result_search = $stmt_search->get_result();
    $applications = [];
    while ($row = $result_search->fetch_assoc()) {
        $row['khoa_display'] = $khoa_display_names[$row['khoa']] ?? $row['khoa'] ?? 'N/A';
        $applications[] = $row;
    }
    $stmt_search->close();
    echo json_encode(['success' => true, 'data' => ['applications' => $applications]]);
    $conn->close();
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản Lý Báo Cáo Thực Tập - Giảng Viên</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../giang_vien/ui_theo_doi_thuc_tap.css" />
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

        .data-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            table-layout: fixed;
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

        .cv-link {
            color: #0078d4;
            text-decoration: none;
            cursor: pointer;
        }

        .cv-link:hover {
            text-decoration: underline;
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

        .modal-content iframe,
        .modal-content img {
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

        .data-table td ul {
            margin: 0;
            padding-left: 20px;
            list-style-type: disc;
        }

        .data-table td ul li {
            margin-bottom: 5px;
        }

        .data-table td ul li small {
            color: #555;
            font-size: 0.9em;
        }

        .accordion-btn {
            background-color: #0078d4;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .accordion-content {
            margin-top: 5px;
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

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .pagination button {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background-color: #fff;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .pagination button:hover {
            background-color: #0078d4;
            color: white;
        }

        .pagination button:disabled {
            background-color: #f0f0f0;
            cursor: not-allowed;
            color: #888;
        }

        .pagination span {
            padding: 8px 12px;
            background-color: #0078d4;
            color: white;
            border-radius: 4px;
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

        .status-going {
            color: #2196f3;
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
                <input type="text" id="searchInput" placeholder="Tìm theo mã SV, họ tên, khoa, tin tuyển dụng..." value="<?php echo htmlspecialchars($keyword); ?>" />
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
                    Quản Lý Báo Cáo Thực Tập
                    <span class="youtube-icon">▶</span>
                </div>
                <div class="button-group">
                    <a href="?export=csv" class="btn">Xuất Excel</a>
                </div>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">STT</th>
                        <th style="width: 120px;">Mã sinh viên</th>
                        <th style="width: 170px;">Họ tên</th>
                        <th style="width: 200px;">Email</th>
                        <th style="width: 100px;">Lớp</th>
                        <th style="width: 100px;">Khoa</th>
                        <th style="width: 200px;">Tin tuyển dụng</th>
                        <th style="width: 250px;">Nội dung</th>
                        <th style="width: 100px;">File đính kèm</th>
                        <th style="width: 150px;">Trạng thái thực tập</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sinh_vien_list)): ?>
                        <tr>
                            <td colspan="10" class="center-text">Không có báo cáo nào từ sinh viên bạn phụ trách.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sinh_vien_list as $index => $sv): ?>
                            <tr data-stt-sv="<?php echo htmlspecialchars($sv['stt_sv']); ?>" data-ma-tuyen-dung="<?php echo htmlspecialchars($sv['ma_tuyen_dung']); ?>">
                                <td class="center-text"><?php echo $index + 1 + $offset; ?></td>
                                <td><?php echo htmlspecialchars($sv['ma_sinh_vien']); ?></td>
                                <td><?php echo htmlspecialchars($sv['ho_ten']); ?></td>
                                <td><?php echo htmlspecialchars($sv['email']); ?></td>
                                <td><?php echo htmlspecialchars($sv['lop'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($sv['khoa_display']); ?></td>
                                <td><?php echo htmlspecialchars($sv['tieu_de'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php if (!empty($sv['noi_dung_list'])): ?>
                                        <button class="accordion-btn" onclick="toggleAccordion(this)">Xem</button>
                                        <div class="accordion-content" style="display: none;">
                                            <ul>
                                                <?php foreach ($sv['noi_dung_list'] as $key => $noi_dung): ?>
                                                    <li>
                                                        <?php echo htmlspecialchars($noi_dung); ?>
                                                        <br>
                                                        <small>(Gửi: <?php echo htmlspecialchars($sv['ngay_gui_list'][$key] ?? 'Không rõ'); ?>)</small>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php else: ?>
                                        Chưa gửi báo cáo
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($sv['file_path_list'])): ?>
                                        <button class="accordion-btn" onclick="toggleAccordion(this)">Xem</button>
                                        <div class="accordion-content" style="display: none;">
                                            <ul>
                                                <?php foreach ($sv['file_path_list'] as $key => $file_path): ?>
                                                    <li>
                                                        <span class="cv-link" onclick="showFile('<?php echo htmlspecialchars($file_path); ?>')">Xem file</span>
                                                        <br>
                                                        <small>(Gửi: <?php echo htmlspecialchars($sv['ngay_gui_list'][$key] ?? 'Không rõ'); ?>)</small>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php else: ?>
                                        Không có file
                                    <?php endif; ?>
                                </td>
                                <td class="status-going">Đang trong quá trình thực tập</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination">
                <button onclick="changePage(<?php echo $page - 1; ?>)" <?php echo $page <= 1 ? 'disabled' : ''; ?>>Trước</button>
                <?php
                $max_pages_to_show = 5;
                $start_page = max(1, $page - floor($max_pages_to_show / 2));
                $end_page = min($total_pages, $start_page + $max_pages_to_show - 1);

                if ($end_page - $start_page + 1 < $max_pages_to_show) {
                    $start_page = max(1, $end_page - $max_pages_to_show + 1);
                }

                for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span><?php echo $i; ?></span>
                    <?php else: ?>
                        <button onclick="changePage(<?php echo $i; ?>)"><?php echo $i; ?></button>
                    <?php endif; ?>
                <?php endfor; ?>
                <button onclick="changePage(<?php echo $page + 1; ?>)" <?php echo $page >= $total_pages ? 'disabled' : ''; ?>>Sau</button>
            </div>
        </div>
    </div>

    <div id="fileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeFile()">×</span>
            <iframe id="fileFrame" src=""></iframe>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const content = document.getElementById("content");
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("collapsed");
        }

        function toggleAccordion(element) {
            const content = element.nextElementSibling;
            if (content.style.display === "none") {
                content.style.display = "block";
                element.textContent = "Ẩn";
            } else {
                content.style.display = "none";
                element.textContent = "Xem";
            }
        }

        function showFile(filePath) {
            const modal = document.getElementById("fileModal");
            const fileFrame = document.getElementById("fileFrame");
            const extension = filePath.split('.').pop().toLowerCase();

            if (['pdf'].includes(extension)) {
                fileFrame.src = filePath;
            } else if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                fileFrame.src = '';
                fileFrame.outerHTML = `<img src="${filePath}" style="width: 100%; height: 90%; object-fit: contain;" />`;
            } else {
                fileFrame.src = '';
                fileFrame.outerHTML = `<a href="${filePath}" download>Tải xuống file</a>`;
            }

            modal.style.display = "block";
        }

        function closeFile() {
            const modal = document.getElementById("fileModal");
            const fileFrameContainer = document.querySelector('.modal-content');

            fileFrameContainer.innerHTML = `
                <span class="close" onclick="closeFile()">×</span>
                <iframe id="fileFrame" src=""></iframe>
            `;
            modal.style.display = "none";
        }

        let debounceTimer;
        document.getElementById("searchInput").addEventListener("keyup", function() {
            clearTimeout(debounceTimer);
            const keyword = this.value.trim();
            const resultsContainer = document.getElementById("searchResults");
            const loadingSpinner = document.getElementById("searchLoading");

            if (keyword === "") {
                resultsContainer.classList.remove("active");
                updateSearch(keyword);
                return;
            }

            loadingSpinner.style.display = 'inline-block';
            debounceTimer = setTimeout(() => {
                const url = `?action=search&keyword=${encodeURIComponent(keyword)}&csrf_token=<?php echo urlencode($_SESSION['csrf_token']); ?>`;

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
                                    </div>
                                `;
                                listItem.addEventListener("click", () => {
                                    updateSearch(app.ho_ten);
                                    resultsContainer.classList.remove("active");
                                });
                                resultList.appendChild(listItem);
                            });
                            resultsContainer.appendChild(resultList);
                            resultsContainer.classList.add("active");
                        } else {
                            resultsContainer.innerHTML = "<p>Không tìm thấy báo cáo phù hợp.</p>";
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

        function updateSearch(keyword) {
            window.location.href = `?page=1&keyword=${encodeURIComponent(keyword)}`;
        }

        function changePage(page) {
            const keyword = '<?php echo htmlspecialchars($keyword); ?>';
            window.location.href = `?page=${page}&keyword=${encodeURIComponent(keyword)}`;
        }

        function showMessage(type, message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.textContent = message;
            document.getElementById('content').insertBefore(messageDiv, document.getElementById('content').children[1]);
            setTimeout(() => messageDiv.remove(), 3000);
        }
    </script>
</body>

</html>