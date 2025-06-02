<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json; charset=utf-8');

$response = ['success' => false, 'data' => [], 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'search') {
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
    $location = isset($_GET['location']) ? trim($_GET['location']) : '';
    $khoa = isset($_GET['khoa']) ? trim($_GET['khoa']) : '';

    $sql = "SELECT td.ma_tuyen_dung, td.tieu_de, td.dia_chi, ct.ten_cong_ty, ct.logo
            FROM tuyen_dung td
            JOIN cong_ty ct ON td.stt_cty = ct.stt_cty
            WHERE td.trang_thai = 'Đã duyệt'";
    $params = [];
    $types = '';

    if (!empty($khoa)) {
        $sql .= " AND LOWER(td.khoa) LIKE LOWER(?)";
        $params[] = "%$khoa%";
        $types .= 's';
    }

    if (!empty($keyword)) {
        $sql .= " AND (LOWER(td.tieu_de) LIKE LOWER(?) OR LOWER(ct.ten_cong_ty) LIKE LOWER(?))";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $types .= 'ss';
    }

    if (!empty($location)) {
        $sql .= " AND LOWER(td.dia_chi) LIKE LOWER(?)";
        $params[] = "%$location%";
        $types .= 's';
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $response['message'] = 'Prepare failed: ' . $conn->error;
        echo json_encode($response);
        exit;
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $jobs = [];
        while ($row = $result->fetch_assoc()) {
            $jobs[] = [
                'ma_tuyen_dung' => $row['ma_tuyen_dung'],
                'tieu_de' => $row['tieu_de'],
                'ten_cong_ty' => $row['ten_cong_ty'],
                'dia_chi' => $row['dia_chi'],
                'logo' => $row['logo'] ? '../sinh_vien/uploads/' . $row['logo'] : '../sinh_vien/uploads/logo.png'
            ];
        }
        $response['success'] = true;
        $response['data'] = $jobs;
        $response['message'] = count($jobs) > 0 ? 'Success' : 'No matching jobs found';
    } else {
        $response['message'] = 'Query error: ' . $stmt->error;
    }

    $stmt->close();
} else {
    $response['message'] = 'Invalid request method or action';
}

echo json_encode($response);
$conn->close();
