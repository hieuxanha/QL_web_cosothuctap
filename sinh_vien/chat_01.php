<?php
header('Content-Type: application/json');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

$apiKey = 'YOUR_API_KEY_HERE';
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . urlencode($apiKey);

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = strtolower(trim($input['message'] ?? ''));

if (!$userMessage) {
    echo json_encode(['error' => 'Không nhận được tin nhắn']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "ql_cosothuctap");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Kết nối CSDL thất bại', 'details' => $conn->connect_error]);
    exit;
}

$dataText = '';
$topic = 'chung';
$matched = false;

function extractMajor($message)
{
    $majors = [
        'Công nghệ thông tin' => ['cntt', 'it', 'công nghệ thông tin', 'ngành cntt'],
        'Kinh tế' => ['kế toán', 'kinh tế', 'tài chính'],
        'Môi trường' => ['môi trường'],
        'Quản lý đất đai' => ['quản lý đất đai', 'qlđđ'],
        'Khí tượng thủy văn' => ['khí tượng', 'thủy văn'],
        'Trắc địa bản đồ' => ['trắc địa', 'bản đồ'],
        'Địa chất' => ['địa chất'],
        'Tài nguyên nước' => ['tài nguyên nước'],
        'Lý luận chính trị' => ['chính trị', 'lý luận'],
        'Biển - Hải đảo' => ['biển', 'hải đảo'],
        'Biến đổi khí hậu' => ['biến đổi khí hậu'],
        'Khoa học đại cương' => ['đại cương'],
        'Thể chất quốc phòng' => ['thể chất', 'quốc phòng'],
        'Luật' => ['luật'],
        'Ngoại ngữ' => ['ngoại ngữ', 'tiếng anh']
    ];
    foreach ($majors as $major => $aliases) {
        foreach ($aliases as $alias) {
            if (strpos($message, $alias) !== false) {
                return $major;
            }
        }
    }
    return '';
}

function safeQuery($conn, $sql, $type, &$dataText)
{
    $res = $conn->query($sql);
    if (!$res) {
        $dataText .= "\n⚠️ Lỗi truy vấn $type: " . $conn->error . "\n";
        return;
    }
    switch ($type) {
        case 'tuyển dụng':
            $dataText .= "**📢 TIN TUYỂN DỤNG**:\n\n";
            while ($r = $res->fetch_assoc()) {
                $dataText .= "💼 **{$r['tieu_de']}**\n🏢 Công ty: {$r['ten_cong_ty']}\n📍 Địa chỉ: {$r['dia_chi']}\n";
                if (!empty($r['luong'])) $dataText .= "💰 Lương: {$r['luong']}\n";
                if (!empty($r['hinh_thuc_lam_viec'])) $dataText .= "⏰ Hình thức: {$r['hinh_thuc_lam_viec']}\n";
                $dataText .= "📄 Mô tả: " . substr($r['mo_ta'], 0, 100) . "...\n\n";
            }
            break;
        case 'cong_ty_nganh':
            $dataText .= "**🏢 CÁC CÔNG TY CHO NGÀNH YÊU CẦU**:\n\n";
            while ($r = $res->fetch_assoc()) {
                $dataText .= "🏢 {$r['ten_cong_ty']}\n📋 Lĩnh vực: {$r['linh_vuc']}\n📍 Địa chỉ: {$r['dia_chi']}\n\n";
            }
            break;
        case 'báo cáo':
            $dataText .= "**📝 BÁO CÁO THỰC TẬP**:\n\n";
            while ($r = $res->fetch_assoc()) {
                $dataText .= "📋 Nội dung: {$r['noi_dung']}\n📅 Ngày gửi: {$r['ngay_gui']}\n\n";
            }
            break;
        case 'đánh giá':
            $dataText .= "**🧾 ĐÁNH GIÁ**:\n\n";
            while ($r = $res->fetch_assoc()) {
                $dataText .= "🏢 Cơ sở: {$r['ten_co_so']}\n💼 Vị trí: {$r['tieu_de_tuyen_dung']}\n📝 Nhận xét: {$r['nhan_xet_chung']}\n\n";
            }
            break;
        case 'khoa':
            $dataText .= "**🏫 DANH SÁCH CÁC KHOA**:\n\n";
            while ($r = $res->fetch_assoc()) {
                $moTa = $r['mo_ta'] ?: "Chưa có mô tả.";
                $dataText .= "🏫 {$r['ten_khoa']}\n📝 {$moTa}\n\n";
            }
            break;
    }
}

$nganh = extractMajor($userMessage);
if ($nganh) {
    $nganhSafe = $conn->real_escape_string($nganh);

    // Danh sách công ty theo ngành
    $sql1 = "SELECT ten_cong_ty, linh_vuc, dia_chi
             FROM cong_ty
             WHERE trang_thai = 'Đã duyệt' AND linh_vuc LIKE '%$nganhSafe%'
             LIMIT 5";
    safeQuery($conn, $sql1, 'cong_ty_nganh', $dataText);

    // Tin tuyển dụng theo ngành
    $sql2 = "SELECT t.tieu_de, t.mo_ta, t.luong, t.hinh_thuc_lam_viec, c.ten_cong_ty, c.dia_chi
             FROM tuyen_dung t
             JOIN cong_ty c ON t.stt_cty = c.stt_cty
             WHERE t.trang_thai = 'Đã duyệt' AND c.linh_vuc LIKE '%$nganhSafe%'
             ORDER BY t.ngay_dang DESC
             LIMIT 5";
    safeQuery($conn, $sql2, 'tuyển dụng', $dataText);

    $topic = "tư vấn ngành $nganh";
    $matched = true;
}

if (!$matched && strpos($userMessage, 'công ty') !== false) {
    $sql = "SELECT c.ten_cong_ty, c.linh_vuc, c.dia_chi, t.tieu_de
            FROM cong_ty c
            LEFT JOIN tuyen_dung t ON c.stt_cty = t.stt_cty
            WHERE c.trang_thai='Đã duyệt'
            ORDER BY c.ten_cong_ty
            LIMIT 5";
    safeQuery($conn, $sql, 'cong_ty_nganh', $dataText);
    $topic = "công ty thực tập";
    $matched = true;
}

if (!$matched && strpos($userMessage, 'tuyển dụng') !== false) {
    $sql = "SELECT t.tieu_de, t.mo_ta, t.luong, t.hinh_thuc_lam_viec, c.ten_cong_ty, c.dia_chi
            FROM tuyen_dung t
            JOIN cong_ty c ON t.stt_cty = c.stt_cty
            WHERE t.trang_thai='Đã duyệt'
            ORDER BY t.ngay_dang DESC
            LIMIT 5";
    safeQuery($conn, $sql, 'tuyển dụng', $dataText);
    $topic = "tuyển dụng";
    $matched = true;
}

if (!$matched && strpos($userMessage, 'báo cáo') !== false) {
    safeQuery($conn, "SELECT noi_dung, ngay_gui FROM bao_cao_thuc_tap ORDER BY ngay_gui DESC LIMIT 3", 'báo cáo', $dataText);
    $topic = "báo cáo";
    $matched = true;
}

if (!$matched && strpos($userMessage, 'đánh giá') !== false) {
    safeQuery($conn, "SELECT ten_co_so, tieu_de_tuyen_dung, nhan_xet_chung FROM danh_gia_thuc_tap LIMIT 3", 'đánh giá', $dataText);
    $topic = "đánh giá";
    $matched = true;
}

if (!$matched && strpos($userMessage, 'khoa') !== false) {
    safeQuery($conn, "SELECT ten_khoa, mo_ta FROM khoa", 'khoa', $dataText);
    $topic = "khoa";
    $matched = true;
}

if (!$matched) {
    $dataText .= "📚 Câu hỏi của bạn nằm ngoài phạm vi dữ liệu nội bộ. Tôi sẽ cố gắng hỗ trợ theo kiến thức chung.\n";
    $topic = "ngoai he thong";
}

$prompt = <<<EOD
Bạn là trợ lý chatbot cho sinh viên HUNRE.\n
**Chủ đề: {$topic}**\n{$dataText}\n
**Câu hỏi:** {$userMessage}\n
Vui lòng trả lời bằng tiếng Việt, thân thiện, dễ hiểu. Nếu không có dữ liệu, hãy dùng kiến thức chung để giúp sinh viên.
EOD;

$payload = ["contents" => [["parts" => [["text" => $prompt]]]]];
$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($payload)
    ]
];

$response = @file_get_contents($apiUrl, false, stream_context_create($options));
if (!$response) {
    echo json_encode(['error' => 'Không thể kết nối Gemini']);
    exit;
}

$json = json_decode($response, true);
$reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? "🤖 Xin lỗi, tôi chưa có câu trả lời phù hợp.";

$conn->close();
echo json_encode(['reply' => $reply]);
