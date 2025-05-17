<?php
session_start();
require_once '../db.php';

// Lấy tham số phân trang và từ khóa tìm kiếm
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Truy vấn danh sách sinh viên đã ứng tuyển
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
    WHERE ut.id IS NOT NULL AND ut.trang_thai = 'Đồng ý'";
$params = [];
$conditions = [];

if ($keyword) {
    $conditions[] = "(sv.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR sv.email LIKE ? OR td.tieu_de LIKE ?)";
    $likeKeyword = "%$keyword%";
    $params[] = $likeKeyword;
    $params[] = $likeKeyword;
    $params[] = $likeKeyword;
    $params[] = $likeKeyword;
}

if ($conditions) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " GROUP BY sv.stt_sv, ut.ma_tuyen_dung ORDER BY ut.ngay_ung_tuyen DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

$stmt = $conn->prepare($sql);
if ($params) {
    $types = str_repeat('s', count($params) - 2) . 'ii';
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$sinh_vien_list = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['noi_dung_list'] = !empty($row['noi_dung_list']) ? explode('|||', $row['noi_dung_list']) : [];
        $row['file_path_list'] = !empty($row['file_path_list']) ? explode('|||', $row['file_path_list']) : [];
        $row['ngay_gui_list'] = !empty($row['ngay_gui_list']) ? explode('|||', $row['ngay_gui_list']) : [];
        $sinh_vien_list[] = $row;
    }
}
$stmt->close();

// Tính tổng số bản ghi cho phân trang
$total_sql = "SELECT COUNT(DISTINCT sv.stt_sv) AS total 
              FROM sinh_vien sv 
              JOIN ung_tuyen ut ON sv.stt_sv = ut.stt_sv 
              LEFT JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung 
              WHERE ut.trang_thai = 'Đồng ý'";
$total_params = [];
if ($keyword) {
    $total_sql .= " AND (sv.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR sv.email LIKE ? OR td.tieu_de LIKE ?)";
    $likeKeyword = "%$keyword%";
    $total_params[] = $likeKeyword;
    $total_params[] = $likeKeyword;
    $total_params[] = $likeKeyword;
    $total_params[] = $likeKeyword;
}

$total_stmt = $conn->prepare($total_sql);
if ($total_params) {
    $total_stmt->bind_param(str_repeat('s', count($total_params)), ...$total_params);
}
$total_stmt->execute();
$total_records = $total_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);
$total_stmt->close();

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

        .action-icon {
            margin: 0 5px;
            cursor: pointer;
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

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            margin: 0 5px;
            padding: 5px 10px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .pagination a:hover {
            background-color: #0078d4;
            color: white;
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
                <input type="text" id="searchInput" placeholder="Tìm Kiếm ...." value="<?php echo htmlspecialchars($keyword); ?>" />
                <span id="searchLoading"><i class="fas fa-spinner fa-spin"></i></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <div id="searchResults"></div>
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

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">STT</th>
                        <th style="width: 150px;">Mã sinh viên</th>
                        <th style="width: 200px;">Họ tên</th>
                        <th style="width: 200px;">Email</th>
                        <th style="width: 50px;">Lớp</th>
                        <th style="width: 100px;">Khoa</th>
                        <th style="width: 120px;">Số điện thoại</th>
                        <th style="width: 200px;">Tin tuyển dụng</th>
                        <th style="width: 250px;">Nội dung</th>
                        <th style="width: 250px;">File đính kèm</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sinh_vien_list)): ?>
                        <tr>
                            <td colspan="10" class="center-text">Không có ứng viên nào được duyệt.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sinh_vien_list as $index => $sv): ?>
                            <tr data-stt-sv="<?php echo htmlspecialchars($sv['stt_sv']); ?>" data-ma-tuyen-dung="<?php echo htmlspecialchars($sv['ma_tuyen_dung']); ?>">
                                <td class="center-text"><?php echo $index + 1 + $offset; ?></td>
                                <td><?php echo htmlspecialchars($sv['ma_sinh_vien']); ?></td>
                                <td><?php echo htmlspecialchars($sv['ho_ten']); ?></td>
                                <td><?php echo htmlspecialchars($sv['email']); ?></td>
                                <td><?php echo htmlspecialchars($sv['lop']); ?></td>
                                <td><?php echo htmlspecialchars($sv['khoa']); ?></td>
                                <td><?php echo htmlspecialchars($sv['so_dien_thoai']); ?></td>
                                <td><?php echo htmlspecialchars($sv['tieu_de']); ?></td>
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
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>" <?php if ($i == $page) echo 'style="font-weight: bold;"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>
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

        // Chức năng tìm kiếm
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
                const url = `../logic_cstt/logic_quanly_baocao.php?action=search&keyword=${encodeURIComponent(keyword)}`;

                fetch(url)
                    .then(response => {
                        loadingSpinner.style.display = 'none';
                        if (!response.ok) throw new Error("HTTP status " + response.status);
                        return response.json();
                    })
                    .then(data => {
                        resultsContainer.innerHTML = "";
                        if (data.success && data.data.applications.length > 0) {
                            const resultList = document.createElement("ul");
                            data.data.applications.slice(0, 10).forEach(app => {
                                const listItem = document.createElement("li");
                                listItem.innerHTML = `
                                    <div>
                                        <strong>${escapeHTML(app.ho_ten)}</strong> (${app.ma_sinh_vien})
                                        <p style="margin: 0; font-size: 12px;">${escapeHTML(app.tieu_de)}</p>
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
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            })[match]);
        }

        function updateSearch(keyword) {
            window.location.href = `?page=1&keyword=${encodeURIComponent(keyword)}`;
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