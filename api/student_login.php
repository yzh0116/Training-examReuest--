<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, '请求方式错误');
}

$data = getJsonInput();
$examId = $data['exam_id'] ?? '';
$password = $data['password'] ?? '';
$captcha = $data['captcha'] ?? '';

if (empty($examId) || empty($password) || empty($captcha)) {
    jsonResponse(false, null, '请填写完整信息');
}

if (!isset($_SESSION['captcha']) || strtoupper($captcha) !== $_SESSION['captcha']) {
    jsonResponse(false, null, '验证码错误');
}

unset($_SESSION['captcha']);

try {
    $stmt = $pdo->prepare("SELECT * FROM student WHERE exam_id = ?");
    $stmt->execute([$examId]);
    $student = $stmt->fetch();
    
    if (!$student) {
        jsonResponse(false, null, '准考证号或密码错误');
    }
    
    if (!password_verify($password, $student['password'])) {
        jsonResponse(false, null, '准考证号或密码错误');
    }
    
    $_SESSION['student_id'] = $student['student_id'];
    $_SESSION['student_name'] = $student['name'];
    $_SESSION['user_type'] = 'student';
    
    jsonResponse(true, [
        'student_id' => $student['student_id'],
        'exam_id' => $student['exam_id'],
        'name' => $student['name'],
        'is_first_login' => $student['is_first_login']
    ], '登录成功');
    
} catch (PDOException $e) {
    jsonResponse(false, null, '登录失败，请稍后重试');
}
