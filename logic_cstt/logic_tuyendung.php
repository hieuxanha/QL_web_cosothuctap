<?php
require_once '../db.php'; // Kết nối database

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['them_tuyen_dung'])) {
    $tieu_de = trim($_POST["tieu_de_tuyen_dung"]); // Sửa tên trường
    $stt_cty = intval($_POST["stt_cty"]); // Công ty đã có sẵn
    $mo_ta = trim($_POST["mo_ta"] ?? "");
    $yeu_cau = trim($_POST["yeu_cau"] ?? "");
    $so_luong = intval($_POST["so_luong"]);
    $han_nop = $_POST["han_nop"];
    $dia_chi = trim($_POST["dia_chi"]);
    $hinh_thuc = trim($_POST["hinh_thuc"]);
    $gioi_tinh = trim($_POST["gioi_tinh"]);

    // Tạo mã tuyển dụng tự động (VD: TD20240326)
    $ma_tuyen_dung = "TD" . date("Ymd") . rand(100, 999);

    // Mặc định trạng thái là 'Đang chờ'
    $trang_thai = 'Đang chờ';

    // Kiểm tra dữ liệu đầu vào
    if (empty($tieu_de) || empty($stt_cty) || empty($dia_chi) || empty($hinh_thuc) || empty($gioi_tinh) || empty($so_luong) || empty($han_nop)) {
        die("Vui lòng nhập đầy đủ thông tin tuyển dụng!");
    }

    // Chèn dữ liệu vào bảng `tuyen_dung`
    $sql = "INSERT INTO tuyen_dung (ma_tuyen_dung, tieu_de, stt_cty, mo_ta, yeu_cau, so_luong, han_nop, trang_thai, dia_chi, hinh_thuc, gioi_tinh) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssississsss", $ma_tuyen_dung, $tieu_de, $stt_cty, $mo_ta, $yeu_cau, $so_luong, $han_nop, $trang_thai, $dia_chi, $hinh_thuc, $gioi_tinh);

    if ($stmt->execute()) {
        echo "Đăng tin tuyển dụng thành công!";
        header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php"); // Chuyển hướng sau khi thành công
        exit();
    } else {
        echo "Lỗi khi đăng tin: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Phương thức không hợp lệ!";
}
?>