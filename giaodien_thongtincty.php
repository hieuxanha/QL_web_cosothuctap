<?php
require 'db.php'; // Kết nối CSDL

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
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin công ty - <?php echo htmlspecialchars($row['ten_cong_ty']); ?></title>
    <link rel="stylesheet" href="./giaodien_thongtincty.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="header">
        <div class="left-section">
            <div class="logo">
                <img alt="Logo" height="40" src="./logo.png" width="100%" />
            </div>
            <div class="ten_trg">
                <h3>ĐẠI HỌC TÀI NGUYÊN VÀ MÔI TRƯỜNG HÀ NỘI</h3>
                <p>Hanoi University of Natural Resources and Environment</p>
            </div>
        </div>
        <div class="nav">
            <a href="#">Việc làm</a>
            <a href="#">Hồ sơ & CV</a>
            <a class="btn" href="./form_dn.html">Đăng nhập</a>
            <a class="btn" href="./form_dk.html">Đăng ký</a>
            <a href="#"><i class="fa-solid fa-user"></i></a>
        </div>
        
    </div>
    <div class="timkiem-job">
        <div class="search-bar">
            <input placeholder="Tìm kiếm công ty..." type="text" id="searchInput" />
            <button onclick="searchCompanies()">Tìm kiếm</button>
        </div>
    </div>

    <!-- Thông tin chi tiết của công ty -->
    <div class="company-list">
        <div class="company-container">
            <div class="company-header">
                <!-- Ảnh bìa -->
                <div class="company-cover">
                    <img src="<?php echo $row['anh_bia'] ? './uploads/' . htmlspecialchars($row['anh_bia']) : 'default_banner.jpg'; ?>" alt="Ảnh bìa công ty">
                </div>
                <!-- Logo -->
                <div class="company-logo">
                    <img src="<?php echo $row['logo'] ? './uploads/' . htmlspecialchars($row['logo']) : 'default_logo.png'; ?>" alt="Logo Công Ty">
                </div>
                <!-- Thông tin công ty -->
                <div class="company-info">
                    <h2><?php echo htmlspecialchars($row['ten_cong_ty']); ?></h2>
                    <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a>
                </div>
                <!-- Nút theo dõi -->
                <div>
                    <a class="follow-button" href="#">+ Theo dõi công ty</a>
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
                    <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($row['dia_chi']); ?></p>
                    <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($row['so_dien_thoai']); ?></p>
                    <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></p>
                    <div class="map">Xem bản đồ</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nút Góp ý -->
    <div class="feedback-button">Góp ý</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>