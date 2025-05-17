<?php
session_start();
require '../db.php'; // Kết nối CSDL

header('Content-Type: application/json'); // Trả về JSON

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

// Handle GET requests for fetching companies
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'get_companies' || $action === 'search_companies') {
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

    // Prepare SQL query
    $sql = "SELECT stt_cty, ten_cong_ty, dia_chi, trang_thai FROM cong_ty";
    if ($action === 'search_companies' && $keyword) {
        $keyword = $conn->real_escape_string($keyword);
        $sql .= " WHERE ten_cong_ty LIKE ? OR dia_chi LIKE ?";
    }

    $stmt = $conn->prepare($sql);
    if ($action === 'search_companies' && $keyword) {
        $likeKeyword = "%$keyword%";
        $stmt->bind_param('ss', $likeKeyword, $likeKeyword);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $companies = [];
        while ($row = $result->fetch_assoc()) {
            $companies[] = [
                'stt_cty' => $row['stt_cty'],
                'ten_cong_ty' => $row['ten_cong_ty'],
                'dia_chi' => $row['dia_chi'],
                'trang_thai' => $row['trang_thai'] ?: 'Đang chờ'
            ];
        }
        sendResponse(true, ['companies' => $companies]);
    } else {
        sendResponse(false, [], 'Lỗi khi truy vấn cơ sở dữ liệu: ' . $stmt->error);
    }

    $stmt->close();
}

// Handle GET request for fetching a single company
if ($action === 'get_company') {
    $stt_cty = isset($_GET['stt_cty']) ? intval($_GET['stt_cty']) : 0;
    if ($stt_cty <= 0) {
        sendResponse(false, [], 'ID công ty không hợp lệ');
    }

    $sql = "SELECT * FROM cong_ty WHERE stt_cty = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $stt_cty);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            sendResponse(true, ['company' => $row]);
        } else {
            sendResponse(false, [], 'Không tìm thấy công ty');
        }
    } else {
        sendResponse(false, [], 'Lỗi khi truy vấn cơ sở dữ liệu: ' . $stmt->error);
    }

    $stmt->close();
}

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Handle status updates
    if (in_array($action, ['approve', 'reject', 'restore', 'cancel'])) {
        $stt_cty = isset($_POST['stt_cty']) ? intval($_POST['stt_cty']) : null;

        if (!$stt_cty) {
            sendResponse(false, [], 'ID công ty không hợp lệ');
        }

        $trang_thai = '';
        $message = '';

        switch ($action) {
            case 'approve':
                $trang_thai = 'Đã duyệt';
                $message = 'Công ty đã được duyệt thành công!';
                break;
            case 'reject':
                $trang_thai = 'Bị từ chối';
                $message = 'Công ty đã bị từ chối!';
                break;
            case 'restore':
            case 'cancel':
                $trang_thai = 'Đang chờ';
                $message = $action == 'restore' ? 'Công ty đã được khôi phục!' : 'Công ty đã bị hủy duyệt!';
                break;
            default:
                sendResponse(false, [], 'Hành động không hợp lệ');
        }

        $sql = "UPDATE cong_ty SET trang_thai = ? WHERE stt_cty = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $trang_thai, $stt_cty);

        if ($stmt->execute()) {
            $_SESSION['message'] = $message;
            sendResponse(true, ['trang_thai' => $trang_thai, 'message' => $message]);
        } else {
            $_SESSION['error'] = "Lỗi khi cập nhật trạng thái: " . $stmt->error;
            sendResponse(false, [], "Lỗi khi cập nhật trạng thái: " . $stmt->error);
        }

        $stmt->close();
    }

    // Handle company edit
    if ($action === 'edit_company') {
        $stt_cty = isset($_POST['stt_cty']) ? intval($_POST['stt_cty']) : 0;
        $ten_cong_ty = isset($_POST['ten_cong_ty']) ? trim($_POST['ten_cong_ty']) : '';
        $dia_chi = isset($_POST['dia_chi']) ? trim($_POST['dia_chi']) : '';
        $so_dien_thoai = isset($_POST['so_dien_thoai']) ? trim($_POST['so_dien_thoai']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $gioi_thieu = isset($_POST['gioi_thieu']) ? trim($_POST['gioi_thieu']) : null;
        $quy_mo = isset($_POST['quy_mo']) ? trim($_POST['quy_mo']) : null;
        $linh_vuc = isset($_POST['linh_vuc']) ? trim($_POST['linh_vuc']) : null;

        // Validate inputs
        if ($stt_cty <= 0 || !$ten_cong_ty || !$dia_chi || !$so_dien_thoai || !$email) {
            sendResponse(false, [], 'Vui lòng điền đầy đủ các trường bắt buộc');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(false, [], 'Email không hợp lệ');
        }

        // Check for duplicate company name
        $sql = "SELECT stt_cty FROM cong_ty WHERE ten_cong_ty = ? AND stt_cty != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $ten_cong_ty, $stt_cty);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            sendResponse(false, [], 'Tên công ty đã tồn tại');
        }
        $stmt->close();

        // Handle file uploads
        $logo = null;
        $anh_bia = null;
        $upload_dir = '../sinh_vien/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Allowed image types
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
            $logo_mime = mime_content_type($_FILES['logo']['tmp_name']);
            if (!in_array($logo_mime, $allowed_types)) {
                sendResponse(false, [], 'Logo phải là định dạng JPEG, PNG hoặc GIF');
            }
            $logo_ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $logo_name = 'logo_' . $stt_cty . '_' . time() . '.' . $logo_ext;
            $logo_path = $upload_dir . $logo_name;
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path)) {
                $logo = $logo_name;
            } else {
                sendResponse(false, [], 'Lỗi khi tải lên logo');
            }
        }

        if (isset($_FILES['anh_bia']) && $_FILES['anh_bia']['error'] == UPLOAD_ERR_OK) {
            $anh_bia_mime = mime_content_type($_FILES['anh_bia']['tmp_name']);
            if (!in_array($anh_bia_mime, $allowed_types)) {
                sendResponse(false, [], 'Ảnh bìa phải là định dạng JPEG, PNG hoặc GIF');
            }
            $anh_bia_ext = pathinfo($_FILES['anh_bia']['name'], PATHINFO_EXTENSION);
            $anh_bia_name = 'anh_bia_' . $stt_cty . '_' . time() . '.' . $anh_bia_ext;
            $anh_bia_path = $upload_dir . $anh_bia_name;
            if (move_uploaded_file($_FILES['anh_bia']['tmp_name'], $anh_bia_path)) {
                $anh_bia = $anh_bia_name;
            } else {
                sendResponse(false, [], 'Lỗi khi tải lên ảnh bìa');
            }
        }

        // Prepare SQL update
        $sql = "UPDATE cong_ty SET ten_cong_ty = ?, dia_chi = ?, so_dien_thoai = ?, email = ?, gioi_thieu = ?, quy_mo = ?, linh_vuc = ?";
        $params = [$ten_cong_ty, $dia_chi, $so_dien_thoai, $email, $gioi_thieu, $quy_mo, $linh_vuc];
        $types = 'sssssss';

        if ($logo) {
            $sql .= ", logo = ?";
            $params[] = $logo;
            $types .= 's';
        }
        if ($anh_bia) {
            $sql .= ", anh_bia = ?";
            $params[] = $anh_bia;
            $types .= 's';
        }

        $sql .= " WHERE stt_cty = ?";
        $params[] = $stt_cty;
        $types .= 'i';

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Chỉnh sửa công ty thành công!';
            sendResponse(true, ['message' => 'Chỉnh sửa công ty thành công!']);
        } else {
            $_SESSION['error'] = 'Lỗi khi cập nhật công ty: ' . $stmt->error;
            sendResponse(false, [], 'Lỗi khi cập nhật công ty: ' . $stmt->error);
        }

        $stmt->close();
    }
}

$conn->close();
sendResponse(false, [], 'Yêu cầu không hợp lệ');
