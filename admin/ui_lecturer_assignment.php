<?php
session_start();
require '../db.php'; // Database connection

// Initialize messages
$success_message = "";
$error_message = "";

// Fetch lecturers with the count of classes and students they are responsible for
$lecturers = [];
$sql_lecturers = "SELECT g.so_hieu_giang_vien, g.ho_ten, g.khoa, g.email, g.so_dien_thoai,
                         COUNT(DISTINCT s.lop) AS so_lop,
                         COUNT(s.ma_sinh_vien) AS so_sinh_vien
                  FROM giang_vien g
                  LEFT JOIN sinh_vien s ON g.so_hieu_giang_vien = s.so_hieu
                  GROUP BY g.so_hieu_giang_vien, g.ho_ten, g.khoa, g.email, g.so_dien_thoai
                  ORDER BY g.ho_ten";
$result_lecturers = $conn->query($sql_lecturers);
if ($result_lecturers && $result_lecturers->num_rows > 0) {
    while ($row = $result_lecturers->fetch_assoc()) {
        $lecturers[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Danh Sách Giảng Viên Phụ Trách</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="./ui_timkiem_gv_phutrach.css" />
    <style>
        .container {
            padding: 20px;
        }

        .table-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .table-container h3 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
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
                <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_admin.php">Trang chủ admin</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_tk_nguoidung.php">Quản lý tài khoản người dùng</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_quanly_cty.php">Phê duyệt công ty</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_quanlytt.php">Phê duyệt tuyển dụng</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_timkiem_gv_phutrach.php">Tìm kiếm giáo viên phụ trách</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_lecturer_assignment.php">Danh sách giảng viên phụ trách</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_thongbao.php">Thông báo</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_baotri.php">Bảo trì hệ thống</a></li>
                <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_coso.php">Cơ sở</a></li>
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

        <div class="container">
            <div class="table-container">
                <h3>Danh sách giảng viên phụ trách</h3>
                <?php if ($error_message): ?>
                    <p style="color: red;"><?php echo $error_message; ?></p>
                <?php endif; ?>
                <table>
                    <thead>
                        <tr>
                            <th>Số hiệu</th>
                            <th>Họ tên</th>
                            <th>Khoa</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Số lớp phụ trách</th>
                            <th>Số sinh viên phụ trách</th>
                        </tr>
                    </thead>
                    <tbody id="lecturerList">
                        <?php if (!empty($lecturers)): ?>
                            <?php foreach ($lecturers as $lecturer): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($lecturer['so_hieu_giang_vien']); ?></td>
                                    <td><?php echo htmlspecialchars($lecturer['ho_ten']); ?></td>
                                    <td><?php echo htmlspecialchars($lecturer['khoa'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($lecturer['email']); ?></td>
                                    <td><?php echo htmlspecialchars($lecturer['so_dien_thoai'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($lecturer['so_lop']); ?></td>
                                    <td><?php echo htmlspecialchars($lecturer['so_sinh_vien']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">Không có giảng viên nào được tìm thấy.</td>
                            </tr>
                        <?php endif; ?>
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

        function loadLecturers(searchTerm = '') {
            const tableBody = document.getElementById("lecturerList");
            tableBody.innerHTML = '<tr><td colspan="7"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>';

            const url = searchTerm ?
                `../logic_giangvien/logic_timkiem_gv.php?action=search_lecturers&keyword=${encodeURIComponent(searchTerm)}` :
                '../logic_giangvien/logic_timkiem_gv.php?action=get_lecturers';

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP status ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Fetched data:', data); // Debug the response
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
                                <td>${lecturer.so_lop || 0}</td>
                                <td>${lecturer.so_sinh_vien || 0}</td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="7">Không tìm thấy giảng viên nào</td></tr>';
                    }
                })
                .catch(error => {
                    tableBody.innerHTML = '<tr><td colspan="7">Lỗi khi tải dữ liệu: ' + error.message + '</td></tr>';
                    showModal('Không thể tải danh sách giảng viên. Vui lòng thử lại. Lỗi: ' + error.message);
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
                        if (!response.ok) throw new Error("HTTP status " + response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Search data:', data); // Debug the search response
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
                        showModal('Có lỗi xảy ra khi tìm kiếm: ' + error.message);
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

        document.addEventListener("DOMContentLoaded", () => {
            loadLecturers();
        });
    </script>
</body>

</html>