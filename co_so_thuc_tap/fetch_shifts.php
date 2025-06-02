<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ma_dang_ky'])) {
    $ma_dang_ky = intval($_POST['ma_dang_ky']);
    $stmt = $conn->prepare("
        SELECT id, ngay_thuc_tap, ca_lam, thoi_gian_ca 
        FROM lich_thuc_tap 
        WHERE ma_dang_ky = ?
        ORDER BY ngay_thuc_tap, ca_lam
    ");
    $stmt->bind_param("i", $ma_dang_ky);
    $stmt->execute();
    $result = $stmt->get_result();
    $shifts = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($shifts);
    $stmt->close();
}
