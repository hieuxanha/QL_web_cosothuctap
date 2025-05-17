```php
<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/htdocs/Ql_web_cosothuctap/logs/error.log');

// Helper function to send JSON response
function sendResponse($success, $data = [], $error = '')
{
    echo json_encode(['success' => $success, 'data' => $data, 'error' => $error]);
    exit;
}

// Check database connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    sendResponse(false, [], 'Lỗi kết nối cơ sở dữ liệu');
}

// Get search term
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

// Determine ORDER BY clause (use ngay_tao if it exists, otherwise omit)
$order_by = ''; // Default: no ORDER BY
// Uncomment the next line if you confirm ngay_tao exists
// $order_by = ' ORDER BY td.ngay_tao DESC';

$sql = "SELECT td.ma_tuyen_dung, td.tieu_de, td.dia_chi, ct.stt_cty, ct.ten_cong_ty, ct.logo
        FROM tuyen_dung td
        JOIN cong_ty ct ON td.stt_cty = ct.stt_cty
        WHERE td.trang_thai = 'Đã duyệt'";
$params = [];

if ($search_term !== '') {
    $sql .= " AND (td.tieu_de LIKE ? OR ct.ten_cong_ty LIKE ? OR td.dia_chi LIKE ?)";
    $search_like = "%$search_term%";
    $params = [$search_like, $search_like, $search_like];
}

$sql .= $order_by;

if ($params) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        sendResponse(false, [], 'Lỗi chuẩn bị truy vấn');
    }
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = [
        'ma_tuyen_dung' => $row['ma_tuyen_dung'],
        'tieu_de' => $row['tieu_de'],
        'dia_chi' => $row['dia_chi'],
        'stt_cty' => $row['stt_cty'],
        'ten_cong_ty' => $row['ten_cong_ty'],
        'logo' => $row['logo'] ? 'uploads/' . $row['logo'] : 'Uploads/logo.png'
    ];
}

if ($params) $stmt->close();
$conn->close();

sendResponse(true, ['jobs' => $jobs]);
?>