<?php
session_start();
require '../db.php';

header('Content-Type: application/json');

// Helper function to send JSON response
function sendResponse($success, $data = [], $error = '')
{
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'error' => $error
    ]);
    exit;
}

// Handle GET requests for fetching or searching lecturers
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'get_lecturers' || $action === 'search_lecturers') {
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

    // Prepare SQL query
    $sql = "SELECT so_hieu_giang_vien, ho_ten, khoa, email, so_dien_thoai FROM giang_vien";
    if ($action === 'search_lecturers' && $keyword) {
        $keyword = $conn->real_escape_string($keyword);
        $sql .= " WHERE ho_ten LIKE ? OR khoa LIKE ?";
    }
    $sql .= " ORDER BY ho_ten";

    $stmt = $conn->prepare($sql);
    if ($action === 'search_lecturers' && $keyword) {
        $likeKeyword = "%$keyword%";
        $stmt->bind_param('ss', $likeKeyword, $likeKeyword);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $lecturers = [];
        while ($row = $result->fetch_assoc()) {
            $lecturers[] = [
                'so_hieu_giang_vien' => $row['so_hieu_giang_vien'],
                'ho_ten' => $row['ho_ten'],
                'khoa' => $row['khoa'],
                'email' => $row['email'],
                'so_dien_thoai' => $row['so_dien_thoai']
            ];
        }
        sendResponse(true, ['lecturers' => $lecturers]);
    } else {
        sendResponse(false, [], 'Lỗi khi truy vấn cơ sở dữ liệu: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
    exit;
}

sendResponse(false, [], 'Yêu cầu không hợp lệ!');
