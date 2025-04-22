<?php
session_start();
require_once '../db.php'; // Adjust path as needed

// Check if db.php exists
if (!file_exists('../db.php')) {
    $_SESSION['error'] = "Không tìm thấy tệp cấu hình cơ sở dữ liệu.";
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}

// CSRF token validation
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['error'] = "Yêu cầu không hợp lệ.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_tuyen_dung'])) {
    // Sanitize and validate input
    $stt_cty = isset($_POST['stt_cty']) ? intval($_POST['stt_cty']) : 0;
    $tieu_de = isset($_POST['tieu_de_tuyen_dung']) ? trim($_POST['tieu_de_tuyen_dung']) : '';
    $dia_chi = isset($_POST['dia_chi']) ? trim($_POST['dia_chi']) : '';
    $hinh_thuc = isset($_POST['hinh_thuc']) ? trim($_POST['hinh_thuc']) : '';
    $gioi_tinh = isset($_POST['gioi_tinh']) ? trim($_POST['gioi_tinh']) : '';
    $khoa = isset($_POST['khoa']) && $_POST['khoa'] !== '' ? trim($_POST['khoa']) : null;
    $mo_ta = isset($_POST['mo_ta']) ? trim($_POST['mo_ta']) : '';
    $trinh_do = isset($_POST['trinh_do']) ? trim($_POST['trinh_do']) : '';
    $so_luong = isset($_POST['so_luong']) ? intval($_POST['so_luong']) : 1;
    $noi_bat = isset($_POST['noi_bat']) && $_POST['noi_bat'] == '1' ? 1 : 0;
    $han_nop = isset($_POST['han_nop']) ? trim($_POST['han_nop']) : '';

    // Append trinh_do to mo_ta if provided
    if ($trinh_do) {
        $mo_ta = $mo_ta ? $mo_ta . "\nTrình độ yêu cầu: $trinh_do" : "Trình độ yêu cầu: $trinh_do";
    }

    // Generate unique ma_tuyen_dung
    $ma_tuyen_dung = 'TD' . time() . rand(1000, 9999);

    // Validate required fields
    if (empty($stt_cty) || empty($tieu_de) || empty($dia_chi) || empty($hinh_thuc) || 
        empty($gioi_tinh) || empty($trinh_do) || $so_luong <= 0 || empty($han_nop)) {
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

    // Validate hinh_thuc
    $valid_hinh_thuc = ['Full-time', 'Part-time'];
    if (!in_array($hinh_thuc, $valid_hinh_thuc)) {
        $_SESSION['error'] = "Hình thức làm việc không hợp lệ.";
        $_SESSION['form_data'] = $_POST;
        header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
        exit();
    }

    // Validate gioi_tinh
    $valid_gioi_tinh = ['Nam', 'Nữ', 'Không giới hạn'];
    if (!in_array($gioi_tinh, $valid_gioi_tinh)) {
        $_SESSION['error'] = "Giới tính không hợp lệ.";
        $_SESSION['form_data'] = $_POST;
        header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
        exit();
    }

    // Validate khoa if provided
    if ($khoa !== null) {
        $valid_khoa = [
            'kinh_te', 'moi_truong', 'quan_ly_dat_dai', 'khi_tuong_thuy_van',
            'trac_dia_ban_do', 'dia_chat', 'tai_nguyen_nuoc', 'cntt',
            'ly_luan_chinh_tri', 'bien_hai_dao', 'khoa_hoc_dai_cuong',
            'the_chat_quoc_phong', 'bo_mon_luat', 'bien_doi_khi_hau', 'ngoai_ngu'
        ];
        if (!in_array($khoa, $valid_khoa)) {
            $_SESSION['error'] = "Khoa không hợp lệ.";
            $_SESSION['form_data'] = $_POST;
            header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
            exit();
        }
    }

    // Validate stt_cty exists in cong_ty
    $sql_check_cty = "SELECT stt_cty FROM cong_ty WHERE stt_cty = ? AND trang_thai = 'Đã duyệt'";
    $stmt_check_cty = $conn->prepare($sql_check_cty);
    if (!$stmt_check_cty) {
        error_log("Prepare failed: " . $conn->error);
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
        error_log("Prepare failed: " . $conn->error);
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
    $khoa = $khoa !== null ? mysqli_real_escape_string($conn, $khoa) : null;

    // Insert into tuyen_dung table
    $sql = "INSERT INTO tuyen_dung (
        ma_tuyen_dung, tieu_de, stt_cty, mo_ta, so_luong, han_nop, 
        trang_thai, dia_chi, hinh_thuc, gioi_tinh, noi_bat, khoa
    ) VALUES (
        ?, ?, ?, ?, ?, ?, 'Đang chờ', ?, ?, ?, ?, ?
    )";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        $_SESSION['error'] = "Lỗi hệ thống. Vui lòng thử lại sau.";
        $_SESSION['form_data'] = $_POST;
        header("Location: ../co_so_thuc_tap/ui_capnhat_tt.php");
        exit();
    }

    $stmt->bind_param(
        'ssisissisis',
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
        $khoa
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
}

$conn->close();
?>