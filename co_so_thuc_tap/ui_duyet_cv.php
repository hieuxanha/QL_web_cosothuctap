<?php
session_start();
require_once '../db.php';

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

// Lấy bộ lọc, từ khóa tìm kiếm và tham số phân trang từ GET
$khoa_filter = isset($_GET['khoa']) ? $_GET['khoa'] : 'Tất cả';
$lop_filter = isset($_GET['lop']) ? $_GET['lop'] : 'Tất cả';
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Truy vấn danh sách sinh viên đã ứng tuyển
$sql = "SELECT ut.stt_sv, sv.ma_sinh_vien, sv.ho_ten, sv.email, sv.lop, sv.khoa, sv.so_dien_thoai, ut.ngay_ung_tuyen, ut.ma_tuyen_dung, td.tieu_de, ut.trang_thai, ut.cv_path
        FROM ung_tuyen ut
        JOIN sinh_vien sv ON ut.stt_sv = sv.stt_sv
        JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung";
$params = [];
$conditions = [];

if ($khoa_filter !== 'Tất cả') {
    $conditions[] = "sv.khoa = ?";
    $params[] = $khoa_filter;
}
if ($lop_filter !== 'Tất cả') {
    $conditions[] = "sv.lop = ?";
    $params[] = $lop_filter;
}
if ($keyword) {
    $conditions[] = "(sv.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR sv.email LIKE ? OR td.tieu_de LIKE ?)";
    $likeKeyword = "%$keyword%";
    $params[] = $likeKeyword;
    $params[] = $likeKeyword;
    $params[] = $likeKeyword;
    $params[] = $likeKeyword;
}

if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY ut.ngay_ung_tuyen DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

$stmt = $conn->prepare($sql);
if ($params) {
    $types = str_repeat('s', count($params) - 2) . 'ii';
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$sinh_vien_list = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Tính tổng số bản ghi cho phân trang
$total_sql = "SELECT COUNT(*) AS total
              FROM ung_tuyen ut
              JOIN sinh_vien sv ON ut.stt_sv = sv.stt_sv
              JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung";
$total_params = [];
$total_conditions = [];

if ($khoa_filter !== 'Tất cả') {
    $total_conditions[] = "sv.khoa = ?";
    $total_params[] = $khoa_filter;
}
if ($lop_filter !== 'Tất cả') {
    $total_conditions[] = "sv.lop = ?";
    $total_params[] = $lop_filter;
}
if ($keyword) {
    $total_conditions[] = "(sv.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR sv.email LIKE ? OR td.tieu_de LIKE ?)";
    $likeKeyword = "%$keyword%";
    $total_params[] = $likeKeyword;
    $total_params[] = $likeKeyword;
    $total_params[] = $likeKeyword;
    $total_params[] = $likeKeyword;
}

if ($total_conditions) {
    $total_sql .= " WHERE " . implode(" AND ", $total_conditions);
}

$total_stmt = $conn->prepare($total_sql);
if ($total_params) {
    $total_stmt->bind_param(str_repeat('s', count($total_params)), ...$total_params);
}
$total_stmt->execute();
$total_records = $total_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);
$total_stmt->close();

// Lấy danh sách khoa và lớp để điền vào bộ lọc
$khoa_list = $conn->query("SELECT DISTINCT khoa FROM sinh_vien WHERE khoa IS NOT NULL ORDER BY khoa")->fetch_all(MYSQLI_ASSOC);
$lop_list = $conn->query("SELECT DISTINCT lop FROM sinh_vien WHERE lop IS NOT NULL ORDER BY lop")->fetch_all(MYSQLI_ASSOC);

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

        .status-pending {
            color: #ff9800;
        }

        .status-approved {
            color: #4caf50;
        }

        .status-rejected {
            color: #f44336;
        }

        .action-btn {
            padding: 5px 10px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .approve-btn {
            background-color: #4caf50;
            color: white;
        }

        .reject-btn {
            background-color: #f44336;
            color: white;
        }

        .cancel-btn {
            background-color: #ff9800;
            color: white;
        }

        .restore-btn {
            background-color: #2196f3;
            color: white;
        }

        .cv-link {
            color: #0078d4;
            text-decoration: none;
            cursor: pointer;
        }

        .cv-link:hover {
            text-decoration: underline;
        }

        .loading {
            color: #666;
            font-style: italic;
            margin-left: 10px;
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

        /* CSS cho tìm kiếm */
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

        /* CSS cho phân trang */
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
                <h3>Quản lý</h3>
                <li><i class="fa-solid fa-building"></i> <a href="ui_cstt.php">Cơ sở thực tập</a></li>
                <li><i class="fa-solid fa-briefcase"></i> <a href="ui_capnhat_cty.php">Đăng ký thông tin công ty</a></li>
                <li><i class="fa-solid fa-bullhorn"></i> <a href="ui_capnhat_tt.php">Cập nhật thông tin tuyển dụng</a></li>
                <li><i class="fa-solid fa-file-alt"></i> <a href="ui_duyet_cv.php">Xét duyệt hồ sơ ứng tuyển</a></li>
                <li><i class="fa-solid fa-chart-line"></i> <a href="Lich_thuctap.php">Gửi lịch</a></li>
                <li><i class="fa-solid fa-file-signature"></i> <a href="ui_quanly_baocao.php">Gửi báo cáo hàng tuần</a></li>
                <li><i class="fa-solid fa-star"></i> <a href="ui_danh_gia_thuc_tap.php">Theo dõi & đánh giá thực tập</a></li>
                <li><i class="fa-solid fa-list-check"></i> <a href="ui_quan_ly_danh_gia.php">Quản lý đánh giá thực tập</a></li>
                <li><i class="fa-solid fa-check-circle"></i> <a href="ui_xac_nhan_hoan_thanh.php">Xác nhận hoàn thành thực tập</a></li>
            </ul>
        </div>
    </div>

    <div class="content" id="content">
        <div class="header">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Tìm theo mã SV, họ tên, email, tin tuyển dụng..." value="<?php echo htmlspecialchars($keyword); ?>" />
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
                    Danh sách tất cả ứng tuyển
                    <span class="youtube-icon">▶</span>
                </div>
            </div>

            <div class="filter-section">
                <div class="filter-title">Hướng dẫn/ Ghi chú: Xem và xét duyệt tất cả ứng viên đã ứng tuyển.</div>
                <form method="GET" class="filter-row" id="filterForm">
                    <div class="filter-item">
                        <div class="filter-label">Khoa:</div>
                        <select name="khoa" class="filter-select" onchange="this.form.submit()">
                            <option value="Tất cả" <?php echo $khoa_filter === 'Tất cả' ? 'selected' : ''; ?>>Tất cả</option>
                            <?php foreach ($khoa_list as $khoa): ?>
                                <option value="<?php echo htmlspecialchars($khoa['khoa']); ?>" <?php echo $khoa_filter === $khoa['khoa'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($khoa_display_names[$khoa['khoa']] ?? $khoa['khoa']); ?>
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
                    <input type="hidden" name="keyword" id="keywordInput" value="<?php echo htmlspecialchars($keyword); ?>" />
                    <input type="hidden" name="page" id="pageInput" value="<?php echo $page; ?>" />
                </form>
            </div>

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
                        <th style="width: 150px;">Ngày ứng tuyển</th>
                        <th style="width: 200px;">Tin tuyển dụng</th>
                        <th style="width: 150px;">Trạng thái</th>
                        <th style="width: 200px;">Hành động</th>
                        <th style="width: 150px;">Xem CV</th>
                    </tr>
                </thead>
                <tbody id="applicationList">
                    <?php if (empty($sinh_vien_list)): ?>
                        <tr>
                            <td colspan="12" class="center-text">Không có ứng viên nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sinh_vien_list as $index => $sv): ?>
                            <?php $trang_thai = trim($sv['trang_thai']) ?: 'Chờ duyệt'; ?>
                            <tr data-stt-sv="<?php echo htmlspecialchars($sv['stt_sv']); ?>" data-ma-tuyen-dung="<?php echo htmlspecialchars($sv['ma_tuyen_dung']); ?>">
                                <td class="center-text"><?php echo $index + 1 + $offset; ?></td>
                                <td><?php echo htmlspecialchars($sv['ma_sinh_vien']); ?></td>
                                <td><?php echo htmlspecialchars($sv['ho_ten']); ?></td>
                                <td><?php echo htmlspecialchars($sv['email']); ?></td>
                                <td><?php echo htmlspecialchars($sv['lop']); ?></td>
                                <td><?php echo htmlspecialchars($khoa_display_names[$sv['khoa']] ?? $sv['khoa'] ?? 'N/A'); ?></td>
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
            cvFrame.src = "../Uploads/" + cvPath;
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
            const statusCell = row.querySelector('td:nth-child(10)');
            const buttonsCell = row.querySelector('.action-buttons');

            buttonsCell.innerHTML += '<span class="loading">Đang xử lý...</span>';

            const data = new URLSearchParams();
            data.append('stt_sv', stt_sv);
            data.append('ma_tuyen_dung', ma_tuyen_dung);
            data.append('action', action);

            fetch('../logic_cstt/logic_duyet_cv.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
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
                        statusCell.textContent = data.trang_thai;
                        statusCell.className = data.trang_thai === 'Chờ duyệt' ? 'status-pending' :
                            data.trang_thai === 'Đồng ý' ? 'status-approved' : 'status-rejected';

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

                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'message success';
                        messageDiv.textContent = data.message || 'Cập nhật trạng thái thành công!';
                        document.getElementById('content').insertBefore(messageDiv, content.children[1]);
                        setTimeout(() => messageDiv.remove(), 3000);
                    } else {
                        showMessage('error', data.error || 'Lỗi khi cập nhật trạng thái!');
                    }
                })
                .catch(error => {
                    buttonsCell.querySelector('.loading')?.remove();
                    showMessage('error', 'Đã có lỗi xảy ra!');
                    console.error('Lỗi:', error);
                });
        }

        function showMessage(type, message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.textContent = message;
            document.getElementById('content').insertBefore(messageDiv, content.children[1]);
            setTimeout(() => messageDiv.remove(), 3000);
        }

        // Chức năng tìm kiếm
        let debounceTimer;
        document.getElementById("searchInput").addEventListener("keyup", function() {
            clearTimeout(debounceTimer);
            const keyword = this.value.trim();
            const resultsContainer = document.getElementById("searchResults");
            const loadingSpinner = document.getElementById("searchLoading");

            if (keyword === "") {
                resultsContainer.classList.remove("active");
                updateFilterForm("");
                return;
            }

            loadingSpinner.style.display = 'inline-block';
            debounceTimer = setTimeout(() => {
                const khoa = document.querySelector('select[name="khoa"]').value;
                const lop = document.querySelector('select[name="lop"]').value;
                const url = `../logic_cstt/logic_duyet_cv.php?action=search&khoa=${encodeURIComponent(khoa)}&lop=${encodeURIComponent(lop)}&keyword=${encodeURIComponent(keyword)}`;

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
                                    updateFilterForm(app.ho_ten);
                                    resultsContainer.classList.remove("active");
                                });
                                resultList.appendChild(listItem);
                            });
                            resultsContainer.appendChild(resultList);
                            resultsContainer.classList.add("active");
                        } else {
                            resultsContainer.innerHTML = "<p>Không tìm thấy ứng viên phù hợp.</p>";
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
            document.getElementById("pageInput").value = 1; // Reset to page 1 on search
            form.submit();
        }

        function changePage(page) {
            const form = document.getElementById("filterForm");
            document.getElementById("pageInput").value = page;
            form.submit();
        }
    </script>
</body>

</html>