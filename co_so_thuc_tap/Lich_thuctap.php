<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once '../db.php';

// Initialize message variables
$success_message = '';
$error_message = '';
$eval_success_message = '';
$eval_error_message = '';

// Handle scheduling form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_schedule'])) {
    $ma_dang_ky = $_POST['ma_dang_ky'];

    // Fetch the stt_sv for the selected application
    $stmt = $conn->prepare("
        SELECT stt_sv 
        FROM ung_tuyen 
        WHERE id = ? AND trang_thai IN ('Đồng ý', 'Hoàn thành')
    ");
    $stmt->bind_param("i", $ma_dang_ky);
    $stmt->execute();
    $result = $stmt->get_result();
    $application = $result->fetch_assoc();

    if (!$application) {
        $error_message = "Đơn ứng tuyển không hợp lệ hoặc chưa được duyệt.";
    } else {
        $sinh_vien = $application['stt_sv'];

        // Process multiple shifts
        $shift_dates = $_POST['shift_date'] ?? [];
        $shift_ca = $_POST['shift_ca'] ?? [];
        $shift_time = $_POST['shift_time'] ?? [];

        $conn->begin_transaction();
        try {
            // Delete existing schedules for this application
            $stmt = $conn->prepare("DELETE FROM lich_thuc_tap WHERE ma_dang_ky = ?");
            $stmt->bind_param("i", $ma_dang_ky);
            $stmt->execute();

            // Insert new schedules
            $stmt = $conn->prepare("INSERT INTO lich_thuc_tap (ma_dang_ky, stt_sv, ngay_thuc_tap, ca_lam, thoi_gian_ca, ngay_cap_nhat, danh_gia) VALUES (?, ?, ?, ?, ?, CURDATE(), NULL)");
            for ($i = 0; $i < count($shift_dates); $i++) {
                if (!empty($shift_dates[$i]) && !empty($shift_ca[$i]) && !empty($shift_time[$i])) {
                    $stmt->bind_param("iisss", $ma_dang_ky, $sinh_vien, $shift_dates[$i], $shift_ca[$i], $shift_time[$i]);
                    $stmt->execute();
                }
            }
            $conn->commit();
            $success_message = "Gửi lịch thực tập theo ca thành công!";
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Lỗi khi gửi lịch thực tập: " . $e->getMessage();
        }
    }
}

// Handle evaluation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_evaluation'])) {
    $shift_id = $_POST['shift_id'];
    $danh_gia = $_POST['danh_gia'];

    if (empty($danh_gia)) {
        $eval_error_message = "Vui lòng nhập đánh giá.";
    } else {
        $stmt = $conn->prepare("UPDATE lich_thuc_tap SET danh_gia = ? WHERE id = ?");
        $stmt->bind_param("si", $danh_gia, $shift_id);
        if ($stmt->execute()) {
            $eval_success_message = "Đánh giá đã được cập nhật thành công!";
        } else {
            $eval_error_message = "Lỗi khi cập nhật đánh giá.";
        }
    }
}

// Fetch approved applications for dropdown
$stmt = $conn->query("
    SELECT ut.id, ut.ho_ten, td.tieu_de, ct.ten_cong_ty 
    FROM ung_tuyen ut
    JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung
    JOIN cong_ty ct ON td.stt_cty = ct.stt_cty
    WHERE ut.trang_thai IN ('Đồng ý', 'Hoàn thành')
");
$applications = $stmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Quản Trị</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="ui_capnhat.css" />
    <style>
        .schedule-form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .schedule-form h3 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .schedule-form label {
            display: block;
            margin: 10px 0 5px;
            color: #555;
        }

        .schedule-form select,
        .schedule-form input[type="date"],
        .schedule-form input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .schedule-form button {

            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .schedule-form button:hover {
            background-color: #218838;
        }

        .shift-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }

        .shift-row input,
        .shift-row select {
            flex: 1;
        }

        .add-shift {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .add-shift:hover {
            background-color: #0056b3;
        }

        .remove-shift {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .remove-shift:hover {
            background-color: #c82333;
        }

        .message {
            text-align: center;
            margin-top: 10px;
        }

        .success {
            color: #28a745;
        }

        .error {
            color: #dc3545;
        }

        .evaluation-form {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
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
                <h3>Quản lý</h3>
                <li><i class="fa-solid fa-building"></i> <a href="ui_cstt.php">Cơ sở thực tập</a></li>
                <li><i class="fa-solid fa-briefcase"></i> <a href="ui_capnhat_cty.php">Đăng ký thông tin công ty</a></li>
                <li><i class="fa-solid fa-bullhorn"></i> <a href="ui_capnhat_tt.php">Cập nhật thông tin tuyển dụng</a></li>
                <li><i class="fa-solid fa-file-alt"></i> <a href="ui_duyet_cv.php">Xét duyệt hồ sơ ứng tuyển</a></li>
                <li><i class="fa-solid fa-file-signature"></i> <a href="ui_quanly_baocao.php">Gửi báo cáo hàng tuần</a></li>
                <li><i class="fa-solid fa-star"></i> <a href="ui_danh_gia_thuc_tap.php">Theo dõi & đánh giá thực tập</a></li>
                <li><i class="fa-solid fa-list-check"></i> <a href="ui_quan_ly_danh_gia.php">Quản lý đánh giá thực tập</a></li>
                <li><i class="fa-solid fa-check-circle"></i> <a href="ui_xac_nhan_hoan_thanh.php">Xác nhận hoàn thành thực tập</a></li>
            </ul>
        </div>
    </div>

    <div class="content" id="content">
        <div class="header">
            <div class="search-bar" style="visibility: hidden">
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
                    echo '<a href="../dang_nhap_dang_ki/form_dn.php">Đăng nhập</a>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <!-- Scheduling form -->
        <div class="schedule-form">
            <h3>Gửi Lịch Thực Tập Theo Ca</h3>
            <form method="POST" action="" id="scheduleForm" onsubmit="return validateForm()">
                <label for="ma_dang_ky">Chọn Vị Trí Ứng Tuyển:</label>
                <select name="ma_dang_ky" id="ma_dang_ky" required>
                    <option value="">-- Chọn Vị Trí --</option>
                    <?php foreach ($applications as $app): ?>
                        <option value="<?php echo htmlspecialchars($app['id']); ?>">
                            <?php echo htmlspecialchars($app['ho_ten'] . ' - ' . $app['tieu_de'] . ' (' . $app['ten_cong_ty'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div id="shift-container">
                    <div class="shift-row">
                        <input type="date" name="shift_date[]" required>
                        <select name="shift_ca[]" required>
                            <option value="">-- Chọn Ca --</option>
                            <option value="Sáng">Sáng</option>
                            <option value="Chiều">Chiều</option>
                            <option value="Tối">Tối</option>
                        </select>
                        <input type="text" name="shift_time[]" placeholder="Thời gian (ví dụ: 8:00 - 12:00)" required>
                        <button type="button" class="remove-shift" onclick="removeShiftRow(this)">Xóa</button>
                    </div>
                </div>
                <button type="button" class="add-shift" onclick="addShiftRow()">Thêm Ca</button>
                <button type="submit" name="submit_schedule">Gửi Lịch Thực Tập</button>
            </form>

            <?php if ($success_message): ?>
                <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <!-- Evaluation form -->
            <div class="evaluation-form">
                <h3>Đánh Giá Ca Thực Tập</h3>
                <form method="POST" action="" id="evaluationForm" onsubmit="return validateEvaluationForm()">
                    <label for="eval_ma_dang_ky">Chọn Vị Trí Ứng Tuyển:</label>
                    <select name="eval_ma_dang_ky" id="eval_ma_dang_ky" required onchange="loadShifts()">
                        <option value="">-- Chọn Vị Trí --</option>
                        <?php foreach ($applications as $app): ?>
                            <option value="<?php echo htmlspecialchars($app['id']); ?>">
                                <?php echo htmlspecialchars($app['ho_ten'] . ' - ' . $app['tieu_de'] . ' (' . $app['ten_cong_ty'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="shift_id">Chọn Ca Thực Tập:</label>
                    <select name="shift_id" id="shift_id" required>
                        <option value="">-- Chọn Ca --</option>
                    </select>

                    <label for="danh_gia">Đánh Giá:</label>
                    <input type="text" name="danh_gia" id="danh_gia" placeholder="Nhập đánh giá (ví dụ: Tốt, Cần cải thiện)" required>

                    <button type="submit" name="submit_evaluation">Gửi Đánh Giá</button>
                </form>

                <?php if ($eval_success_message): ?>
                    <div class="message success"><?php echo htmlspecialchars($eval_success_message); ?></div>
                <?php endif; ?>
                <?php if ($eval_error_message): ?>
                    <div class="message error"><?php echo htmlspecialchars($eval_error_message); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const content = document.getElementById("content");
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("collapsed");
        }

        function addShiftRow() {
            const container = document.getElementById('shift-container');
            const row = document.createElement('div');
            row.className = 'shift-row';
            row.innerHTML = `
                <input type="date" name="shift_date[]" required>
                <select name="shift_ca[]" required>
                    <option value="">-- Chọn Ca --</option>
                    <option value="Sáng">Sáng</option>
                    <option value="Chiều">Chiều</option>
                    <option value="Tối">Tối</option>
                </select>
                <input type="text" name="shift_time[]" placeholder="Thời gian (ví dụ: 8:00 - 12:00)" required>
                <button type="button" class="remove-shift" onclick="removeShiftRow(this)">Xóa</button>
            `;
            container.appendChild(row);
        }

        function removeShiftRow(button) {
            if (document.querySelectorAll('.shift-row').length > 1) {
                button.parentElement.remove();
            }
        }

        function validateForm() {
            const shifts = document.querySelectorAll('.shift-row');
            for (let shift of shifts) {
                const date = shift.querySelector('input[name="shift_date[]"]').value;
                const ca = shift.querySelector('select[name="shift_ca[]"]').value;
                const time = shift.querySelector('input[name="shift_time[]"]').value;
                if (!date || !ca || !time) {
                    alert('Vui lòng điền đầy đủ thông tin cho tất cả các ca.');
                    return false;
                }
            }
            return true;
        }

        function validateEvaluationForm() {
            const shift_id = document.getElementById('shift_id').value;
            const danh_gia = document.getElementById('danh_gia').value;
            if (!shift_id || !danh_gia) {
                alert('Vui lòng chọn ca thực tập và nhập đánh giá.');
                return false;
            }
            return true;
        }

        function loadShifts() {
            const ma_dang_ky = document.getElementById('eval_ma_dang_ky').value;
            const shiftSelect = document.getElementById('shift_id');
            shiftSelect.innerHTML = '<option value="">-- Chọn Ca --</option>';

            if (ma_dang_ky) {
                $.ajax({
                    url: 'fetch_shifts.php',
                    type: 'POST',
                    data: {
                        ma_dang_ky: ma_dang_ky
                    },
                    dataType: 'json',
                    success: function(shifts) {
                        shifts.forEach(shift => {
                            const option = document.createElement('option');
                            option.value = shift.id;
                            option.text = `${shift.ngay_thuc_tap} - ${shift.ca_lam} (${shift.thoi_gian_ca})`;
                            shiftSelect.appendChild(option);
                        });
                    },
                    error: function() {
                        alert('Lỗi khi tải danh sách ca thực tập.');
                    }
                });
            }
        }
    </script>
</body>

</html>