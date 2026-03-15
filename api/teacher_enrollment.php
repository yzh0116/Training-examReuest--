<?php
require_once 'db.php';

if (!isset($_SESSION['teacher_id']) || $_SESSION['user_type'] !== 'teacher') {
    jsonResponse(false, null, '请先登录');
}

// 获取报名列表
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $examId = $_GET['exam_id'] ?? '';
    
    try {
        $sql = "
            SELECT en.enrollment_id, en.enroll_time, en.status,
                   s.student_id, s.exam_id as student_exam_id, s.name, s.age, s.grade, s.class, s.photo,
                   e.exam_name, e.exam_date, e.exam_location, e.deadline
            FROM enrollment en
            INNER JOIN student s ON en.student_id = s.student_id
            INNER JOIN exam e ON en.exam_id = e.exam_id
        ";
        
        $params = [];
        if ($examId) {
            $sql .= " WHERE en.exam_id = ?";
            $params[] = $examId;
        }
        
        $sql .= " ORDER BY en.enroll_time DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $enrollments = $stmt->fetchAll();
        
        jsonResponse(true, $enrollments);
        
    } catch (PDOException $e) {
        jsonResponse(false, null, '获取报名列表失败');
    }
}

// 获取准考证信息（用于打印）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = getJsonInput();
    $enrollmentIds = $data['enrollment_ids'] ?? [];
    
    if (empty($enrollmentIds)) {
        jsonResponse(false, null, '请选择要打印的报名记录');
    }
    
    try {
        $placeholders = implode(',', array_fill(0, count($enrollmentIds), '?'));
        $stmt = $pdo->prepare("
            SELECT en.enrollment_id, en.enroll_time,
                   s.student_id, s.exam_id as student_exam_id, s.name, s.age, s.grade, s.class, s.photo,
                   e.exam_name, e.exam_description, e.exam_date, e.exam_location, e.published_by, e.deadline
            FROM enrollment en
            INNER JOIN student s ON en.student_id = s.student_id
            INNER JOIN exam e ON en.exam_id = e.exam_id
            WHERE en.enrollment_id IN ($placeholders)
            ORDER BY s.name
        ");
        $stmt->execute($enrollmentIds);
        $enrollments = $stmt->fetchAll();
        
        jsonResponse(true, $enrollments);
        
    } catch (PDOException $e) {
        jsonResponse(false, null, '获取准考证信息失败');
    }
}

jsonResponse(false, null, '请求方式错误');
