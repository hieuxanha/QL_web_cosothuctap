<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Ensure the user is a lecturer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'giang_vien' || !isset($_SESSION['so_hieu_giang_vien'])) {
    header("Location: ../dang_nhap_dang_ki/form_dn.php");
    exit;
}

// Kết nối cơ sở dữ liệu
require_once '../db.php';
$conn->set_charset("utf8mb4");

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

try {
    // Lấy danh sách PDF đã nhận
    $sql = "
        SELECT p.id, p.stt_danhgia, p.filename, p.filepath, p.created_at, p.ket_qua, dg.ma_sinh_vien, sv.ho_ten
        FROM pdf_nhan p
        JOIN danh_gia_thuc_tap dg ON p.stt_danhgia = dg.stt_danhgia
        JOIN sinh_vien sv ON dg.stt_sv = sv.stt_sv
        WHERE sv.so_hieu = ?";
    $params = [$_SESSION['so_hieu_giang_vien']];

    if ($keyword) {
        $sql .= " AND (dg.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR p.filename LIKE ?)";
        $likeKeyword = "%$keyword%";
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
    }

    $sql .= " ORDER BY p.created_at DESC";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Lỗi prepare: " . $conn->error);
    }

    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    if (!$stmt->execute()) {
        throw new Exception("Lỗi execute: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $files = [];
    while ($row = $result->fetch_assoc()) {
        $files[] = $row;
    }

    $stmt->close();
} catch (Exception $e) {
    $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    header("Location: ui_nhan_pdf.php");
    exit;
} finally {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách PDF Đã Nhận</title>
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

        .btn {
            background-color: #0078d4;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            margin-left: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .cv-link {
            color: #0078d4;
            text-decoration: none;
            cursor: pointer;
        }

        .cv-link:hover {
            text-decoration: underline;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
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
            overflow-y: auto;
        }

        .modal-content iframe {
            width: 100%;
            height: 90%;
            border: none;
        }

        .modal-content div {
            margin-bottom: 15px;
        }

        .modal-content label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .modal-content select,
        .modal-content textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .modal-content textarea {
            height: 60px;
            resize: vertical;
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
            width: 500px;
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
        <div class="icons"><i class="fa-solid fa-circle-user"></i></div>
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
                <input type="text" id="searchInput" placeholder="Tìm theo mã SV, họ tên, tên file..." value="<?php echo htmlspecialchars($keyword); ?>" />
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
                    <img src="/api/placeholder/24/24" alt="Icon" /> Danh Sách PDF Đã Nhận
                    <span class="youtube-icon">▶</span>
                </div>
            </div>

            <div id="pdfModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closePdf()">×</span>
                    <iframe id="pdfFrame" src=""></iframe>
                </div>
            </div>

            <div id="evaluationModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeEvaluationForm()">×</span>
                    <h2>Chấm Điểm Sinh Viên</h2>
                    <form id="evaluationForm" method="POST" action="../logic_giangvien/logic_xoa_sua_pdf.php">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" id="stt_danhgia" name="stt_danhgia">
                        <input type="hidden" name="action" value="edit_grade">
                        <div>
                            <label>Thang điểm:</label>
                            <select name="ket_qua" id="ket_qua" required>
                                <option value="">-- Thu hồi điểm --</option>
                                <option value="A">A</option>
                                <option value="B+">B+</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="F">F</option>
                            </select>
                        </div>
                        <button type="submit" class="btn">Lưu Đánh Giá</button>
                    </form>
                </div>
            </div>

            <?php if (!empty($files)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">STT</th>
                            <th style="width: 150px;">Mã Sinh Viên</th>
                            <th style="width: 200px;">Họ Tên</th>
                            <th style="width: 300px;">Tên File</th>
                            <th style="width: 150px;">Thời Gian Nhận</th>
                            <th style="width: 100px;">Hành Động</th>
                            <th style="width: 150px;">Chấm Điểm</th>
                            <th style="width: 100px;">Xóa</th>
                        </tr>
                    </thead>
                    <tbody id="pdfList">
                        <?php foreach ($files as $index => $file): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($file['ma_sinh_vien']); ?></td>
                                <td><?php echo htmlspecialchars($file['ho_ten']); ?></td>
                                <td><?php echo htmlspecialchars($file['filename']); ?></td>
                                <td><?php echo htmlspecialchars($file['created_at']); ?></td>
                                <td>
                                    <?php
                                    $relativePath = basename($file['filepath']);
                                    $fullPath = '../uploads/' . $relativePath;
                                    if (file_exists($fullPath)):
                                    ?>
                                        <span class="cv-link" onclick="showPdf('<?php echo htmlspecialchars($relativePath); ?>')">Xem PDF</span>
                                    <?php else: ?>
                                        <span>File không tồn tại</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($file['ket_qua']) && in_array($file['ket_qua'], ['A', 'B+', 'B', 'C', 'D', 'F'])): ?>
                                        Đã chấm: <?php echo htmlspecialchars($file['ket_qua']); ?>
                                    <?php else: ?>
                                        <button class="btn" onclick="showEvaluationForm(<?php echo htmlspecialchars($file['stt_danhgia']); ?>, '')">Chấm điểm</button>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-danger" onclick="deletePdf(<?php echo htmlspecialchars($file['id']); ?>)">Xóa</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 20px;">Không có file PDF nào.</p>
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

        function showPdf(filepath) {
            const modal = document.getElementById("pdfModal");
            const pdfFrame = document.getElementById("pdfFrame");
            pdfFrame.src = "../uploads/" + encodeURIComponent(filepath);
            modal.style.display = "block";
        }

        function closePdf() {
            const modal = document.getElementById("pdfModal");
            const pdfFrame = document.getElementById("pdfFrame");
            pdfFrame.src = "";
            modal.style.display = "none";
        }

        function showEvaluationForm(stt_danhgia, currentGrade) {
            const modal = document.getElementById("evaluationModal");
            const ketQuaSelect = document.getElementById("ket_qua");
            document.getElementById("stt_danhgia").value = stt_danhgia;
            ketQuaSelect.value = currentGrade;
            modal.style.display = "block";
        }

        function closeEvaluationForm() {
            const modal = document.getElementById("evaluationModal");
            modal.style.display = "none";
        }

        function deletePdf(id) {
            if (!confirm("Bạn có chắc chắn muốn xóa PDF này không?")) return;

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);
            formData.append('csrf_token', '<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>');

            fetch('../logic_giangvien/logic_xoa_sua_pdf.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('success', data.message);
                        if (data.redirect) {
                            setTimeout(() => window.location.href = data.redirect, 1000);
                        } else {
                            setTimeout(() => location.reload(), 1000);
                        }
                    } else {
                        showMessage('error', data.message);
                    }
                })
                .catch(error => {
                    showMessage('error', 'Đã xảy ra lỗi: ' + error.message);
                });
        }

        // Handle form submission for evaluation
        document.getElementById('evaluationForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('action', 'edit_grade');
            formData.append('csrf_token', '<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>');

            fetch('../logic_giangvien/logic_xoa_sua_pdf.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    closeEvaluationForm();
                    if (data.success) {
                        showMessage('success', data.message);
                        if (data.redirect) {
                            setTimeout(() => window.location.href = data.redirect, 1000);
                        } else {
                            setTimeout(() => location.reload(), 1000);
                        }
                    } else {
                        showMessage('error', data.message);
                    }
                })
                .catch(error => {
                    showMessage('error', 'Đã xảy ra lỗi: ' + error.message);
                });
        });

        window.onclick = function(event) {
            const pdfModal = document.getElementById("pdfModal");
            const evaluationModal = document.getElementById("evaluationModal");
            if (event.target == pdfModal) {
                closePdf();
            }
            if (event.target == evaluationModal) {
                closeEvaluationForm();
            }
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
                const url = `../logic_giangvien/logic_pdf_timkiem.php?action=search_pdf&keyword=${encodeURIComponent(keyword)}&csrf_token=<?php echo urlencode($_SESSION['csrf_token']); ?>`;

                fetch(url)
                    .then(response => {
                        loadingSpinner.style.display = 'none';
                        if (!response.ok) throw new Error("HTTP status " + response.status);
                        return response.json();
                    })
                    .then(data => {
                        resultsContainer.innerHTML = "";
                        if (data.success && data.data.files.length > 0) {
                            const resultList = document.createElement("ul");
                            data.data.files.slice(0, 10).forEach(file => {
                                const listItem = document.createElement("li");
                                listItem.innerHTML = `
                                <div>
                                    <strong>${escapeHTML(file.ho_ten)}</strong> (${file.ma_sinh_vien})
                                    <p style="margin: 0; font-size: 12px;">${escapeHTML(file.filename)}</p>
                                    <small>${escapeHTML(file.created_at)}</small>
                                </div>
                            `;
                                listItem.dataset.keyword = file.ma_sinh_vien;
                                listItem.addEventListener("click", () => {
                                    updateSearch(file.ma_sinh_vien);
                                    resultsContainer.classList.remove("active");
                                });
                                resultList.appendChild(listItem);
                            });
                            resultsContainer.appendChild(resultList);
                            resultsContainer.classList.add("active");
                        } else {
                            resultsContainer.innerHTML = "<p>Không tìm thấy file PDF phù hợp.</p>";
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
            window.location.href = `?keyword=${encodeURIComponent(keyword)}`;
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