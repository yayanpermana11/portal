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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['icon'])) {
    $file = $_FILES['icon'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'];
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
    $newName = 'icon_' . time() . '_' . uniqid() . '.' . $ext;
    $targetPath = $targetDir . $newName;
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $url = '/img/' . $newName;
        echo json_encode(['url' => $url]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Gagal menyimpan file.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Tidak ada file yang dikirim.']);
}