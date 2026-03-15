<?php
/**
 * 密码哈希转换工具
 * 用于将明文密码转换为 bcrypt 哈希值
 * 
 * 使用方法:
 * 1. 命令行: php hash_password.php your_password
 * 2. 浏览器访问: https://your-domain.com/api/hash_password.php?password=your_password
 */

// 如果是命令行模式
if (php_sapi_name() === 'cli') {
    if ($argc < 2) {
        echo "使用方法: php hash_password.php <密码>\n";
        echo "示例: php hash_password.php admin123\n";
        exit(1);
    }
    
    $password = $argv[1];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "原始密码: {$password}\n";
    echo "哈希值: {$hash}\n";
    echo "验证结果: " . (password_verify($password, $hash) ? '成功' : '失败') . "\n";
    exit(0);
}

// 如果是 Web 访问
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$password = $_GET['password'] ?? '';

if (empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => '请提供密码参数，例如: ?password=your_password'
    ]);
    exit;
}

// 生成哈希
$hash = password_hash($password, PASSWORD_DEFAULT);

// 验证哈希
$verify = password_verify($password, $hash);

echo json_encode([
    'success' => true,
    'original_password' => $password,
    'password_hash' => $hash,
    'verify_result' => $verify,
    'algorithm' => PASSWORD_DEFAULT,
    'usage' => "INSERT INTO table (username, password) VALUES ('user', '{$hash}');"
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
