<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

// Helper function to send JSON response
function sendResponse($success, $data = [], $error = '')
{
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'error' => $error
    ]);
    exit;
}

// Handle GET requests for fetching or searching recruitments
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'get_recruitments' || $action === 'search_recruitments') {
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default to 10 records per page
    $offset = ($page - 1) * $limit;

    // Prepare SQL query for counting total recruitments
    $countSql = "SELECT COUNT(*) FROM tuyen_dung td JOIN cong_ty ct ON td.stt_cty = ct.stt_cty";
    if ($action === 'search_recruitments' && $keyword) {
        $keyword = $conn->real_escape_string($keyword);
        $countSql .= " WHERE td.tieu_de LIKE ? OR ct.ten_cong_ty LIKE ?";
    }

    $stmt = $conn->prepare($countSql);
    if ($action === 'search_recruitments' && $keyword) {
        $likeKeyword = "%$keyword%";
        $stmt->bind_param('ss', $likeKeyword, $likeKeyword);
    }
    $stmt->execute();
    $stmt->bind_result($totalRecruitments);
    $stmt->fetch();
    $stmt->close();

    $totalPages = ceil($totalRecruitments / $limit);

    // Prepare SQL query for fetching paginated recruitments
    $sql = "SELECT td.ma_tuyen_dung, td.tieu_de, td.trang_thai, td.noi_bat, ct.ten_cong_ty
            FROM tuyen_dung td
            JOIN cong_ty ct ON td.stt_cty = ct.stt_cty";
    if ($action === 'search_recruitments' && $keyword) {
        $sql .= " WHERE td.tieu_de LIKE ? OR ct.ten_cong_ty LIKE ?";
    }
    $sql .= " ORDER BY td.tieu_de ASC LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    if ($action === 'search_recruitments' && $keyword) {
        $likeKeyword = "%$keyword%";
        $stmt->bind_param('ssii', $likeKeyword, $likeKeyword, $limit, $offset);
    } else {
        $stmt->bind_param('ii', $limit, $offset);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $recruitments = [];
        while ($row = $result->fetch_assoc()) {
            $recruitments[] = [
                'ma_tuyen_dung' => $row['ma_tuyen_dung'],
                'tieu_de' => $row['tieu_de'],
                'ten_cong_ty' => $row['ten_cong_ty'],
                'trang_thai' => $row['trang_thai'] ?: 'Đang chờ',
                'noi_bat' => $row['noi_bat']
            ];
        }
        sendResponse(true, [
            'recruitments' => $recruitments,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalRecruitments' => $totalRecruitments
        ]);
    } else {
        sendResponse(false, [], 'Lỗi khi truy vấn cơ sở dữ liệu: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Handle GET request for fetching a single recruitment
if ($action === 'get_recruitment') {
    $ma_tuyen_dung = isset($_GET['ma_tuyen_dung']) ? trim($_GET['ma_tuyen_dung']) : '';
    if (empty($ma_tuyen_dung)) {
        sendResponse(false, [], 'Mã tuyển dụng không hợp lệ');
    }

    $sql = "SELECT tieu_de, mo_ta, so_luong, han_nop, dia_chi, hinh_thuc, gioi_tinh, noi_bat, khoa, trinh_do, ma_tuyen_dung
            FROM tuyen_dung WHERE ma_tuyen_dung = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $ma_tuyen_dung);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            sendResponse(true, ['recruitment' => $row]);
        } else {
            sendResponse(false, [], 'Không tìm thấy tin tuyển dụng');
        }
    } else {
        sendResponse(false, [], 'Lỗi khi truy vấn cơ sở dữ liệu: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    sendResponse(false, [], 'Yêu cầu không hợp lệ!');
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

// Handle status updates
if (in_array($action, ['approve', 'reject', 'restore', 'cancel'])) {
    $ma_tuyen_dung = isset($_POST['ma_tuyen_dung']) ? trim($_POST['ma_tuyen_dung']) : '';
    if (empty($ma_tuyen_dung)) {
        sendResponse(false, [], 'Mã tuyển dụng không hợp lệ');
    }

    if (!$conn) {
        sendResponse(false, [], 'Không thể kết nối cơ sở dữ liệu!');
    }

    // Check current status of the recruitment
    $sql_check = "SELECT trang_thai FROM tuyen_dung WHERE ma_tuyen_dung = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $ma_tuyen_dung);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        $stmt_check->close();
        sendResponse(false, [], 'Tin tuyển dụng không tồn tại!');
    }

    $row = $result_check->fetch_assoc();
    $current_status = trim($row['trang_thai']);
    $stmt_check->close();

    // Validate action based on current status
    if ($action === 'approve' || $action === 'reject') {
        if ($current_status !== 'Đang chờ' && $current_status !== 'Bị từ chối') {
            sendResponse(false, [], 'Tin tuyển dụng không ở trạng thái phù hợp để thực hiện hành động này! Trạng thái hiện tại: ' . $current_status);
            $conn->close();
            exit;
        }
    } elseif ($action === 'restore') {
        if ($current_status !== 'Bị từ chối') {
            sendResponse(false, [], 'Tin tuyển dụng không ở trạng thái bị từ chối! Trạng thái hiện tại: ' . $current_status);
            $conn->close();
            exit;
        }
    } elseif ($action === 'cancel') {
        if ($current_status !== 'Đã duyệt') {
            sendResponse(false, [], 'Tin tuyển dụng không ở trạng thái đã duyệt! Trạng thái hiện tại: ' . $current_status);
            $conn->close();
            exit;
        }
    }

    $trang_thai = '';
    $message = '';

    switch ($action) {
        case 'approve':
            $trang_thai = 'Đã duyệt';
            $message = 'Duyệt tin tuyển dụng thành công!';
            break;
        case 'reject':
            $trang_thai = 'Bị từ chối';
            $message = 'Đã từ chối tin tuyển dụng!';
            break;
        case 'restore':
            $trang_thai = 'Đang chờ';
            $message = 'Đã khôi phục tin tuyển dụng!';
            break;
        case 'cancel':
            $trang_thai = 'Đang chờ';
            $message = 'Đã hủy duyệt tin tuyển dụng!';
            break;
        default:
            sendResponse(false, [], 'Hành động không hợp lệ!');
            $conn->close();
            exit;
    }

    $sql = "UPDATE tuyen_dung SET trang_thai = ? WHERE ma_tuyen_dung = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $trang_thai, $ma_tuyen_dung);

    if ($stmt->execute()) {
        $_SESSION['message'] = $message;
        sendResponse(true, ['trang_thai' => $trang_thai, 'message' => $message]);
    } else {
        $_SESSION['error'] = 'Lỗi khi cập nhật tin tuyển dụng: ' . $stmt->error;
        sendResponse(false, [], 'Lỗi khi cập nhật tin tuyển dụng: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Handle recruitment edit
if ($action === 'edit_recruitment') {
    $ma_tuyen_dung = isset($_POST['ma_tuyen_dung']) ? trim($_POST['ma_tuyen_dung']) : '';
    $tieu_de = isset($_POST['tieu_de']) ? trim($_POST['tieu_de']) : '';
    $mo_ta = isset($_POST['mo_ta']) ? trim($_POST['mo_ta']) : null;
    $so_luong = isset($_POST['so_luong']) ? intval($_POST['so_luong']) : 0;
    $han_nop = isset($_POST['han_nop']) ? trim($_POST['han_nop']) : '';
    $dia_chi = isset($_POST['dia_chi']) ? trim($_POST['dia_chi']) : '';
    $hinh_thuc = isset($_POST['hinh_thuc']) ? trim($_POST['hinh_thuc']) : '';
    $gioi_tinh = isset($_POST['gioi_tinh']) ? trim($_POST['gioi_tinh']) : '';
    $noi_bat = isset($_POST['noi_bat']) ? intval($_POST['noi_bat']) : 0;
    $khoa = isset($_POST['khoa']) ? trim($_POST['khoa']) : null;
    $trinh_do = isset($_POST['trinh_do']) ? trim($_POST['trinh_do']) : null;

    // Validate inputs
    if (empty($ma_tuyen_dung) || empty($tieu_de) || $so_luong <= 0 || empty($han_nop) || empty($dia_chi) || empty($hinh_thuc) || empty($gioi_tinh)) {
        sendResponse(false, [], 'Vui lòng điền đầy đủ các trường bắt buộc');
    }

    // Validate han_nop is a valid date
    if (!DateTime::createFromFormat('Y-m-d', $han_nop)) {
        sendResponse(false, [], 'Hạn nộp không hợp lệ');
    }

    // Validate enum values
    $valid_hinh_thuc = ['Full-time', 'Part-time'];
    $valid_gioi_tinh = ['Nam', 'Nữ', 'Không giới hạn'];
    $valid_khoa = [
        'kinh_te',
        'moi_truong',
        'quan_ly_dat_dai',
        'khi_tuong_thuy_van',
        'trac_dia_ban_do',
        'dia_chat',
        'tai_nguyen_nuoc',
        'cntt',
        'ly_luan_chinh_tri',
        'bien_hai_dao',
        'khoa_hoc_dai_cuong',
        'the_chat_quoc_phong',
        'bo_mon_luat',
        'bien_doi_khi_hau',
        'ngoai_ngu',
        ''
    ];
    $valid_trinh_do = ['Không yêu cầu', 'Trung cấp', 'Cao đẳng', 'Đại học', 'Thạc sĩ', 'Tiến sĩ', ''];

    if (!in_array($hinh_thuc, $valid_hinh_thuc)) {
        sendResponse(false, [], 'Hình thức không hợp lệ');
    }
    if (!in_array($gioi_tinh, $valid_gioi_tinh)) {
        sendResponse(false, [], 'Giới tính không hợp lệ');
    }
    if (!in_array($khoa, $valid_khoa)) {
        sendResponse(false, [], 'Khoa không hợp lệ');
    }
    if (!in_array($trinh_do, $valid_trinh_do)) {
        sendResponse(false, [], 'Trình độ không hợp lệ');
    }

    // Check if recruitment exists
    $sql = "SELECT ma_tuyen_dung FROM tuyen_dung WHERE ma_tuyen_dung = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $ma_tuyen_dung);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        $stmt->close();
        sendResponse(false, [], 'Tin tuyển dụng không tồn tại');
    }
    $stmt->close();

    // Prepare SQL update
    $sql = "UPDATE tuyen_dung SET tieu_de = ?, mo_ta = ?, so_luong = ?, han_nop = ?, dia_chi = ?, hinh_thuc = ?, gioi_tinh = ?, noi_bat = ?, khoa = ?, trinh_do = ? WHERE ma_tuyen_dung = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssissssisis', $tieu_de, $mo_ta, $so_luong, $han_nop, $dia_chi, $hinh_thuc, $gioi_tinh, $noi_bat, $khoa, $trinh_do, $ma_tuyen_dung);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Chỉnh sửa tin tuyển dụng thành công!';
        sendResponse(true, ['message' => 'Chỉnh sửa tin tuyển dụng thành công!']);
    } else {
        $_SESSION['error'] = 'Lỗi khi cập nhật tin tuyển dụng: ' . $stmt->error;
        sendResponse(false, [], 'Lỗi khi cập nhật tin tuyển dụng: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
    exit;
}

$conn->close();
sendResponse(false, [], 'Yêu cầu không hợp lệ!');
