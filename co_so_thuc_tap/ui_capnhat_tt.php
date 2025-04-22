<?php
session_start();
require_once '../db.php'; // Kết nối CSDL

// Generate CSRF token
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

// Lấy danh sách công ty đã được duyệt
$sql = "SELECT stt_cty, ten_cong_ty FROM cong_ty WHERE trang_thai = 'Đã duyệt'";
$result = $conn->query($sql);
if (!$result) {
    error_log("Query failed: " . $conn->error);
    $_SESSION['error'] = "Lỗi hệ thống khi lấy danh sách công ty.";
}

// Danh sách khoa (khớp với ENUM trong tuyen_dung)
$khoa_options = [
    'kinh_te' => 'Khoa Kinh tế',
    'moi_truong' => 'Khoa Môi trường',
    'quan_ly_dat_dai' => 'Khoa Quản lý đất đai',
    'khi_tuong_thuy_van' => 'Khoa Khí tượng thủy văn',
    'trac_dia_ban_do' => 'Khoa Trắc địa bản đồ và Thông tin địa lý',
    'dia_chat' => 'Khoa Địa chất',
    'tai_nguyen_nuoc' => 'Khoa Tài nguyên nước',
    'cntt' => 'Khoa Công nghệ thông tin',
    'ly_luan_chinh_tri' => 'Khoa Lý luận chính trị',
    'bien_hai_dao' => 'Khoa Khoa học Biển và Hải đảo',
    'khoa_hoc_dai_cuong' => 'Khoa Khoa học Đại cương',
    'the_chat_quoc_phong' => 'Khoa Giáo dục thể chất và Giáo dục quốc phòng',
    'bo_mon_luat' => 'Bộ môn Luật',
    'bien_doi_khi_hau' => 'Bộ môn Biến đổi khí hậu và PT bền vững',
    'ngoai_ngu' => 'Bộ môn Ngoại ngữ'
];

// Load form data from session if available
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
unset($_SESSION['form_data']); // Clear after use
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .user-name {
            cursor: pointer;
            padding: 8px;
            background-color: #f0f0f0;
            border-radius: 4px;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #ffffff;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            margin-top: 8px;
            padding: 8px;
            border-radius: 15px;
            top: 20px;
        }
        .dropdown-content a {
            color: black;
            text-decoration: none;
            display: block;
            padding: 8px 12px;
        }
        .dropdown-content a:hover {
            background-color: #ddd;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
    <link rel="stylesheet" href="./ui_capnhat.css">
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
                <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_cstt.php">cstttt..</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_capnhat_cty.php">Đăng ký thông tin cty</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_capnhat_tt.php">Cập nhật thông tin tuyển dụng</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_duyet_cv.php">Xét duyệt hồ sơ ứng tuyển</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_quanly_baocao.php">Gửi báo cáo hàng tuần </a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_danh_gia_thuc_tap.php">Theo dõi & đánh giá quá trình TT</a></li>
                <li><i class="fa-brands fa-windows"></i> <a href="../co_so_thuc_tap/ui_xac_nhan_hoan_thanh.php">Xác nhận hoàn thành TT</a></li>
            </ul>
        </div>
    </div>

    <div class="content" id="content">
        <div class="header">
            <div class="search-bar">
                <input type="text" placeholder="Tìm kiếm..." />
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
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
                    echo '<a href="../dang_nhap_dang_ki/dang_nhap.php">Đăng nhập</a>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <div class="tuyendung">
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
            <form id="formTuyenDung" action="../logic_cstt/logic_tuyendung.php" method="post">
                <h2>Tin tuyển dụng</h2>

                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="them_tuyen_dung" value="1">

                <label for="stt_cty">Công ty:</label>
                <select name="stt_cty" required>
                    <option value="">Chọn công ty</option>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $selected = isset($form_data['stt_cty']) && $form_data['stt_cty'] == $row['stt_cty'] ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row['stt_cty']) . "' $selected>" . htmlspecialchars($row['ten_cong_ty']) . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>Không có công ty nào được duyệt</option>";
                    }
                    ?>
                </select>

                <label for="tieu_de_tuyen_dung">Tiêu đề:</label>
                <input type="text" name="tieu_de_tuyen_dung" maxlength="255" value="<?php echo isset($form_data['tieu_de_tuyen_dung']) ? htmlspecialchars($form_data['tieu_de_tuyen_dung']) : ''; ?>" required>

                <label for="dia_chi">Địa chỉ:</label>
                <input type="text" name="dia_chi" maxlength="255" value="<?php echo isset($form_data['dia_chi']) ? htmlspecialchars($form_data['dia_chi']) : ''; ?>" required>

                <label for="hinh_thuc">Hình thức làm việc:</label>
                <select name="hinh_thuc" required>
                    <option value="Full-time" <?php echo isset($form_data['hinh_thuc']) && $form_data['hinh_thuc'] == 'Full-time' ? 'selected' : ''; ?>>Full-time</option>
                    <option value="Part-time" <?php echo isset($form_data['hinh_thuc']) && $form_data['hinh_thuc'] == 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
                </select>

                <label for="gioi_tinh">Giới tính:</label>
                <select name="gioi_tinh" required>
                    <option value="Nam" <?php echo isset($form_data['gioi_tinh']) && $form_data['gioi_tinh'] == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                    <option value="Nữ" <?php echo isset($form_data['gioi_tinh']) && $form_data['gioi_tinh'] == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                    <option value="Không giới hạn" <?php echo isset($form_data['gioi_tinh']) && $form_data['gioi_tinh'] == 'Không giới hạn' ? 'selected' : ''; ?>>Không giới hạn</option>
                </select>

                <label for="khoa">Khoa (tuỳ chọn):</label>
                <select name="khoa">
                    <option value="">Không chọn</option>
                    <?php
                    foreach ($khoa_options as $value => $name) {
                        $selected = isset($form_data['khoa']) && $form_data['khoa'] == $value ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($value) . "' $selected>" . htmlspecialchars($name) . "</option>";
                    }
                    ?>
                </select>

                <label for="mo_ta">Mô tả:</label>
                <textarea name="mo_ta"><?php echo isset($form_data['mo_ta']) ? htmlspecialchars($form_data['mo_ta']) : ''; ?></textarea>

                <label for="trinh_do">Trình độ:</label>
                <select name="trinh_do" required>
                    <option value="Không yêu cầu" <?php echo isset($form_data['trinh_do']) && $form_data['trinh_do'] == 'Không yêu cầu' ? 'selected' : ''; ?>>Không yêu cầu</option>
                    <option value="Trung cấp" <?php echo isset($form_data['trinh_do']) && $form_data['trinh_do'] == 'Trung cấp' ? 'selected' : ''; ?>>Trung cấp</option>
                    <option value="Cao đẳng" <?php echo isset($form_data['trinh_do']) && $form_data['trinh_do'] == 'Cao đẳng' ? 'selected' : ''; ?>>Cao đẳng</option>
                    <option value="Đại học" <?php echo isset($form_data['trinh_do']) && $form_data['trinh_do'] == 'Đại học' ? 'selected' : ''; ?>>Đại học</option>
                    <option value="Thạc sĩ" <?php echo isset($form_data['trinh_do']) && $form_data['trinh_do'] == 'Thạc sĩ' ? 'selected' : ''; ?>>Thạc sĩ</option>
                    <option value="Tiến sĩ" <?php echo isset($form_data['trinh_do']) && $form_data['trinh_do'] == 'Tiến sĩ' ? 'selected' : ''; ?>>Tiến sĩ</option>
                </select>

                <label for="so_luong">Số lượng tuyển:</label>
                <input type="number" name="so_luong" min="1" value="<?php echo isset($form_data['so_luong']) ? htmlspecialchars($form_data['so_luong']) : '1'; ?>" required>

                <label for="noi_bat">Nổi bật:</label>
                <input type="checkbox" name="noi_bat" value="1" <?php echo isset($form_data['noi_bat']) && $form_data['noi_bat'] == '1' ? 'checked' : ''; ?>>

                <label for="han_nop">Hạn nộp:</label>
                <input type="date" name="han_nop" min="<?php echo date('Y-m-d'); ?>" value="<?php echo isset($form_data['han_nop']) ? htmlspecialchars($form_data['han_nop']) : ''; ?>" required>

                <button type="submit" class="submit-btn">Đăng tin</button>
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

        // Client-side validation for date
        document.getElementById('formTuyenDung').addEventListener('submit', function(e) {
            const hanNop = new Date(document.querySelector('input[name="han_nop"]').value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (hanNop < today) {
                e.preventDefault();
                alert('Hạn nộp phải từ hôm nay trở đi.');
            }
        });
    </script>
</body>
</html>
<?php
if ($result) {
    $result->free();
}
$conn->close();
?>