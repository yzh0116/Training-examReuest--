<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, '请求方式错误');
}

$data = getJsonInput();
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';
$captcha = $data['captcha'] ?? '';

if (empty($username) || empty($password) || empty($captcha)) {
    jsonResponse(false, null, '请填写完整信息');
}

if (!isset($_SESSION['captcha']) || strtoupper($captcha) !== $_SESSION['captcha']) {
    jsonResponse(false, null, '验证码错误');
}

unset($_SESSION['captcha']);

try {
    $stmt = $pdo->prepare("SELECT * FROM teacher WHERE username = ?");
    $stmt->execute([$username]);
    $teacher = $stmt->fetch();
    
    if (!$teacher) {
        jsonResponse(false, null, '账号或密码错误');
    }
    
    if (!password_verify($password, $teacher['password'])) {
        jsonResponse(false, null, '账号或密码错误');
    }
    
    $_SESSION['teacher_id'] = $teacher['teacher_id'];
    $_SESSION['teacher_name'] = $teacher['username'];
    $_SESSION['user_type'] = 'teacher';
    
    jsonResponse(true, [
        'teacher_id' => $teacher['teacher_id'],
        'username' => $teacher['username']
    ], '登录成功');
    
} catch (PDOException $e) {
    jsonResponse(false, null, '登录失败，请稍后重试');
}
