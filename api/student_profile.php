<?php
require_once 'db.php';

if (!isset($_SESSION['student_id']) || $_SESSION['user_type'] !== 'student') {
    jsonResponse(false, null, '请先登录');
}

$studentId = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("
            SELECT student_id, exam_id, name, age, grade, class, photo 
            FROM student 
            WHERE student_id = ?
        ");
        $stmt->execute([$studentId]);
        $student = $stmt->fetch();
        
        if (!$student) {
            jsonResponse(false, null, '学生信息不存在');
        }
        
        jsonResponse(true, $student);
        
    } catch (PDOException $e) {
        jsonResponse(false, null, '获取信息失败');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = getJsonInput();
    
    $name = $data['name'] ?? '';
    $age = $data['age'] ?? null;
    $grade = $data['grade'] ?? '';
    $class = $data['class'] ?? '';
    
    if (empty($name)) {
        jsonResponse(false, null, '姓名不能为空');
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE student 
            SET name = ?, age = ?, grade = ?, class = ? 
            WHERE student_id = ?
        ");
        $stmt->execute([$name, $age, $grade, $class, $studentId]);
        
        jsonResponse(true, null, '信息更新成功');
        
    } catch (PDOException $e) {
        jsonResponse(false, null, '更新失败');
    }
}

jsonResponse(false, null, '请求方式错误');
