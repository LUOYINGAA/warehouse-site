<?php
// 数据库配置文件
class Database {
    private static $instance = null;
    private $db;

    private function __construct() {
        $this->db = new SQLite3('database.sqlite');
        $this->db->exec("PRAGMA encoding = 'UTF-8'");
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->db;
    }
}

// 登录验证函数
function isLoggedIn() {
    session_start();
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// 登录检查
function checkLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// 安全输出函数
function safeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// 上传文件函数
function uploadFile($file, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']) {
    $targetDir = 'uploads/';
    $fileName = uniqid() . '_' . basename($file['name']);
    $targetPath = $targetDir . $fileName;
    $fileType = mime_content_type($file['tmp_name']);

    if (!in_array($fileType, $allowedTypes)) {
        return ['error' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'];
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        return ['error' => 'File size exceeds 5MB limit.'];
    }

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => $fileName];
    } else {
        return ['error' => 'Failed to upload file.'];
    }
}

// 删除文件函数
function deleteFile($fileName) {
    $filePath = 'uploads/' . $fileName;
    if (file_exists($filePath)) {
        unlink($filePath);
        return true;
    }
    return false;
}
?>
