<?php
session_start();
require_once '../db.php'; // Adjust path as needed

// Check if db.php exists
if (!file_exists('../db.php')) {
    $_SESSION['error'] = "Không tìm thấy tệp cấu hình cơ sở dữ liệu.";
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['them_tuyen_dung'])) {
    $_SESSION['error'] = "Yêu cầu không hợp lệ.";
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Yêu cầu không hợp lệ (CSRF token).";
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}

// Sanitize and validate input
$stt_cty = isset($_POST['stt_cty']) ? intval($_POST['stt_cty']) : 0;
$tieu_de = isset($_POST['tieu_de_tuyen_dung']) ? trim($_POST['tieu_de_tuyen_dung']) : '';
$dia_chi = isset($_POST['dia_chi']) ? trim($_POST['dia_chi']) : '';

// Validate hinh_thuc with strict checking
$valid_hinh_thuc = ['Full-time', 'Part-time'];
$hinh_thuc_raw = isset($_POST['hinh_thuc']) ? trim($_POST['hinh_thuc']) : '';
$hinh_thuc = in_array($hinh_thuc_raw, $valid_hinh_thuc) ? $hinh_thuc_raw : 'Full-time';
if (empty($hinh_thuc_raw) || !in_array($hinh_thuc_raw, $valid_hinh_thuc)) {
    error_log("Invalid or empty hinh_thuc: '$hinh_thuc_raw', defaulting to 'Full-time'");
}

// Validate gioi_tinh
$valid_gioi_tinh = ['Nam', 'Nữ', 'Không giới hạn'];
$gioi_tinh_raw = isset($_POST['gioi_tinh']) ? trim($_POST['gioi_tinh']) : '';
$gioi_tinh = in_array($gioi_tinh_raw, $valid_gioi_tinh) ? $gioi_tinh_raw : 'Nam';

if (empty($gioi_tinh_raw) || !in_array($gioi_tinh_raw, $valid_gioi_tinh)) {
    error_log("Invalid or empty gioi_tinh: '$gioi_tinh_raw', defaulting to 'Nam'");
}

$khoa = isset($_POST['khoa']) && !empty(trim($_POST['khoa'])) ? trim($_POST['khoa']) : null;
$mo_ta = isset($_POST['mo_ta']) ? trim($_POST['mo_ta']) : '';
$trinh_do = isset($_POST['trinh_do']) && !empty(trim($_POST['trinh_do'])) ? trim($_POST['trinh_do']) : 'Không yêu cầu';
$so_luong = isset($_POST['so_luong']) ? intval($_POST['so_luong']) : 1;
$noi_bat = isset($_POST['noi_bat']) && $_POST['noi_bat'] == '1' ? 1 : 0;
$han_nop = isset($_POST['han_nop']) ? trim($_POST['han_nop']) : '';

// Generate unique ma_tuyen_dung
$ma_tuyen_dung = 'TD' . time() . rand(1000, 9999);

// Validate required fields
if (empty($stt_cty) || empty($tieu_de) || empty($dia_chi) || empty($gioi_tinh) || $so_luong <= 0 || empty($han_nop)) {
    $_SESSION['error'] = "Vui lòng điền đầy đủ các trường bắt buộc.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}

// Validate field lengths (based on table schema)
if (strlen($tieu_de) > 255 || strlen($dia_chi) > 255 || strlen($ma_tuyen_dung) > 50) {
    $_SESSION['error'] = "Dữ liệu vượt quá độ dài cho phép.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}

// Validate han_nop (must be valid date and not in the past)
$han_nop_date = DateTime::createFromFormat('Y-m-d', $han_nop);
if (!$han_nop_date || $han_nop_date < new DateTime('today')) {
    $_SESSION['error'] = "Hạn nộp không hợp lệ hoặc đã qua.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}
$han_nop = $han_nop_date->format('Y-m-d');

// Validate khoa if provided
if ($khoa !== null) {
    $valid_khoa = array_keys([
        'kinh_te' => 'Khoa Kinh tế',
        'moi_truong' => 'Khoa Môi trường',
        'quan_ly_dat_dai' => 'Khoa Quản lý đất đai',
        'khi_tuong_thuy_van' => 'Khoa Khí tượng thủy văn',
        'trac_dia_ban_do' => 'Khoa Trắc địa bản đồ và Thông tin địa lý',
        'dia_chat' => 'Khoa Địa chất',
        'tai_nguyen_nuoc' => 'Khoa Tài nguyên nước',
        'cntt' => 'Khoa Công nghệ thông tin',
        'ly_luan_chinh_tri' => 'Khoa Lý luận chính trị',
        'bien_hai_dao' => 'Khoa Khoa học Biển và Hải đảo',
        'khoa_hoc_dai_cuong' => 'Khoa Khoa học Đại cương',
        'the_chat_quoc_phong' => 'Khoa Giáo dục thể chất và Giáo dục quốc phòng',
        'bo_mon_luat' => 'Bộ môn Luật',
        'bien_doi_khi_hau' => 'Bộ môn Biến đổi khí hậu và PT bền vững',
        'ngoai_ngu' => 'Bộ môn Ngoại ngữ'
    ]);
    if (!in_array($khoa, $valid_khoa)) {
        $_SESSION['error'] = "Khoa không hợp lệ.";
        $_SESSION['form_data'] = $_POST;
        header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
        exit();
    }
}

// Validate trinh_do
$valid_trinh_do = ['Không yêu cầu', 'Trung cấp', 'Cao đẳng', 'Đại học', 'Thạc sĩ', 'Tiến sĩ'];
if (!in_array($trinh_do, $valid_trinh_do)) {
    $trinh_do = 'Không yêu cầu';
    error_log("Invalid trinh_do provided, defaulting to 'Không yêu cầu'");
}

// Validate stt_cty exists in cong_ty
$sql_check_cty = "SELECT stt_cty FROM cong_ty WHERE stt_cty = ? AND trang_thai = 'Đã duyệt'";
$stmt_check_cty = $conn->prepare($sql_check_cty);
if (!$stmt_check_cty) {
    error_log("Prepare failed (check_cty): " . $conn->error);
    $_SESSION['error'] = "Lỗi hệ thống. Vui lòng thử lại sau.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}
$stmt_check_cty->bind_param('i', $stt_cty);
$stmt_check_cty->execute();
$result_check_cty = $stmt_check_cty->get_result();
if ($result_check_cty->num_rows === 0) {
    $_SESSION['error'] = "Công ty không tồn tại hoặc chưa được duyệt.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}
$stmt_check_cty->close();

// Check for duplicate ma_tuyen_dung
$sql_check_ma = "SELECT ma_tuyen_dung FROM tuyen_dung WHERE ma_tuyen_dung = ?";
$stmt_check_ma = $conn->prepare($sql_check_ma);
if (!$stmt_check_ma) {
    error_log("Prepare failed (check_ma): " . $conn->error);
    $_SESSION['error'] = "Lỗi hệ thống. Vui lòng thử lại sau.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}
$stmt_check_ma->bind_param('s', $ma_tuyen_dung);
$stmt_check_ma->execute();
if ($stmt_check_ma->get_result()->num_rows > 0) {
    $_SESSION['error'] = "Mã tuyển dụng đã tồn tại.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}
$stmt_check_ma->close();

// Sanitize inputs for SQL
$tieu_de = mysqli_real_escape_string($conn, $tieu_de);
$dia_chi = mysqli_real_escape_string($conn, $dia_chi);
$mo_ta = mysqli_real_escape_string($conn, $mo_ta);
$hinh_thuc = mysqli_real_escape_string($conn, $hinh_thuc);
$gioi_tinh = mysqli_real_escape_string($conn, $gioi_tinh);
$trinh_do = mysqli_real_escape_string($conn, $trinh_do);
$khoa = $khoa !== null ? mysqli_real_escape_string($conn, $khoa) : null;

// Final validation before insert
if (!in_array($hinh_thuc, $valid_hinh_thuc)) {
    error_log("Final check failed: hinh_thuc is '$hinh_thuc', forcing to 'Full-time'");
    $hinh_thuc = 'Full-time';
}

// Insert into tuyen_dung table
$sql = "INSERT INTO tuyen_dung (
    ma_tuyen_dung, tieu_de, stt_cty, mo_ta, so_luong, han_nop, 
    trang_thai, dia_chi, hinh_thuc, gioi_tinh, noi_bat, khoa, trinh_do
) VALUES (
    ?, ?, ?, ?, ?, ?, 'Đang chờ', ?, ?, ?, ?, ?, ?
)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed (insert): " . $conn->error);
    $_SESSION['error'] = "Lỗi hệ thống. Vui lòng thử lại sau.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}

$stmt->bind_param(
    'ssisissisiss',
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

try {
    if ($stmt->execute()) {
        $_SESSION['message'] = "Tin tuyển dụng đã được đăng thành công.";
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    error_log("Insert failed: " . $e->getMessage());
    $_SESSION['error'] = "Lỗi khi đăng tin: " . $e->getMessage();
    $_SESSION['form_data'] = $_POST;
}

$stmt->close();
header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
exit();

$conn->close();
