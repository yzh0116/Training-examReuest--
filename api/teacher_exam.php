<?php
require_once 'db.php';

if (!isset($_SESSION['teacher_id']) || $_SESSION['user_type'] !== 'teacher') {
    jsonResponse(false, null, '请先登录');
}

// 获取考试列表
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("
            SELECT e.exam_id, e.exam_name, e.exam_description, e.exam_date, e.deadline, 
                   e.exam_location, e.published_by, e.create_time,
                   COUNT(DISTINCT sea.student_id) as auth_count,
                   COUNT(DISTINCT en.student_id) as enroll_count
            FROM exam e
            LEFT JOIN student_exam_auth sea ON e.exam_id = sea.exam_id
            LEFT JOIN enrollment en ON e.exam_id = en.exam_id
            GROUP BY e.exam_id
            ORDER BY e.create_time DESC
        ");
        $exams = $stmt->fetchAll();
        
        // 检查每个考试是否已截止
        foreach ($exams as &$exam) {
            $exam['is_expired'] = $exam['deadline'] && strtotime($exam['deadline']) < time();
        }
        
        jsonResponse(true, $exams);
        
    } catch (PDOException $e) {
        jsonResponse(false, null, '获取考试列表失败');
    }
}

// 创建考试
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = getJsonInput();
    $examName = $data['exam_name'] ?? '';
    $examDescription = $data['exam_description'] ?? '';
    $examDate = $data['exam_date'] ?? '';
    $deadline = $data['deadline'] ?? '';
    $examLocation = $data['exam_location'] ?? '';
    $publishedBy = $data['published_by'] ?? '';
    
    if (empty($examName)) {
        jsonResponse(false, null, '考试名称不能为空');
    }
    
    // 如果没有提供发布教师，使用当前登录教师
    if (empty($publishedBy)) {
        $publishedBy = $_SESSION['teacher_name'] ?? '管理员';
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO exam (exam_name, exam_description, exam_date, deadline, exam_location, published_by, teacher_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$examName, $examDescription, $examDate, $deadline, $examLocation, $publishedBy, $_SESSION['teacher_id']]);
        
        jsonResponse(true, null, '考试创建成功');
        
    } catch (PDOException $e) {
        jsonResponse(false, null, '创建失败：' . $e->getMessage());
    }
}

// 删除考试
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = getJsonInput();
    $examId = $data['exam_id'] ?? '';
    
    if (empty($examId)) {
        jsonResponse(false, null, '参数错误');
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM exam WHERE exam_id = ?");
        $stmt->execute([$examId]);
        
        jsonResponse(true, null, '考试删除成功');
        
    } catch (PDOException $e) {
        jsonResponse(false, null, '删除失败');
    }
}

jsonResponse(false, null, '请求方式错误');
