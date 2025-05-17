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
            sv.khoa, 
            td.tieu_de
        FROM sinh_vien sv
        LEFT JOIN ung_tuyen ut ON sv.stt_sv = ut.stt_sv
        LEFT JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung
        WHERE ut.id IS NOT NULL AND ut.trang_thai = 'Đồng ý'";
    $params = [];
    $conditions = [];

    if ($keyword) {
        $conditions[] = "(sv.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR sv.khoa LIKE ? OR td.tieu_de LIKE ?)";
        $likeKeyword = "%$keyword%";
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
    }

    if ($conditions) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY ut.ngay_ung_tuyen DESC LIMIT 10";

    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                'stt_sv' => $row['stt_sv'],
                'ma_sinh_vien' => $row['ma_sinh_vien'],
                'ho_ten' => $row['ho_ten'],
                'khoa' => $row['khoa'],
                'tieu_de' => $row['tieu_de']
            ];
        }
        sendResponse(true, ['students' => $students]);
    } else {
        sendResponse(false, [], 'Lỗi khi truy vấn cơ sở dữ liệu: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
    exit;
}

sendResponse(false, [], 'Yêu cầu không hợp lệ');
