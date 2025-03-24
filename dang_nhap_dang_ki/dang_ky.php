<?php
session_start();
require '../db.php';

header('Content-Type: application/json'); // Định dạng JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hoTen = trim($_POST['ho_ten'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $userType = $_POST['user_type'] ?? '';
    $khoa = $_POST['khoa'] ?? '';

    
   
    if (empty($hoTen) || empty($email) || empty($password) || empty($userType)) {
        echo json_encode(["status" => "error", "message" => "Vui lòng nhập đầy đủ thông tin!"]);
        exit();
    }
 // Kiểm tra mật khẩu phải có ít nhất 6 ký tự
 if (strlen($password) < 6) {
    echo json_encode(["status" => "error", "message" => "Mật khẩu phải có ít nhất 6 ký tự!"]);
    exit();
}
    // Kiểm tra email trùng
    $tables = ['sinh_vien', 'giang_vien', 'co_so_thuc_tap'];
    foreach ($tables as $table) {
        $checkEmailQuery = "SELECT 1 FROM $table WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($checkEmailQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "Email đã tồn tại trong hệ thống!"]);
            exit();
        }
    }

    // Mã hóa mật khẩu
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Xử lý loại tài khoản
    switch ($userType) {
        case 'sinhvien':
            $maSinhVien = trim($_POST['ma_sinh_vien'] ?? '');
            if (empty($maSinhVien)) {
                echo json_encode(["status" => "error", "message" => "Vui lòng nhập mã sinh viên!"]);
                exit();
            }
            
            if (empty($khoa)) {
                echo json_encode(["status" => "error", "message" => "Vui lòng chọn khoa!"]);
                exit();
            }
            
            $sql = "INSERT INTO sinh_vien (ma_sinh_vien, ho_ten, email, password, khoa) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $maSinhVien, $hoTen, $email, $hashedPassword, $khoa);
            break;

        case 'giangvien':
            $soHieuGiangVien = trim($_POST['so_hieu_giangvien'] ?? '');
            if (empty($soHieuGiangVien)) {
                echo json_encode(["status" => "error", "message" => "Vui lòng nhập số hiệu giảng viên!"]);
                exit();
            }
            
            if (empty($khoa)) {
                echo json_encode(["status" => "error", "message" => "Vui lòng chọn khoa!"]);
                exit();
            }
            
            $sql = "INSERT INTO giang_vien (so_hieu_giang_vien, ho_ten, email, password, khoa) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $soHieuGiangVien, $hoTen, $email, $hashedPassword, $khoa);
            break;

        case 'coso':
            $maCoSo = trim($_POST['ma_co_so'] ?? '');
            $tenCoSo = trim($_POST['ten_co_so'] ?? '');

            if (empty($maCoSo)) {
                echo json_encode(["status" => "error", "message" => "Vui lòng nhập mã cơ sở!"]);
                exit();
            }
            
            if (empty($tenCoSo)) {
                echo json_encode(["status" => "error", "message" => "Vui lòng nhập tên cơ sở!"]);
                exit();
            }
            

            $sql = "INSERT INTO co_so_thuc_tap (ma_co_so, ten_co_so, email, password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $maCoSo, $tenCoSo, $email, $hashedPassword);
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Loại người dùng không hợp lệ!"]);
            exit();
    }

    // Thực thi lệnh SQL
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Đăng ký thành công!"]);

    } else {
        echo json_encode(["status" => "error", "message" => "Đăng ký thất bại: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
