<?php
session_start();
header('Content-Type: application/json');

$dataDir = __DIR__ . '/data';
$dataFile = $dataDir . '/links.json';

// Buat folder data jika belum ada
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// Data default jika file belum ada
$defaultLinks = [
    ["id"=>"1","name"=>"Google Classroom","url"=>"https://classroom.google.com","category"=>"E-Learning","icon"=>"🏫","color"=>"color-1","clicks"=>0,"createdAt"=>1700000000000],
    ["id"=>"2","name"=>"Google Meet","url"=>"https://meet.google.com","category"=>"E-Learning","icon"=>"📹","color"=>"color-2","clicks"=>0,"createdAt"=>1700100000000],
    ["id"=>"3","name"=>"Khan Academy","url"=>"https://www.khanacademy.org","category"=>"Video Pembelajaran","icon"=>"🎓","color"=>"color-3","clicks"=>0,"createdAt"=>1700200000000],
    ["id"=>"4","name"=>"Wikipedia Indonesia","url"=>"https://id.wikipedia.org","category"=>"Referensi Akademik","icon"=>"📖","color"=>"color-4","clicks"=>0,"createdAt"=>1700300000000],
    ["id"=>"5","name"=>"Google Drive","url"=>"https://drive.google.com","category"=>"Tools & Utilitas","icon"=>"💾","color"=>"color-5","clicks"=>0,"createdAt"=>1700400000000],
    ["id"=>"6","name"=>"Google Docs","url"=>"https://docs.google.com","category"=>"Tools & Utilitas","icon"=>"📝","color"=>"color-6","clicks"=>0,"createdAt"=>1700500000000],
    ["id"=>"7","name"=>"Quizizz","url"=>"https://quizizz.com","category"=>"E-Learning","icon"=>"🎯","color"=>"color-1","clicks"=>0,"createdAt"=>1700600000000],
    ["id"=>"8","name"=>"Perpustakaan Nasional RI","url"=>"https://www.perpusnas.go.id","category"=>"Perpustakaan Digital","icon"=>"🏛️","color"=>"color-2","clicks"=>0,"createdAt"=>1700700000000],
    ["id"=>"9","name"=>"Kemendikbud RI","url"=>"https://www.kemdikbud.go.id","category"=>"Administrasi Sekolah","icon"=>"🇮🇩","color"=>"color-3","clicks"=>0,"createdAt"=>1700800000000],
    ["id"=>"10","name"=>"Canva for Education","url"=>"https://www.canva.com/education","category"=>"Tools & Utilitas","icon"=>"🎨","color"=>"color-4","clicks"=>0,"createdAt"=>1700900000000],
    ["id"=>"11","name"=>"Google Scholar","url"=>"https://scholar.google.com","category"=>"Referensi Akademik","icon"=>"🔬","color"=>"color-5","clicks"=>0,"createdAt"=>1701000000000],
    ["id"=>"12","name"=>"YouTube Edukasi","url"=>"https://www.youtube.com/education","category"=>"Video Pembelajaran","icon"=>"▶️","color"=>"color-6","clicks"=>0,"createdAt"=>1701100000000]
];

function loadLinks() {
    global $dataFile, $defaultLinks;
    if (!file_exists($dataFile)) {
        file_put_contents($dataFile, json_encode($defaultLinks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $defaultLinks;
    }
    $links = json_decode(file_get_contents($dataFile), true) ?: [];
    $updated = false;
    foreach ($links as &$link) {
        // Jika masih ada 'category' (string) dan belum ada 'categories', konversi
        if (isset($link['category']) && !isset($link['categories'])) {
            $link['categories'] = [$link['category']];
            unset($link['category']);
            $updated = true;
        }
        // Jika sudah ada 'categories' tapi bukan array, perbaiki
        if (isset($link['categories']) && !is_array($link['categories'])) {
            $link['categories'] = [$link['categories']];
            $updated = true;
        }
    }
    if ($updated) {
        saveLinks($links);
    }
    return $links;
}

function saveLinks($links) {
    global $dataFile;
    file_put_contents($dataFile, json_encode($links, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Cek login untuk aksi yang membutuhkan otentikasi
$method = $_SERVER['REQUEST_METHOD'];
$needsAuth = in_array($method, ['POST', 'PUT', 'DELETE']);
if ($needsAuth) {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        http_response_code(403);
        echo json_encode(['error' => 'Akses ditolak. Silakan login sebagai admin.']);
        exit;
    }
}

// Routing
if ($method === 'GET') {
    $links = loadLinks();
    usort($links, fn($a, $b) => $b['createdAt'] - $a['createdAt']);
    echo json_encode($links);

} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $links = loadLinks();
    $newLink = [
        'id' => 'link_' . uniqid(),
        'name' => trim($input['name']),
        'url' => preg_match('#^https?://#i', $input['url']) ? $input['url'] : 'https://' . $input['url'],
        'categories' => isset($input['categories']) ? $input['categories'] : ['Lainnya'], // array
        'icon' => $input['icon'] ?? '🔗',
        'color' => $input['color'] ?? 'color-1',
        'clicks' => 0,
        'createdAt' => round(microtime(true) * 1000)
    ];
    array_unshift($links, $newLink);
    saveLinks($links);
    echo json_encode($newLink);
} elseif ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $links = loadLinks();
    $updated = false;
    foreach ($links as &$link) {
        if ($link['id'] === $input['id']) {
            $link['name'] = trim($input['name']);
            $link['url'] = preg_match('#^https?://#i', $input['url']) ? $input['url'] : 'https://' . $input['url'];
            $link['categories'] = isset($input['categories']) ? $input['categories'] : $link['categories'];
            $link['icon'] = $input['icon'] ?? '🔗';
            $link['color'] = $input['color'] ?? $link['color'];
            $updated = true;
            break;
        }
    }

    if ($updated) {
        saveLinks($links);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Link tidak ditemukan']);
    }

} elseif ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $links = loadLinks();
    $links = array_values(array_filter($links, fn($l) => $l['id'] !== $input['id']));
    saveLinks($links);
    echo json_encode(['success' => true]);
}