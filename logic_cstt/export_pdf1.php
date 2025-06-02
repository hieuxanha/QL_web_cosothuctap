<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Kết nối cơ sở dữ liệu
require_once '../db.php';
$conn->set_charset("utf8mb4");

// Tải thư viện TCPDF
require_once 'C:/xampp/htdocs/Ql_web_cosothuctap/TCPDF-main/TCPDF-main/tcpdf.php';

$stt_danhgia = filter_input(INPUT_GET, 'stt_danhgia', FILTER_SANITIZE_NUMBER_INT);
$send_to_ui = isset($_GET['send_to_ui']) && $_GET['send_to_ui'] === 'true';

if (!$stt_danhgia) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Mã đánh giá không hợp lệ']);
    exit();
}

// Kiểm tra xem PDF đã tồn tại chưa
$stmt = $conn->prepare("
    SELECT filename FROM pdf_nhan 
    WHERE stt_danhgia = ? AND created_at > NOW() - INTERVAL 5 MINUTE
");
$stmt->bind_param("i", $stt_danhgia);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($existing) {
    // Nếu PDF đã tồn tại, trả về thông tin file hiện có
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'filename' => $existing['filename'],
        'filepath' => $existing['filename']
    ]);
    $conn->close();
    exit();
}

// Lấy thông tin đánh giá
$stmt = $conn->prepare("
    SELECT dg.*, sv.ho_ten AS ho_ten_sinh_vien
    FROM danh_gia_thuc_tap dg
    JOIN sinh_vien sv ON dg.stt_sv = sv.stt_sv
    WHERE dg.stt_danhgia = ?
");
$stmt->bind_param("i", $stt_danhgia);
$stmt->execute();
$eval = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$eval) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy đánh giá']);
    exit();
}

// Tạo đối tượng TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Thiết lập thông tin tài liệu
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Hệ thống quản lý thực tập');
$pdf->SetTitle('Phiếu Đánh Giá Thực Tập');
$pdf->SetSubject('Đánh giá thực tập sinh viên');
$pdf->SetKeywords('Đánh giá, Thực tập, Sinh viên');

// Thiết lập font hỗ trợ tiếng Việt
$pdf->SetFont('dejavusans', '', 12);

// Thêm một trang
$pdf->AddPage();

// Tiêu đề
$pdf->SetFont('dejavusans', 'B', 16);
$pdf->Cell(0, 10, 'PHIẾU ĐÁNH GIÁ QUÁ TRÌNH THỰC TẬP', 0, 1, 'C');
$pdf->Ln(10);

// 1. Thông tin cơ sở thực tập
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '1. Thông tin cơ sở thực tập', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(50, 10, 'Tên cơ sở:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['ten_co_so'] ?? 'N/A'), 0, 1);
$pdf->Cell(50, 10, 'Tiêu đề tuyển dụng:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['tieu_de_tuyen_dung'] ?? 'N/A'), 0, 1);
$pdf->Cell(50, 10, 'Công ty:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['cong_ty'] ?? 'N/A'), 0, 1);
$pdf->Cell(50, 10, 'Email liên hệ:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['email_lien_he'] ?? 'N/A'), 0, 1);
$pdf->Ln(5);

// 2. Thông tin giảng viên
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '2. Thông tin giảng viên', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(50, 10, 'Giảng viên hướng dẫn:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['giang_vien_huong_dan'] ?? 'N/A'), 0, 1);
$pdf->Ln(5);

// 3. Thông tin sinh viên
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '3. Thông tin sinh viên', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(50, 10, 'Họ và tên:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['ho_ten_sinh_vien'] ?? 'N/A'), 0, 1);
$pdf->Cell(50, 10, 'Mã số sinh viên:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['ma_sinh_vien'] ?? 'N/A'), 0, 1);
$pdf->Cell(50, 10, 'Lớp - Khóa:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['lop_khoa'] ?? 'N/A'), 0, 1);
$pdf->Cell(50, 10, 'Ngành học:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['nganh_hoc'] ?? 'N/A'), 0, 1);
$pdf->Cell(50, 10, 'Thời gian thực tập:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['thoi_gian_thuc_tap'] ?? 'N/A'), 0, 1);
$pdf->Ln(5);

// 4. Nội dung đánh giá
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '4. Nội dung đánh giá', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$html = '
<table border="1" cellpadding="5">
    <tr><th>Tiêu chí đánh giá</th><th>Mức độ</th><th>Ghi chú</th></tr>
    <tr><td>Thái độ, tinh thần trách nhiệm</td><td>' . htmlspecialchars($eval['thai_do'] ?? 'N/A') . '</td><td>' . htmlspecialchars($eval['thai_do_ghi_chu'] ?? 'N/A') . '</td></tr>
    <tr><td>Kỹ năng chuyên môn</td><td>' . htmlspecialchars($eval['ky_nang_chuyen_mon'] ?? 'N/A') . '</td><td>' . htmlspecialchars($eval['ky_nang_ghi_chu'] ?? 'N/A') . '</td></tr>
    <tr><td>Khả năng làm việc nhóm</td><td>' . htmlspecialchars($eval['lam_viec_nhom'] ?? 'N/A') . '</td><td>' . htmlspecialchars($eval['lam_viec_nhom_ghi_chu'] ?? 'N/A') . '</td></tr>
    <tr><td>Kỹ năng giao tiếp</td><td>' . htmlspecialchars($eval['ky_nang_giao_tiep'] ?? 'N/A') . '</td><td>' . htmlspecialchars($eval['ky_nang_giao_tiep_ghi_chu'] ?? 'N/A') . '</td></tr>
    <tr><td>Khả năng thích nghi với môi trường</td><td>' . htmlspecialchars($eval['thich_nghi'] ?? 'N/A') . '</td><td>' . htmlspecialchars($eval['thich_nghi_ghi_chu'] ?? 'N/A') . '</td></tr>
    <tr><td>Tuân thủ nội quy, kỷ luật</td><td>' . htmlspecialchars($eval['tuan_thu'] ?? 'N/A') . '</td><td>' . htmlspecialchars($eval['tuan_thu_ghi_chu'] ?? 'N/A') . '</td></tr>
</table>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln(5);

// 5. Nhận xét chung
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '5. Nhận xét chung', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$pdf->MultiCell(0, 10, htmlspecialchars($eval['nhan_xet_chung'] ?? 'N/A'), 0, 'L');
$pdf->Ln(5);

// 6. Kết quả đề xuất
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '6. Kết quả đề xuất', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(0, 10, htmlspecialchars($eval['ket_qua_de_xuat'] ?? 'N/A'), 0, 1);
$pdf->Ln(5);

// 7. Ngày đánh giá và người đánh giá
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '7. Ngày đánh giá và người đánh giá', 0, 1);
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(50, 10, 'Ngày đánh giá:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['ngay_danh_gia'] ?? 'N/A'), 0, 1);
$pdf->Cell(50, 10, 'Người đánh giá:', 0, 0);
$pdf->Cell(0, 10, htmlspecialchars($eval['nguoi_danh_gia'] ?? 'N/A'), 0, 1);

// Lưu file PDF vào thư mục Uploads
$filename = 'DanhGiaThucTap_' . ($eval['ma_sinh_vien'] ?? 'Unknown') . '_' . time() . '.pdf';
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Ql_web_cosothuctap/uploads/';
$filepath = $uploadDir . $filename;

if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Không thể tạo thư mục Uploads']);
        exit();
    }
}
if (!is_writable($uploadDir)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Thư mục Uploads không thể ghi']);
    exit();
}
$pdf->Output($filepath, 'F'); // Lưu file vào server

// Lưu thông tin file vào bảng pdf_nhan
$stmt = $conn->prepare("
    INSERT INTO pdf_nhan (stt_danhgia, filename, filepath, created_at)
    VALUES (?, ?, ?, NOW())
");
$stmt->bind_param("iss", $stt_danhgia, $filename, $filename); // Lưu chỉ filename
$stmt->execute();
$stmt->close();

// Trả về JSON cho AJAX
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'filename' => $filename,
    'filepath' => $filename // Trả về filename để sử dụng trong UI
]);

$conn->close();
exit();
