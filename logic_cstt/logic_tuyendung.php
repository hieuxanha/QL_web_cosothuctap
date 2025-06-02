<?php
session_start();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['them_tuyen_dung'])) {
    $_SESSION['error'] = "Yêu cầu không hợp lệ.";
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}

// CSRF check
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "CSRF token không hợp lệ.";
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}

// Lấy và xử lý dữ liệu đầu vào
$stt_cty = intval($_POST['stt_cty']);
$tieu_de = trim($_POST['tieu_de_tuyen_dung']);
$dia_chi = trim($_POST['dia_chi']);
$mo_ta = trim($_POST['mo_ta']);
$so_luong = max(1, intval($_POST['so_luong']));
$han_nop = $_POST['han_nop'];
$hinh_thuc = in_array($_POST['hinh_thuc'], ['Full-time', 'Part-time']) ? $_POST['hinh_thuc'] : 'Full-time';
$gioi_tinh = in_array($_POST['gioi_tinh'], ['Nam', 'Nữ', 'Không giới hạn']) ? $_POST['gioi_tinh'] : 'Không giới hạn';
$noi_bat = isset($_POST['noi_bat']) && $_POST['noi_bat'] == '1' ? 1 : 0;
$khoa = !empty($_POST['khoa']) ? $_POST['khoa'] : null;
$trinh_do = in_array($_POST['trinh_do'], ['Không yêu cầu', 'Trung cấp', 'Cao đẳng', 'Đại học', 'Thạc sĩ', 'Tiến sĩ']) ? $_POST['trinh_do'] : 'Không yêu cầu';

// Kiểm tra hạn nộp
$han_nop_date = DateTime::createFromFormat('Y-m-d', $han_nop);
if (!$han_nop_date || $han_nop_date < new DateTime('today')) {
    $_SESSION['error'] = "Hạn nộp không hợp lệ hoặc đã qua.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}
$han_nop = $han_nop_date->format('Y-m-d');

// Kiểm tra công ty tồn tại và đã được duyệt
$sql_check_cty = "SELECT stt_cty FROM cong_ty WHERE stt_cty = ? AND trang_thai = 'Đã duyệt'";
$stmt_check_cty = $conn->prepare($sql_check_cty);
$stmt_check_cty->bind_param("i", $stt_cty);
$stmt_check_cty->execute();
$result = $stmt_check_cty->get_result();
if ($result->num_rows === 0) {
    $_SESSION['error'] = "Công ty không tồn tại hoặc chưa được duyệt.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}
$stmt_check_cty->close();

// Tạo mã tuyển dụng duy nhất
$ma_tuyen_dung = 'TD' . time() . rand(1000, 9999);

// Chèn dữ liệu vào bảng tuyển dụng
$sql_insert = "INSERT INTO tuyen_dung (
    ma_tuyen_dung, tieu_de, stt_cty, mo_ta, so_luong, han_nop, 
    trang_thai, dia_chi, hinh_thuc, gioi_tinh, noi_bat, khoa, trinh_do
) VALUES (?, ?, ?, ?, ?, ?, 'Đang chờ', ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql_insert);
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    $_SESSION['error'] = "Lỗi hệ thống khi chuẩn bị truy vấn.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}
//   'ssisissisiss', saiiii
$stmt->bind_param(
    "ssisisssssss",
    $ma_tuyen_dung,
    $tieu_de,
    $stt_cty,
    $mo_ta,
    $so_luong,
    $han_nop,
    $dia_chi,
    $hinh_thuc,
    $gioi_tinh,
    $noi_bat,
    $khoa,
    $trinh_do
);


if ($stmt->execute()) {
    $_SESSION['message'] = "Tin tuyển dụng đã được đăng thành công.";
} else {
    error_log("Insert failed: " . $stmt->error);
    $_SESSION['error'] = "Lỗi khi đăng tin tuyển dụng.";
    $_SESSION['form_data'] = $_POST;
}

$stmt->close();
$conn->close();
header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
exit();
