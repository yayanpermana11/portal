<?php
session_start();
if (empty($_SESSION['logged_in'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$targetDir = __DIR__ . '/img/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$targetFile = $targetDir . 'logo.png'; // nama tetap logo.png

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['logo'])) {
    $file = $_FILES['logo'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['png', 'jpg', 'jpeg', 'svg', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) {
        http_response_code(400);
        echo json_encode(['error' => 'Format tidak diizinkan. Hanya: ' . implode(', ', $allowed)]);
        exit;
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['error' => 'Ukuran maksimal 2MB.']);
        exit;
    }
    // Simpan dengan nama logo.png (timpa jika sudah ada)
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        echo json_encode(['url' => '/img/logo.png']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Gagal menyimpan file.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Tidak ada file yang dikirim.']);
}