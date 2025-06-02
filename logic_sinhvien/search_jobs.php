<?php
header('Content-Type: application/json; charset=utf-8');

// Include database connection
require_once '../db.php';

// Initialize response
$response = ['success' => false, 'data' => [], 'message' => ''];

// Get input parameters
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';

try {
    // Base query for approved job postings
    $sql = "SELECT td.ma_tuyen_dung, td.tieu_de, td.dia_chi, ct.stt_cty, ct.ten_cong_ty, ct.logo
            FROM tuyen_dung td
            JOIN cong_ty ct ON td.stt_cty = ct.stt_cty
            WHERE td.trang_thai = 'Đã duyệt'";
    $params = [];
    $types = '';

    // Add keyword filter if provided
    if ($keyword) {
        $sql .= " AND (td.tieu_de LIKE ? OR ct.ten_cong_ty LIKE ? OR td.dia_chi LIKE ?)";
        $searchTerm = '%' . $keyword . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'sss';
    }

    // Add location filter if provided
    if ($location) {
        $sql .= " AND td.dia_chi LIKE ?";
        $locationParam = '%' . $location . '%';
        $params[] = $locationParam;
        $types .= 's';
    }

    // Limit results to prevent overload
    $sql .= " LIMIT 10";

    // Prepare and execute query
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Collect results
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

    // Set response
    $response['success'] = true;
    $response['data']['jobs'] = $jobs;
    $response['message'] = $jobs ? 'Tìm thấy kết quả.' : 'Không tìm thấy kết quả phù hợp.';

    $stmt->close();
} catch (Exception $e) {
    $response['message'] = 'Lỗi server: ' . $e->getMessage();
    error_log("Search jobs error: " . $e->getMessage());
}

$conn->close();
echo json_encode($response);
