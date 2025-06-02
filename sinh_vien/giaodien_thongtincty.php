<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../db.php';

// Lấy stt_cty từ tham số URL
$stt_cty = isset($_GET['stt_cty']) ? (int)$_GET['stt_cty'] : 0;

// Kiểm tra nếu stt_cty không hợp lệ
if ($stt_cty <= 0) {
    die("Lỗi: Không tìm thấy công ty. Vui lòng cung cấp stt_cty hợp lệ.");
}

// Truy vấn để lấy thông tin công ty dựa trên stt_cty
$sql = "SELECT * FROM cong_ty WHERE stt_cty = ? AND trang_thai = 'Đã duyệt'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $stt_cty);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Kiểm tra nếu không tìm thấy công ty
if (!$row) {
    die("Lỗi: Không tìm thấy công ty với stt_cty = $stt_cty hoặc công ty chưa được duyệt.");
}
$stmt->close();

// Truy vấn 3 tin tuyển dụng ngẫu nhiên, ưu tiên công ty hiện tại
$random_jobs = [];
// Lấy jobs của công ty hiện tại
$sql = "SELECT td.ma_tuyen_dung, td.tieu_de, td.dia_chi, ct.ten_cong_ty, ct.logo
        FROM tuyen_dung td
        JOIN cong_ty ct ON td.stt_cty = ct.stt_cty
        WHERE td.trang_thai = 'Đã duyệt' AND td.stt_cty = ?
        ORDER BY RAND() LIMIT 3";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $stt_cty);
$stmt->execute();
$result = $stmt->get_result();
while ($job = $result->fetch_assoc()) {
    $random_jobs[] = $job;
}
$stmt->close();

$jobs_needed = 3 - count($random_jobs);
if ($jobs_needed > 0) {
    // Lấy thêm jobs từ các công ty khác nếu cần
    $sql = "SELECT td.ma_tuyen_dung, td.tieu_de, td.dia_chi, ct.ten_cong_ty, ct.logo
            FROM tuyen_dung td
            JOIN cong_ty ct ON td.stt_cty = ct.stt_cty
            WHERE td.trang_thai = 'Đã duyệt' AND td.stt_cty != ?
            ORDER BY RAND() LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $stt_cty, $jobs_needed);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($job = $result->fetch_assoc()) {
        $random_jobs[] = $job;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin công ty - <?php echo htmlspecialchars($row['ten_cong_ty']); ?></title>
    <link rel="stylesheet" href="./giaodien_thongtincty.css">
    <link rel="stylesheet" href="../sinh_vien/footer.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        #map {
            width: 100%;
            height: 200px;
            border: 1px solid #ccc;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

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

        .random-jobs-section {
            padding: 40px 20px;
            background-color: #f8f9fa;
        }

        .random-jobs-section h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
            color: #333;
        }

        .random-jobs-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .job-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            padding: 20px;
            transition: transform 0.2s;
        }

        .job-card:hover {
            transform: translateY(-5px);
        }

        .job-card img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .job-card h3 {
            font-size: 18px;
            margin: 0 0 10px;
            color: #0078d4;
        }

        .job-card p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }

        .job-card a {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background-color: #28a745;

            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .job-card a:hover {
            background-color: #2e7d32;
        }

        @media (max-width: 768px) {
            .job-card {
                width: 100%;
                max-width: 300px;
            }
        }

        /* Thanh tìm kiếm */
        .timkiem-job {
            background: #19734e;
            padding: 20px;
            text-align: center;
        }

        .search-bar {
            display: inline-flex;
            gap: 10px;
            background: #fff;
            padding: 12px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .search-bar input,
        .search-bar select,
        .search-bar button {
            padding: 10px;
            border: none;
            border-radius: 5px;
        }

        .search-bar input {
            width: 800px;
        }

        .search-bar select {
            width: 200px;
            background: #f8f9fa;
        }

        .search-bar button {
            background: #28a745;
            color: #fff;
            cursor: pointer;
        }

        .search-bar button:hover {
            background: #218838;
        }

        #searchLoading {
            margin-left: 10px;
            color: #28a745;
            font-size: 16px;
            align-self: center;
        }

        #searchResults {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            margin-top: 5px;
            display: none;
        }

        .search-result-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            margin-right: 10px;
        }

        .search-result-item h4 {
            margin: 0;
            font-size: 16px;
        }

        .search-result-item h4 a {
            color: #0078d4;
            text-decoration: none;
        }

        .search-result-item h4 a:hover {
            text-decoration: underline;
        }

        .search-result-item p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #555;
        }

        @media (max-width: 900px) {
            .search-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-bar input,
            .search-bar select {
                width: 100%;
                max-width: 500px;
                margin-bottom: 10px;
            }

            #searchResults {
                position: static;
                max-height: 200px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="left-section">
            <div class="logo">
                <img alt="Logo" height="40" src="../img/logo.png" width="100%" />
            </div>
            <div class="ten_trg">
                <h3>ĐẠI HỌC TÀI NGUYÊN VÀ MÔI TRƯỜNG HÀ NỘI</h3>
                <p>Hanoi University of Natural Resources and Environment</p>
            </div>
        </div>
        <div class="nav">
            <div class="account">
                <?php
                if (isset($_SESSION['name'])) {
                    echo '<div class="dropdown">';
                    echo '<span class="user-name">Xin chào, ' . htmlspecialchars($_SESSION['name']) . '</span>';
                    echo '<div class="dropdown-content">';
                    echo '<a href="../dang_nhap_dang_ki/logic_dangxuat.php">Đăng xuất</a>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
            <a href="./giaodien_sinhvien.php">Trang chủ</a>
            <?php
            if (!isset($_SESSION['name'])) {
                echo '<a class="btn" href="../dang_nhap_dang_ki/form_dn.php">Đăng nhập</a>';
                echo '<a class="btn" href="../dang_nhap_dang_ki/form_dk.php">Đăng ký</a>';
            }
            ?>
            <?php
            if (isset($_SESSION['name'])) {
                echo '<a href="./profile.php"><i class="fa-solid fa-user"></i></a>';
            } else {
                echo '<a href="../dang_nhap_dang_ki/form_dn.php"><i class="fa-solid fa-user"></i></a>';
            }
            ?>
        </div>
    </div>

    <div class="timkiem-job">
        <div class="search-bar">
            <input id="searchInput" placeholder="Tìm theo tiêu đề, công ty..." type="text" />
            <select id="locationFilter">
                <option value="">Địa điểm</option>
                <option value="Ba Đình">Ba Đình</option>
                <option value="Hoàn Kiếm">Hoàn Kiếm</option>
                <option value="Tây Hồ">Tây Hồ</option>
                <option value="Cầu Giấy">Cầu Giấy</option>
                <option value="Đống Đa">Đống Đa</option>
                <option value="Hai Bà Trưng">Hai Bà Trưng</option>
                <option value="Hoàng Mai">Hoàng Mai</option>
                <option value="Long Biên">Long Biên</option>
                <option value="Nam Từ Liêm">Nam Từ Liêm</option>
                <option value="Bắc Từ Liêm">Bắc Từ Liêm</option>
                <option value="Thanh Xuân">Thanh Xuân</option>
                <option value="Sơn Tây">Sơn Tây</option>
                <option value="Ba Vì">Ba Vì</option>
                <option value="Chương Mỹ">Chương Mỹ</option>
                <option value="Đan Phượng">Đan Phượng</option>
                <option value="Đông Anh">Đông Anh</option>
                <option value="Gia Lâm">Gia Lâm</option>
                <option value="Hoài Đức">Hoài Đức</option>
                <option value="Mỹ Đức">Mỹ Đức</option>
                <option value="Phú Xuyên">Phú Xuyên</option>
                <option value="Quốc Oai">Quốc Oai</option>
                <option value="Thạch Thất">Thạch Thất</option>
                <option value="Thái Nguyên">Thái Nguyên</option>
                <option value="Thường Tín">Thường Tín</option>
                <option value="Ứng Hòa">Ứng Hòa</option>
                <option value="Phúc Thọ">Phúc Thọ</option>
                <option value="Hà Nội (ngoại thành)">Hà Nội (ngoại thành)</option>
            </select>
            <button onclick="searchJobs()">Tìm kiếm</button>
            <span id="searchLoading" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
            <div id="searchResults"></div>
        </div>
    </div>

    <!-- Thông tin chi tiết của công ty -->
    <div class="company-list">
        <div class="company-container">
            <div class="company-header">
                <!-- Ảnh bìa -->
                <div class="company-cover">
                    <img src="<?php echo $row['anh_bia'] ? './Uploads/' . htmlspecialchars($row['anh_bia']) : './Uploads/default_banner.jpg'; ?>" alt="Ảnh bìa công ty">
                </div>
                <!-- Logo -->
                <div class="company-logo">
                    <img src="<?php echo $row['logo'] ? './Uploads/' . htmlspecialchars($row['logo']) : './Uploads/default_logo.png'; ?>" alt="Logo Công Ty">
                </div>
                <!-- Thông tin công ty -->
                <div class="company-info">
                    <h2><?php echo htmlspecialchars($row['ten_cong_ty']); ?></h2>
                    <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a>
                </div>
            </div>

            <!-- Nội dung chi tiết -->
            <div class="company-content">
                <div class="company-left">
                    <h3>Giới thiệu công ty</h3>
                    <p><?php echo nl2br(htmlspecialchars($row['gioi_thieu'])); ?></p>
                </div>
                <div class="company-right">
                    <h3>Thông tin liên hệ</h3>
                    <p><strong>Lĩnh vực:</strong> <?php echo htmlspecialchars($row['linh_vuc']); ?></p>
                    <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($row['dia_chi']); ?></p>
                    <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($row['so_dien_thoai']); ?></p>
                    <p><strong>Email:</strong>
                        <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>">
                            <?php echo htmlspecialchars($row['email']); ?>
                        </a>
                    </p>
                    <div id="map" style="height: 300px; margin-top: 10px; border-radius: 10px;"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Random Jobs Section -->
    <section class="random-jobs-section">
        <h2>Một số tin thực tập</h2>
        <div class="random-jobs-container">
            <?php if (empty($random_jobs)): ?>
                <p>Không có tin tuyển dụng nào để hiển thị.</p>
            <?php else: ?>
                <?php foreach ($random_jobs as $random_job): ?>
                    <div class="job-card">
                        <img src="<?php echo !empty($random_job['logo']) ? './Uploads/' . htmlspecialchars($random_job['logo']) : './Uploads/default_logo.png'; ?>" alt="Company Logo" />
                        <h3><?php echo htmlspecialchars($random_job['tieu_de']); ?></h3>
                        <p><strong>Công ty:</strong> <?php echo htmlspecialchars($random_job['ten_cong_ty']); ?></p>
                        <p><strong>Địa điểm:</strong> <?php echo htmlspecialchars($random_job['dia_chi']); ?></p>
                        <a href="chi_tiet.php?ma_tuyen_dung=<?php echo htmlspecialchars($random_job['ma_tuyen_dung']); ?>">Xem chi tiết</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <img src="../img/logo.png" alt="TopCV Logo" class="footer-logo" />
                <p>Tiếp lợi thế - Nối thành công</p>
                <img src="../img/google_for_startup.webp" alt="Google for Startups" />
                <p>Liên hệ</p>
                <p>Hotline: <a href="tel:02466805958"> 0902.130.130</a> (Giờ hành chính)</p>
                <p>Email: <a href="mailto:hotro@topcv.vn">DHTNMT@hunre.edu.vn</a></p>
                <p>Ứng dụng tải xuống</p>
                <div class="app-links">
                    <img src="../img/app_store.webp" alt="App Store" />
                    <img src="../img/chplay.webp" alt="Google Play" />
                </div>
                <div class="social-icons">
                    <a href="#"><img src="../img/facebook.webp" alt="Facebook" /></a>
                    <a href="#"><img src="../img/youtube.webp" alt="YouTube" /></a>
                    <a href="#"><img src="../img/linkedin.webp" alt="LinkedIn" /></a>
                    <a href="#"><img src="../img/tiktok.webp" alt="TikTok" /></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Về TopCV</h4>
                <ul>
                    <li><a href="#">Giới thiệu</a></li>
                    <li><a href="#">Góc báo chí</a></li>
                    <li><a href="#">Tuyển dụng</a></li>
                    <li><a href="#">Liên hệ</a></li>
                    <li><a href="#">Hỏi đáp</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Hồ sơ và CV</h4>
                <ul>
                    <li><a href="#">Quản lý CV của bạn</a></li>
                    <li><a href="#">TopCV Profile</a></li>
                    <li><a href="#">Hướng dẫn viết CV</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Khám phá</h4>
                <ul>
                    <li><a href="#">Ứng dụng di động TopCV</a></li>
                    <li><a href="#">Tính lương Gross - Net</a></li>
                    <li><a href="#">Tính lãi suất kép</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Xây dựng sự nghiệp</h4>
                <ul>
                    <li><a href="#">Việc làm tốt nhất</a></li>
                    <li><a href="#">Việc làm lương cao</a></li>
                    <li><a href="#">Việc làm quản lý</a></li>
                </ul>
            </div>
        </div>
    </footer>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const diaChi = <?php echo json_encode($row['dia_chi']); ?>;

            // Gọi API Nominatim để lấy tọa độ từ địa chỉ
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(diaChi)}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = data[0].lat;
                        const lon = data[0].lon;

                        // Hiển thị bản đồ tại địa chỉ đó
                        const map = L.map('map').setView([lat, lon], 15);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(map);

                        L.marker([lat, lon])
                            .addTo(map)
                            .bindPopup("Địa chỉ công ty: " + diaChi)
                            .openPopup();
                    } else {
                        document.getElementById('map').innerHTML = "Không thể tìm thấy bản đồ cho địa chỉ này.";
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi gọi API geocoding:", error);
                    document.getElementById('map').innerHTML = "Lỗi khi tải bản đồ.";
                });
        });

        // Placeholder for searchCompanies function (not implemented)
        function searchCompanies() {
            alert("Chức năng tìm kiếm công ty đang được phát triển!");
        }
    </script>

    <script>
        function showApplicationModal() {
            document.getElementById('applicationModal').style.display = 'flex';
        }

        function closeApplicationModal() {
            document.getElementById('applicationModal').style.display = 'none';
        }

        function searchJobs() {
            const keyword = document.getElementById('searchInput').value;
            const location = document.getElementById('locationFilter').value;
            const khoa = document.getElementById('searchInput').value; // Use same input for khoa
            const resultsDiv = document.getElementById('searchResults');
            const loadingSpan = document.getElementById('searchLoading');

            resultsDiv.innerHTML = '';
            resultsDiv.style.display = 'none';
            loadingSpan.style.display = 'inline-block';

            fetch(`../logic_sinhvien/logic_search_jobs.php?action=search&khoa=${encodeURIComponent(khoa)}&keyword=${encodeURIComponent(keyword)}&location=${encodeURIComponent(location)}`)
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP error ${response.status}: ${text.substring(0, 100)}...`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    loadingSpan.style.display = 'none';
                    if (data.success && data.data.length > 0) {
                        resultsDiv.style.display = 'block';
                        data.data.forEach(job => {
                            const jobDiv = document.createElement('div');
                            jobDiv.className = 'search-result-item';
                            jobDiv.innerHTML = `
                            <img src="${job.logo}" alt="Company Logo" />
                            <div>
                                <h4><a href="chi_tiet.php?ma_tuyen_dung=${job.ma_tuyen_dung}">${job.tieu_de}</a></h4>
                                <p>${job.ten_cong_ty} - ${job.dia_chi}</p>
                            </div>
                        `;
                            resultsDiv.appendChild(jobDiv);
                        });
                    } else {
                        resultsDiv.style.display = 'block';
                        resultsDiv.innerHTML = '<p>Không tìm thấy công việc phù hợp.</p>';
                    }
                })
                .catch(error => {
                    loadingSpan.style.display = 'none';
                    resultsDiv.style.display = 'block';
                    resultsDiv.innerHTML = '<p>Lỗi khi tìm kiếm: ' + error.message + '</p>';
                    console.error('Search error:', error);
                });
        }

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchJobs();
            }
        });
    </script>
</body>

</html>

<?php
$conn->close();
?>