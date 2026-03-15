<?php
require_once 'db.php';

if (!isset($_SESSION['student_id']) || $_SESSION['user_type'] !== 'student') {
    jsonResponse(false, null, '请先登录');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, '请求方式错误');
}

$data = getJsonInput();
$examId = $data['exam_id'] ?? '';

if (empty($examId)) {
    jsonResponse(false, null, '请选择考试');
}

$studentId = $_SESSION['student_id'];

try {
    // 检查学生是否有该考试的报名权限
    $checkStmt = $pdo->prepare("SELECT * FROM student_exam_auth WHERE student_id = ? AND exam_id = ?");
    $checkStmt->execute([$studentId, $examId]);
    if (!$checkStmt->fetch()) {
        jsonResponse(false, null, '您没有该考试的报名权限');
    }
    
    // 检查考试是否已截止
    $examStmt = $pdo->prepare("SELECT exam_name, deadline FROM exam WHERE exam_id = ?");
    $examStmt->execute([$examId]);
    $exam = $examStmt->fetch();
    
    if (!$exam) {
        jsonResponse(false, null, '考试不存在');
    }
    
    // 检查是否已过截止时间
    if ($exam['deadline'] && strtotime($exam['deadline']) < time()) {
        jsonResponse(false, null, '该考试报名已截止');
    }
    
    // 检查是否已报名
    $enrollCheckStmt = $pdo->prepare("SELECT * FROM enrollment WHERE student_id = ? AND exam_id = ?");
    $enrollCheckStmt->execute([$studentId, $examId]);
    if ($enrollCheckStmt->fetch()) {
        jsonResponse(false, null, '您已经报名过该考试');
    }
    
    // 执行报名
    $stmt = $pdo->prepare("INSERT INTO enrollment (student_id, exam_id) VALUES (?, ?)");
    $stmt->execute([$studentId, $examId]);
    
    jsonResponse(true, null, '报名成功');
    
} catch (PDOException $e) {
    jsonResponse(false, null, '报名失败：' . $e->getMessage());
}
