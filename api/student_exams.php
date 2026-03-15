<?php
require_once 'db.php';

if (!isset($_SESSION['student_id']) || $_SESSION['user_type'] !== 'student') {
    jsonResponse(false, null, '请先登录');
}

$studentId = $_SESSION['student_id'];

try {
    $stmt = $pdo->prepare("
        SELECT e.exam_id, e.exam_name, e.exam_description, e.exam_date, e.deadline, 
               e.exam_location, e.published_by, e.create_time,
               CASE WHEN en.enrollment_id IS NOT NULL THEN 1 ELSE 0 END as is_enrolled,
               CASE WHEN e.deadline IS NOT NULL AND e.deadline < NOW() THEN 1 ELSE 0 END as is_expired
        FROM exam e
        INNER JOIN student_exam_auth sea ON e.exam_id = sea.exam_id
        LEFT JOIN enrollment en ON e.exam_id = en.exam_id AND en.student_id = ?
        WHERE sea.student_id = ?
        ORDER BY e.create_time DESC
    ");
    $stmt->execute([$studentId, $studentId]);
    $exams = $stmt->fetchAll();
    
    jsonResponse(true, $exams);
    
} catch (PDOException $e) {
    jsonResponse(false, null, '获取考试列表失败');
}
