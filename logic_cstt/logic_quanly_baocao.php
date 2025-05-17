<?php
session_start();
require_once '../db.php';

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

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'search') {
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

    $sql = "
        SELECT 
            sv.stt_sv, 
            sv.ma_sinh_vien, 
            sv.ho_ten, 
            sv.email, 
            sv.lop, 
            sv.khoa, 
            sv.so_dien_thoai, 
            ut.ma_tuyen_dung, 
            ut.ngay_ung_tuyen, 
            ut.trang_thai, 
            ut.cv_path,
            td.tieu_de
        FROM sinh_vien sv
        LEFT JOIN ung_tuyen ut ON sv.stt_sv = ut.stt_sv
        LEFT JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung
        WHERE ut.id IS NOT NULL AND ut.trang_thai = 'Đồng ý'";
    $params = [];
    $conditions = [];

    if ($keyword) {
        $conditions[] = "(sv.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR sv.email LIKE ? OR td.tieu_de LIKE ?)";
        $likeKeyword = "%$keyword%";
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
    }

    if ($conditions) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }

    $sql .= " GROUP BY sv.stt_sv, ut.ma_tuyen_dung ORDER BY ut.ngay_ung_tuyen DESC LIMIT 10";

    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $applications = [];
        while ($row = $result->fetch_assoc()) {
            $applications[] = [
                'stt_sv' => $row['stt_sv'],
                'ma_sinh_vien' => $row['ma_sinh_vien'],
                'ho_ten' => $row['ho_ten'],
                'email' => $row['email'],
                'lop' => $row['lop'],
                'khoa' => $row['khoa'],
                'so_dien_thoai' => $row['so_dien_thoai'],
                'ngay_ung_tuyen' => $row['ngay_ung_tuyen'],
                'ma_tuyen_dung' => $row['ma_tuyen_dung'],
                'tieu_de' => $row['tieu_de'],
                'trang_thai' => $row['trang_thai'] ?: 'Đồng ý',
                'cv_path' => $row['cv_path']
            ];
        }
        sendResponse(true, ['applications' => $applications]);
    } else {
        sendResponse(false, [], 'Lỗi khi truy vấn cơ sở dữ liệu: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
    exit;
}

sendResponse(false, [], 'Yêu cầu không hợp lệ');
