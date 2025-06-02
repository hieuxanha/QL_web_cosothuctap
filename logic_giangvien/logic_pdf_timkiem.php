<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../db.php';
$conn->set_charset("utf8mb4");

header('Content-Type: application/json');

function sendResponse($success, $data = [], $error = '')
{
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'error' => $error
    ]);
    exit;
}

// Check if the user is a lecturer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'giang_vien' || !isset($_SESSION['so_hieu_giang_vien'])) {
    sendResponse(false, [], 'Không có quyền truy cập hoặc chưa đăng nhập.');
}

// Validate CSRF token
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    sendResponse(false, [], 'Token CSRF không hợp lệ.');
}

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$so_hieu_giang_vien = $_SESSION['so_hieu_giang_vien'];

try {
    $sql = "
        SELECT 
            p.id, 
            p.stt_danhgia, 
            p.filename, 
            p.filepath, 
            p.created_at, 
            dg.ma_sinh_vien, 
            sv.ho_ten, 
            dg.ket_qua_de_xuat
        FROM pdf_nhan p
        JOIN danh_gia_thuc_tap dg ON p.stt_danhgia = dg.stt_danhgia
        JOIN sinh_vien sv ON dg.stt_sv = sv.stt_sv
        WHERE sv.so_hieu = ?";
    $params = [$so_hieu_giang_vien];

    if ($keyword) {
        $sql .= " AND (sv.ho_ten LIKE ? OR p.filename LIKE ?)";
        $likeKeyword = "%$keyword%";
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
    }

    $sql .= " ORDER BY p.created_at DESC LIMIT 10";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception('Lỗi prepare: ' . $conn->error);
    }

    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    if (!$stmt->execute()) {
        throw new Exception('Lỗi execute: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $files = [];
    while ($row = $result->fetch_assoc()) {
        $files[] = [
            'id' => $row['id'],
            'stt_danhgia' => $row['stt_danhgia'],
            'filename' => $row['filename'],
            'filepath' => basename($row['filepath']),
            'created_at' => $row['created_at'],
            'ma_sinh_vien' => $row['ma_sinh_vien'],
            'ho_ten' => $row['ho_ten'],
            'ket_qua_de_xuat' => $row['ket_qua_de_xuat']
        ];
    }

    $stmt->close();
    sendResponse(true, ['files' => $files]);
} catch (Exception $e) {
    sendResponse(false, [], 'Lỗi khi truy vấn cơ sở dữ liệu: ' . $e->getMessage());
} finally {
    $conn->close();
}
