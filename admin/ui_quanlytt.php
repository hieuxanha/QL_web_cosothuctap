<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý Tuyển Dụng</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="./giaodien_quanlytt.css" />
    <style>
        .pending-list table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .pending-list th,
        .pending-list td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            word-wrap: break-word;
            white-space: normal;
        }

        .pending-list th:nth-child(1),
        .pending-list td:nth-child(1) {
            width: 3%;
        }

        .pending-list th:nth-child(2),
        .pending-list td:nth-child(2) {
            width: 20%;
        }

        .pending-list th:nth-child(3),
        .pending-list td:nth-child(3) {
            width: 30%;
        }

        .pending-list th:nth-child(4),
        .pending-list td:nth-child(4) {
            width: 15%;
        }

        .pending-list th:nth-child(5),
        .pending-list td:nth-child(5) {
            width: 5%;
        }

        .pending-list th:nth-child(6),
        .pending-list td:nth-child(6) {
            width: 20%;
        }

        .approve {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        .reject {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        .cancel {
            background-color: #ffc107;
            color: black;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        .restore {
            background-color: #17a2b8;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        .delete {
            background-color: #6c757d;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            margin-left: 5px;
            border-radius: 3px;
        }

        .edit {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            margin-left: 5px;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }

        .edit:hover {
            background-color: #0056b3;
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

        /* Style cho modal */
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
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input[type="checkbox"] {
            width: auto;
        }

        .save {
            background-color: #28a745;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        .save:hover {
            background-color: #218838;
        }

        .cancel {
            background-color: #ffc107;
            color: black;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .cancel:hover {
            background-color: #e0a800;
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


            </ul>
        </div>
    </div>

    <div class="content" id="content">
        <div class="header">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Tìm kiếm tin tuyển dụng..." aria-label="Tìm kiếm tin tuyển dụng" />
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

        <div class="pending-list">
            <h3>Danh sách tin tuyển dụng</h3>
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên công ty</th>
                        <th>Tiêu đề tuyển dụng</th>
                        <th>Trạng thái</th>
                        <th>Nổi bật</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody id="recruitmentList">
                    <tr>
                        <td colspan="6"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td>
                    </tr>
                </tbody>
            </table>
            <div class="pagination" id="pagination">
                <!-- Pagination controls will be dynamically added here -->
            </div>
        </div>

        <!-- Modal chỉnh sửa tin tuyển dụng -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">×</span>
                <h2>Chỉnh sửa tin tuyển dụng</h2>
                <form id="editRecruitmentForm">
                    <input type="hidden" name="action" value="edit_recruitment">
                    <input type="hidden" id="edit_ma_tuyen_dung" name="ma_tuyen_dung">
                    <div class="form-group">
                        <label for="edit_tieu_de">Tiêu đề</label>
                        <input type="text" id="edit_tieu_de" name="tieu_de" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_mo_ta">Mô tả</label>
                        <textarea id="edit_mo_ta" name="mo_ta"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_so_luong">Số lượng</label>
                        <input type="number" id="edit_so_luong" name="so_luong" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_han_nop">Hạn nộp</label>
                        <input type="date" id="edit_han_nop" name="han_nop" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_dia_chi">Địa chỉ</label>
                        <input type="text" id="edit_dia_chi" name="dia_chi" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_hinh_thuc">Hình thức</label>
                        <select id="edit_hinh_thuc" name="hinh_thuc" required>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_gioi_tinh">Giới tính</label>
                        <select id="edit_gioi_tinh" name="gioi_tinh" required>
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                            <option value="Không giới hạn">Không giới hạn</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_noi_bat">Nổi bật</label>
                        <input type="checkbox" id="edit_noi_bat" name="noi_bat">
                    </div>
                    <div class="form-group">
                        <label for="edit_khoa">Khoa</label>
                        <select id="edit_khoa" name="khoa">
                            <option value="">Không chọn</option>
                            <option value="kinh_te">Kinh tế</option>
                            <option value="moi_truong">Môi trường</option>
                            <option value="quan_ly_dat_dai">Quản lý đất đai</option>
                            <option value="khi_tuong_thuy_van">Khí tượng thủy văn</option>
                            <option value="trac_dia_ban_do">Trắc địa bản đồ</option>
                            <option value="dia_chat">Địa chất</option>
                            <option value="tai_nguyen_nuoc">Tài nguyên nước</option>
                            <option value="cntt">Công nghệ thông tin</option>
                            <option value="ly_luan_chinh_tri">Lý luận chính trị</option>
                            <option value="bien_hai_dao">Biển hải đảo</option>
                            <option value="khoa_hoc_dai_cuong">Khoa học đại cương</option>
                            <option value="the_chat_quoc_phong">Thể chất quốc phòng</option>
                            <option value="bo_mon_luat">Bộ môn luật</option>
                            <option value="bien_doi_khi_hau">Biến đổi khí hậu</option>
                            <option value="ngoai_ngu">Ngoại ngữ</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_trinh_do">Trình độ</label>
                        <select id="edit_trinh_do" name="trinh_do">
                            <option value="">Không chọn</option>
                            <option value="Không yêu cầu">Không yêu cầu</option>
                            <option value="Trung cấp">Trung cấp</option>
                            <option value="Cao đẳng">Cao đẳng</option>
                            <option value="Đại học">Đại học</option>
                            <option value="Thạc sĩ">Thạc sĩ</option>
                            <option value="Tiến sĩ">Tiến sĩ</option>
                        </select>
                    </div>
                    <button type="submit" class="save">Lưu thay đổi</button>
                    <button type="button" class="cancel" onclick="closeModal()">Hủy</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        const recordsPerPage = 10;

        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("collapsed");
            document.getElementById("content").classList.toggle("collapsed");
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

        function getActionButtons(ma_tuyen_dung, trang_thai) {
            let buttons = `<button class="edit" onclick="openEditModal('${ma_tuyen_dung}')">Sửa</button>`;
            if (trang_thai === 'Đang chờ') {
                buttons += `
                    <button class="approve" onclick="updateStatus('${ma_tuyen_dung}', 'approve')">Duyệt</button>
                    <button class="reject" onclick="updateStatus('${ma_tuyen_dung}', 'reject')">Từ chối</button>
                    <button class="delete" onclick="deleteTuyenDung('${ma_tuyen_dung}')">Xóa</button>
                `;
            } else if (trang_thai === 'Đã duyệt') {
                buttons += `
                    <button class="cancel" onclick="updateStatus('${ma_tuyen_dung}', 'cancel')">Hủy duyệt</button>
                    <button class="reject" onclick="updateStatus('${ma_tuyen_dung}', 'reject')">Từ chối</button>
                    <button class="delete" onclick="deleteTuyenDung('${ma_tuyen_dung}')">Xóa</button>
                `;
            } else if (trang_thai === 'Bị từ chối') {
                buttons += `
                    <button class="restore" onclick="updateStatus('${ma_tuyen_dung}', 'restore')">Khôi phục</button>
                    <button class="approve" onclick="updateStatus('${ma_tuyen_dung}', 'approve')">Duyệt</button>
                    <button class="delete" onclick="deleteTuyenDung('${ma_tuyen_dung}')">Xóa</button>
                `;
            }
            return buttons;
        }

        function loadRecruitments(searchTerm = '', page = 1) {
            const tableBody = document.getElementById("recruitmentList");
            tableBody.innerHTML = '<tr><td colspan="6"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>';

            const url = searchTerm ?
                `../logic_admin/logic_duyet_tuyendung.php?action=search_recruitments&keyword=${encodeURIComponent(searchTerm)}&page=${page}&limit=${recordsPerPage}` :
                `../logic_admin/logic_duyet_tuyendung.php?action=get_recruitments&page=${page}&limit=${recordsPerPage}`;

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP status ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    tableBody.innerHTML = '';
                    if (data.success && data.data.recruitments.length > 0) {
                        let stt = (page - 1) * recordsPerPage + 1;
                        data.data.recruitments.forEach(recruitment => {
                            const row = document.createElement('tr');
                            row.setAttribute('data-ma-tuyen-dung', recruitment.ma_tuyen_dung);
                            const trang_thai = recruitment.trang_thai || 'Đang chờ';
                            const noi_bat = recruitment.noi_bat ? 'Có' : 'Không';
                            row.innerHTML = `
                                <td>${stt++}</td>
                                <td data-tooltip="${escapeHTML(recruitment.ten_cong_ty)}">${escapeHTML(recruitment.ten_cong_ty)}</td>
                                <td data-tooltip="${escapeHTML(recruitment.tieu_de)}">${escapeHTML(recruitment.tieu_de)}</td>
                                <td class="trang-thai">${escapeHTML(trang_thai)}</td>
                                <td>${noi_bat}</td>
                                <td class="action-buttons">${getActionButtons(recruitment.ma_tuyen_dung, trang_thai)}</td>
                            `;
                            tableBody.appendChild(row);
                        });
                        // Render pagination controls
                        renderPagination(data.data.totalPages, page, searchTerm);
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="6">Không tìm thấy tin tuyển dụng nào</td></tr>';
                        renderPagination(0, page, searchTerm);
                    }
                })
                .catch(error => {
                    tableBody.innerHTML = '<tr><td colspan="6">Lỗi khi tải dữ liệu</td></tr>';
                    showMessage('error', 'Không thể tải danh sách tin tuyển dụng. Vui lòng thử lại.');
                    console.error('Error:', error);
                });
        }

        function renderPagination(totalPages, currentPage, searchTerm) {
            const pagination = document.getElementById("pagination");
            pagination.innerHTML = "";

            // Previous button
            const prevButton = document.createElement("button");
            prevButton.textContent = "Trước";
            prevButton.disabled = currentPage === 1;
            prevButton.onclick = () => loadRecruitments(searchTerm, currentPage - 1);
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
                    pageSpan.onclick = () => loadRecruitments(searchTerm, i);
                }
                pagination.appendChild(pageSpan);
            }

            // Next button
            const nextButton = document.createElement("button");
            nextButton.textContent = "Sau";
            nextButton.disabled = currentPage === totalPages || totalPages === 0;
            nextButton.onclick = () => loadRecruitments(searchTerm, currentPage + 1);
            pagination.appendChild(nextButton);
        }

        let debounceTimer;
        document.getElementById("searchInput").addEventListener("keyup", function() {
            clearTimeout(debounceTimer);
            const keyword = this.value.trim();
            const resultsContainer = document.getElementById("searchResults");

            if (keyword === "") {
                resultsContainer.classList.remove("active");
                loadRecruitments();
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`../logic_admin/logic_duyet_tuyendung.php?action=search_recruitments&keyword=${encodeURIComponent(keyword)}&page=1&limit=${recordsPerPage}`)
                    .then(response => {
                        if (!response.ok) throw new Error("HTTP status " + response.status);
                        return response.json();
                    })
                    .then(data => {
                        resultsContainer.innerHTML = "";
                        if (data.success && data.data.recruitments.length > 0) {
                            const resultList = document.createElement("ul");
                            data.data.recruitments.forEach(recruitment => {
                                const listItem = document.createElement("li");
                                listItem.innerHTML = `
                                    <div style="display: flex; align-items: center;">
                                        <div>
                                            <strong>${escapeHTML(recruitment.tieu_de)}</strong>
                                            <p style="margin: 0; font-size: 12px;">${escapeHTML(recruitment.ten_cong_ty)}</p>
                                        </div>
                                    </div>
                                `;
                                listItem.addEventListener("click", () => {
                                    loadRecruitments(recruitment.tieu_de, 1);
                                    resultsContainer.classList.remove("active");
                                    document.getElementById("searchInput").value = recruitment.tieu_de;
                                });
                                resultList.appendChild(listItem);
                            });
                            resultsContainer.appendChild(resultList);
                            resultsContainer.classList.add("active");
                        } else {
                            resultsContainer.innerHTML = "<p>Không tìm thấy tin tuyển dụng phù hợp.</p>";
                            resultsContainer.classList.add("active");
                        }
                    })
                    .catch(error => {
                        console.error("Lỗi tìm kiếm:", error);
                        showMessage('error', 'Có lỗi xảy ra khi tìm kiếm.');
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

        function showMessage(type, message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.textContent = message;
            document.querySelector('.content').prepend(messageDiv);
            setTimeout(() => messageDiv.remove(), 3000);
        }

        function updateStatus(ma_tuyen_dung, action) {
            const row = document.querySelector(`tr[data-ma-tuyen-dung="${ma_tuyen_dung}"]`);
            const statusCell = row.querySelector('.trang-thai');
            const buttonsCell = row.querySelector('.action-buttons');

            buttonsCell.innerHTML += '<span class="loading">Đang xử lý...</span>';

            fetch('../logic_admin/logic_duyet_tuyendung.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `ma_tuyen_dung=${encodeURIComponent(ma_tuyen_dung)}&action=${action}`
                })
                .then(response => response.json())
                .then(data => {
                    buttonsCell.querySelector('.loading')?.remove();
                    if (data.success) {
                        statusCell.textContent = data.data.trang_thai;
                        buttonsCell.innerHTML = getActionButtons(ma_tuyen_dung, data.data.trang_thai);
                        showMessage('success', data.data.message);
                    } else {
                        showMessage('error', data.error || 'Lỗi khi xử lý yêu cầu!');
                    }
                })
                .catch(error => {
                    buttonsCell.querySelector('.loading')?.remove();
                    showMessage('error', 'Đã có lỗi xảy ra!');
                    console.error('Lỗi:', error);
                });
        }

        function deleteTuyenDung(ma_tuyen_dung) {
            if (!confirm('Bạn có chắc chắn muốn xóa tin tuyển dụng này?')) {
                return;
            }

            const row = document.querySelector(`tr[data-ma-tuyen-dung="${ma_tuyen_dung}"]`);
            const buttonsCell = row.querySelector('.action-buttons');

            buttonsCell.innerHTML += '<span class="loading">Đang xóa...</span>';

            fetch('../logic_admin/logic_xoa_tuyendung.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `ma_tuyen_dung=${encodeURIComponent(ma_tuyen_dung)}`
                })
                .then(response => response.json())
                .then(data => {
                    buttonsCell.querySelector('.loading')?.remove();
                    if (data.success) {
                        row.remove();
                        showMessage('success', 'Xóa tin tuyển dụng thành công!');
                        loadRecruitments('', currentPage); // Refresh with current page
                    } else {
                        showMessage('error', data.error || 'Lỗi khi xóa tin tuyển dụng!');
                    }
                })
                .catch(error => {
                    buttonsCell.querySelector('.loading')?.remove();
                    showMessage('error', 'Đã có lỗi xảy ra!');
                    console.error('Lỗi:', error);
                });
        }

        function openEditModal(ma_tuyen_dung) {
            fetch(`../logic_admin/logic_duyet_tuyendung.php?action=get_recruitment&ma_tuyen_dung=${encodeURIComponent(ma_tuyen_dung)}`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP status ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const recruitment = data.data.recruitment;
                        const fields = {
                            'edit_ma_tuyen_dung': recruitment.ma_tuyen_dung,
                            'edit_tieu_de': recruitment.tieu_de,
                            'edit_mo_ta': recruitment.mo_ta || '',
                            'edit_so_luong': recruitment.so_luong,
                            'edit_han_nop': recruitment.han_nop,
                            'edit_dia_chi': recruitment.dia_chi,
                            'edit_hinh_thuc': recruitment.hinh_thuc,
                            'edit_gioi_tinh': recruitment.gioi_tinh,
                            'edit_khoa': recruitment.khoa || '',
                            'edit_trinh_do': recruitment.trinh_do || ''
                        };

                        for (const [id, value] of Object.entries(fields)) {
                            const element = document.getElementById(id);
                            if (element) {
                                element.value = value;
                            } else {
                                console.error(`Element with ID ${id} not found`);
                                showMessage('error', `Không tìm thấy trường ${id}`);
                                return;
                            }
                        }

                        // Handle checkbox for noi_bat
                        const noiBatCheckbox = document.getElementById('edit_noi_bat');
                        noiBatCheckbox.checked = recruitment.noi_bat == 1;

                        document.getElementById('editModal').style.display = 'block';
                    } else {
                        showMessage('error', data.error || 'Không thể tải thông tin tin tuyển dụng!');
                    }
                })
                .catch(error => {
                    showMessage('error', 'Đã có lỗi xảy ra khi tải thông tin tin tuyển dụng!');
                    console.error('Lỗi:', error);
                });
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
            document.getElementById('editRecruitmentForm').reset();
        }

        document.getElementById('editRecruitmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Convert noi_bat checkbox to 1 or 0
            formData.set('noi_bat', document.getElementById('edit_noi_bat').checked ? '1' : '0');

            fetch('../logic_admin/logic_duyet_tuyendung.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal();
                        loadRecruitments('', currentPage); // Refresh with current page
                        showMessage('success', 'Chỉnh sửa tin tuyển dụng thành công!');
                    } else {
                        showMessage('error', data.error || 'Lỗi khi chỉnh sửa tin tuyển dụng!');
                    }
                })
                .catch(error => {
                    showMessage('error', 'Đã có lỗi xảy ra khi lưu thông tin!');
                    console.error('Lỗi:', error);
                });
        });

        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeModal();
            }
        };

        document.addEventListener("DOMContentLoaded", () => {
            loadRecruitments();
        });
    </script>
</body>

</html>