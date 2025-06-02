<?php
header('Content-Type: application/json');

// API key & URL
$apiKey = 'AIzaSyBsrEtn27f_uUTuRd8Q7ML6GOdjQkmcnXo'; // <- Thay bằng API key thật của bạn
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . urlencode($apiKey);
// Nhận nội dung người dùng gửi lên
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = strtolower(trim($input['message'] ?? ''));

if (!$userMessage) {
    echo json_encode(['error' => 'No message received']);
    exit;
}

// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "ql_cosothuctap");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed', 'details' => $conn->connect_error]);
    exit;
}

// ====== TRUY VẤN DỮ LIỆU PHÙ HỢP VỚI CÂU HỎI ======
$dataText = '';
$topic = 'chung';

if (strpos($userMessage, 'công ty') !== false || strpos($userMessage, 'thực tập') !== false) {
    $result = $conn->query("SELECT ten_cong_ty, linh_vuc, dia_chi FROM cong_ty WHERE trang_thai='Đã duyệt' LIMIT 5");
    while ($row = $result->fetch_assoc()) {
        $dataText .= "- {$row['ten_cong_ty']} | Lĩnh vực: {$row['linh_vuc']} | Địa chỉ: {$row['dia_chi']}\n";
    }
    $topic = 'công ty thực tập';
} elseif (strpos($userMessage, 'báo cáo') !== false) {
    $result = $conn->query("SELECT noi_dung, ngay_gui FROM bao_cao_thuc_tap ORDER BY ngay_gui DESC LIMIT 3");
    while ($row = $result->fetch_assoc()) {
        $dataText .= "- Nội dung: {$row['noi_dung']} | Ngày gửi: {$row['ngay_gui']}\n";
    }
    $topic = 'báo cáo thực tập';
} elseif (strpos($userMessage, 'đánh giá') !== false) {
    $result = $conn->query("SELECT ten_co_so, tieu_de_tuyen_dung, nhan_xet_chung FROM danh_gia_thuc_tap LIMIT 3");
    while ($row = $result->fetch_assoc()) {
        $dataText .= "- Cơ sở: {$row['ten_co_so']} | Vị trí: {$row['tieu_de_tuyen_dung']} | Nhận xét: {$row['nhan_xet_chung']}\n";
    }
    $topic = 'đánh giá thực tập';
} elseif (
    strpos($userMessage, 'tuyển dụng') !== false ||
    strpos($userMessage, 'việc làm') !== false ||
    strpos($userMessage, 'job') !== false
) {
    $sql = "SELECT t.tieu_de, t.mo_ta, c.ten_cong_ty, c.dia_chi
            FROM tuyen_dung t
            JOIN cong_ty c ON t.stt_cty = c.stt_cty
            WHERE t.trang_thai = 'Đã duyệt'
            LIMIT 5";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $dataText .= "- Vị trí: {$row['tieu_de']} | Công ty: {$row['ten_cong_ty']} | Địa chỉ: {$row['dia_chi']} | Mô tả: {$row['mo_ta']}\n";
    }
    $topic = 'tuyển dụng';
}

// ===== TẠO PROMPT GỬI ĐẾN GEMINI =====
$prompt = <<<EOD
Bạn là trợ lý chatbot cho hệ thống thực tập của Trường Đại học Tài nguyên và Môi trường Hà Nội (HUNRE).

Thông tin liên quan đến chủ đề "{$topic}":
{$dataText}

Câu hỏi từ sinh viên:
"{$userMessage}"

Vui lòng trả lời rõ ràng, lịch sự và đúng theo dữ liệu trên. Nếu thiếu dữ liệu, hãy nói rõ điều đó.
EOD;

// ===== GỬI YÊU CẦU TỚI GEMINI =====
$data = [
    "contents" => [
        ["parts" => [["text" => $prompt]]]
    ]
];

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
    ]
];

$context  = stream_context_create($options);
$response = file_get_contents($apiUrl, false, $context);

if (!$response) {
    echo json_encode(['error' => 'Failed to connect to Gemini']);
    exit;
}

$json = json_decode($response, true);
$reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? "Xin lỗi, tôi chưa có câu trả lời phù hợp.";

$conn->close();
echo json_encode(['reply' => $reply]);
