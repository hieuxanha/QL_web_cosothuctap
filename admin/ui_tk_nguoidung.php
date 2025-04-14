



<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý tài khoản người dùng</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap"rel="stylesheet"/>
      <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="./ui_tk_nguoidung.css" />
   
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
             <li><i class="fa-brands fa-windows"></i><a href="../admin/ui_timkiem_gv_phutrach.php">Tìm kiếm giáo viên phụ trách</a></a></li>

             <li><i class="fa-brands fa-windows"></i><a href="#">Thông báo</a></li>
             <li><i class="fa-brands fa-windows"></i><a href="#">Bảo trì hệ thống</a></li>
             <li><i class="fa-brands fa-windows"></i><a href="#">Cơ sở</a></li>

            </ul>
        </div>
    </div>

    <div class="content" id="content">
        <div class="header">
            <div class="search-bar">
                <input type="text" placeholder="Tìm kiếm..." />
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    viewBox="0 0 24 24"
                >
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <div class="profile">
                <span>Nguyễn Thị My</span>
                <img src="profile.jpg" alt="Ảnh đại diện" />
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
                    <!-- Dữ liệu sẽ được load bằng JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const content = document.getElementById("content");
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("collapsed");
        }

        // Load danh sách người dùng từ CSDL
       // Load danh sách người dùng từ CSDL
       function loadUsers() {
            fetch('../logic_admin/logic_quanly_taikhoan.php?action=get_users')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const userList = document.getElementById("userList");
                        userList.innerHTML = "";
                        let index = 1; // Biến đếm STT giả
                        data.users.forEach(user => {
                            userList.innerHTML += `
                                <tr>
                                    <td>${index++}</td> <!-- Sử dụng STT giả -->
                                    <td>${user.name}</td>
                                    <td>${user.email}</td>
                                    <td>
                                        <select onchange="updateRole(${user.id}, this.value, '${user.table}')">
                                            <option value="sinh_vien" ${user.role === "sinh_vien" ? "selected" : ""}>Sinh viên</option>
                                            <option value="giang_vien" ${user.role === "giang_vien" ? "selected" : ""}>Giảng viên</option>
                                            <option value="co_so_thuc_tap" ${user.role === "co_so_thuc_tap" ? "selected" : ""}>Cơ sở thực tập</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-delete" onclick="deleteUser(${user.id}, '${user.table}')">Xóa</button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        showMessage('error', 'Lỗi khi tải danh sách tài khoản: ' + data.error);
                    }
                })
                .catch(error => {
                    showMessage('error', 'Đã có lỗi xảy ra khi tải danh sách tài khoản!');
                    console.error('Error:', error);
                });
        }

        // Cập nhật quyền người dùng
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
                    } else {
                        showMessage('error', data.error);
                    }
                })
                .catch(error => {
                    showMessage('error', 'Đã có lỗi xảy ra khi cập nhật quyền!');
                    console.error('Error:', error);
                });
        }

        // Xóa người dùng
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
                            loadUsers(); // Tải lại danh sách sau khi xóa
                        } else {
                            showMessage('error', data.error);
                        }
                    })
                    .catch(error => {
                        showMessage('error', 'Đã có lỗi xảy ra khi xóa tài khoản!');
                        console.error('Error:', error);
                    });
            }
        }

        // Hiển thị thông báo
        function showMessage(type, message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.textContent = message;
            document.querySelector('.content').prepend(messageDiv);
            setTimeout(() => messageDiv.remove(), 3000);
        }

        // Load danh sách khi trang tải
        document.addEventListener("DOMContentLoaded", loadUsers);
    </script>
</body>
</html>
