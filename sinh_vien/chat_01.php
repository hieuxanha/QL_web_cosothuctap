<?php
header('Content-Type: application/json');

// API key & URL
$apiKey = 'AIzaSyBsrEtn27f_uUTuRd8Q7ML6GOdjQkmcnXo'; // Thay bằng API key thật
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . urlencode($apiKey);

// Nhận nội dung người dùng
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = strtolower(trim($input['message'] ?? ''));

if (!$userMessage) {
    echo json_encode(['error' => 'Không nhận được tin nhắn']);
    exit;
}

// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "ql_cosothuctap");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Kết nối cơ sở dữ liệu thất bại', 'details' => $conn->connect_error]);
    exit;
}

$dataText = '';
$topic = 'chung';
$matched = false;

// === 1. XỬ LÝ NGÀNH ===
function extractMajor($message)
{
    $aliasMajors = [
        'Công nghệ thông tin' => ['công nghệ thông tin', 'cntt', 'it', 'ngành cntt', 'ngành it', 'khoa cntt'],
        'Kinh tế' => ['kinh tế', 'kế toán', 'tài chính', 'khoa kinh tế'],
        'Môi trường' => ['môi trường', 'khoa môi trường'],
        'Quản lý đất đai' => ['quản lý đất đai', 'qlđđ', 'ngành ql đất đai'],
        'Khí tượng thủy văn' => ['khí tượng', 'thủy văn', 'khí tượng thủy văn'],
        'Trắc địa bản đồ' => ['trắc địa', 'bản đồ', 'trắc địa bản đồ'],
        'Địa chất' => ['địa chất', 'khoa địa chất'],
        'Tài nguyên nước' => ['tài nguyên nước', 'nước'],
        'Lý luận chính trị' => ['lý luận', 'chính trị', 'lý luận chính trị'],
        'Biển - Hải đảo' => ['biển', 'hải đảo'],
        'Biến đổi khí hậu' => ['biến đổi khí hậu', 'biến đổi thời tiết'],
        'Khoa học đại cương' => ['đại cương', 'khoa học đại cương'],
        'Thể chất quốc phòng' => ['thể chất', 'quốc phòng', 'tcqp'],
        'Luật' => ['luật', 'bộ môn luật'],
        'Ngoại ngữ' => ['ngoại ngữ', 'tiếng anh', 'english'],
    ];

    foreach ($aliasMajors as $major => $aliases) {
        foreach ($aliases as $alias) {
            if (strpos($message, $alias) !== false) {
                return $major;
            }
        }
    }
    return '';
}


$nganhHoc = extractMajor($userMessage);
if ($nganhHoc) {
    $result = $conn->query("SELECT ten_cong_ty, linh_vuc, dia_chi 
                            FROM cong_ty 
                            WHERE trang_thai='Đã duyệt' AND linh_vuc LIKE '%$nganhHoc%' 
                            LIMIT 5");
    $dataText .= "**GỢI Ý CÔNG TY THỰC TẬP CHO NGÀNH _{$nganhHoc}_**:\n\n";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dataText .= "🏢 **{$row['ten_cong_ty']}**\n📋 **Lĩnh vực**: {$row['linh_vuc']}\n📍 **Địa chỉ**: {$row['dia_chi']}\n\n";
        }
    } else {
        $dataText .= "Hiện tại chưa có công ty nào phù hợp với ngành *{$nganhHoc}*.\n";
    }
    $topic = "tư vấn ngành {$nganhHoc}";
    $matched = true;
}

// === 2. CÔNG TY ===
if (!$matched && (strpos($userMessage, 'công ty') !== false || strpos($userMessage, 'thực tập') !== false)) {
    $result = $conn->query("SELECT ten_cong_ty, linh_vuc, dia_chi FROM cong_ty WHERE trang_thai='Đã duyệt' LIMIT 5");
    if ($result->num_rows > 0) {
        $dataText .= "**DANH SÁCH CÔNG TY THỰC TẬP**:\n\n";
        while ($row = $result->fetch_assoc()) {
            $dataText .= "🏢 **{$row['ten_cong_ty']}**\n📋 **Lĩnh vực**: {$row['linh_vuc']}\n📍 **Địa chỉ**: {$row['dia_chi']}\n\n";
        }
    } else {
        $dataText .= "Không tìm thấy công ty thực tập nào phù hợp.\n";
    }
    $topic = 'công ty thực tập';
    $matched = true;
}

// === 3. TUYỂN DỤNG ===
if (!$matched && (strpos($userMessage, 'tuyển dụng') !== false || strpos($userMessage, 'việc làm') !== false || strpos($userMessage, 'job') !== false)) {
    $sql = "SELECT t.tieu_de, t.mo_ta, t.luong, t.hinh_thuc_lam_viec, c.ten_cong_ty, c.dia_chi
            FROM tuyen_dung t
            JOIN cong_ty c ON t.stt_cty = c.stt_cty
            WHERE t.trang_thai = 'Đã duyệt'
            ORDER BY t.ngay_dang DESC
            LIMIT 5";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $dataText .= "**DANH SÁCH TIN TUYỂN DỤNG MỚI NHẤT**:\n\n";
        while ($row = $result->fetch_assoc()) {
            $dataText .= "💼 **{$row['tieu_de']}**\n🏢 **Công ty**: {$row['ten_cong_ty']}\n📍 **Địa chỉ**: {$row['dia_chi']}\n";
            if (!empty($row['luong'])) $dataText .= "💰 **Lương**: {$row['luong']}\n";
            if (!empty($row['hinh_thuc_lam_viec'])) $dataText .= "⏰ **Hình thức**: {$row['hinh_thuc_lam_viec']}\n";
            $dataText .= "📄 **Mô tả**: " . substr($row['mo_ta'], 0, 100) . "...\n\n";
        }
    } else {
        $dataText .= "Không tìm thấy tin tuyển dụng nào.\n";
    }
    $topic = 'tuyển dụng';
    $matched = true;
}

// === 4. BÁO CÁO ===
if (!$matched && strpos($userMessage, 'báo cáo') !== false) {
    $result = $conn->query("SELECT noi_dung, ngay_gui FROM bao_cao_thuc_tap ORDER BY ngay_gui DESC LIMIT 3");
    if ($result->num_rows > 0) {
        $dataText .= "**THÔNG TIN BÁO CÁO THỰC TẬP**:\n\n";
        while ($row = $result->fetch_assoc()) {
            $dataText .= "📋 **Nội dung**: {$row['noi_dung']}\n📅 **Ngày gửi**: {$row['ngay_gui']}\n\n";
        }
    } else {
        $dataText .= "Không tìm thấy báo cáo thực tập nào.\n";
    }
    $topic = 'báo cáo thực tập';
    $matched = true;
}

// === 5. ĐÁNH GIÁ ===
if (!$matched && strpos($userMessage, 'đánh giá') !== false) {
    $result = $conn->query("SELECT ten_co_so, tieu_de_tuyen_dung, nhan_xet_chung FROM danh_gia_thuc_tap LIMIT 3");
    if ($result->num_rows > 0) {
        $dataText .= "**ĐÁNH GIÁ THỰC TẬP**:\n\n";
        while ($row = $result->fetch_assoc()) {
            $dataText .= "🏢 **Cơ sở**: {$row['ten_co_so']}\n💼 **Vị trí**: {$row['tieu_de_tuyen_dung']}\n📝 **Nhận xét**: {$row['nhan_xet_chung']}\n\n";
        }
    } else {
        $dataText .= "Không tìm thấy đánh giá thực tập nào.\n";
    }
    $topic = 'đánh giá thực tập';
    $matched = true;
}

// === 6. KHOA ===
if (!$matched && (strpos($userMessage, 'khoa') !== false || strpos($userMessage, 'thông báo các khoa') !== false)) {
    $result = $conn->query("SELECT ten_khoa, mo_ta FROM khoa");
    if ($result && $result->num_rows > 0) {
        $dataText .= "**DANH SÁCH CÁC KHOA**:\n\n";
        while ($row = $result->fetch_assoc()) {
            $dataText .= "🏫 **Khoa**: {$row['ten_khoa']}\n📝 **Mô tả**: " . (!empty($row['mo_ta']) ? $row['mo_ta'] : "Chưa có mô tả.") . "\n\n";
        }
    } else {
        $dataText .= "Không tìm thấy thông tin về các khoa.\n";
    }
    $topic = 'thông tin khoa';
    $matched = true;
}

// === 7. KIẾN THỨC NGOÀI CSDL ===
if (!$matched) {
    $dataText .= "Câu hỏi của bạn không có dữ liệu trong hệ thống, tôi sẽ hỗ trợ bằng kiến thức tổng quát.\n";
    $topic = 'hỏi đáp ngoài hệ thống';
}

// === GỬI PROMPT TỚI GEMINI ===
$prompt = <<<EOD
Bạn là trợ lý chatbot cho hệ thống thực tập của Trường Đại học Tài nguyên và Môi trường Hà Nội (HUNRE).

**Thông tin liên quan đến chủ đề "{$topic}"**:
{$dataText}

**Câu hỏi từ sinh viên**: "{$userMessage}"

**Hướng dẫn trả lời**:
1. Trả lời thân thiện, chuyên nghiệp bằng tiếng Việt.
2. Sử dụng dữ liệu trên để cung cấp thông tin chính xác (nếu có).
3. Nếu không có dữ liệu, sử dụng kiến thức của bạn để đưa ra lời khuyên hữu ích.
4. Định dạng dễ đọc với emoji, căn lề rõ ràng.
5. Nếu cần thêm hỗ trợ, gợi ý sinh viên liên hệ qua email: support@hunre.edu.vn.
6. Sử dụng markdown để định dạng.
EOD;

$data = ["contents" => [["parts" => [["text" => $prompt]]]]];

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($apiUrl, false, $context);

if (!$response) {
    echo json_encode(['error' => 'Không thể kết nối tới Gemini API']);
    exit;
}

$json = json_decode($response, true);
$reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? "Xin lỗi, tôi chưa có câu trả lời phù hợp.";

$conn->close();
echo json_encode(['reply' => $reply]);
