<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Kết nối cơ sở dữ liệu
require_once '../db.php';
$conn->set_charset("utf8mb4");

// Tạo CSRF token nếu chưa có
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Lấy danh sách sinh viên đã ứng tuyển thành công
$stmt = $conn->prepare("
    SELECT DISTINCT sv.stt_sv, sv.ho_ten
    FROM sinh_vien sv
    JOIN ung_tuyen ut ON sv.stt_sv = ut.stt_sv
    WHERE ut.trang_thai = 'Đồng ý'
    ORDER BY sv.ho_ten
");
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

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
    <title>Đánh Giá Quá Trình Thực Tập</title>
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

        select,
        input[type="text"],
        input[type="email"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        input[readonly] {
            background-color: #f5f5f5;
        }

        .required::after {
            content: " *";
            color: red;
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
                <li><i class="fa-brands fa-windows"></i> <a href="ui_cstt.php">Cơ sở thực tập</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="ui_capnhat_cty.php">Đăng ký thông tin công ty</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="ui_capnhat_tt.php">Cập nhật thông tin tuyển dụng</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="ui_duyet_cv.php">Xét duyệt hồ sơ ứng tuyển</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="ui_quanly_baocao.php">Gửi báo cáo hàng tuần</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="ui_danh_gia_thuc_tap.php">Theo dõi & đánh giá thực tập</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="ui_xac_nhan_hoan_thanh.php">Xác nhận hoàn thành thực tập</a></li>
            </ul>
        </div>
    </div>

    <div class="content" id="content">
        <div class="header">
            <div class="search-bar">
                <input type="text" placeholder="Tìm kiếm..." tabindex="1" aria-label="Tìm kiếm thông tin" />
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
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
                    Phiếu Đánh Giá Quá Trình Thực Tập
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

            <form id="eval-form" class="eval-form" action="../logic_cstt/process_evaluation.php" method="POST">
                <input type="hidden" name="ma_dang_ky" id="ma_dang_ky" value="" />
                <input type="hidden" name="stt_cstt" id="stt_cstt" value="" />
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>" />

                <h1>PHIẾU ĐÁNH GIÁ QUÁ TRÌNH THỰC TẬP</h1>
                <div class="form-section">
                    <h2>1. Thông tin cơ sở thực tập</h2>
                    <label class="required">Tên cơ sở</label>
                    <input type="text" name="ten_co_so" id="ten_co_so" value="" required tabindex="2" readonly />
                    <label class="required">Tiêu đề tuyển dụng</label>
                    <input type="text" name="tieu_de_tuyen_dung" id="tieu_de_tuyen_dung" value="" required tabindex="3" readonly />
                    <label class="required">Công ty</label>
                    <input type="text" name="cong_ty" id="cong_ty" value="" required tabindex="4" readonly />
                    <label class="required">Email liên hệ</label>
                    <input type="email" name="email_lien_he" id="email_lien_he" value="" required tabindex="5" placeholder="example@company.com" />
                </div>

                <div class="form-section">
                    <h2>2. Thông tin giảng viên</h2>
                    <label class="required">Giảng viên hướng dẫn</label>
                    <input type="text" name="giang_vien_huong_dan" id="giang_vien_huong_dan" value="" required tabindex="6" placeholder="Nhập tên giảng viên" />
                </div>

                <div class="form-section">
                    <h2>3. Thông tin sinh viên</h2>
                    <label class="required">Họ và tên sinh viên</label>
                    <select name="stt_sv" id="stt_sv" required tabindex="7" onchange="fetchStudentInfo(this.value)">
                        <option value="">-- Chọn sinh viên --</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo htmlspecialchars($student['stt_sv']); ?>">
                                <?php echo htmlspecialchars($student['ho_ten']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label class="required">Mã số sinh viên</label>
                    <input type="text" name="ma_sinh_vien" id="ma_sinh_vien" value="" required tabindex="8" readonly />
                    <label class="required">Lớp - Khóa</label>
                    <input type="text" name="lop_khoa" id="lop_khoa" value="" required tabindex="9" readonly />
                    <label class="required">Ngành học</label>
                    <input type="text" name="nganh_hoc" id="nganh_hoc" value="" required tabindex="10" readonly />
                    <label class="required">Thời gian thực tập</label>
                    <input type="text" name="thoi_gian_thuc_tap" id="thoi_gian_thuc_tap" value="" placeholder="Ví dụ: Từ 01/01/2025 đến 31/03/2025" required tabindex="11" />
                </div>

                <div class="form-section">
                    <h2>4. Nội dung đánh giá</h2>
                    <table class="eval-table">
                        <thead>
                            <tr>
                                <th>Tiêu chí đánh giá</th>
                                <th>Mức độ</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Thái độ, tinh thần trách nhiệm</td>
                                <td class="rating-options">
                                    <label><input type="radio" name="thai_do" value="Xuất sắc" required tabindex="12"> Xuất sắc</label>
                                    <label><input type="radio" name="thai_do" value="Tốt" tabindex="13"> Tốt</label>
                                    <label><input type="radio" name="thai_do" value="Trung bình" tabindex="14"> Trung bình</label>
                                    <label><input type="radio" name="thai_do" value="Yếu" tabindex="15"> Yếu</label>
                                </td>
                                <td><input type="text" name="thai_do_ghi_chu" tabindex="16" /></td>
                            </tr>
                            <tr>
                                <td>Kỹ năng chuyên môn</td>
                                <td class="rating-options">
                                    <label><input type="radio" name="ky_nang_chuyen_mon" value="Xuất sắc" required tabindex="17"> Xuất sắc</label>
                                    <label><input type="radio" name="ky_nang_chuyen_mon" value="Tốt" tabindex="18"> Tốt</label>
                                    <label><input type="radio" name="ky_nang_chuyen_mon" value="Trung bình" tabindex="19"> Trung bình</label>
                                    <label><input type="radio" name="ky_nang_chuyen_mon" value="Yếu" tabindex="20"> Yếu</label>
                                </td>
                                <td><input type="text" name="ky_nang_ghi_chu" tabindex="21" /></td>
                            </tr>
                            <tr>
                                <td>Khả năng làm việc nhóm</td>
                                <td class="rating-options">
                                    <label><input type="radio" name="lam_viec_nhom" value="Xuất sắc" required tabindex="22"> Xuất sắc</label>
                                    <label><input type="radio" name="lam_viec_nhom" value="Tốt" tabindex="23"> Tốt</label>
                                    <label><input type="radio" name="lam_viec_nhom" value="Trung bình" tabindex="24"> Trung bình</label>
                                    <label><input type="radio" name="lam_viec_nhom" value="Yếu" tabindex="25"> Yếu</label>
                                </td>
                                <td><input type="text" name="lam_viec_nhom_ghi_chu" tabindex="26" /></td>
                            </tr>
                            <tr>
                                <td>Kỹ năng giao tiếp</td>
                                <td class="rating-options">
                                    <label><input type="radio" name="ky_nang_giao_tiep" value="Xuất sắc" required tabindex="27"> Xuất sắc</label>
                                    <label><input type="radio" name="ky_nang_giao_tiep" value="Tốt" tabindex="28"> Tốt</label>
                                    <label><input type="radio" name="ky_nang_giao_tiep" value="Trung bình" tabindex="29"> Trung bình</label>
                                    <label><input type="radio" name="ky_nang_giao_tiep" value="Yếu" tabindex="30"> Yếu</label>
                                </td>
                                <td><input type="text" name="ky_nang_giao_tiep_ghi_chu" tabindex="31" /></td>
                            </tr>
                            <tr>
                                <td>Khả năng thích nghi với môi trường</td>
                                <td class="rating-options">
                                    <label><input type="radio" name="thich_nghi" value="Xuất sắc" required tabindex="32"> Xuất sắc</label>
                                    <label><input type="radio" name="thich_nghi" value="Tốt" tabindex="33"> Tốt</label>
                                    <label><input type="radio" name="thich_nghi" value="Trung bình" tabindex="34"> Trung bình</label>
                                    <label><input type="radio" name="thich_nghi" value="Yếu" tabindex="35"> Yếu</label>
                                </td>
                                <td><input type="text" name="thich_nghi_ghi_chu" tabindex="36" /></td>
                            </tr>
                            <tr>
                                <td>Tuân thủ nội quy, kỷ luật</td>
                                <td class="rating-options">
                                    <label><input type="radio" name="tuan_thu" value="Xuất sắc" required tabindex="37"> Xuất sắc</label>
                                    <label><input type="radio" name="tuan_thu" value="Tốt" tabindex="38"> Tốt</label>
                                    <label><input type="radio" name="tuan_thu" value="Trung bình" tabindex="39"> Trung bình</label>
                                    <label><input type="radio" name="tuan_thu" value="Yếu" tabindex="40"> Yếu</label>
                                </td>
                                <td><input type="text" name="tuan_thu_ghi_chu" tabindex="41" /></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="form-section">
                    <h2>5. Nhận xét chung</h2>
                    <textarea name="nhan_xet_chung" rows="4" placeholder="Nhập nhận xét chung" tabindex="42"></textarea>
                </div>

                <div class="form-section">
                    <h2>6. Kết quả đề xuất</h2>
                    <label><input type="checkbox" name="ket_qua_de_xuat[]" value="Đạt yêu cầu" tabindex="43"> Đạt yêu cầu</label>
                    <label><input type="checkbox" name="ket_qua_de_xuat[]" value="Chưa đạt yêu cầu" tabindex="44"> Chưa đạt yêu cầu</label>
                    <label><input type="checkbox" name="ket_qua_de_xuat[]" value="Đề nghị khen thưởng" tabindex="45"> Đề nghị khen thưởng</label>
                    <label><input type="checkbox" name="ket_qua_de_xuat[]" value="Đề nghị không công nhận" tabindex="46"> Đề nghị không công nhận</label>
                </div>

                <div class="form-section">
                    <label class="required">Ngày đánh giá</label>
                    <input type="date" name="ngay_danh_gia" required tabindex="47" max="<?php echo date('Y-m-d'); ?>" />
                    <label class="required">Người đánh giá (ký tên, ghi rõ họ tên)</label>
                    <input type="text" name="nguoi_danh_gia" required tabindex="48" />
                </div>

                <button type="submit" class="submit-button" tabindex="49">Gửi đánh giá</button>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const content = document.getElementById("content");
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("collapsed");
        }

        document.addEventListener("DOMContentLoaded", function() {
            const successMessage = document.querySelector("#message");
            const errorMessage = document.querySelector("#error-message");
            if (successMessage && successMessage.innerText) {
                successMessage.style.display = "block";
                setTimeout(() => successMessage.style.display = "none", 5000);
            }
            if (errorMessage && errorMessage.innerText) {
                errorMessage.style.display = "block";
                setTimeout(() => errorMessage.style.display = "none", 5000);
            }

            document.getElementById("eval-form").addEventListener("submit", function(event) {
                const ma_dang_ky = document.getElementById("ma_dang_ky").value;
                const stt_cstt = document.getElementById("stt_cstt").value;
                const stt_sv = document.getElementById("stt_sv").value;
                const email = document.getElementById("email_lien_he").value;
                const ngay_danh_gia = document.getElementById("ngay_danh_gia").value;

                // Kiểm tra các trường bắt buộc
                if (!ma_dang_ky || !stt_cstt || !stt_sv) {
                    alert("Vui lòng chọn sinh viên và đảm bảo thông tin cơ sở thực tập được điền đầy đủ!");
                    event.preventDefault();
                    return;
                }

                // Kiểm tra email
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    alert("Email liên hệ không hợp lệ!");
                    event.preventDefault();
                    return;
                }

                // Kiểm tra ngày đánh giá
                const today = new Date().toISOString().split("T")[0];
                if (ngay_danh_gia > today) {
                    alert("Ngày đánh giá không được là ngày trong tương lai!");
                    event.preventDefault();
                    return;
                }
            });
        });

        function fetchStudentInfo(stt_sv) {
            if (!stt_sv) {
                document.getElementById("ma_dang_ky").value = "";
                document.getElementById("ma_sinh_vien").value = "";
                document.getElementById("lop_khoa").value = "";
                document.getElementById("nganh_hoc").value = "";
                document.getElementById("ten_co_so").value = "";
                document.getElementById("tieu_de_tuyen_dung").value = "";
                document.getElementById("cong_ty").value = "";
                document.getElementById("stt_cstt").value = "";
                document.getElementById("email_lien_he").value = "";
                document.getElementById("giang_vien_huong_dan").value = "";
                return;
            }

            fetch("../logic_cstt/fetch_student_info.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "stt_sv=" + encodeURIComponent(stt_sv)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Lỗi HTTP: " + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById("ma_dang_ky").value = data.ma_dang_ky || "";
                        document.getElementById("ma_sinh_vien").value = data.ma_sinh_vien || "";
                        document.getElementById("lop_khoa").value = data.lop_khoa || "";
                        document.getElementById("nganh_hoc").value = data.nganh_hoc || "";
                        document.getElementById("ten_co_so").value = data.ten_co_so || "";
                        document.getElementById("tieu_de_tuyen_dung").value = data.tieu_de_tuyen_dung || "";
                        document.getElementById("cong_ty").value = data.cong_ty || "";
                        document.getElementById("stt_cstt").value = data.stt_cstt || "";
                        document.getElementById("email_lien_he").value = data.email_lien_he || "";
                        document.getElementById("giang_vien_huong_dan").value = data.giang_vien_huong_dan || "";
                    } else {
                        alert("Không thể lấy thông tin sinh viên: " + data.message);
                        // Reset các trường
                        document.getElementById("ma_dang_ky").value = "";
                        document.getElementById("ma_sinh_vien").value = "";
                        document.getElementById("lop_khoa").value = "";
                        document.getElementById("nganh_hoc").value = "";
                        document.getElementById("ten_co_so").value = "";
                        document.getElementById("tieu_de_tuyen_dung").value = "";
                        document.getElementById("cong_ty").value = "";
                        document.getElementById("stt_cstt").value = "";
                        document.getElementById("email_lien_he").value = "";
                        document.getElementById("giang_vien_huong_dan").value = "";
                    }
                })
                .catch(error => {
                    alert("Đã xảy ra lỗi khi lấy thông tin sinh viên: " + error.message);
                    // Reset các trường
                    document.getElementById("ma_dang_ky").value = "";
                    document.getElementById("ma_sinh_vien").value = "";
                    document.getElementById("lop_khoa").value = "";
                    document.getElementById("nganh_hoc").value = "";
                    document.getElementById("ten_co_so").value = "";
                    document.getElementById("tieu_de_tuyen_dung").value = "";
                    document.getElementById("cong_ty").value = "";
                    document.getElementById("stt_cstt").value = "";
                    document.getElementById("email_lien_he").value = "";
                    document.getElementById("giang_vien_huong_dan").value = "";
                });
        }
    </script>
</body>

</html>