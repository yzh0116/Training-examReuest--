-- 学生考试管理系统数据库脚本
-- 创建数据库
CREATE DATABASE IF NOT EXISTS exam_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE exam_system;

-- 学生表
CREATE TABLE IF NOT EXISTS student (
    student_id INT AUTO_INCREMENT PRIMARY KEY COMMENT '学生ID',
    exam_id VARCHAR(50) UNIQUE NOT NULL COMMENT '准考证号',
    name VARCHAR(50) NOT NULL COMMENT '姓名',
    age INT COMMENT '年龄',
    grade VARCHAR(20) COMMENT '年级',
    class VARCHAR(20) COMMENT '班级',
    password VARCHAR(255) NOT NULL COMMENT '加密后的密码',
    photo VARCHAR(255) COMMENT '照片存储路径',
    is_first_login TINYINT DEFAULT 1 COMMENT '首次登录标识（0-否，1-是）',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生表';

-- 教师表
CREATE TABLE IF NOT EXISTS teacher (
    teacher_id INT AUTO_INCREMENT PRIMARY KEY COMMENT '教师ID',
    username VARCHAR(50) UNIQUE NOT NULL COMMENT '教师账号',
    password VARCHAR(255) NOT NULL COMMENT '加密后的密码',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='教师表';

-- 考试表
CREATE TABLE IF NOT EXISTS exam (
    exam_id INT AUTO_INCREMENT PRIMARY KEY COMMENT '考试ID',
    exam_name VARCHAR(100) NOT NULL COMMENT '考试名称',
    exam_date DATE COMMENT '考试日期',
    exam_location VARCHAR(200) COMMENT '考试地点',
    teacher_id INT COMMENT '关联教师ID',
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    FOREIGN KEY (teacher_id) REFERENCES teacher(teacher_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='考试表';

-- 学生考试权限表
CREATE TABLE IF NOT EXISTS student_exam_auth (
    auth_id INT AUTO_INCREMENT PRIMARY KEY COMMENT '权限ID',
    student_id INT NOT NULL COMMENT '关联学生ID',
    exam_id INT NOT NULL COMMENT '关联考试ID',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    FOREIGN KEY (student_id) REFERENCES student(student_id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES exam(exam_id) ON DELETE CASCADE,
    UNIQUE KEY unique_auth (student_id, exam_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生考试权限表';

-- 报名信息表
CREATE TABLE IF NOT EXISTS enrollment (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY COMMENT '报名ID',
    student_id INT NOT NULL COMMENT '关联学生ID',
    exam_id INT NOT NULL COMMENT '关联考试ID',
    enroll_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '报名时间',
    status TINYINT DEFAULT 0 COMMENT '审核状态（0-未审核，1-已审核）',
    FOREIGN KEY (student_id) REFERENCES student(student_id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES exam(exam_id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, exam_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='报名信息表';

-- 插入默认教师账号（密码: admin123）
INSERT INTO teacher (username, password) VALUES 
('admin', '$2y$10$mX0i3lQevuSTcfXcQKtcY.f.puWEMlvMEbN1tm4V8iPd6NnrzmAVu');

-- 修改考试表，添加新字段
ALTER TABLE exam 
ADD COLUMN exam_description TEXT COMMENT '考试简介' AFTER exam_name,
ADD COLUMN published_by VARCHAR(100) COMMENT '发布教师' AFTER exam_location,
ADD COLUMN deadline DATETIME COMMENT '报名截止时间' AFTER exam_date;

-- 更新现有考试数据（可选）
UPDATE exam SET 
    exam_description = '暂无简介',
    published_by = '管理员',
    deadline = DATE_ADD(exam_date, INTERVAL -1 DAY)
WHERE exam_description IS NULL;
