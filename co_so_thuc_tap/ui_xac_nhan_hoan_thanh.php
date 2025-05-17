<?php
session_start();
require_once '../db.php';

// Handle confirmation of internship completion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $stt_sv = filter_input(INPUT_POST, 'stt_sv', FILTER_VALIDATE_INT);
    if ($stt_sv) {
        $stmt = $conn->prepare("UPDATE ung_tuyen SET trang_thai = 'Hoàn thành' WHERE stt_sv = ? AND trang_thai = 'Đồng ý'");
        $stmt->bind_param("i", $stt_sv);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Xác nhận hoàn thành thực tập thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi xác nhận: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Vui lòng chọn sinh viên hợp lệ.";
    }
    header("Location: ui_xac_nhan_hoan_thanh.php");
    exit;
}

// Pagination setup
$per_page = 10;
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$offset = ($page - 1) * $per_page;
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Fetch list of approved students (including completed ones)
$sinh_vien_list = [];
$sql = "
    SELECT 
        sv.stt_sv, 
        sv.ma_sinh_vien, 
        sv.ho_ten, 
        sv.khoa, 
        td.tieu_de,
        ut.trang_thai
    FROM sinh_vien sv
    LEFT JOIN ung_tuyen ut ON sv.stt_sv = ut.stt_sv
    LEFT JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung
    WHERE ut.id IS NOT NULL AND ut.trang_thai IN ('Đồng ý', 'Hoàn thành')";
$params = [];
$conditions = [];

if ($keyword) {
    $conditions[] = "(sv.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR sv.khoa LIKE ? OR td.tieu_de LIKE ?)";
    $likeKeyword1 = "%$keyword%";
    $likeKeyword2 = "%$keyword%";
    $likeKeyword3 = "%$keyword%";
    $likeKeyword4 = "%$keyword%";
    $params[] = $likeKeyword1;
    $params[] = $likeKeyword2;
    $params[] = $likeKeyword3;
    $params[] = $likeKeyword4;
}

if ($conditions) {
    $sql .= " AND " . implode(" AND ", $conditions);
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

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $sinh_vien_list[] = $row;
    }
} else {
    $_SESSION['error'] = "Lỗi truy vấn: " . $conn->error;
}
$stmt->close();

// Get total records for pagination
$total_sql = "SELECT COUNT(DISTINCT sv.stt_sv) AS total FROM sinh_vien sv JOIN ung_tuyen ut ON sv.stt_sv = ut.stt_sv WHERE ut.trang_thai IN ('Đồng ý', 'Hoàn thành')";
$total_params = [];
if ($keyword) {
    $total_sql .= " AND (sv.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR sv.khoa LIKE ? OR td.tieu_de LIKE ?)";
    $likeKeyword1 = "%$keyword%";
    $likeKeyword2 = "%$keyword%";
    $likeKeyword3 = "%$keyword%";
    $likeKeyword4 = "%$keyword%";
    $total_params[] = $likeKeyword1;
    $total_params[] = $likeKeyword2;
    $total_params[] = $likeKeyword3;
    $total_params[] = $likeKeyword4;
}

$stmt = $conn->prepare($total_sql);
if ($total_params) {
    $stmt->bind_param(str_repeat('s', count($total_params)), ...$total_params);
}
$stmt->execute();
$total_records = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Hoàn Thành Thực Tập</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../co_so_thuc_tap/ui_cv.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .subnav {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .subnav-title {
            display: flex;
            align-items: center;
            font-size: 18px;
            font-weight: 500;
            color: #0078d4;
        }

        .subnav-title i {
            margin-right: 8px;
            color: #0078d4;
        }

        .button-group {
            margin-left: auto;
        }

        .btn {
            background-color: #0078d4;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-left: 8px;
            transition: background-color 0.2s;
        }

        .btn:hover {
            background-color: #005ba1;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }

        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .data-table th {
            background-color: #0078d4;
            color: #fff;
            font-weight: 500;
        }

        .data-table tr:hover {
            background-color: #f5f5f5;
        }

        .center-text {
            text-align: center;
        }

        .message {
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
            font-size: 14px;
        }

        .message.success {
            background-color: #e0ffe0;
            color: #2e7d32;
        }

        .message.error {
            background-color: #ffe0e0;
            color: #d32f2f;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 4px;
            text-decoration: none;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            color: #0078d4;
            transition: background-color 0.2s;
        }

        .pagination a:hover {
            休闲-background-color: #0078d4;
            color: #fff;
        }

        .pagination a.active {
            background-color: #0078d4;
            color: #fff;
            font-weight: bold;
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
            border: 1px solid #ddd;
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
            <hr>
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
                <input type="text" id="searchInput" placeholder="Tìm theo mã SV, họ tên, khoa, tin tuyển dụng..." value="<?php echo htmlspecialchars($keyword); ?>">
                <span id="searchLoading"><i class="fas fa-spinner fa-spin"></i></span>
                <i class="fa-solid fa-magnifying-glass"></i>
                <div id="searchResults"></div>
            </div>
            <div class="profile">
                <span><?php echo htmlspecialchars($_SESSION['name'] ?? 'Tên người dùng'); ?></span>
                <img src="../assets/profile.jpg" alt="Ảnh đại diện" width="40" height="40">
            </div>
        </div>

        <div class="container">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message success"><?php echo htmlspecialchars($_SESSION['message']);
                                                unset($_SESSION['message']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="message error"><?php echo htmlspecialchars($_SESSION['error']);
                                            unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="subnav">
                <div class="subnav-title">
                    <i class="fa-solid fa-check-circle"></i> Xác nhận hoàn thành thực tập
                </div>
                <div class="button-group">
                    <button class="btn" onclick="exportToExcel()">Xuất Excel</button>
                    <button class="btn" onclick="configureColumns()">Cấu hình cột hiển thị</button>
                </div>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">STT</th>
                        <th style="width: 15%;">Mã sinh viên</th>
                        <th style="width: 20%;">Họ tên</th>
                        <th style="width: 15%;">Khoa</th>
                        <th style="width: 25%;">Tin tuyển dụng</th>
                        <th style="width: 10%;">Đã hoàn thành</th>
                        <th style="width: 10%;">Xác nhận</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sinh_vien_list)): ?>
                        <tr>
                            <td colspan="7" class="center-text">Không có sinh viên nào cần xác nhận.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sinh_vien_list as $index => $sv): ?>
                            <tr>
                                <td class="center-text"><?php echo $index + 1 + $offset; ?></td>
                                <td><?php echo htmlspecialchars($sv['ma_sinh_vien']); ?></td>
                                <td><?php echo htmlspecialchars($sv['ho_ten']); ?></td>
                                <td><?php echo htmlspecialchars($sv['khoa'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($sv['tieu_de'] ?? 'N/A'); ?></td>
                                <td class="center-text"><?php echo $sv['trang_thai'] === 'Hoàn thành' ? '<i class="fas fa-check-circle" style="color: green;"></i>' : ''; ?></td>
                                <td class="center-text">
                                    <?php if ($sv['trang_thai'] === 'Đồng ý'): ?>
                                        <form method="POST" action="ui_xac_nhan_hoan_thanh.php">
                                            <input type="hidden" name="stt_sv" value="<?php echo $sv['stt_sv']; ?>">
                                            <button type="submit" name="confirm" class="btn" onclick="return confirm('Xác nhận hoàn thành thực tập cho sinh viên này?');">Xác nhận</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("collapsed");
            document.getElementById("content").classList.toggle("collapsed");
        }

        function exportToExcel() {
            alert("Chức năng xuất Excel đang được phát triển!");
        }

        function configureColumns() {
            alert("Chức năng cấu hình cột hiển thị đang được phát triển!");
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
                const url = `../logic_cstt/logic_xac_nhan_hoan_thanh.php?action=search&keyword=${encodeURIComponent(keyword)}`;

                fetch(url)
                    .then(response => {
                        loadingSpinner.style.display = 'none';
                        if (!response.ok) throw new Error("HTTP status " + response.status);
                        return response.json();
                    })
                    .then(data => {
                        resultsContainer.innerHTML = "";
                        if (data.success && data.data.students.length > 0) {
                            const resultList = document.createElement("ul");
                            data.data.students.slice(0, 10).forEach(student => {
                                const listItem = document.createElement(" underpinning li");
                                listItem.innerHTML = `
                                    <div>
                                        <strong>${escapeHTML(student.ho_ten)}</strong> (${student.ma_sinh_vien})
                                        <p style="margin: 0; font-size: 12px;">${escapeHTML(student.tieu_de)}</p>
                                    </div>
                                `;
                                listItem.addEventListener("click", () => {
                                    updateSearch(student.ho_ten);
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
            document.getElementById('content').insertBefore(messageDiv, document.querySelector('.container'));
            setTimeout(() => messageDiv.remove(), 3000);
        }
    </script>
</body>

</html>