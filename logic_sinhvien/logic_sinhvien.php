<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'data' => [], 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'search') {
    $keyword = isset($_GET['khoa']) ? trim($_GET['khoa']) : '';

    // Danh sách ánh xạ khoa từ CSDL sang giá trị URL (dựa trên chi_tiet_khoa.php)
    $khoa_mapping = [
        'kinh_te' => 'kinh_te',
        'moi_truong' => 'moi_truong',
        'quan_ly_dat_dai' => 'quan_ly_dat_dai',
        'khi_tuong_thuy_van' => 'khi_tuong_thuy_van',
        'trac_dia_ban_do' => 'trac_dia_ban_do',
        'dia_chat' => 'dia_chat',
        'tai_nguyen_nuoc' => 'tai_nguyen_nuoc',
        'cntt' => 'cong_nghe_thong_tin',
        'ly_luan_chinh_tri' => 'ly_luan_chinh_tri',
        'bien_hai_dao' => 'khoa_hoc_bien_hai_dao',
        'khoa_hoc_dai_cuong' => 'khoa_hoc_dai_cuong',
        'the_chat_quoc_phong' => 'giao_duc_the_chat_quoc_phong',
        'bo_mon_luat' => 'bo_mon_luat',
        'bien_doi_khi_hau' => 'bien_doi_khi_hau',
        'ngoai_ngu' => 'ngoai_ngu'
    ];

    // Truy vấn danh sách khoa từ bảng tuyen_dung
    $sql = "SELECT DISTINCT khoa FROM tuyen_dung WHERE trang_thai = 'Đã duyệt'";
    $params = [];
    if (!empty($keyword)) {
        $sql .= " AND khoa LIKE ?";
        $params[] = "%" . $conn->real_escape_string($keyword) . "%";
    }

    $stmt = $conn->prepare($sql);
    if (!empty($keyword)) {
        $stmt->bind_param("s", $params[0]);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $khoa_list = [];
        while ($row = $result->fetch_assoc()) {
            $khoa_list[] = $row['khoa'];
        }

        // Chuyển đổi giá trị khoa từ CSDL sang giá trị URL
        $khoa_mapping_reverse = array_flip($khoa_mapping);
        $filtered_khoa = [];
        foreach ($khoa_list as $khoa) {
            $khoa_url = isset($khoa_mapping_reverse[$khoa]) ? $khoa_mapping_reverse[$khoa] : $khoa;
            $filtered_khoa[] = $khoa_url;
        }

        $response['success'] = true;
        $response['data'] = $filtered_khoa;
        $response['message'] = count($filtered_khoa) > 0 ? 'Success' : 'No matching departments found';
    } else {
        $response['message'] = 'Database query error: ' . $conn->error;
    }

    $stmt->close();
} else {
    $response['message'] = 'Invalid request method or action';
}

echo json_encode($response);
$conn->close();
