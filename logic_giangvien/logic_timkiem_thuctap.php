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
            td.tieu_de, 
            ut.ngay_ung_tuyen, 
            sv.ho_ten, 
            sv.email, 
            sv.lop, 
            sv.khoa, 
            sv.ma_sinh_vien
        FROM sinh_vien sv
        JOIN ung_tuyen ut ON sv.stt_sv = ut.stt_sv
        JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung
        WHERE sv.so_hieu = ? AND ut.trang_thai = 'Hoàn thành'";
    $params = [$so_hieu_giang_vien];

    if ($keyword) {
        $sql .= " AND (sv.ho_ten LIKE ? OR sv.ma_sinh_vien LIKE ?)";
        $likeKeyword = "%$keyword%";
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
    }

    $sql .= " ORDER BY sv.ho_ten LIMIT 10";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception('Lỗi prepare: ' . $conn->error);
    }

    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    if (!$stmt->execute()) {
        throw new Exception('Lỗi execute: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $internships = [];
    while ($row = $result->fetch_assoc()) {
        $internships[] = [
            'tieu_de' => $row['tieu_de'],
            'ngay_ung_tuyen' => $row['ngay_ung_tuyen'],
            'ho_ten' => $row['ho_ten'],
            'email' => $row['email'],
            'lop' => $row['lop'],
            'khoa' => $row['khoa'],
            'ma_sinh_vien' => $row['ma_sinh_vien']
        ];
    }

    $stmt->close();
    sendResponse(true, ['internships' => $internships]);
} catch (Exception $e) {
    sendResponse(false, [], 'Lỗi khi truy vấn cơ sở dữ liệu: ' . $e->getMessage());
} finally {
    $conn->close();
}
