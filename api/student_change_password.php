<?php
require_once 'db.php';

if (!isset($_SESSION['student_id']) || $_SESSION['user_type'] !== 'student') {
    jsonResponse(false, null, '请先登录');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, '请求方式错误');
}

$data = getJsonInput();
$oldPassword = $data['old_password'] ?? '';
$newPassword = $data['new_password'] ?? '';

if (empty($oldPassword) || empty($newPassword)) {
    jsonResponse(false, null, '请填写完整信息');
}

if (strlen($newPassword) < 6) {
    jsonResponse(false, null, '新密码长度至少6位');
}

try {
    $stmt = $pdo->prepare("SELECT password, is_first_login FROM student WHERE student_id = ?");
    $stmt->execute([$_SESSION['student_id']]);
    $student = $stmt->fetch();
    
    if (!$student) {
        jsonResponse(false, null, '用户不存在');
    }
    
    if ($student['is_first_login'] == 0) {
        if (!password_verify($oldPassword, $student['password'])) {
            jsonResponse(false, null, '原密码错误');
        }
    }
    
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateStmt = $pdo->prepare("UPDATE student SET password = ?, is_first_login = 0 WHERE student_id = ?");
    $updateStmt->execute([$hashedPassword, $_SESSION['student_id']]);
    
    jsonResponse(true, null, '密码修改成功');
    
} catch (PDOException $e) {
    jsonResponse(false, null, '修改失败，请稍后重试');
}
