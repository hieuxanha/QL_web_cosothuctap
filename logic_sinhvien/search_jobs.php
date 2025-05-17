<?php
header('Content-Type: application/json');

// Include database connection
require_once '../db.php';

// Check if keyword is provided
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$response = ['success' => false, 'data' => [], 'message' => ''];

if (empty($keyword)) {
    $response['message'] = 'Vui lòng nhập từ khóa tìm kiếm.';
    echo json_encode($response);
    exit;
}

try {
    // Prepare the SQL query to search for jobs
    $sql = "SELECT td.ma_tuyen_dung, td.tieu_de, td.dia_chi, ct.stt_cty, ct.ten_cong_ty, ct.logo
            FROM tuyen_dung td
            JOIN cong_ty ct ON td.stt_cty = ct.stt_cty
            WHERE td.trang_thai = 'Đã duyệt'
            AND (td.tieu_de LIKE ? OR ct.ten_cong_ty LIKE ? OR td.dia_chi LIKE ?)
            LIMIT 10";

    $stmt = $conn->prepare($sql);
    $searchTerm = '%' . $keyword . '%';
    $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $jobs = [];
    while ($row = $result->fetch_assoc()) {
        $jobs[] = [
            'ma_tuyen_dung' => $row['ma_tuyen_dung'],
            'tieu_de' => $row['tieu_de'],
            'dia_chi' => $row['dia_chi'],
            'stt_cty' => $row['stt_cty'],
            'ten_cong_ty' => $row['ten_cong_ty'],
            'logo' => !empty($row['logo']) ? 'uploads/' . $row['logo'] : 'uploads/logo.png'
        ];
    }

    $response['success'] = true;
    $response['data']['jobs'] = $jobs;
    $response['message'] = $jobs ? 'Tìm thấy kết quả.' : 'Không tìm thấy kết quả phù hợp.';
} catch (Exception $e) {
    $response['message'] = 'Lỗi server: ' . $e->getMessage();
}

echo json_encode($response);
$conn->close();
