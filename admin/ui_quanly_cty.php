<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý công ty</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="../admin/ui_quanly_cty.css" />
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
            width: 5%;
        }

        .pending-list th:nth-child(2),
        .pending-list td:nth-child(2) {
            width: 25%;
        }

        .pending-list th:nth-child(3),
        .pending-list td:nth-child(3) {
            width: 25%;
        }

        .pending-list th:nth-child(4),
        .pending-list td:nth-child(4) {
            width: 15%;
        }

        .pending-list th:nth-child(5),
        .pending-list td:nth-child(5) {
            width: 30%;
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
        .form-group textarea {
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

        .form-group input[type="file"] {
            padding: 3px;
        }

        .form-group img {
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 5px;
            object-fit: cover;
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
                <input type="text" id="searchInput" placeholder="Tìm kiếm công ty..." aria-label="Tìm kiếm công ty" />
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
            <h2>Danh sách công ty</h2>
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên cơ sở</th>
                        <th>Địa chỉ</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody id="companyList">
                    <tr>
                        <td colspan="5"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal chỉnh sửa công ty -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">×</span>
                <h2>Chỉnh sửa thông tin công ty</h2>
                <form id="editCompanyForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_company">
                    <input type="hidden" id="edit_stt_cty" name="stt_cty">
                    <div class="form-group">
                        <label for="edit_ten_cong_ty">Tên công ty</label>
                        <input type="text" id="edit_ten_cong_ty" name="ten_cong_ty" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_dia_chi">Địa chỉ</label>
                        <input type="text" id="edit_dia_chi" name="dia_chi" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_so_dien_thoai">Số điện thoại</label>
                        <input type="text" id="edit_so_dien_thoai" name="so_dien_thoai" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Email</label>
                        <input type="email" id="edit_email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_gioi_thieu">Giới thiệu</label>
                        <textarea id="edit_gioi_thieu" name="gioi_thieu"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_quy_mo">Quy mô</label>
                        <input type="text" id="edit_quy_mo" name="quy_mo">
                    </div>
                    <div class="form-group">
                        <label for="edit_linh_vuc">Lĩnh vực</label>
                        <input type="text" id="edit_linh_vuc" name="linh_vuc">
                    </div>
                    <div class="form-group">
                        <label for="edit_logo">Logo</label>
                        <img id="current_logo" src="" style="display: none;">
                        <input type="file" id="edit_logo" name="logo" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="edit_anh_bia">Ảnh bìa</label>
                        <img id="current_anh_bia" src="" style="display: none;">
                        <input type="file" id="edit_anh_bia" name="anh_bia" accept="image/*">
                    </div>
                    <button type="submit" class="save">Lưu thay đổi</button>
                    <button type="button" class="cancel" onclick="closeModal()">Hủy</button>
                </form>
            </div>
        </div>
    </div>

    <script>
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

        function getActionButtons(stt_cty, trang_thai) {
            let buttons = `<button class="edit" onclick="openEditModal(${stt_cty})">Sửa</button>`;
            // let buttons = `<button class="edit" onclick="openEditModal(${stt_cty})">Xem</button>`;

            if (trang_thai === 'Đang chờ') {
                buttons += `
                    <button class="approve" onclick="updateStatus(${stt_cty}, 'approve')">Duyệt</button>
                    <button class="reject" onclick="updateStatus(${stt_cty}, 'reject')">Từ chối</button>
                    <button class="delete" onclick="deleteCongTy(${stt_cty})">Xóa</button>
                `;
            } else if (trang_thai === 'Đã duyệt') {
                buttons += `
                    <button class="cancel" onclick="updateStatus(${stt_cty}, 'cancel')">Hủy duyệt</button>
                    <button class="reject" onclick="updateStatus(${stt_cty}, 'reject')">Từ chối</button>
                    <button class="delete" onclick="deleteCongTy(${stt_cty})">Xóa</button>
                `;
            } else if (trang_thai === 'Bị từ chối') {
                buttons += `
                    <button class="restore" onclick="updateStatus(${stt_cty}, 'restore')">Khôi phục</button>
                    <button class="approve" onclick="updateStatus(${stt_cty}, 'approve')">Duyệt</button>
                    <button class="delete" onclick="deleteCongTy(${stt_cty})">Xóa</button>
                `;
            }
            return buttons;
        }

        function loadCompanies(searchTerm = '') {
            const tableBody = document.getElementById("companyList");
            tableBody.innerHTML = '<tr><td colspan="5"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>';

            const url = searchTerm ?
                `../logic_admin/logic_duyet_cty.php?action=search_companies&keyword=${encodeURIComponent(searchTerm)}` :
                '../logic_admin/logic_duyet_cty.php?action=get_companies';

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP status ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    tableBody.innerHTML = '';
                    if (data.success && data.data.companies.length > 0) {
                        let stt = 1;
                        data.data.companies.forEach(company => {
                            const row = document.createElement('tr');
                            row.setAttribute('data-stt-cty', company.stt_cty);
                            const trang_thai = company.trang_thai || 'Đang chờ';
                            row.innerHTML = `
                                <td>${stt++}</td>
                                <td>${escapeHTML(company.ten_cong_ty)}</td>
                                <td>${escapeHTML(company.dia_chi)}</td>
                                <td class="trang-thai">${escapeHTML(trang_thai)}</td>
                                <td class="action-buttons">${getActionButtons(company.stt_cty, trang_thai)}</td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="5">Không tìm thấy công ty nào</td></tr>';
                    }
                })
                .catch(error => {
                    tableBody.innerHTML = '<tr><td colspan="5">Lỗi khi tải dữ liệu</td></tr>';
                    showMessage('error', 'Không thể tải danh sách công ty. Vui lòng thử lại.');
                    console.error('Error:', error);
                });
        }

        let debounceTimer;
        document.getElementById("searchInput").addEventListener("keyup", function() {
            clearTimeout(debounceTimer);
            const keyword = this.value.trim();
            const resultsContainer = document.getElementById("searchResults");

            if (keyword === "") {
                resultsContainer.classList.remove("active");
                loadCompanies();
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`../logic_admin/logic_duyet_cty.php?action=search_companies&keyword=${encodeURIComponent(keyword)}`)
                    .then(response => {
                        if (!response.ok) throw new Error("HTTP status " + response.status);
                        return response.json();
                    })
                    .then(data => {
                        resultsContainer.innerHTML = "";
                        if (data.success && data.data.companies.length > 0) {
                            const resultList = document.createElement("ul");
                            data.data.companies.forEach(company => {
                                const listItem = document.createElement("li");
                                listItem.innerHTML = `
                                    <div style="display: flex; align-items: center;">
                                        <div>
                                            <strong>${escapeHTML(company.ten_cong_ty)}</strong>
                                            <p style="margin: 0; font-size: 12px;">${escapeHTML(company.dia_chi)}</p>
                                        </div>
                                    </div>
                                `;
                                listItem.addEventListener("click", () => {
                                    loadCompanies(company.ten_cong_ty);
                                    resultsContainer.classList.remove("active");
                                    document.getElementById("searchInput").value = company.ten_cong_ty;
                                });
                                resultList.appendChild(listItem);
                            });
                            resultsContainer.appendChild(resultList);
                            resultsContainer.classList.add("active");
                        } else {
                            resultsContainer.innerHTML = "<p>Không tìm thấy công ty phù hợp.</p>";
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

        function updateStatus(stt_cty, action) {
            const row = document.querySelector(`tr[data-stt-cty="${stt_cty}"]`);
            const statusCell = row.querySelector('.trang-thai');
            const buttonsCell = row.querySelector('.action-buttons');

            buttonsCell.innerHTML += '<span class="loading">Đang xử lý...</span>';

            fetch('../logic_admin/logic_duyet_cty.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `stt_cty=${stt_cty}&action=${action}`
                })
                .then(response => response.json())
                .then(data => {
                    buttonsCell.querySelector('.loading')?.remove();
                    if (data.success) {
                        statusCell.textContent = data.data.trang_thai;
                        buttonsCell.innerHTML = getActionButtons(stt_cty, data.data.trang_thai);
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

        function deleteCongTy(stt_cty) {
            if (!confirm('Bạn có chắc chắn muốn xóa công ty này?')) {
                return;
            }

            const row = document.querySelector(`tr[data-stt-cty="${stt_cty}"]`);
            const buttonsCell = row.querySelector('.action-buttons');

            buttonsCell.innerHTML += '<span class="loading">Đang xóa...</span>';

            fetch('../logic_admin/logic_xoa_cty.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `stt_cty=${stt_cty}`
                })
                .then(response => response.json())
                .then(data => {
                    buttonsCell.querySelector('.loading')?.remove();
                    if (data.success) {
                        row.remove();
                        showMessage('success', 'Xóa công ty thành công!');
                    } else {
                        showMessage('error', data.error || 'Lỗi khi xóa công ty!');
                    }
                })
                .catch(error => {
                    buttonsCell.querySelector('.loading')?.remove();
                    showMessage('error', 'Đã có lỗi xảy ra!');
                    console.error('Lỗi:', error);
                });
        }

        // Hàm mở modal và lấy thông tin công ty
        function openEditModal(stt_cty) {
            console.log('Opening modal for company:', stt_cty);
            fetch(`../logic_admin/logic_duyet_cty.php?action=get_company&stt_cty=${stt_cty}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) throw new Error(`HTTP status ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        const company = data.data.company;
                        const fields = {
                            'edit_stt_cty': company.stt_cty,
                            'edit_ten_cong_ty': company.ten_cong_ty,
                            'edit_dia_chi': company.dia_chi,
                            'edit_so_dien_thoai': company.so_dien_thoai,
                            'edit_email': company.email,
                            'edit_gioi_thieu': company.gioi_thieu || '',
                            'edit_quy_mo': company.quy_mo || '',
                            'edit_linh_vuc': company.linh_vuc || ''
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

                        // Hiển thị ảnh logo hiện tại
                        const logoImg = document.getElementById('current_logo');
                        if (company.logo && company.logo !== '') {
                            logoImg.src = '../sinh_vien/uploads/' + company.logo;
                            logoImg.style.display = 'block';
                        } else {
                            logoImg.src = '';
                            logoImg.style.display = 'none';
                        }

                        // Hiển thị ảnh bìa hiện tại
                        const anhBiaImg = document.getElementById('current_anh_bia');
                        if (company.anh_bia && company.anh_bia !== '') {
                            anhBiaImg.src = '../sinh_vien/uploads/' + company.anh_bia;
                            anhBiaImg.style.display = 'block';
                        } else {
                            anhBiaImg.src = '';
                            anhBiaImg.style.display = 'none';
                        }

                        document.getElementById('editModal').style.display = 'block';
                    } else {
                        showMessage('error', data.error || 'Không thể tải thông tin công ty!');
                    }
                })
                .catch(error => {
                    showMessage('error', 'Đã có lỗi xảy ra khi tải thông tin công ty!');
                    console.error('Lỗi:', error);
                });
        }

        // Hàm đóng modal
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
            document.getElementById('editCompanyForm').reset();
            // Ẩn ảnh preview khi đóng modal
            document.getElementById('current_logo').style.display = 'none';
            document.getElementById('current_anh_bia').style.display = 'none';
        }

        // Xử lý submit form chỉnh sửa
        document.getElementById('editCompanyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('../logic_admin/logic_duyet_cty.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal();
                        loadCompanies();
                        showMessage('success', 'Chỉnh sửa công ty thành công!');
                    } else {
                        showMessage('error', data.error || 'Lỗi khi chỉnh sửa công ty!');
                    }
                })
                .catch(error => {
                    showMessage('error', 'Đã có lỗi xảy ra khi lưu thông tin!');
                    console.error('Lỗi:', error);
                });
        });

        // Đóng modal khi nhấp ra ngoài
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeModal();
            }
        };

        document.addEventListener("DOMContentLoaded", () => {
            loadCompanies();
        });
    </script>
</body>

</html>