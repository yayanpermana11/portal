<?php
session_start();
header('Content-Type: application/json');

$dataDir = __DIR__ . '/data';
$dataFile = $dataDir . '/links.json';

// Buat folder data jika belum ada
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

function loadLinks() {
    global $dataFile;
    if (!file_exists($dataFile)) return [];
    $json = file_get_contents($dataFile);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function saveLinks($links) {
    global $dataFile;
    file_put_contents($dataFile, json_encode($links, JSON_PRETTY_PRINT));
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    echo json_encode(loadLinks());

} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $links = loadLinks();
    
    // --- FITUR AUTO ICON DARI URL ---
    $finalUrl = preg_match('#^https?://#i', $input['url']) ? $input['url'] : 'https://' . $input['url'];
    $domain = parse_url($finalUrl, PHP_URL_HOST);
    $autoIcon = $domain ? "https://s2.googleusercontent.com/s2/favicons?domain=" . $domain . "&sz=128" : "🔗";
    
    $newLink = [
        'id' => uniqid('link_'),
        'name' => trim($input['name']),
        'url' => $finalUrl,
        'categories' => isset($input['categories']) ? $input['categories'] : ['Lainnya'],
        'icon' => $autoIcon, // Otomatis menyimpan icon dari Google Favicon
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
            $finalUrl = preg_match('#^https?://#i', $input['url']) ? $input['url'] : 'https://' . $input['url'];
            $link['url'] = $finalUrl;
            $link['categories'] = isset($input['categories']) ? $input['categories'] : $link['categories'];
            
            // --- FITUR AUTO ICON DARI URL ---
            $domain = parse_url($finalUrl, PHP_URL_HOST);
            $link['icon'] = $domain ? "https://s2.googleusercontent.com/s2/favicons?domain=" . $domain . "&sz=128" : "🔗";
            
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
    $newLinks = array_filter($links, function($l) use ($input) {
        return $l['id'] !== $input['id'];
    });
    saveLinks(array_values($newLinks));
    echo json_encode(['success' => true]);
}
?>
