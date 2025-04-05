<?php
session_start();
require_once '../db.php'; // Kết nối CSDL

header('Content-Type: application/json');

// Hàm lấy danh sách tất cả tài khoản
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_users') {
    $users = [];

    // Lấy danh sách sinh viên
    $sql_sinh_vien = "SELECT stt_sv AS id, ho_ten, email, role FROM sinh_vien";
    $result_sinh_vien = $conn->query($sql_sinh_vien);
    while ($row = $result_sinh_vien->fetch_assoc()) {
        $users[] = [
            'id' => $row['id'],
            'name' => $row['ho_ten'],
            'email' => $row['email'],
            'role' => $row['role'],
            'table' => 'sinh_vien'
        ];
    }

    // Lấy danh sách giảng viên
    $sql_giang_vien = "SELECT stt_gv AS id, ho_ten, email, role FROM giang_vien";
    $result_giang_vien = $conn->query($sql_giang_vien);
    while ($row = $result_giang_vien->fetch_assoc()) {
        $users[] = [
            'id' => $row['id'],
            'name' => $row['ho_ten'],
            'email' => $row['email'],
            'role' => $row['role'],
            'table' => 'giang_vien'
        ];
    }

    // Lấy danh sách cơ sở thực tập
    $sql_co_so = "SELECT stt_cstt AS id, ten_co_so AS ho_ten, email, role FROM co_so_thuc_tap";
    $result_co_so = $conn->query($sql_co_so);
    while ($row = $result_co_so->fetch_assoc()) {
        $users[] = [
            'id' => $row['id'],
            'name' => $row['ho_ten'],
            'email' => $row['email'],
            'role' => $row['role'],
            'table' => 'co_so_thuc_tap'
        ];
    }

    echo json_encode(['success' => true, 'users' => $users]);
    exit;
}

// Hàm cập nhật quyền
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_role') {
    $id = $_POST['id'];
    $new_role = $_POST['new_role'];
    $table = $_POST['table'];

    // Xác định bảng và cột ID tương ứng
    $id_column = '';
    if ($table === 'sinh_vien') {
        $id_column = 'stt_sv';
    } elseif ($table === 'giang_vien') {
        $id_column = 'stt_gv';
    } elseif ($table === 'co_so_thuc_tap') {
        $id_column = 'stt_cstt';
    }

    // Kiểm tra quyền hợp lệ
    if (!in_array($new_role, ['sinh_vien', 'giang_vien', 'co_so_thuc_tap'])) {
        echo json_encode(['success' => false, 'error' => 'Quyền không hợp lệ!']);
        exit;
    }

    // Cập nhật quyền trong CSDL
    $sql = "UPDATE $table SET role = ? WHERE $id_column = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_role, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật quyền thành công!']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Lỗi khi cập nhật quyền: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

// Hàm xóa tài khoản
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    $id = $_POST['id'];
    $table = $_POST['table'];

    // Xác định bảng và cột ID tương ứng
    $id_column = '';
    if ($table === 'sinh_vien') {
        $id_column = 'stt_sv';
    } elseif ($table === 'giang_vien') {
        $id_column = 'stt_gv';
    } elseif ($table === 'co_so_thuc_tap') {
        $id_column = 'stt_cstt';
    }

    // Xóa tài khoản từ CSDL
    $sql = "DELETE FROM $table WHERE $id_column = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Xóa tài khoản thành công!']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Lỗi khi xóa tài khoản: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

echo json_encode(['success' => false, 'error' => 'Yêu cầu không hợp lệ!']);
$conn->close();
?>