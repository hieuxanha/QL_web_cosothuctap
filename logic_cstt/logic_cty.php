<?php
require '../db.php'; // Kết nối CSDL

// Kiểm tra nếu form được gửi đi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_cong_ty = $_POST['ten_cong_ty'];
    $dia_chi = $_POST['dia_chi'];
    $so_dien_thoai = $_POST['so_dien_thoai'];
    $email = $_POST['email'];
    $gioi_thieu = $_POST['gioi_thieu'];

    // Kiểm tra dữ liệu đầu vào
    if (empty($ten_cong_ty) || empty($dia_chi) || empty($so_dien_thoai) || empty($email)) {
        echo "Vui lòng điền đầy đủ các trường bắt buộc!";
        exit();
    }

    $target_dir = "../uploads/";

    // Xử lý tải lên logo
    $logo = null;
    if (!empty($_FILES["logo"]["name"])) {
        $logo_name = time() . "_logo_" . basename($_FILES["logo"]["name"]); // Đổi tên file tránh trùng lặp
        $target_logo = $target_dir . $logo_name;

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_logo)) {
            $logo = $logo_name; // Chỉ lưu tên file
        } else {
            echo "Lỗi khi tải lên logo!";
            exit();
        }
    }

    // Xử lý tải lên ảnh bìa
    $anh_bia = null;
    if (!empty($_FILES["anh_bia"]["name"])) {
        $anh_bia_name = time() . "_anhbia_" . basename($_FILES["anh_bia"]["name"]); 
        $target_anh_bia = $target_dir . $anh_bia_name;

        if (move_uploaded_file($_FILES["anh_bia"]["tmp_name"], $target_anh_bia)) {
            $anh_bia = $anh_bia_name; 
        } else {
            echo "Lỗi khi tải lên ảnh bìa!";
            exit();
        }
    }

    // Câu lệnh SQL để thêm công ty vào database
    $sql = "INSERT INTO cong_ty (ten_cong_ty, dia_chi, so_dien_thoai, email, gioi_thieu, logo, anh_bia, trang_thai) 
    VALUES (?, ?, ?, ?, ?, ?, ?, 'Chờ duyệt')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $ten_cong_ty, $dia_chi, $so_dien_thoai, $email, $gioi_thieu, $logo, $anh_bia);
    // Thực thi câu lệnh
    if ($stmt->execute()) {
        echo "Thêm công ty thành công!";
        header("Location: ../co_so_thuc_tap/ui_capnhat_cty.php"); // Điều hướng sau khi thêm thành công
        exit();
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    // Đóng statement
    $stmt->close();
}

// Đóng kết nối
$conn->close();
?>
