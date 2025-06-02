<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý tài khoản người dùng</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="./ui_tk_nguoidung.css" />

    <style>
        #searchResults {
            justify-content: space-between;
            align-items: center;
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

        .search-bar {
            position: relative;
        }

        .user-management table {
            width: 100%;
            border-collapse: collapse;
        }

        .user-management th,
        .user-management td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .user-management th {
            background-color: #1e657e;
            color: white;
        }

        .message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            z-index: 1000;
        }

        .message.success {
            background-color: #28a745;
        }

        .message.error {
            background-color: #dc3545;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        /* Pagination styles */
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
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .pagination button:hover {
            background-color: #1e657e;
            color: white;
        }

        .pagination button:disabled {
            background-color: #f0f0f0;
            cursor: not-allowed;
            color: #888;
        }

        .pagination span {
            padding: 8px 12px;
            background-color: #1e657e;
            color: white;
            border-radius: 5px;
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
                <li><i class="fa-solid fa-chart-line"></i> <a href="ui_admin.php">Tổng quan</a></li>
                <li><i class="fa-solid fa-users"></i> <a href="ui_tk_nguoidung.php">Quản lý tài khoản người dùng</a></li>
                <li><i class="fa-solid fa-building-circle-check"></i> <a href="ui_quanly_cty.php">Phê duyệt công ty</a></li>
                <li><i class="fa-solid fa-file-circle-check"></i> <a href="ui_quanlytt.php">Phê duyệt tuyển dụng</a></li>
                <li><i class="fa-solid fa-chalkboard-user"></i> <a href="ui_timkiem_gv_phutrach.php">Tìm kiếm giáo viên phụ trách</a></li>


        </div>
    </div>

    <div class="content" id="content">
        <div class="header">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Tìm kiếm..." />
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

        <div class="user-management">
            <h2>Phân quyền & Danh sách tài khoản</h2>
            <table>
                <thead>
                    <tr>
                        <th>stt</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Quyền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="userList">
                    <tr>
                        <td colspan="5">Đang tải dữ liệu...</td>
                    </tr>
                </tbody>
            </table>
            <div class="pagination" id="pagination">
                <!-- Pagination controls will be dynamically added here -->
            </div>
        </div>
    </div>

    <script>
        // Phân trang
        let currentPage = 1;
        const recordsPerPage = 15;

        function loadUsers(searchTerm = '', page = 1) {
            const url = searchTerm ?
                `../logic_admin/logic_quanly_taikhoan.php?action=search_users&keyword=${encodeURIComponent(searchTerm)}&page=${page}&limit=${recordsPerPage}` :
                `../logic_admin/logic_quanly_taikhoan.php?action=get_users&page=${page}&limit=${recordsPerPage}`;

            console.log('Fetching URL:', url);

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP status ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.success) {
                        const userList = document.getElementById("userList");
                        userList.innerHTML = "";
                        let index = (page - 1) * recordsPerPage + 1;
                        data.users.forEach(user => {
                            userList.innerHTML += `
                                <tr>
                                    <td>${index++}</td>
                                    <td>${user.name}</td>
                                    <td>${user.email}</td>
                                    <td>
                                        <select onchange="updateRole(${user.id}, this.value, '${user.table}')">
                                            <option value="sinh_vien" ${user.role === "sinh_vien" ? "selected" : ""}>Sinh viên</option>
                                            <option value="giang_vien" ${user.role === "giang_vien" ? "selected" : ""}>Giảng viên</option>
                                            <option value="co_so_thuc_tap" ${user.role === "co_so_thuc_tap" ? "selected" : ""}>Cơ sở thực tập</option>
                                            <option value="admin" ${user.role === "admin" ? "selected" : ""}>Admin</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-delete" onclick="deleteUser(${user.id}, '${user.table}')">Xóa</button>
                                    </td>
                                </tr>
                            `;
                        });
                        if (data.users.length === 0) {
                            userList.innerHTML = '<tr><td colspan="5">Không tìm thấy người dùng phù hợp.</td></tr>';
                        }

                        // Render pagination controls
                        renderPagination(data.totalPages, page, searchTerm);
                    } else {
                        showMessage('error', 'Lỗi khi tải danh sách tài khoản: ' + data.error);
                    }
                })
                .catch(error => {
                    showMessage('error', 'Đã có lỗi xảy ra khi tải danh sách tài khoản: ' + error.message);
                    console.error('Fetch error:', error);
                });
        }

        function renderPagination(totalPages, currentPage, searchTerm) {
            const pagination = document.getElementById("pagination");
            pagination.innerHTML = "";

            // Previous button
            const prevButton = document.createElement("button");
            prevButton.textContent = "Trước";
            prevButton.disabled = currentPage === 1;
            prevButton.onclick = () => loadUsers(searchTerm, currentPage - 1);
            pagination.appendChild(prevButton);

            // Page numbers (show limited range for better UX)
            const maxPagesToShow = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
            let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

            if (endPage - startPage + 1 < maxPagesToShow) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                const pageSpan = document.createElement(i === currentPage ? "span" : "button");
                pageSpan.textContent = i;
                if (i !== currentPage) {
                    pageSpan.onclick = () => loadUsers(searchTerm, i);
                }
                pagination.appendChild(pageSpan);
            }

            // Next button
            const nextButton = document.createElement("button");
            nextButton.textContent = "Sau";
            nextButton.disabled = currentPage === totalPages;
            nextButton.onclick = () => loadUsers(searchTerm, currentPage + 1);
            pagination.appendChild(nextButton);
        }

        let debounceTimer;
        document.getElementById("searchInput").addEventListener("keyup", function() {
            clearTimeout(debounceTimer);
            const keyword = this.value.trim();
            const resultsContainer = document.getElementById("searchResults");

            if (keyword === "") {
                resultsContainer.classList.remove("active");
                loadUsers();
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`../logic_admin/logic_quanly_taikhoan.php?action=search_users&keyword=${encodeURIComponent(keyword)}&page=1&limit=${recordsPerPage}`)
                    .then(response => {
                        if (!response.ok) throw new Error("HTTP status " + response.status);
                        return response.json();
                    })
                    .then(data => {
                        resultsContainer.innerHTML = "";
                        if (data.success && data.users.length > 0) {
                            const resultList = document.createElement("ul");
                            data.users.forEach(user => {
                                const listItem = document.createElement("li");
                                listItem.innerHTML = `
                                    <div style="display: flex; align-items: center;">
                                        <div>
                                            <strong>${user.name}</strong>
                                            <p style="margin: 0; font-size: 12px;">${user.email}</p>
                                        </div>
                                    </div>
                                `;
                                listItem.addEventListener("click", () => {
                                    loadUsers(user.name, 1);
                                    resultsContainer.classList.remove("active");
                                    document.getElementById("searchInput").value = user.name;
                                });
                                resultList.appendChild(listItem);
                            });
                            resultsContainer.appendChild(resultList);
                            resultsContainer.classList.add("active");
                        } else {
                            resultsContainer.innerHTML = "<p>Không tìm thấy người dùng phù hợp.</p>";
                            resultsContainer.classList.add("active");
                        }
                    })
                    .catch(error => {
                        console.error("Lỗi tìm kiếm:", error);
                        showMessage('error', 'Có lỗi xảy ra khi tìm kiếm: ' + error.message);
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

        function updateRole(id, newRole, table) {
            fetch('../logic_admin/logic_quanly_taikhoan.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_role&id=${id}&new_role=${newRole}&table=${table}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('success', data.message);
                        loadUsers('', currentPage); // Refresh with current page
                    } else {
                        showMessage('error', data.error);
                    }
                })
                .catch(error => {
                    showMessage('error', 'Đã có lỗi xảy ra khi cập nhật quyền: ' + error.message);
                    console.error('Error:', error);
                });
        }

        function deleteUser(id, table) {
            if (confirm('Bạn có chắc chắn muốn xóa tài khoản này?')) {
                fetch('../logic_admin/logic_quanly_taikhoan.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=delete_user&id=${id}&table=${table}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showMessage('success', data.message);
                            loadUsers('', currentPage);
                        } else {
                            showMessage('error', data.error);
                        }
                    })
                    .catch(error => {
                        showMessage('error', 'Đã có lỗi xảy ra khi xóa tài khoản: ' + error.message);
                        console.error('Error:', error);
                    });
            }
        }

        function showMessage(type, message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.textContent = message;
            document.body.appendChild(messageDiv);
            setTimeout(() => messageDiv.remove(), 3000);
        }

        document.addEventListener("DOMContentLoaded", () => {
            console.log('DOM loaded, calling loadUsers');
            loadUsers();
        });
    </script>
</body>

</html>