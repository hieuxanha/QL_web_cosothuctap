<?php
session_start();
require '../db.php'; // Database connection

// Initialize messages
$success_message = "";
$error_message = "";

// Fetch all classes (lop)
$classes = [];
$sql_classes = "SELECT DISTINCT lop FROM sinh_vien WHERE lop IS NOT NULL ORDER BY lop";
$result_classes = $conn->query($sql_classes);
if ($result_classes && $result_classes->num_rows > 0) {
    while ($row = $result_classes->fetch_assoc()) {
        $classes[] = $row['lop'];
    }
}

// Fetch students based on selected lop (if provided)
$selected_lop = isset($_POST['lop']) ? trim($_POST['lop']) : '';
$students = [];
if (!empty($selected_lop)) {
    $sql_students = "SELECT ma_sinh_vien, ho_ten, email, so_dien_thoai, so_hieu FROM sinh_vien WHERE lop = ?";
    $stmt = $conn->prepare($sql_students);
    $stmt->bind_param("s", $selected_lop);
    $stmt->execute();
    $result_students = $stmt->get_result();
    if ($result_students && $result_students->num_rows > 0) {
        while ($row = $result_students->fetch_assoc()) {
            $students[] = $row;
        }
    }
    $stmt->close();
}

// Fetch all lecturers (giang_vien)
$sql_lecturers = "SELECT so_hieu_giang_vien, ho_ten, khoa, so_dien_thoai, email FROM giang_vien ORDER BY ho_ten";
$result_lecturers = $conn->query($sql_lecturers);
$lecturers = [];
if ($result_lecturers && $result_lecturers->num_rows > 0) {
    while ($row = $result_lecturers->fetch_assoc()) {
        $lecturers[] = $row;
    }
}

// Handle form submission for lecturer assignment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_lecturer'])) {
    $selected_class = trim($_POST['lop']);
    $selected_lecturer = trim($_POST['so_hieu_giang_vien']);

    if (!empty($selected_class) && !empty($selected_lecturer)) {
        // Update so_hieu for all students in the selected class
        $sql_update = "UPDATE sinh_vien SET so_hieu = ? WHERE lop = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ss", $selected_lecturer, $selected_class);

        if ($stmt->execute()) {
            $success_message = "Phân công giảng viên thành công cho lớp $selected_class!";
        } else {
            $error_message = "Lỗi khi phân công giảng viên: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error_message = "Vui lòng chọn lớp và giảng viên!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>tìm kiếm giang viên phụ trách</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="./ui_timkiem_gv_phutrach.css" />
    <style>
        .container {
            padding: 20px;
        }
        .form-container, .table-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .form-container h3, .table-container h3 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }
        .form-group select, .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-group button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-group button:hover {
            background: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
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
            from { opacity: 0; }
            to { opacity: 1; }
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
                <span><?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></span>
                <img src="profile.jpg" alt="Ảnh đại diện" />
            </div>
        </div>

        <!-- Form Phân Công Giảng Viên -->
        <div class="container">
            <div class="form-container">
                <h3>Tìm kiếm gv phụ trách lớp</h3>
                <form method="POST" action="" id="assignForm">
                    <div class="form-group">
                        <label for="lop">Chọn lớp</label>
                        <select name="lop" id="lop" required onchange="loadStudents()">
                            <option value="">-- Chọn lớp --</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo htmlspecialchars($class); ?>" <?php echo $selected_lop === $class ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($class); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="so_hieu_giang_vien">Chọn giảng viên</label>
                        <select name="so_hieu_giang_vien" id="so_hieu_giang_vien" required>
                            <option value="">-- Chọn giảng viên --</option>
                            <?php foreach ($lecturers as $lecturer): ?>
                                <option value="<?php echo htmlspecialchars($lecturer['so_hieu_giang_vien']); ?>">
                                    <?php echo htmlspecialchars($lecturer['ho_ten']) . ' (' . htmlspecialchars($lecturer['khoa']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="assign_lecturer">Phân công</button>
                    </div>
                </form>
            </div>

            <!-- Student List -->
            <?php if (!empty($students)): ?>
                <div class="table-container">
                    <h3>Danh sách sinh viên lớp <?php echo htmlspecialchars($selected_lop); ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Mã SV</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Giảng viên hiện tại</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['ma_sinh_vien']); ?></td>
                                    <td><?php echo htmlspecialchars($student['ho_ten']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['so_dien_thoai'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php
                                        if (!empty($student['so_hieu'])) {
                                            $sql_gv = "SELECT ho_ten FROM giang_vien WHERE so_hieu_giang_vien = ?";
                                            $stmt_gv = $conn->prepare($sql_gv);
                                            $stmt_gv->bind_param("s", $student['so_hieu']);
                                            $stmt_gv->execute();
                                            $result_gv = $stmt_gv->get_result();
                                            if ($gv = $result_gv->fetch_assoc()) {
                                                echo htmlspecialchars($gv['ho_ten']);
                                            } else {
                                                echo 'Chưa phân công';
                                            }
                                            $stmt_gv->close();
                                        } else {
                                            echo 'Chưa phân công';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Lecturer List -->
            <div class="table-container">
                <h3>Danh sách giảng viên</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Số hiệu</th>
                            <th>Họ tên</th>
                            <th>Khoa</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lecturers as $lecturer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($lecturer['so_hieu_giang_vien']); ?></td>
                                <td><?php echo htmlspecialchars($lecturer['ho_ten']); ?></td>
                                <td><?php echo htmlspecialchars($lecturer['khoa'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($lecturer['email']); ?></td>
                                <td><?php echo htmlspecialchars($lecturer['so_dien_thoai'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
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

        // Load students based on lop
        function loadStudents() {
            document.getElementById('assignForm').submit();
        }

        // Show success or error message if set
        <?php if (!empty($success_message)): ?>
            showModal(<?php echo json_encode($success_message); ?>);
        <?php elseif (!empty($error_message)): ?>
            showModal(<?php echo json_encode($error_message); ?>);
        <?php endif; ?>
    </script>
</body>
</html>