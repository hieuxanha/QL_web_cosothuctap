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

// Xử lý yêu cầu tìm kiếm
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'search') {
    $khoa = isset($_GET['khoa']) ? $_GET['khoa'] : 'Tất cả';
    $lop = isset($_GET['lop']) ? $_GET['lop'] : 'Tất cả';
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

    $sql = "SELECT ut.stt_sv, sv.ma_sinh_vien, sv.ho_ten, sv.email, sv.lop, sv.khoa, sv.so_dien_thoai, ut.ngay_ung_tuyen, ut.ma_tuyen_dung, td.tieu_de, ut.trang_thai, ut.cv_path
            FROM ung_tuyen ut
            JOIN sinh_vien sv ON ut.stt_sv = sv.stt_sv
            JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung";
    $params = [];
    $conditions = [];

    if ($khoa !== 'Tất cả') {
        $conditions[] = "sv.khoa = ?";
        $params[] = $khoa;
    }
    if ($lop !== 'Tất cả') {
        $conditions[] = "sv.lop = ?";
        $params[] = $lop;
    }
    if ($keyword) {
        $conditions[] = "(sv.ma_sinh_vien LIKE ? OR sv.ho_ten LIKE ? OR sv.email LIKE ? OR td.tieu_de LIKE ?)";
        $likeKeyword = "%$keyword%";
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
    }

    if ($conditions) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY ut.ngay_ung_tuyen DESC LIMIT 10";

    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $applications = [];
        while ($row = $result->fetch_assoc()) {
            $applications[] = [
                'stt_sv' => $row['stt_sv'],
                'ma_sinh_vien' => $row['ma_sinh_vien'],
                'ho_ten' => $row['ho_ten'],
                'email' => $row['email'],
                'lop' => $row['lop'],
                'khoa' => $row['khoa'],
                'so_dien_thoai' => $row['so_dien_thoai'],
                'ngay_ung_tuyen' => $row['ngay_ung_tuyen'],
                'ma_tuyen_dung' => $row['ma_tuyen_dung'],
                'tieu_de' => $row['tieu_de'],
                'trang_thai' => $row['trang_thai'] ?: 'Chờ duyệt',
                'cv_path' => $row['cv_path']
            ];
        }
        sendResponse(true, ['applications' => $applications]);
    } else {
        sendResponse(false, [], 'Lỗi khi truy vấn cơ sở dữ liệu: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Xử lý yêu cầu cập nhật trạng thái (giữ nguyên)
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['stt_sv']) || !isset($_POST['ma_tuyen_dung']) || !isset($_POST['action'])) {
    sendResponse(false, [], 'Dữ liệu không hợp lệ hoặc thiếu');
}

$stt_sv = $_POST['stt_sv'];
$ma_tuyen_dung = $_POST['ma_tuyen_dung'];
$action = $_POST['action'];

switch ($action) {
    case 'approve':
        $trang_thai = 'Đồng ý';
        break;
    case 'reject':
        $trang_thai = 'Không đồng ý';
        break;
    case 'cancel':
    case 'restore':
        $trang_thai = 'Chờ duyệt';
        break;
    default:
        sendResponse(false, [], 'Hành động không hợp lệ');
}

if (!in_array($trang_thai, ['Chờ duyệt', 'Đồng ý', 'Không đồng ý'])) {
    sendResponse(false, [], 'Trạng thái không hợp lệ');
}

$sql_update = "UPDATE ung_tuyen SET trang_thai = ? WHERE stt_sv = ? AND ma_tuyen_dung = ?";
$stmt = $conn->prepare($sql_update);
if (!$stmt) {
    sendResponse(false, [], 'Lỗi chuẩn bị câu lệnh SQL: ' . $conn->error);
}

$stmt->bind_param("sis", $trang_thai, $stt_sv, $ma_tuyen_dung);

if ($stmt->execute()) {
    sendResponse(true, ['trang_thai' => $trang_thai, 'message' => 'Cập nhật trạng thái thành công!']);
} else {
    sendResponse(false, [], 'Lỗi thực thi SQL: ' . $stmt->error);
}

$stmt->close();
$conn->close();
