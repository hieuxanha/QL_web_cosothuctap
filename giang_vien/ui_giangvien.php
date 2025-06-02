<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require '../db.php'; // Database connection

// Initialize statistics
$total_lecturers = 0;
$lecturer_counts = [];
$lecturer_labels = [];
$lecturer_data = [];

$total_students = 0;
$student_counts = [];
$student_labels = [];
$student_data = [];

$department_counts = [];
$department_labels = [];
$department_data = [];

// Fetch lecturers by khoa
$sql_lecturers = "SELECT khoa, COUNT(*) as count FROM giang_vien WHERE khoa IS NOT NULL GROUP BY khoa";
$result_lecturers = $conn->query($sql_lecturers);
if ($result_lecturers && $result_lecturers->num_rows > 0) {
    while ($row = $result_lecturers->fetch_assoc()) {
        $khoa = $row['khoa'];
        $count = $row['count'];
        $lecturer_counts[$khoa] = $count;
        $lecturer_labels[] = $khoa;
        $lecturer_data[] = $count;
        $total_lecturers += $count;
    }
}

// Fetch students by khoa
$sql_students = "SELECT khoa, COUNT(*) as count FROM sinh_vien WHERE khoa IS NOT NULL GROUP BY khoa";
$result_students = $conn->query($sql_students);
if ($result_students && $result_students->num_rows > 0) {
    while ($row = $result_students->fetch_assoc()) {
        $khoa = $row['khoa'];
        $count = $row['count'];
        $student_counts[$khoa] = $count;
        $student_labels[] = $khoa;
        $student_data[] = $count;
        $total_students += $count;
    }
}

// Fetch distinct departments
$sql_departments = "SELECT DISTINCT khoa FROM (SELECT khoa FROM sinh_vien WHERE khoa IS NOT NULL UNION SELECT khoa FROM giang_vien WHERE khoa IS NOT NULL) AS combined ORDER BY khoa";
$result_departments = $conn->query($sql_departments);
$department_count = 0;
if ($result_departments && $result_departments->num_rows > 0) {
    while ($row = $result_departments->fetch_assoc()) {
        $khoa = $row['khoa'];
        $department_labels[] = $khoa;
        $department_data[] = 1; // Each khoa counts as 1 for pie chart
        $department_count++;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - Giảng Viên</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="./ui_giangvien.css" />
    <style>
        .dashboard-content .card p {
            margin: 10px 0;
            font-size: 14px;
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
                <li><i class="fa-solid fa-house"></i><a href="./ui_giangvien.php">Trang chủ giảng viên</a></li>

                <li><i class="fa-solid fa-users"></i><a href="./ui_danhsach_sinhvien.php">Danh sách sinh viên</a></li>

                <li><i class="fa-solid fa-user-graduate"></i><a href="./ui_danhsach_thuctap.php">Danh Sách Sinh Viên Đang Thực Tập</a></li>
                <li><i class="fa-solid fa-chart-line"></i><a href="./ui_theo_doi_thuc_tap.php">Theo dõi và đánh giá qtrinh tt của tts</a></li>
                <li><i class="fa-solid fa-file-pdf"></i><a href="./ui_nhan_pdf.php">Chấm Điểm</a></li>



                <li><i class="fa-solid fa-check-circle"></i><a href="./ui_completed_internships.php">Xác nhận hoàn thành thực tập</a></li>
            </ul>
        </div>
    </div>

    <div class="content" id="content">
        <div class="header">
            <div class="search-bar" style="visibility: hidden">
                <input type="text" placeholder="" />
                <!-- <svg
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
                </svg> -->
            </div>
            <div class="account">
                <?php
                if (isset($_SESSION['name'])) {
                    echo '<div class="dropdown">';
                    echo '<span class="user-name">Xin chào, ' . htmlspecialchars($_SESSION['name']) . '</span>';
                    echo '<div class="dropdown-content">';
                    echo '<a href="../dang_nhap_dang_ki/logic_dangxuat.php">Đăng xuất</a>';
                    echo '</div>';
                    echo '</div>';
                } else {
                }
                ?>

                <a href="./giang_vien_profile.php"><i class="fas fa-user"></i></a>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="card">
                <h3>Giảng viên: <?php echo $total_lecturers; ?></h3>
                <p>
                    <?php
                    $lecturer_summary = [];
                    foreach ($lecturer_counts as $khoa => $count) {
                        $lecturer_summary[] = "$khoa: $count";
                    }
                    echo implode(' | ', $lecturer_summary);
                    ?>
                </p>
                <canvas id="lecturersChart"></canvas>
            </div>


            <div class="card">
                <h3>Khoa: <?php echo $department_count; ?></h3>
                <p>Có <?php echo $department_count; ?> khoa và bộ môn trực thuộc</p>
                <canvas id="departmentsChart"></canvas>
            </div>

            <div class="card">
                <h3>Sinh viên: <?php echo $total_students; ?></h3>
                <p>
                    <?php
                    $student_summary = [];
                    foreach ($student_counts as $khoa => $count) {
                        $student_summary[] = "$khoa: $count";
                    }
                    echo implode(' | ', $student_summary);
                    ?>
                </p>
                <canvas id="studentsChart"></canvas>
            </div>

            <div class="card" style="grid-column: span 2">
                <h3>Bảng tin</h3>
                <p>Thông báo hệ thống cập nhật vào ngày 18/09/2024</p>
                <p>Bài viết mới: "Xu hướng công nghệ môi trường 2025"</p>
                <p>Hội thảo: "Phát triển bền vững trong kỷ nguyên số"</p>
                <p>Tạp chí: "Nghiên cứu và ứng dụng khoa học môi trường"</p>
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

        // Biểu đồ giảng viên theo khoa
        new Chart(document.getElementById("lecturersChart"), {
            type: "doughnut",
            data: {
                labels: <?php echo json_encode($lecturer_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($lecturer_data); ?>,
                    backgroundColor: ["#3498db", "#e74c3c", "#2ecc71", "#f1c40f", "#9b59b6", "#1abc9c"],
                }],
            },
        });

        // Biểu đồ sinh viên theo khoa
        new Chart(document.getElementById("studentsChart"), {
            type: "bar",
            data: {
                labels: <?php echo json_encode($student_labels); ?>,
                datasets: [{
                    label: "Số sinh viên",
                    data: <?php echo json_encode($student_data); ?>,
                    backgroundColor: "#f39c12",
                }],
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    },
                },
            },
        });

        // Biểu đồ số khoa
        new Chart(document.getElementById("departmentsChart"), {
            type: "pie",
            data: {
                labels: <?php echo json_encode($department_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($department_data); ?>,
                    backgroundColor: [
                        "#FF6F61", "#6B7280", "#4A90E2", "#50C878", "#F7B32B",
                        "#A3BFFA", "#FF9999", "#2E8B57", "#D4A017", "#4682B4",
                        "#FF7F50", "#9ACD32", "#CD5C5C", "#87CEEB", "#FFA07A"
                    ],
                }],
            },
        });
    </script>
</body>

</html>