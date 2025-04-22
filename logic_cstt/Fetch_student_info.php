<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập!']);
    exit();
}

// Kết nối cơ sở dữ liệu
require_once '../db.php';
$conn->set_charset("utf8mb4");

$stt_sv = filter_input(INPUT_POST, 'stt_sv', FILTER_SANITIZE_NUMBER_INT);
if (!$stt_sv) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Mã sinh viên không hợp lệ!']);
    exit();
}

// Truy vấn thông tin sinh viên, ứng tuyển, công ty
$stmt = $conn->prepare("
    SELECT 
        ut.id AS ma_dang_ky, 
        sv.ma_sinh_vien, 
        sv.lop, 
        sv.khoa,
        c.ten_cong_ty AS ten_co_so, 
        t.tieu_de AS tieu_de_tuyen_dung, 
        c.ten_cong_ty AS cong_ty, 
        c.email AS email_lien_he,
        g.ho_ten AS giang_vien_huong_dan,
        (SELECT stt_cstt FROM co_so_thuc_tap LIMIT 1) AS stt_cstt
    FROM sinh_vien sv
    JOIN ung_tuyen ut ON sv.stt_sv = ut.stt_sv
    JOIN tuyen_dung t ON ut.ma_tuyen_dung = t.ma_tuyen_dung
    JOIN cong_ty c ON t.stt_cty = c.stt_cty
    LEFT JOIN giang_vien g ON sv.so_hieu = g.so_hieu_giang_vien
    WHERE sv.stt_sv = ? AND ut.trang_thai = 'Đồng ý'
    LIMIT 1
");
$stmt->bind_param("i", $stt_sv);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $response = [
        'success' => true,
        'ma_dang_ky' => $row['ma_dang_ky'],
        'ma_sinh_vien' => $row['ma_sinh_vien'],
        'lop_khoa' => $row['lop'] . ' - ' . $row['khoa'],
        'nganh_hoc' => $row['khoa'],
        'ten_co_so' => $row['ten_co_so'],
        'tieu_de_tuyen_dung' => $row['tieu_de_tuyen_dung'],
        'cong_ty' => $row['cong_ty'],
        'stt_cstt' => $row['stt_cstt'],
        'email_lien_he' => $row['email_lien_he'],
        'giang_vien_huong_dan' => $row['giang_vien_huong_dan'] ?? ''
    ];
} else {
    error_log("Không tìm thấy thông tin ứng tuyển cho stt_sv = $stt_sv", 3, "../logs/error.log");
    $response = ['success' => false, 'message' => 'Không tìm thấy thông tin ứng tuyển của sinh viên!'];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
