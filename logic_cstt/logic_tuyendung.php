<?php
session_start();
require_once '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['them_tuyen_dung'])) {
    // Lấy dữ liệu từ form
    $ma_tuyen_dung = rand(1000, 9999); // Tạo mã ngẫu nhiên
    $stt_cty = $_POST['stt_cty'];
    $tieu_de = $_POST['tieu_de_tuyen_dung'];
    $dia_chi = $_POST['dia_chi'];
    $hinh_thuc = $_POST['hinh_thuc'];
    $gioi_tinh = $_POST['gioi_tinh'];
    $mo_ta = $_POST['mo_ta'] ?? '';
    $so_luong = $_POST['so_luong'];
    $yeu_cau = $_POST['yeu_cau'] ?? '';
    $han_nop = $_POST['han_nop'];
    $noi_bat = isset($_POST['noi_bat']) ? 1 : 0;

    // Kiểm tra các trường bắt buộc
    if (empty($stt_cty) || empty($tieu_de) || empty($dia_chi) || empty($hinh_thuc) || empty($gioi_tinh) || empty($so_luong) || empty($han_nop)) {
        $_SESSION['error'] = "Vui lòng điền đầy đủ các trường bắt buộc!";
        header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
        exit;
    }

    // Thêm tin tuyển dụng vào cơ sở dữ liệu
    $sql = "INSERT INTO tuyen_dung (ma_tuyen_dung, stt_cty, tieu_de, dia_chi, hinh_thuc, gioi_tinh, mo_ta, so_luong, yeu_cau, han_nop, noi_bat, trang_thai) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Đang chờ')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssssisss", $ma_tuyen_dung, $stt_cty, $tieu_de, $dia_chi, $hinh_thuc, $gioi_tinh, $mo_ta, $so_luong, $yeu_cau, $han_nop, $noi_bat);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Tin tuyển dụng đã được gửi để chờ duyệt!";
    } else {
        $_SESSION['error'] = "Lỗi khi thêm tin tuyển dụng: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit;
} else {
    $_SESSION['error'] = "Yêu cầu không hợp lệ!";
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit;
}
?>