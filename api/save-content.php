<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$file_path = '../content/data.json';

// إذا كانت الطريقة GET، نقوم بقراءة الملف
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);
        if ($content === false) {
            http_response_code(500);
            echo json_encode(['error' => 'فشل في قراءة الملف']);
            exit;
        }
        echo $content;
    } else {
        // إذا لم يكن الملف موجوداً، نرجع الهيكل الافتراضي
        $default_content = [
            'pageDescription' => [
                'title' => '',
                'content' => ''
            ],
            'navigation' => [
                'logo' => '',
                'github_link' => '',
                'menu_items' => []
            ],
            'sections' => []
        ];
        echo json_encode($default_content);
    }
    exit;
}

// التأكد من أن الطريقة POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'طريقة غير مسموح بها']);
    exit;
}

// تلقي البيانات
$input = file_get_contents('php://input');
if (empty($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'لم يتم إرسال أي بيانات']);
    exit;
}

$data = json_decode($input, true);

// التحقق من صحة البيانات
if ($data === null) {
    http_response_code(400);
    echo json_encode(['error' => 'بيانات غير صالحة']);
    exit;
}

// التحقق من هيكل البيانات
if (!isset($data['pageDescription'])) {
    http_response_code(400);
    echo json_encode(['error' => 'هيكل البيانات غير صحيح - pageDescription مفقود']);
    exit;
}

// حفظ البيانات في الملف
if (file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT)) === false) {
    http_response_code(500);
    echo json_encode(['error' => 'فشل في حفظ البيانات']);
    exit;
}

echo json_encode(['success' => true]);
