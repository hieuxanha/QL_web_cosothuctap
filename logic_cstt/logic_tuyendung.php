<?php
require '../db.php'; // Kết nối CSDL

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tieu_de = $_POST["tieu_de_tuyen_dung"];
    $quy_mo = $_POST["quy_mo"];
    $khoa = $_POST["khoa"];
    $mo_ta = $_POST["mo_ta"];
    $so_luong = $_POST["so_luong"];
    $yeu_cau = $_POST["yeu_cau"];
    $han_nop = $_POST["han_nop"];

    // Xử lý ảnh tải lên
    $hinh_anh = null;
    if (!empty($_FILES["anh"]["name"])) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["anh"]["name"]);
        if (move_uploaded_file($_FILES["anh"]["tmp_name"], $target_file)) {
            $hinh_anh = $target_file;
        }
    }

    // Chèn vào CSDL (bỏ phuc_loi)
    $sql = "INSERT INTO tuyen_dung (tieu_de, quy_mo, khoa, mo_ta, so_luong, yeu_cau, han_nop, hinh_anh) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sississs", $tieu_de, $quy_mo, $khoa, $mo_ta, $so_luong, $yeu_cau, $han_nop, $hinh_anh);
    
    if ($stmt->execute()) {
        echo "Đăng tin thành công!";
    } else {
        echo "Lỗi: " . $stmt->error;
    }
}
?>
