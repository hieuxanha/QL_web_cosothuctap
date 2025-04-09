<?php
// Khởi tạo session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Hủy toàn bộ session
session_unset(); // Xóa tất cả các biến session
session_destroy(); // Hủy session hoàn toàn

// Chuyển hướng về trang đăng nhập hoặc trang chính
header("Location: ../dang_nhap_dang_ki/form_dn.php"); // Chuyển về trang đăng nhập
// Hoặc: header("Location: ../sinh_vien/giaodien_chinh.php"); // Chuyển về trang chính
exit();
?>