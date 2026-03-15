<?php
require_once 'db.php';

if (!isset($_SESSION['teacher_id']) || $_SESSION['user_type'] !== 'teacher') {
    jsonResponse(false, null, '请先登录');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $search = $_GET['search'] ?? '';
        
        if ($search) {
            $stmt = $pdo->prepare("
                SELECT s.student_id, s.exam_id, s.name, s.age, s.grade, s.class, s.photo
                FROM student s
                WHERE s.name LIKE ? OR s.exam_id LIKE ?
                ORDER BY s.created_at DESC
            ");
            $searchTerm = "%$search%";
            $stmt->execute([$searchTerm, $searchTerm]);
        } else {
            $stmt = $pdo->query("
                SELECT s.student_id, s.exam_id, s.name, s.age, s.grade, s.class, s.photo
                FROM student s
                ORDER BY s.created_at DESC
            ");
        }
        
        $students = $stmt->fetchAll();
        jsonResponse(true, $students);
        
    } catch (PDOException $e) {
        jsonResponse(false, null, '获取学生列表失败');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $examId = $_POST['exam_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $age = $_POST['age'] ?? null;
    $grade = $_POST['grade'] ?? '';
    $class = $_POST['class'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($examId) || empty($name) || empty($password)) {
        jsonResponse(false, null, '请填写完整信息');
    }
    
    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileExt = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExt;
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
            $photoPath = 'uploads/photos/' . $fileName;
        }
    }
    
    try {
        $checkStmt = $pdo->prepare("SELECT * FROM student WHERE exam_id = ?");
        $checkStmt->execute([$examId]);
        if ($checkStmt->fetch()) {
            jsonResponse(false, null, '准考证号已存在');
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO student (exam_id, name, age, grade, class, password, photo)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$examId, $name, $age, $grade, $class, $hashedPassword, $photoPath]);
        
        jsonResponse(true, null, '学生注册成功');
        
    } catch (PDOException $e) {
        jsonResponse(false, null, '注册失败：' . $e->getMessage());
    }
}

jsonResponse(false, null, '请求方式错误');
