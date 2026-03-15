# 考试管理系统

一个基于 Vue 3 + PHP 8.2 + MySQL 的现代化考试管理系统，支持学生报名、教师管理、准考证打印等功能。  
项目开发：ZhehanYang  
本仓库创建于2026年03月15日，因为作者今年刚好参与中考，觉得杭州中考的网站太丑了，简单使用Vue框架搭建了一些样式，功能也搭建了，但是不是很完美，后续持续更新。

## 功能特点

### 🎓 学生端
- ✅ 学生登录（支持验证码）
- ✅ 考试列表查看
- ✅ 考试详情查看
- ✅ 自定义弹窗报名确认
- ✅ 个人信息管理
- ✅ 密码修改

### 👨‍🏫 教师端
- ✅ 教师登录
- ✅ 学生注册管理
- ✅ 考试创建与管理
- ✅ 报名权限分配
- ✅ 报名处理与筛选
- ✅ A4 规格准考证打印
- ✅ 数据概览统计

### 📋 核心功能
- **考试管理**：支持创建考试，设置考试简介、发布教师、截止时间
- **报名系统**：学生可查看考试详情并报名，截止时间后自动关闭报名
- **权限管理**：教师可为学生分配特定考试的报名权限
- **准考证系统**：支持批量打印 A4 规格准考证
- **数据统计**：实时显示学生、考试、报名等数据统计

## 技术栈

- **前端**：Vue 3 + Vite + JavaScript
- **后端**：PHP 8.2 + PDO
- **数据库**：MySQL/MariaDB
- **样式**：自定义 CSS（响应式设计）
- **安全**：密码哈希加密、验证码登录

## 安装部署

### 环境要求
- PHP 8.2+
- MySQL 5.7+
- Nginx/Apache
- Node.js 16+（用于构建前端）

### 部署步骤

1. **克隆项目**
   ```bash
   git clone https://github.com/yzh0116/Training-examRequest-.git
   cd Training-examRequest-
   ```

2. **安装前端依赖**
   ```bash
   npm install
   ```

3. **构建前端**
   ```bash
   npm run build
   ```

4. **数据库配置**
   - 创建数据库：`exam_system`
   - 导入 `database.sql` 文件

5. **服务器配置**
   - 参考 `DEPLOY.md` 文件中的详细部署指南

6. **配置文件修改**
   - 修改 `src/config.js` 中的 API 地址为您的实际域名

### 初始账户

- **教师账户**：
  - 用户名：`admin`
  - 密码：`admin123`

- **学生账户**：
  - 由教师在后台注册生成

## 项目结构

```
├── api/             # PHP API 文件
├── src/             # Vue 源码
│   ├── views/       # 页面组件
│   ├── utils/       # 工具函数
│   ├── router/      # 路由配置
│   └── assets/      # 静态资源
├── dist/            # 构建后的前端文件
├── database.sql     # 数据库初始化脚本
├── DEPLOY.md        # 部署指南
└── README.md        # 项目说明
```

## 核心文件说明

- **api/teacher_exam.php**：教师考试管理 API
- **api/student_enroll.php**：学生报名 API
- **api/teacher_enrollment.php**：报名处理 API
- **src/views/StudentDashboard.vue**：学生端考试列表
- **src/views/TeacherDashboard.vue**：教师管理后台

## 安全特性

- 密码使用 password_hash 加密存储
- 登录支持验证码防暴力破解
- 后端 API 权限验证
- 前端路由守卫

## 浏览器支持

- Chrome / Edge（推荐）
- Firefox
- Safari

## 许可证

MIT License

## 贡献

欢迎提交 Issue 和 Pull Request 来改进这个项目！

---

**注意**：本项目仅用于学习和教学目的，实际生产环境部署请根据需要进行安全加固。