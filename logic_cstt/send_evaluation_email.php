<?php
session_start();

// Tắt hiển thị lỗi trên màn hình
ini_set('display_errors', 0);
// Ghi lỗi vào log
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/htdocs/Ql_web_cosothuctap/logs/error.log');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập!']);
    exit();
}

// Kết nối cơ sở dữ liệu
require_once '../db.php';
if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối cơ sở dữ liệu!']);
    exit();
}
$conn->set_charset("utf8mb4");

// Tải PHPMailer với kiểm tra tệp
if (!file_exists('C:/xampp/htdocs/Ql_web_cosothuctap/PHPMailer-master/PHPMailer-master/src/PHPMailer.php')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy thư viện PHPMailer!']);
    exit();
}
require_once 'C:/xampp/htdocs/Ql_web_cosothuctap/PHPMailer-master/PHPMailer-master/src/Exception.php';
require_once 'C:/xampp/htdocs/Ql_web_cosothuctap/PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require_once 'C:/xampp/htdocs/Ql_web_cosothuctap/PHPMailer-master/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$stt_danhgia = filter_input(INPUT_POST, 'stt_danhgia', FILTER_SANITIZE_NUMBER_INT);
if (!$stt_danhgia) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Mã đánh giá không hợp lệ!']);
    exit();
}

// Lấy thông tin đánh giá và email sinh viên
$stmt = $conn->prepare("
    SELECT dg.*, sv.ho_ten AS ho_ten_sinh_vien, sv.email AS email_sinh_vien
    FROM danh_gia_thuc_tap dg
    JOIN sinh_vien sv ON dg.stt_sv = sv.stt_sv
    WHERE dg.stt_danhgia = ?
");
if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn cơ sở dữ liệu!']);
    exit();
}
$stmt->bind_param("i", $stt_danhgia);
$stmt->execute();
$eval = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$eval) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy đánh giá!']);
    exit();
}

// Kiểm tra email sinh viên
if (empty($eval['email_sinh_vien'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Sinh viên chưa có email!']);
    exit();
}

// Tải TCPDF với kiểm tra tệp
if (!file_exists('C:/xampp/htdocs/Ql_web_cosothuctap/TCPDF-main/TCPDF-main/tcpdf.php')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy thư viện TCPDF!']);
    exit();
}
require_once 'C:/xampp/htdocs/Ql_web_cosothuctap/TCPDF-main/TCPDF-main/tcpdf.php';

// Kiểm tra quyền ghi tệp PDF
$temp_dir = sys_get_temp_dir();
if (!is_writable($temp_dir)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Không thể ghi tệp PDF vào thư mục tạm!']);
    exit();
}

// Tạo PDF tạm thời
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Hệ thống quản lý thực tập');
$pdf->SetTitle('Phiếu Đánh Giá Thực Tập');
$pdf->SetSubject('Đánh giá thực tập sinh viên');
$pdf->SetFont('dejavusans', '', 12);
$pdf->AddPage();
$pdf->SetFont('dejavusans', 'B', 16);
$pdf->Cell(0, 10, 'PHIẾU ĐÁNH GIÁ QUÁ TRÌNH THỰC TẬP', 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '1. Thông tin cơ sở thực tập', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(50, 10, 'Tên cơ sở:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['ten_co_so']), 0, 1);
$pdf->Cell(50, 10, 'Tiêu đề tuyển dụng:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['tieu_de_tuyen_dung']), 0, 1);
$pdf->Cell(50, 10, 'Công ty:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['cong_ty']), 0, 1);
$pdf->Cell(50, 10, 'Email liên hệ:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['email_lien_he']), 0, 1);
$pdf->Ln(5);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '2. Thông tin giảng viên', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(50, 10, 'Giảng viên hướng dẫn:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['giang_vien_huong_dan']), 0, 1);
$pdf->Ln(5);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '3. Thông tin sinh viên', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(50, 10, 'Họ và tên:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['ho_ten_sinh_vien']), 0, 1);
$pdf->Cell(50, 10, 'Mã số sinh viên:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['ma_sinh_vien']), 0, 1);
$pdf->Cell(50, 10, 'Lớp - Khóa:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['lop_khoa']), 0, 1);
$pdf->Cell(50, 10, 'Ngành học:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['nganh_hoc']), 0, 1);
$pdf->Cell(50, 10, 'Thời gian thực tập:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['thoi_gian_thuc_tap']), 0, 1);
$pdf->Ln(5);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '4. Nội dung đánh giá', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$html = '
<table border="1" cellpadding="5">
    <tr><th>Tiêu chí đánh giá</th><th>Mức độ</th><th>Ghi chú</th></tr>
    <tr><td>Thái độ, tinh thần trách nhiệm</td><td>' . htmlspecialchars($eval['thai_do']) . '</td><td>' . htmlspecialchars($eval['thai_do_ghi_chu']) . '</td></tr>
    <tr><td>Kỹ năng chuyên môn</td><td>' . htmlspecialchars($eval['ky_nang_chuyen_mon']) . '</td><td>' . htmlspecialchars($eval['ky_nang_ghi_chu']) . '</td></tr>
    <tr><td>Khả năng làm việc nhóm</td><td>' . htmlspecialchars($eval['lam_viec_nhom']) . '</td><td>' . htmlspecialchars($eval['lam_viec_nhom_ghi_chu']) . '</td></tr>
    <tr><td>Kỹ năng giao tiếp</td><td>' . htmlspecialchars($eval['ky_nang_giao_tiep']) . '</td><td>' . htmlspecialchars($eval['ky_nang_giao_tiep_ghi_chu']) . '</td></tr>
    <tr><td>Khả năng thích nghi với môi trường</td><td>' . htmlspecialchars($eval['thich_nghi']) . '</td><td>' . htmlspecialchars($eval['thich_nghi_ghi_chu']) . '</td></tr>
    <tr><td>Tuân thủ nội quy, kỷ luật</td><td>' . htmlspecialchars($eval['tuan_thu']) . '</td><td>' . htmlspecialchars($eval['tuan_thu_ghi_chu']) . '</td></tr>
</table>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln(5);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '5. Nhận xét chung', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$pdf->MultiCell(0, 10, htmlspecialchars($eval['nhan_xet_chung']), 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '6. Kết quả đề xuất', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(0, 10, htmlspecialchars($eval['ket_qua_de_xuat']), 0, 1);
$pdf->Ln(5);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '7. Ngày đánh giá và người đánh giá', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(50, 10, 'Ngày đánh giá:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['ngay_danh_gia']), 0, 1);
$pdf->Cell(50, 10, 'Người đánh giá:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['nguoi_danh_gia']), 0, 1);

// Lưu PDF vào file tạm thời
$filename = 'DanhGiaThucTap_' . $eval['ma_sinh_vien'] . '.pdf';
$pdf_path = sys_get_temp_dir() . '/' . $filename;
$pdf->Output($pdf_path, 'F');

// Thiết lập PHPMailer
$mail = new PHPMailer(true);
try {
    // Cấu hình server SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'nguyenconghieu7924@gmail.com';
    $mail->Password = 'fdgmilbrhtgkxbev';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Bật debug để kiểm tra lỗi SMTP
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function ($str, $level) {
        file_put_contents('C:/xampp/htdocs/Ql_web_cosothuctap/logs/smtp_debug.log', "[$level] $str\n", FILE_APPEND);
    };

    // Người gửi và người nhận
    $mail->setFrom('nguyenconghieu7924@gmail.com', 'Hệ thống quản lý thực tập');
    $mail->addAddress($eval['email_sinh_vien'], $eval['ho_ten_sinh_vien']);

    // Nội dung email
    $mail->isHTML(true);
    $mail->Subject = 'Phiếu Đánh Giá Thực Tập - ' . $eval['ma_sinh_vien'];
    $mail->Body = '
        <h2>Phiếu Đánh Giá Thực Tập</h2>
        <p>Kính gửi ' . htmlspecialchars($eval['ho_ten_sinh_vien']) . ',</p>
        <p>Đính kèm là phiếu đánh giá thực tập của bạn tại ' . htmlspecialchars($eval['ten_co_so']) . '.</p>
        <p>Trân trọng,<br>Hệ thống quản lý thực tập</p>
    ';
    $mail->addAttachment($pdf_path, $filename);

    // Gửi email
    $mail->send();

    // Xóa file PDF tạm thời
    if (file_exists($pdf_path)) {
        unlink($pdf_path);
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Email đã được gửi thành công!']);
} catch (Exception $e) {
    // Xóa file PDF tạm thời nếu có lỗi
    if (file_exists($pdf_path)) {
        unlink($pdf_path);
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Lỗi khi gửi email: ' . $mail->ErrorInfo]);
}
