<?php
require_once 'db.php';

if (!isset($_SESSION['teacher_id']) || $_SESSION['user_type'] !== 'teacher') {
    jsonResponse(false, null, '请先登录');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $studentId = $_GET['student_id'] ?? '';
    
    if (empty($studentId)) {
        jsonResponse(false, null, '参数错误');
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT e.exam_id, e.exam_name,
                   CASE WHEN sea.auth_id IS NOT NULL THEN 1 ELSE 0 END as has_auth
            FROM exam e
            LEFT JOIN student_exam_auth sea ON e.exam_id = sea.exam_id AND sea.student_id = ?
            ORDER BY e.create_time DESC
        ");
        $stmt->execute([$studentId]);
        $exams = $stmt->fetchAll();
        
        jsonResponse(true, $exams);
        
    } catch (PDOException $e) {
        jsonResponse(false, null, '获取考试权限失败');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = getJsonInput();
    $studentId = $data['student_id'] ?? '';
    $examIds = $data['exam_ids'] ?? [];
    
    if (empty($studentId)) {
        jsonResponse(false, null, '参数错误');
    }
    
    try {
        $pdo->beginTransaction();
        
        $deleteStmt = $pdo->prepare("DELETE FROM student_exam_auth WHERE student_id = ?");
        $deleteStmt->execute([$studentId]);
        
        if (!empty($examIds)) {
            $insertStmt = $pdo->prepare("INSERT INTO student_exam_auth (student_id, exam_id) VALUES (?, ?)");
            foreach ($examIds as $examId) {
                $insertStmt->execute([$studentId, $examId]);
            }
        }
        
        $pdo->commit();
        jsonResponse(true, null, '权限分配成功');
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        jsonResponse(false, null, '权限分配失败');
    }
}

jsonResponse(false, null, '请求方式错误');
