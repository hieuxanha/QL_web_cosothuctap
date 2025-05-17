<?php
session_start();
require_once '../db.php';
$conn->set_charset("utf8mb4");

// Lấy tham số phân trang và từ khóa tìm kiếm
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Truy vấn danh sách đánh giá
$sql = "
    SELECT 
        dg.stt_danhgia, dg.ma_dang_ky, dg.stt_sv, dg.ten_co_so, dg.tieu_de_tuyen_dung, 
        dg.cong_ty, dg.email_lien_he, dg.ma_sinh_vien, sv.ho_ten AS ho_ten_sinh_vien, 
        dg.ngay_danh_gia, dg.nguoi_danh_gia
    FROM danh_gia_thuc_tap dg
    JOIN sinh_vien sv ON dg.stt_sv = sv.stt_sv";
$params = [];
$conditions = [];

if ($keyword) {
    $conditions[] = "(dg.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR dg.cong_ty LIKE ? OR dg.tieu_de_tuyen_dung LIKE ?)";
    $likeKeyword = "%$keyword%";
    $params[] = $likeKeyword;
    $params[] = $likeKeyword;
    $params[] = $likeKeyword;
    $params[] = $likeKeyword;
}

if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY dg.ngay_danh_gia DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

$stmt = $conn->prepare($sql);
if ($params) {
    $types = str_repeat('s', count($params) - 2) . 'ii';
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$evaluations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Tính tổng số bản ghi cho phân trang
$total_sql = "SELECT COUNT(*) AS total FROM danh_gia_thuc_tap dg JOIN sinh_vien sv ON dg.stt_sv = sv.stt_sv";
$total_params = [];
if ($keyword) {
    $total_sql .= " WHERE (dg.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR dg.cong_ty LIKE ? OR dg.tieu_de_tuyen_dung LIKE ?)";
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

// Kiểm tra thông báo từ session
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản Lý Đánh Giá Thực Tập</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="ui_danh_gia_thuc_tap.css" />
    <style>
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .action-buttons a,
        .action-buttons button {
            padding: 5px 10px;
            margin: 0 5px;
            text-decoration: none;
            color: white;
            border-radius: 3px;
        }

        .action-buttons .pdf {
            background-color: #dc3545;
        }

        .action-buttons .email {
            background-color: #007bff;
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

        /* CSS cho phân trang */
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
            background-color: #007bff;
            color: white;
        }

        .pagination a.active {
            font-weight: bold;
            background-color: #007bff;
            color: white;
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
                <input type="text" id="searchInput" placeholder="Tìm theo mã SV, họ tên, công ty, tiêu đề..." value="<?php echo htmlspecialchars($keyword); ?>" tabindex="1" aria-label="Tìm kiếm thông tin" />
                <span id="searchLoading"><i class="fas fa-spinner fa-spin"></i></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <div id="searchResults"></div>
            </div>
            <div class="profile">
                <span><?php echo htmlspecialchars($_SESSION['name'] ?? 'Tên người dùng'); ?></span>
                <img src="images/profile.jpg" alt="Ảnh đại diện" />
            </div>
        </div>

        <div class="container">
            <div class="subnav">
                <div class="subnav-title">
                    <img src="images/icon.png" alt="Icon" />
                    Quản Lý Đánh Giá Thực Tập
                </div>
            </div>

            <!-- Hiển thị thông báo -->
            <?php if ($success_message): ?>
                <div id="message" class="message success">
                    <?php echo htmlspecialchars($success_message); ?>
                    <button onclick="this.parentElement.style.display='none'">Đóng</button>
                </div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div id="error-message" class="message error">
                    <?php echo htmlspecialchars($error_message); ?>
                    <button onclick="this.parentElement.style.display='none'">Đóng</button>
                </div>
            <?php endif; ?>

            <h1>Danh Sách Đánh Giá Thực Tập</h1>
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã Sinh Viên</th>
                        <th>Họ Tên</th>
                        <th>Công Ty</th>
                        <th>Tiêu Đề Tuyển Dụng</th>
                        <th>Ngày Đánh Giá</th>
                        <th>Người Đánh Giá</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($evaluations)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">Không có đánh giá nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($evaluations as $index => $eval): ?>
                            <tr>
                                <td><?php echo $index + 1 + $offset; ?></td>
                                <td><?php echo htmlspecialchars($eval['ma_sinh_vien']); ?></td>
                                <td><?php echo htmlspecialchars($eval['ho_ten_sinh_vien']); ?></td>
                                <td><?php echo htmlspecialchars($eval['cong_ty']); ?></td>
                                <td><?php echo htmlspecialchars($eval['tieu_de_tuyen_dung']); ?></td>
                                <td><?php echo htmlspecialchars($eval['ngay_danh_gia']); ?></td>
                                <td><?php echo htmlspecialchars($eval['nguoi_danh_gia']); ?></td>
                                <td class="action-buttons">
                                    <a href="../logic_cstt/export_pdf.php?stt_danhgia=<?php echo $eval['stt_danhgia']; ?>" class="pdf">Xuất PDF</a>
                                    <button onclick="sendEmail(<?php echo $eval['stt_danhgia']; ?>)" class="email">Gửi Email</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const content = document.getElementById("content");
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("collapsed");
        }

        function sendEmail(stt_danhgia) {
            if (confirm("Bạn có chắc chắn muốn gửi email với file PDF đánh giá này?")) {
                fetch("../logic_cstt/send_evaluation_email.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "stt_danhgia=" + encodeURIComponent(stt_danhgia)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Email đã được gửi thành công!");
                        } else {
                            alert("Lỗi khi gửi email: " + data.message);
                        }
                    })
                    .catch(error => {
                        alert("Đã xảy ra lỗi: " + error.message);
                    });
            }
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
                const url = `../logic_cstt/logic_quan_ly_danh_gia.php?action=search&keyword=${encodeURIComponent(keyword)}`;

                fetch(url)
                    .then(response => {
                        loadingSpinner.style.display = 'none';
                        if (!response.ok) throw new Error("HTTP status " + response.status);
                        return response.json();
                    })
                    .then(data => {
                        resultsContainer.innerHTML = "";
                        if (data.success && data.data.evaluations.length > 0) {
                            const resultList = document.createElement("ul");
                            data.data.evaluations.slice(0, 10).forEach(eval => {
                                const listItem = document.createElement("li");
                                listItem.innerHTML = `
                                    <div>
                                        <strong>${escapeHTML(eval.ho_ten_sinh_vien)}</strong> (${eval.ma_sinh_vien})
                                        <p style="margin: 0; font-size: 12px;">${escapeHTML(eval.cong_ty)} - ${escapeHTML(eval.tieu_de_tuyen_dung)}</p>
                                    </div>
                                `;
                                listItem.addEventListener("click", () => {
                                    updateSearch(eval.ho_ten_sinh_vien);
                                    resultsContainer.classList.remove("active");
                                });
                                resultList.appendChild(listItem);
                            });
                            resultsContainer.appendChild(resultList);
                            resultsContainer.classList.add("active");
                        } else {
                            resultsContainer.innerHTML = "<p>Không tìm thấy đánh giá phù hợp.</p>";
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
            messageDiv.style.margin = '10px';
            document.getElementById('content').insertBefore(messageDiv, document.querySelector('.container'));
            setTimeout(() => messageDiv.remove(), 3000);
        }
    </script>
</body>

</html>