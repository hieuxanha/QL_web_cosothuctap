<?php
require_once '../db.php';

header('Content-Type: application/json; charset=utf-8');

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
$khoa = isset($_GET['khoa']) ? $_GET['khoa'] : '';
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';

$khoa_mapping = [
    'kinh_te' => 'kinh_te',
    'moi_truong' => 'moi_truong',
    'quan_ly_dat_dai' => 'quan_ly_dat_dai',
    'khi_tuong_thuy_van' => 'khi_tuong_thuy_van',
    'trac_dia_ban_do' => 'trac_dia_ban_do',
    'dia_chat' => 'dia_chat',
    'tai_nguyen_nuoc' => 'tai_nguyen_nuoc',
    'cong_nghe_thong_tin' => 'cntt',
    'ly_luan_chinh_tri' => 'ly_luan_chinh_tri',
    'khoa_hoc_bien_hai_dao' => 'bien_hai_dao',
    'khoa_hoc_dai_cuong' => 'khoa_hoc_dai_cuong',
    'giao_duc_the_chat_quoc_phong' => 'the_chat_quoc_phong',
    'bo_mon_luat' => 'bo_mon_luat',
    'bien_doi_khi_hau' => 'bien_doi_khi_hau',
    'ngoai_ngu' => 'ngoai_ngu'
];

$khoa_value = isset($khoa_mapping[$khoa]) ? $khoa_mapping[$khoa] : '';

if ($action === 'search') {
    $sql = "
    SELECT t.ma_tuyen_dung, t.tieu_de, c.ten_cong_ty, c.stt_cty, c.logo
    FROM tuyen_dung t 
    JOIN cong_ty c ON t.stt_cty = c.stt_cty 
    WHERE t.khoa = ? AND t.trang_thai = 'Đã duyệt'";
    $params = [$khoa_value];
    $types = 's';

    if ($keyword) {
        $sql .= " AND (t.tieu_de LIKE ? OR c.ten_cong_ty LIKE ?)";
        $likeKeyword = "%$keyword%";
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $types .= 'ss';
    }

    if ($location) {
        $sql .= " AND t.dia_chi LIKE ?";
        $likeLocation = "%$location%";
        $params[] = $likeLocation;
        $types .= 's';
    }

    $sql .= " ORDER BY t.han_nop DESC LIMIT 10";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        sendResponse(false, [], 'Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $jobs = [];
        while ($row = $result->fetch_assoc()) {
            $jobs[] = [
                'ma_tuyen_dung' => $row['ma_tuyen_dung'],
                'tieu_de' => $row['tieu_de'],
                'ten_cong_ty' => $row['ten_cong_ty'],
                'stt_cty' => $row['stt_cty'],
                'logo' => !empty($row['logo']) ? '../sinh_vien/uploads/' . $row['logo'] : '../sinh_vien/uploads/logo.png'
            ];
        }
        sendResponse(true, ['jobs' => $jobs]);
    } else {
        sendResponse(false, [], 'Query error: ' . $stmt->error);
    }

    $stmt->close();
}

sendResponse(false, [], 'Invalid request');
$conn->close();
