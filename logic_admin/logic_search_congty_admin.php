<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../db.php';
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$companies = [];
if ($keyword !== '') {
    $query = "SELECT stt_cty, ten_cong_ty, dia_chi, trang_thai 
              FROM cong_ty 
              WHERE ten_cong_ty LIKE ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(['success' => false, 'error' => 'Lỗi hệ thống khi chuẩn bị truy vấn']);
        exit;
    }
    $searchTerm = "%$keyword%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $companies[] = [
            'stt_cty' => $row['stt_cty'],
            'ten_cong_ty' => $row['ten_cong_ty'],
            'ma_cong_ty' => '',
            'dia_chi' => $row['dia_chi'],
            'trang_thai' => trim($row['trang_thai']) ?: 'Đang chờ'
        ];
    }
    $stmt->close();
} else {
    $query = "SELECT stt_cty, ten_cong_ty, dia_chi, trang_thai FROM cong_ty";
    $result = $conn->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $companies[] = [
                'stt_cty' => $row['stt_cty'],
                'ten_cong_ty' => $row['ten_cong_ty'],
                'ma_cong_ty' => '',
                'dia_chi' => $row['dia_chi'],
                'trang_thai' => trim($row['trang_thai']) ?: 'Đang chờ'
            ];
        }
        $result->free();
    } else {
        error_log("Query failed: " . $conn->error);
        echo json_encode(['success' => false, 'error' => 'Lỗi hệ thống khi lấy danh sách công ty']);
        exit;
    }
}
$conn->close();
echo json_encode(['success' => true, 'companies' => $companies]);
