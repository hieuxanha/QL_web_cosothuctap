<?php
session_start();
require '../db.php'; // Kết nối CSDL

// Kiểm tra nếu form được gửi đi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $ten_cong_ty = $_POST['ten_cong_ty'] ?? '';
    $dia_chi = $_POST['dia_chi'] ?? '';
    $so_dien_thoai = $_POST['so_dien_thoai'] ?? '';
    $email = $_POST['email'] ?? '';
    $gioi_thieu = $_POST['gioi_thieu'] ?? '';
    $quy_mo = $_POST['quy_mo'] ?? null; // Lấy giá trị quy_mo, nếu không có thì để null
    $linh_vuc = $_POST['linh_vuc'] ?? null; // Lấy giá trị linh_vuc, nếu không có thì để null

    // Kiểm tra dữ liệu đầu vào
    if (empty($ten_cong_ty) || empty($dia_chi) || empty($so_dien_thoai) || empty($email)) {
        $_SESSION['error'] = "Vui lòng điền đầy đủ các trường bắt buộc!";
        header("Location: ../co_so_thuc_tap/ui_capnhat_cty.php");
        exit();
    }

    // Đảm bảo thư mục uploads tồn tại
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Xử lý tải lên logo
    $logo = null;
    if (!empty($_FILES["logo"]["name"])) {
        $logo_name = time() . "_logo_" . basename($_FILES["logo"]["name"]); // Đổi tên file tránh trùng lặp
        $target_logo = $target_dir . $logo_name;

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_logo)) {
            $logo = $logo_name; // Chỉ lưu tên file
        } else {
            $_SESSION['error'] = "Lỗi khi tải lên logo!";
            header("Location: ../co_so_thuc_tap/ui_capnhat_cty.php");
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
            $_SESSION['error'] = "Lỗi khi tải lên ảnh bìa!";
            header("Location: ../co_so_thuc_tap/ui_capnhat_cty.php");
            exit();
        }
    }

    // Loại bỏ khoảng trắng thừa
    $ten_cong_ty = trim($ten_cong_ty);
    $dia_chi = trim($dia_chi);
    $so_dien_thoai = trim($so_dien_thoai);
    $email = trim($email);
    $gioi_thieu = trim($gioi_thieu);
    $quy_mo = $quy_mo ? trim($quy_mo) : null; // Chỉ trim nếu không null
    $linh_vuc = $linh_vuc ? trim($linh_vuc) : null; // Chỉ trim nếu không null

    // Đặt trạng thái mặc định là 'Đang chờ' (đồng bộ với định nghĩa bảng)
    $trang_thai = 'Đang chờ';

    // Câu lệnh SQL để thêm công ty vào database
    $sql = "INSERT INTO cong_ty (ten_cong_ty, dia_chi, so_dien_thoai, email, gioi_thieu, logo, anh_bia, trang_thai, quy_mo, linh_vuc) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $ten_cong_ty, $dia_chi, $so_dien_thoai, $email, $gioi_thieu, $logo, $anh_bia, $trang_thai, $quy_mo, $linh_vuc);

    // Thực thi câu lệnh
    if ($stmt->execute()) {
        $_SESSION['message'] = "Thêm công ty thành công!";
        header("Location: ../co_so_thuc_tap/ui_capnhat_cty.php"); // Điều hướng sau khi thêm thành công
        exit();
    } else {
        $_SESSION['error'] = "Lỗi: " . $stmt->error;
        header("Location: ../co_so_thuc_tap/ui_capnhat_cty.php");
        exit();
    }

    // Đóng statement
    $stmt->close();
}

// Đóng kết nối
$conn->close();
?>