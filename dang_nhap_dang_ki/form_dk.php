<?php
session_start();
require '../db.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng ký - TopCV</title>
    <link rel="stylesheet" href="./dn_dk.css" />
    <style>
        /* CSS cho modal */
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

        button {
            padding: 10px 20px;
            margin-top: 10px;
            border: none;
            border-radius: 8px;
            background: #4CAF50;
            color: white;
            cursor: pointer;
        }

        button:hover {
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
    </style>
</head>

<body>
    <div class="container">

        <!-- Cột bên trái: Form đăng ký -->
        <div class="left">
            <h2>Chào mừng bạn đến</h2>
            <p>Cùng xây dựng một hồ sơ nổi bật và nhận được các cơ hội sự nghiệp lý tưởng</p>

            <form id="registerForm" action="./logic_dang_ky.php" method="POST" onsubmit="return validateForm()">
                <div class="input-group">
                    <label>Họ và tên</label>
                    <input type="text" name="ho_ten" placeholder="Nhập họ tên" required />
                </div>
                <div class="input-group" id="coso-group" style="display: none;">
                    <label>Mã cơ sở</label>
                    <input type="text" name="ma_co_so" placeholder="Nhập mã cơ sở" />
                </div>
                <div class="input-group" id="tenCosoGroup" style="display: none;">
                    <label>Tên cơ sở</label>
                    <input type="text" name="ten_co_so" placeholder="Nhập tên cơ sở" />
                </div>
                <div class="input-group" id="masv-group" style="display: none;">
                    <label>Mã sinh viên</label>
                    <input type="number" name="ma_sinh_vien" placeholder="Nhập mã sinh viên" />
                </div>
                <div class="input-group" id="magv-group" style="display: none;">
                    <label>Số hiệu giảng viên</label>
                    <input type="number" name="so_hieu_giangvien" placeholder="Số hiệu giảng viên" />
                </div>

                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Nhập email" required />
                </div>
                <div class="input-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" placeholder="Nhập mật khẩu" required />
                </div>
                <div class="input-group">
                    <label>Xác nhận mật khẩu</label>
                    <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required />
                </div>
                <div class="input-group">
                    <select id="khoa" name="khoa" class="khoakhoa">
                        <option value="" disabled selected>Chọn danh mục</option>
                        <option value="kinh_te">Khoa Kinh tế</option>
                        <option value="moi_truong">Khoa Môi trường</option>
                        <option value="quan_ly_dat_dai">Khoa Quản lý đất đai</option>
                        <option value="khi_tuong_thuy_van">Khoa Khí tượng thủy văn</option>
                        <option value="trac_dia_ban_do">Khoa Trắc địa bản đồ và Thông tin địa lý</option>
                        <option value="dia_chat">Khoa Địa chất</option>
                        <option value="tai_nguyen_nuoc">Khoa Tài nguyên nước</option>
                        <option value="cntt">Khoa Công nghệ thông tin</option>
                        <option value="ly_luan_chinh_tri">Khoa Lý luận chính trị</option>
                        <option value="bien_hai_dao">Khoa Khoa học Biển và Hải đảo</option>
                        <option value="khoa_hoc_dai_cuong">Khoa Khoa học Đại cương</option>
                        <option value="the_chat_quoc_phong">Khoa Giáo dục thể chất và Giáo dục quốc phòng</option>
                        <option value="bo_mon_luat">Bộ môn Luật |</option>
                        <option value="bien_doi_khi_hau">Bộ môn Biến đổi khí hậu và PT bền vững</option>
                        <option value="ngoai_ngu">Bộ môn Ngoại ngữ</option>
                    </select>
                </div>
                <div class="checkbox-group">
                    <input type="radio" id="sinhvien" name="user_type" value="sinhvien" required onclick="toggleSelect(true, this.value)" />
                    <label for="sinhvien">Sinh viên</label>
                    <input type="radio" id="giangvien" name="user_type" value="giangvien" required onclick="toggleSelect(true, this.value)" />
                    <label for="giangvien">Giảng viên</label>
                    <input type="radio" id="coso" name="user_type" value="coso" required onclick="toggleSelect(false, this.value)" />
                    <label for="coso">Cơ sở thực tập</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="agree" name="agree_terms" required />
                    <label for="agree">Tôi đã đọc và đồng ý với <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách bảo mật</a></label>
                </div>
                <button type="submit" class="btn">Đăng ký</button>
                <p>Bạn đã có tài khoản? <a href="./form_dn.php">Đăng nhập ngay</a></p>
            </form>
        </div>
        <!-- Cột bên phải: Hình ảnh -->
        <div class="right">
            <img src="../img/dk_dn.jpg" alt="Banner" />
        </div>
    </div>

    <!-- Modal thông báo -->
    <div id="customModal" class="modal" style="display: none;">
        <div class="modal-content" id="modalContent">
            <!-- Nội dung sẽ được chèn động -->
        </div>
    </div>

    <script>
        // Kiểm tra checkbox trước khi gửi form
        function validateForm() {
            const checkBox = document.getElementById('agree');
            if (!checkBox.checked) {
                showModal("Bạn phải đồng ý với Điều khoản dịch vụ và Chính sách bảo mật!");
                return false; // Ngăn form gửi đi
            }
            return true; // Cho phép gửi form
        }

        // Hiển thị modal thông báo
        function showModal(message) {
            const modal = document.getElementById('customModal');
            const modalContent = document.getElementById('modalContent');

            // Xóa nội dung cũ (nếu có)
            modalContent.innerHTML = '';

            // Tạo nội dung mới
            const messageElement = document.createElement('p');
            messageElement.textContent = message;

            const closeButton = document.createElement('button');
            closeButton.textContent = 'Đóng';
            closeButton.onclick = closeModal;

            // Chèn nội dung vào modal
            modalContent.appendChild(messageElement);
            modalContent.appendChild(closeButton);

            // Hiển thị modal
            modal.style.display = 'flex';
        }

        // Đóng modal
        function closeModal() {
            document.getElementById('customModal').style.display = 'none';
        }

        // Xử lý gửi form bằng Fetch API
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Ngăn gửi form truyền thống

            if (!validateForm()) return; // Kiểm tra checkbox

            let formData = new FormData(this); // Lấy dữ liệu form

            fetch('./logic_dang_ky.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json()) // Chuyển đổi JSON phản hồi từ PHP
                .then(data => {
                    showModal(data.message); // Hiển thị thông báo bằng modal
                })
                .catch(error => {
                    showModal("Lỗi kết nối, vui lòng thử lại!");
                });
        });

        // Hàm toggleSelect để hiển thị/ẩn các trường theo loại người dùng
        function toggleSelect(enable, userType) {
            document.getElementById("khoa").disabled = !enable;

            const masv = document.getElementById("masv-group");
            const magv = document.getElementById("magv-group");
            const maCoso = document.getElementById("coso-group");
            const tenCoso = document.getElementById("tenCosoGroup");
            const hoTen = document.querySelector("input[name='ho_ten']").parentElement;

            // Ẩn toàn bộ trước khi hiển thị cái cần thiết
            masv.style.display = "none";
            magv.style.display = "none";
            maCoso.style.display = "none";
            tenCoso.style.display = "none";
            hoTen.style.display = "block";

            if (userType === "sinhvien") {
                masv.style.display = "block";
            } else if (userType === "giangvien") {
                magv.style.display = "block";
            } else if (userType === "coso") {
                maCoso.style.display = "block";
                tenCoso.style.display = "block";
                hoTen.style.display = "none";
            }
        }
    </script>
</body>

</html>