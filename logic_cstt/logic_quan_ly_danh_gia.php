<?php
//tim kiem
session_start();
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

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'search') {
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

    $sql = "
        SELECT 
            dg.stt_danhgia, dg.ma_dang_ky, dg.stt_sv, dg.ten_co_so, dg.tieu_de_tuyen_dung, 
            dg.cong_ty, dg.email_lien_he, dg.ma_sinh_vien, sv.ho_ten AS ho_ten_sinh_vien, 
            dg.ngay_danh_gia, dg.nguoi_danh_gia
        FROM danh_gia_thuc_tap dg
        JOIN sinh_vien sv ON dg.stt_sv = sv.stt_sv";
    $params = [];
    $conditions = [];

    if ($keyword) {
        $conditions[] = "(dg.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR dg.cong_ty LIKE ? OR dg.tieu_de_tuyen_dung LIKE ?)";
        $likeKeyword = "%$keyword%";
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
    }

    if ($conditions) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY dg.ngay_danh_gia DESC LIMIT 10";

    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $evaluations = [];
        while ($row = $result->fetch_assoc()) {
            $evaluations[] = [
                'stt_danhgia' => $row['stt_danhgia'],
                'ma_dang_ky' => $row['ma_dang_ky'],
                'stt_sv' => $row['stt_sv'],
                'ten_co_so' => $row['ten_co_so'],
                'tieu_de_tuyen_dung' => $row['tieu_de_tuyen_dung'],
                'cong_ty' => $row['cong_ty'],
                'email_lien_he' => $row['email_lien_he'],
                'ma_sinh_vien' => $row['ma_sinh_vien'],
                'ho_ten_sinh_vien' => $row['ho_ten_sinh_vien'],
                'ngay_danh_gia' => $row['ngay_danh_gia'],
                'nguoi_danh_gia' => $row['nguoi_danh_gia']
            ];
        }
        sendResponse(true, ['evaluations' => $evaluations]);
    } else {
        sendResponse(false, [], 'Lỗi khi truy vấn cơ sở dữ liệu: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
    exit;
}

sendResponse(false, [], 'Yêu cầu không hợp lệ');
