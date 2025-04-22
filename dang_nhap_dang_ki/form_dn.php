<?php
session_start();
require '../db.php'; // Kết nối database

$error = "";
$error1 = ""; // Initialize $error1 to avoid undefined variable issues
$logout_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {
        $tables = [
            'admin' => 'id',
            'giang_vien' => 'stt_gv',
            'sinh_vien' => 'stt_sv',
            'co_so_thuc_tap' => 'stt_cstt'
        ];

        $user = null;
        foreach ($tables as $table => $id_field) {
            $sql = "SELECT * FROM $table WHERE email = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                $user['table'] = $table;
                break;
            }
        }

        if ($user) {
            $isValidPassword = false;
            if ($user['table'] == 'admin' && $user['password'] === $password) {
                $isValidPassword = true;
            } elseif (password_verify($password, $user['password'])) {
                $isValidPassword = true;
            }

            if ($isValidPassword) {
                $_SESSION["user_id"] = $user[$tables[$user['table']]];
                $_SESSION["name"] = $user["ho_ten"] ?? $user["ten_co_so"] ?? "User";
                $_SESSION["role"] = $user["role"];

                // Lưu thêm thông tin cho giảng viên
                if ($user["role"] == "giang_vien") {
                    $_SESSION["so_hieu_giang_vien"] = $user["so_hieu_giang_vien"];
                    $_SESSION["khoa"] = $user["khoa"]; // Nếu cần dùng khoa
                }

                switch ($user["role"]) {
                    case "admin":
                        header("Location: ../admin/ui_admin.php");
                        break;
                    case "giang_vien":
                        header("Location: ../giang_vien/ui_giangvien.php");
                        break;
                    case "sinh_vien":
                        header("Location: ../sinh_vien/giaodien_sinhvien.php");
                        break;
                    case "co_so_thuc_tap":
                        header("Location: ../co_so_thuc_tap/ui_cstt.php");
                        break;
                    default:
                        header("Location: giaodien_chung.php");
                        break;
                }
                exit();
            } else {
                $error1 = "Mật khẩu không đúng!";
            }
        } else {
            $error = "Email không tồn tại!";
        }
    } else {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    }
}

if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $logout_message = "Bạn đã đăng xuất thành công!";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - TopCV</title>
    <link rel="stylesheet" href="./dn_dk.css">
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
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <h2>Chào mừng bạn đến</h2>
            <p>Cùng xây dựng một hồ sơ nổi bật và nhận được các cơ hội sự nghiệp lý tưởng</p>

            <form action="" method="POST" onsubmit="return validateForm()">
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Nhập email" required>
                </div>

                <div class="input-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" placeholder="Nhập mật khẩu" required>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="agree">
                    <label for="agree">Tôi đã đọc và đồng ý với <a href="#"> Điều khoản dịch vụ </a> và <a href="#">Chính sách bảo mật</a></label>
                </div>

                <button type="submit" class="btn">Đăng nhập</button>

                <p>Bạn chưa có tài khoản? <a href="../dang_nhap_dang_ki/form_dk.php">Đăng ký ngay</a></p>
            </form>
        </div>

        <div class="right">
            <img src="../img/dk_dn.jpg" alt="Banner">
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

        // Hiển thị thông báo lỗi hoặc logout từ PHP
        <?php if (!empty($error)): ?>
            showModal(<?php echo json_encode($error); ?>);
        <?php elseif (!empty($error1)): ?>
            showModal(<?php echo json_encode($error1); ?>);
        <?php elseif (!empty($logout_message)): ?>
            showModal(<?php echo json_encode($logout_message); ?>);
        <?php endif; ?>
    </script>
</body>
</html>