<?php
session_start();
require '../db.php'; // Database connection

// Initialize messages
$success_message = "";
$error_message = "";

// Fetch all departments (khoa)
$departments = [];
$sql_departments = "SELECT DISTINCT khoa FROM sinh_vien WHERE khoa IS NOT NULL ORDER BY khoa";
$result_departments = $conn->query($sql_departments);
if ($result_departments && $result_departments->num_rows > 0) {
    while ($row = $result_departments->fetch_assoc()) {
        $departments[] = $row['khoa'];
    }
}

// Fetch all classes (lop), filtered by selected khoa if provided
$selected_khoa = isset($_POST['khoa']) ? trim($_POST['khoa']) : '';
$classes = [];
$sql_classes = "SELECT DISTINCT lop FROM sinh_vien WHERE lop IS NOT NULL";
if (!empty($selected_khoa)) {
    $sql_classes .= " AND khoa = ?";
}
$sql_classes .= " ORDER BY lop";
$stmt_classes = $conn->prepare($sql_classes);
if (!empty($selected_khoa)) {
    $stmt_classes->bind_param("s", $selected_khoa);
}
$stmt_classes->execute();
$result_classes = $stmt_classes->get_result();
if ($result_classes && $result_classes->num_rows > 0) {
    while ($row = $result_classes->fetch_assoc()) {
        $classes[] = $row['lop'];
    }
}
$stmt_classes->close();

// Fetch all lecturers for the dropdown
$lecturers = [];
$sql_lecturers = "SELECT so_hieu_giang_vien, ho_ten, khoa FROM giang_vien ORDER BY ho_ten";
$result_lecturers = $conn->query($sql_lecturers);
if ($result_lecturers && $result_lecturers->num_rows > 0) {
    while ($row = $result_lecturers->fetch_assoc()) {
        $lecturers[] = $row;
    }
}

// Fetch students based on selected khoa (required) and lop (optional)
$selected_lop = isset($_POST['lop']) ? trim($_POST['lop']) : '';
$students = [];
if (!empty($selected_khoa)) {
    $sql_students = "SELECT ma_sinh_vien, ho_ten, email, so_dien_thoai, khoa, so_hieu FROM sinh_vien WHERE khoa = ?";
    $params = [$selected_khoa];
    $types = "s";
    if (!empty($selected_lop)) {
        $sql_students .= " AND lop = ?";
        $params[] = $selected_lop;
        $types .= "s";
    }
    $stmt = $conn->prepare($sql_students);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result_students = $stmt->get_result();
    if ($result_students && $result_students->num_rows > 0) {
        while ($row = $result_students->fetch_assoc()) {
            $students[] = $row;
        }
    }
    $stmt->close();
}

// Handle form submission for lecturer assignment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_lecturer'])) {
    $selected_class = trim($_POST['lop']);
    $selected_lecturer = trim($_POST['so_hieu_giang_vien']);

    if (!empty($selected_class) && !empty($selected_lecturer)) {
        // Update so_hieu for all students in the selected class
        $sql_update = "UPDATE sinh_vien SET so_hieu = ? WHERE lop = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ss", $selected_lecturer, $selected_class);

        if ($stmt->execute()) {
            $success_message = "Phân công giảng viên thành công cho lớp $selected_class!";
        } else {
            $error_message = "Lỗi khi phân công giảng viên: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error_message = "Vui lòng chọn lớp và giảng viên!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tìm kiếm giảng viên phụ trách</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="./ui_timkiem_gv_phutrach.css" />
    <style>
        .container {
            padding: 20px;
        }

        .form-container,
        .table-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-container h3,
        .table-container h3 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-group button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-group button:hover {
            background: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f4f4f4;
            font-weight: 500;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: fadeIn 0.3s ease-in-out;
        }

        .modal-content p {
            color: #333;
        }

        .modal-content button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
        }

        .modal-content button:hover {
            background: #45a049;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .search-bar {
            position: relative;
        }

        #searchResults {
            position: absolute;
            top: 40px;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            transform: translateY(-20px);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
            z-index: 1000;
            display: none;
        }

        #searchResults.active {
            display: block;
            transform: translateY(0);
            opacity: 1;
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
                <h2>Quản lý</h2>
                <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_admin.php">admin..</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_tk_nguoidung.php">Quản lý tài khoản người dùng</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_quanly_cty.php">Phê duyệt công ty</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_quanlytt.php">Phê duyệt tuyển dụng</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_timkiem_gv_phutrach.php">Tìm kiếm giáo viên phụ trách</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="#">Thông báo</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="#">Bảo trì hệ thống</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="#">Cơ sở</a></li>
            </ul>
        </div>
    </div>

    <div class="content" id="content">
        <div class="header">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Tìm kiếm giảng viên..." aria-label="Tìm kiếm giảng viên" />
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
                if (isset($_SESSION['name']) && !empty($_SESSION['name'])) {
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
                    echo '<a href="../dang_nhap_dang_ki/form_dn.php">Đăng nhập</a>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <!-- Form Phân Công Giảng Viên -->
        <div class="container">
            <div class="form-container">
                <h3>Phân công giảng viên phụ trách lớp</h3>
                <form method="POST" action="" id="assignForm">
                    <div class="form-group">
                        <label for="khoa">Chọn khoa</label>
                        <select name="khoa" id="khoa" required onchange="this.form.submit()">
                            <option value="">-- Chọn khoa --</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?php echo htmlspecialchars($department); ?>" <?php echo $selected_khoa === $department ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($department); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="lop">Chọn lớp (tùy chọn)</label>
                        <select name="lop" id="lop" onchange="this.form.submit()">
                            <option value="">-- Tất cả lớp --</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo htmlspecialchars($class); ?>" <?php echo $selected_lop === $class ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($class); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="so_hieu_giang_vien">Chọn giảng viên</label>
                        <select name="so_hieu_giang_vien" id="so_hieu_giang_vien" required>
                            <option value="">-- Chọn giảng viên --</option>
                            <?php foreach ($lecturers as $lecturer): ?>
                                <option value="<?php echo htmlspecialchars($lecturer['so_hieu_giang_vien']); ?>">
                                    <?php echo htmlspecialchars($lecturer['ho_ten']) . ' (' . htmlspecialchars($lecturer['khoa']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="assign_lecturer">Phân công</button>
                    </div>
                </form>
            </div>

            <!-- Student List -->
            <?php if (!empty($students)): ?>
                <div class="table-container">
                    <h3>
                        Danh sách sinh viên
                        <?php echo !empty($selected_lop) ? 'lớp ' . htmlspecialchars($selected_lop) : 'khoa ' . htmlspecialchars($selected_khoa); ?>
                    </h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Mã SV</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Khoa</th>
                                <th>Giảng viên hiện tại</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['ma_sinh_vien']); ?></td>
                                    <td><?php echo htmlspecialchars($student['ho_ten']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['so_dien_thoai'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($student['khoa'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php
                                        if (!empty($student['so_hieu'])) {
                                            $sql_gv = "SELECT ho_ten FROM giang_vien WHERE so_hieu_giang_vien = ?";
                                            $stmt_gv = $conn->prepare($sql_gv);
                                            $stmt_gv->bind_param("s", $student['so_hieu']);
                                            $stmt_gv->execute();
                                            $result_gv = $stmt_gv->get_result();
                                            if ($gv = $result_gv->fetch_assoc()) {
                                                echo htmlspecialchars($gv['ho_ten']);
                                            } else {
                                                echo 'Chưa phân công';
                                            }
                                            $stmt_gv->close();
                                        } else {
                                            echo 'Chưa phân công';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Lecturer List -->
            <div class="table-container">
                <h3>Danh sách giảng viên</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Số hiệu</th>
                            <th>Họ tên</th>
                            <th>Khoa</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                        </tr>
                    </thead>
                    <tbody id="lecturerList">
                        <tr>
                            <td colspan="5"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal thông báo -->
    <div id="customModal" class="modal" style="display: none;">
        <div class="modal-content" id="modalContent">
            <!-- Nội dung sẽ được chèn động -->
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const content = document.getElementById("content");
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("collapsed");
        }

        function showModal(message) {
            const modal = document.getElementById("customModal");
            const modalContent = document.getElementById("modalContent");
            modalContent.innerHTML = '';
            const messageElement = document.createElement('p');
            messageElement.textContent = message;
            const closeButton = document.createElement('button');
            closeButton.textContent = 'Đóng';
            closeButton.onclick = closeModal;
            modalContent.appendChild(messageElement);
            modalContent.appendChild(closeButton);
            modal.style.display = 'flex';
        }

        function closeModal() {
            document.getElementById("customModal").style.display = 'none';
        }

        function escapeHTML(str) {
            return str.replace(/[&<>"']/g, match => ({
                '&': '&',
                '<': '<',
                '>': '>',
                '"': '"',
                "'": "'"
            })[match]);
        }

        function loadStudents() {
            document.getElementById('assignForm').submit();
        }

        function loadLecturers(searchTerm = '') {
            const tableBody = document.getElementById("lecturerList");
            tableBody.innerHTML = '<tr><td colspan="5"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>';

            const url = searchTerm ?
                `../logic_giangvien/logic_timkiem_gv.php?action=search_lecturers&keyword=${encodeURIComponent(searchTerm)}` :
                '../logic_giangvien/logic_timkiem_gv.php?action=get_lecturers';

            fetch(url)
                .then(response => {
                    console.log('Fetch Response:', response);
                    if (!response.ok) {
                        throw new Error(`HTTP status ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Fetch Data:', data);
                    tableBody.innerHTML = '';
                    if (data.success && data.data.lecturers.length > 0) {
                        data.data.lecturers.forEach(lecturer => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${escapeHTML(lecturer.so_hieu_giang_vien)}</td>
                                <td>${escapeHTML(lecturer.ho_ten)}</td>
                                <td>${escapeHTML(lecturer.khoa || 'N/A')}</td>
                                <td>${escapeHTML(lecturer.email)}</td>
                                <td>${escapeHTML(lecturer.so_dien_thoai || 'N/A')}</td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="5">Không tìm thấy giảng viên nào</td></tr>';
                    }
                })
                .catch(error => {
                    tableBody.innerHTML = '<tr><td colspan="5">Lỗi khi tải dữ liệu</td></tr>';
                    showModal('Không thể tải danh sách giảng viên. Vui lòng thử lại.');
                    console.error('Fetch Error:', error);
                });
        }

        let debounceTimer;
        document.getElementById("searchInput").addEventListener("keyup", function() {
            clearTimeout(debounceTimer);
            const keyword = this.value.trim();
            const resultsContainer = document.getElementById("searchResults");

            if (keyword === "") {
                resultsContainer.classList.remove("active");
                loadLecturers();
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`../logic_giangvien/logic_timkiem_gv.php?action=search_lecturers&keyword=${encodeURIComponent(keyword)}`)
                    .then(response => {
                        console.log('Search Response:', response);
                        if (!response.ok) throw new Error("HTTP status " + response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Search Data:', data);
                        resultsContainer.innerHTML = "";
                        if (data.success && data.data.lecturers.length > 0) {
                            const resultList = document.createElement("ul");
                            data.data.lecturers.forEach(lecturer => {
                                const listItem = document.createElement("li");
                                listItem.innerHTML = `
                                    <div style="display: flex; align-items: center;">
                                        <div>
                                            <strong>${escapeHTML(lecturer.ho_ten)}</strong>
                                            <p style="margin: 0; font-size: 12px;">${escapeHTML(lecturer.khoa || 'N/A')}</p>
                                        </div>
                                    </div>
                                `;
                                listItem.addEventListener("click", () => {
                                    loadLecturers(lecturer.ho_ten);
                                    resultsContainer.classList.remove("active");
                                    document.getElementById("searchInput").value = lecturer.ho_ten;
                                });
                                resultList.appendChild(listItem);
                            });
                            resultsContainer.appendChild(resultList);
                            resultsContainer.classList.add("active");
                        } else {
                            resultsContainer.innerHTML = "<p>Không tìm thấy giảng viên phù hợp.</p>";
                            resultsContainer.classList.add("active");
                        }
                    })
                    .catch(error => {
                        console.error("Search Error:", error);
                        showModal('Có lỗi xảy ra khi tìm kiếm.');
                        resultsContainer.classList.remove("active");
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

        // Show success or error message if set
        <?php if (!empty($success_message)): ?>
            showModal(<?php echo json_encode($success_message); ?>);
        <?php elseif (!empty($error_message)): ?>
            showModal(<?php echo json_encode($error_message); ?>);
        <?php endif; ?>

        // Load lecturers on page load
        document.addEventListener("DOMContentLoaded", () => {
            loadLecturers();
        });
    </script>
</body>

</html>