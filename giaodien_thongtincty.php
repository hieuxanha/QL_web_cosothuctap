<?php

require './db.php';



$sql = "SELECT * FROM cong_ty ORDER BY stt_cty  DESC LIMIT 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$company = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./giaodien_thongtincty.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>
<body>
    <div class="header">
        <div class="left-section">
            <div class="logo">
                <img alt="TopCV Logo" height="40" src="/logo.png" width="100%" />
            </div>
            <div class="ten_trg">
                <h3>ĐẠI HỌC TRƯỜNG NGUYÊN MÔI TRƯỜNG HÀ NỘI</h3>
                <p>Hanoi University of Natural Resources and Environment</p>
            </div>
        </div>
        <div class="nav">
            <a href="#">Việc làm</a>
            <a href="#">Hồ sơ &amp; CV</a>
            <a class="btn" href="./form_dn.html">Đăng nhập</a>
            <a class="btn" href="./form_dk.html">Đăng ký</a>
            <a href="#"><i class="fa-solid fa-user"></i></a>
        </div>
    </div>
    
    <div class="timkiem-job">
      <div class="search-bar">
          <input placeholder="Khoa ..." type="text" />
          <button>Tìm kiếm</button>
      </div>
    </div>
    <!-- gioi thieu cty -->
    <div class="company-container">
        <div class="company-header">
            <!-- Ảnh nền -->
            <div class="company-cover">
                <?php
                require './db.php';
                $result = $conn->query("SELECT * FROM cong_ty ORDER BY stt_cty DESC LIMIT 1");
                $row = $result->fetch_assoc();
                if ($row['anh_bia']) {
                    echo '<img src="../uploads/' . $row['anh_bia'] . '" alt="Ảnh bìa công ty">';
                } else {
                    echo '<img src="default_banner.jpg" alt="Ảnh bìa mặc định">';
                }
                ?>
            </div>
            
        
            <div class="company-logo">
                <?php
                if ($row['logo']) {
                    echo '<img src="../uploads/' . $row['logo'] . '" alt="Logo Công Ty">';
                } else {
                    echo '<img src="default_logo.png" alt="Logo mặc định">';
                }
                ?>
            </div>
            
            <!-- Nội dung công ty -->
            <div class="company-info">
            <h2><?php echo htmlspecialchars($company['ten_cong_ty']); ?></h2>
            <a href="<?php echo htmlspecialchars($company['email']); ?>"><?php echo htmlspecialchars($company['email']); ?></a>
        </div>

        <!-- Nút theo dõi -->
        <div>
            <a class="follow-button" href="#">+ Theo dõi công ty</a>
        </div>
        </div>
        
    
      
         <!-- Nội dung chi tiết -->
    <div class="company-content">
        <!-- Cột trái -->
        <div class="company-left">
            <h3>Giới thiệu công ty</h3>
            <p><?php echo nl2br(htmlspecialchars($company['gioi_thieu'])); ?></p>
        </div>

        <!-- Cột phải -->
        <div class="company-right">
            <h3>Thông tin liên hệ</h3>
            <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($company['dia_chi']); ?></p>
            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($company['so_dien_thoai']); ?></p>
            <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($company['email']); ?>"><?php echo htmlspecialchars($company['email']); ?></a></p>
            <div class="map">Xem bản đồ</div>
        </div>
    </div>
    </div>
    
    <!-- Nút Góp ý -->
    <div class="feedback-button">Góp ý</div>
    



    
</body>
</html>