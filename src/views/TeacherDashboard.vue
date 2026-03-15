<template>
  <div>
    <nav class="navbar">
      <div class="navbar-content">
        <span class="navbar-brand">教师管理后台</span>
        <ul class="navbar-nav">
          <li><span style="color: var(--charcoal-600)">教师</span></li>
          <li><a @click="logout">退出</a></li>
        </ul>
      </div>
    </nav>

    <div class="teacher-layout">
      <aside class="teacher-sidebar">
        <div class="menu-section">
          <div class="menu-title">概览</div>
          <a :class="['menu-item', { active: currentSection === 'overview' }]" @click="currentSection = 'overview'">数据概览</a>
        </div>
        <div class="menu-section">
          <div class="menu-title">学生管理</div>
          <a :class="['menu-item', { active: currentSection === 'student-register' }]" @click="currentSection = 'student-register'">学生注册</a>
          <a :class="['menu-item', { active: currentSection === 'student-manage' }]" @click="currentSection = 'student-manage'">学生管理</a>
        </div>
        <div class="menu-section">
          <div class="menu-title">报名中心</div>
          <a :class="['menu-item', { active: currentSection === 'exam-manage' }]" @click="currentSection = 'exam-manage'">报名管理</a>
          <a :class="['menu-item', { active: currentSection === 'enrollment-process' }]" @click="currentSection = 'enrollment-process'">报名处理</a>
        </div>
      </aside>

      <main class="teacher-main">
        <div v-if="message" :class="['message', `message-${messageType}`]">
          {{ message }}
        </div>

        <!-- 数据概览 -->
        <div v-show="currentSection === 'overview'" class="content-section active">
          <h2 class="text-h1" style="margin-bottom: 2rem">数据概览</h2>
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-value">{{ stats.students }}</div>
              <div class="stat-label">注册学生</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">{{ stats.exams }}</div>
              <div class="stat-label">考试项目</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">{{ stats.enrollments }}</div>
              <div class="stat-label">报名人次</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">{{ stats.auths }}</div>
              <div class="stat-label">权限分配</div>
            </div>
          </div>
        </div>

        <!-- 学生注册 -->
        <div v-show="currentSection === 'student-register'" class="content-section active">
          <h2 class="text-h1" style="margin-bottom: 2rem">学生注册</h2>
          <div class="card-luxury" style="max-width: 600px">
            <form @submit.prevent="registerStudent">
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem">
                <div class="form-group">
                  <label class="form-label">准考证号 *</label>
                  <input v-model="registerForm.exam_id" type="text" class="form-input" required />
                </div>
                <div class="form-group">
                  <label class="form-label">姓名 *</label>
                  <input v-model="registerForm.name" type="text" class="form-input" required />
                </div>
                <div class="form-group">
                  <label class="form-label">年龄</label>
                  <input v-model="registerForm.age" type="number" class="form-input" />
                </div>
                <div class="form-group">
                  <label class="form-label">年级</label>
                  <input v-model="registerForm.grade" type="text" class="form-input" placeholder="如：高三" />
                </div>
                <div class="form-group">
                  <label class="form-label">班级</label>
                  <input v-model="registerForm.class" type="text" class="form-input" placeholder="如：1班" />
                </div>
                <div class="form-group">
                  <label class="form-label">初始密码 *</label>
                  <input v-model="registerForm.password" type="password" class="form-input" required />
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">一寸照片</label>
                <input ref="photoInput" type="file" class="form-input" accept="image/*" />
              </div>
              <button type="submit" class="btn-magnetic btn-brass" style="width: 100%">注册学生</button>
            </form>
          </div>
        </div>

        <!-- 学生管理 -->
        <div v-show="currentSection === 'student-manage'" class="content-section active">
          <h2 class="text-h1" style="margin-bottom: 2rem">学生管理</h2>
          <div style="margin-bottom: 1rem">
            <input v-model="searchQuery" type="text" class="form-input" placeholder="搜索学生姓名或准考证号..." style="max-width: 300px" @input="searchStudents" />
          </div>
          <div class="card-luxury" style="padding: 0; overflow: hidden">
            <table class="table-luxury">
              <thead>
                <tr>
                  <th>照片</th>
                  <th>准考证号</th>
                  <th>姓名</th>
                  <th>年级</th>
                  <th>班级</th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="student in students" :key="student.student_id">
                  <td>
                    <img v-if="student.photo" :src="student.photo" class="avatar" />
                    <span v-else>-</span>
                  </td>
                  <td>{{ student.exam_id }}</td>
                  <td>{{ student.name }}</td>
                  <td>{{ student.grade || '-' }}</td>
                  <td>{{ student.class || '-' }}</td>
                  <td>
                    <button class="btn-magnetic" style="padding: 0.5rem 1rem; font-size: 0.875rem" @click="openAuthModal(student)">分配权限</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 报名管理 -->
        <div v-show="currentSection === 'exam-manage'" class="content-section active">
          <h2 class="text-h1" style="margin-bottom: 2rem">报名管理</h2>
          <div class="card-luxury" style="max-width: 700px; margin-bottom: 2rem">
            <h3 class="text-h2" style="margin-bottom: 1.5rem">创建考试</h3>
            <form @submit.prevent="createExam">
              <div class="form-group">
                <label class="form-label">考试名称 *</label>
                <input v-model="examForm.exam_name" type="text" class="form-input" required />
              </div>
              <div class="form-group">
                <label class="form-label">考试简介</label>
                <textarea v-model="examForm.exam_description" class="form-input" rows="3" placeholder="请输入考试简介..."></textarea>
              </div>
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem">
                <div class="form-group">
                  <label class="form-label">考试日期</label>
                  <input v-model="examForm.exam_date" type="date" class="form-input" />
                </div>
                <div class="form-group">
                  <label class="form-label">报名截止时间 *</label>
                  <input v-model="examForm.deadline" type="datetime-local" class="form-input" required />
                </div>
              </div>
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem">
                <div class="form-group">
                  <label class="form-label">考试地点</label>
                  <input v-model="examForm.exam_location" type="text" class="form-input" placeholder="如：第一教学楼" />
                </div>
                <div class="form-group">
                  <label class="form-label">发布教师</label>
                  <input v-model="examForm.published_by" type="text" class="form-input" placeholder="默认为当前教师" />
                </div>
              </div>
              <button type="submit" class="btn-magnetic btn-brass" style="width: 100%">创建考试</button>
            </form>
          </div>
          <div class="card-luxury" style="padding: 0; overflow: hidden">
            <table class="table-luxury">
              <thead>
                <tr>
                  <th>考试名称</th>
                  <th>发布教师</th>
                  <th>考试日期</th>
                  <th>截止时间</th>
                  <th>授权人数</th>
                  <th>报名人数</th>
                  <th>状态</th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="exam in exams" :key="exam.exam_id">
                  <td>{{ exam.exam_name }}</td>
                  <td>{{ exam.published_by || '-' }}</td>
                  <td>{{ exam.exam_date || '-' }}</td>
                  <td :style="{ color: exam.is_expired ? 'var(--error-red)' : '' }">{{ formatDeadline(exam.deadline) }}</td>
                  <td>{{ exam.auth_count }}</td>
                  <td>{{ exam.enroll_count }}</td>
                  <td>
                    <span :class="['exam-card-status', exam.is_expired ? 'status-expired' : 'status-pending']">
                      {{ exam.is_expired ? '已截止' : '进行中' }}
                    </span>
                  </td>
                  <td>
                    <button class="btn-magnetic btn-danger" style="padding: 0.5rem 1rem; font-size: 0.875rem" @click="deleteExam(exam.exam_id)">删除</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 报名处理 -->
        <div v-show="currentSection === 'enrollment-process'" class="content-section active">
          <h2 class="text-h1" style="margin-bottom: 2rem">报名处理</h2>
          <div style="display: flex; gap: 1rem; margin-bottom: 1rem; align-items: center;">
            <select v-model="selectedExamFilter" class="form-input" style="max-width: 250px" @change="loadEnrollments">
              <option value="">全部考试</option>
              <option v-for="exam in examOptions" :key="exam.exam_id" :value="exam.exam_id">
                {{ exam.exam_name }}
              </option>
            </select>
            <button class="btn-magnetic btn-brass" @click="printSelected">打印选中准考证</button>
          </div>
          <div class="card-luxury" style="padding: 0; overflow: hidden">
            <table class="table-luxury">
              <thead>
                <tr>
                  <th><input type="checkbox" v-model="selectAll" @change="toggleSelectAll" /></th>
                  <th>照片</th>
                  <th>准考证号</th>
                  <th>姓名</th>
                  <th>考试名称</th>
                  <th>报名时间</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="enrollment in enrollments" :key="enrollment.enrollment_id">
                  <td><input type="checkbox" v-model="selectedEnrollments" :value="enrollment.enrollment_id" /></td>
                  <td>
                    <img v-if="enrollment.photo" :src="enrollment.photo" class="avatar" />
                    <span v-else>-</span>
                  </td>
                  <td>{{ enrollment.student_exam_id }}</td>
                  <td>{{ enrollment.name }}</td>
                  <td>{{ enrollment.exam_name }}</td>
                  <td>{{ enrollment.enroll_time }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>

    <!-- 权限分配模态框 -->
    <div :class="['modal-overlay', { active: showAuthModal }]">
      <div class="modal-content">
        <h3 class="text-h2" style="margin-bottom: 1.5rem">分配考试权限</h3>
        <div style="max-height: 300px; overflow-y: auto">
          <label v-for="exam in authExams" :key="exam.exam_id" class="checkbox-wrapper" style="padding: 0.75rem; display: block">
            <input type="checkbox" v-model="selectedExams" :value="exam.exam_id" />
            <span>{{ exam.exam_name }}</span>
          </label>
        </div>
        <div style="display: flex; gap: 1rem; margin-top: 1.5rem">
          <button class="btn-magnetic" style="flex: 1" @click="saveAuth">保存</button>
          <button class="btn-magnetic btn-secondary" @click="showAuthModal = false">取消</button>
        </div>
      </div>
    </div>

    <!-- 打印预览模态框 -->
    <div :class="['modal-overlay', { active: showPrintModal }]">
      <div class="modal-content ticket-a4">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
          <h3 class="text-h2">准考证预览</h3>
          <button class="btn-magnetic btn-secondary" @click="showPrintModal = false">关闭</button>
        </div>
        <div id="printPreview">
          <div v-for="ticket in printTickets" :key="ticket.enrollment_id" class="ticket-a4-page">
            <div class="ticket-header-a4">
              <h1 class="ticket-title">{{ ticket.exam_name }}</h1>
              <p class="ticket-subtitle">准考证</p>
              <div class="ticket-divider"></div>
            </div>
            <div class="ticket-body-a4">
              <div class="ticket-photo-section">
                <img v-if="ticket.photo" :src="ticket.photo" class="ticket-photo-large" />
                <div v-else class="ticket-photo-placeholder">无照片</div>
              </div>
              <div class="ticket-info-section">
                <div class="ticket-info-row">
                  <div class="ticket-info-block">
                    <span class="ticket-label-a4">准考证号</span>
                    <span class="ticket-value-a4">{{ ticket.student_exam_id }}</span>
                  </div>
                  <div class="ticket-info-block">
                    <span class="ticket-label-a4">姓名</span>
                    <span class="ticket-value-a4">{{ ticket.name }}</span>
                  </div>
                </div>
                <div class="ticket-info-row">
                  <div class="ticket-info-block">
                    <span class="ticket-label-a4">年级</span>
                    <span class="ticket-value-a4">{{ ticket.grade || '-' }}</span>
                  </div>
                  <div class="ticket-info-block">
                    <span class="ticket-label-a4">班级</span>
                    <span class="ticket-value-a4">{{ ticket.class || '-' }}</span>
                  </div>
                </div>
                <div class="ticket-info-row">
                  <div class="ticket-info-block">
                    <span class="ticket-label-a4">考试日期</span>
                    <span class="ticket-value-a4">{{ ticket.exam_date || '-' }}</span>
                  </div>
                  <div class="ticket-info-block">
                    <span class="ticket-label-a4">考试地点</span>
                    <span class="ticket-value-a4">{{ ticket.exam_location || '-' }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div style="margin-top: 1.5rem; text-align: center;">
          <button class="btn-magnetic btn-brass" style="padding: 1rem 3rem;" @click="doPrint">打印准考证</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { request, submitForm } from '../utils/request.js'
import { API_BASE_URL } from '../config.js'

const router = useRouter()
const currentSection = ref('overview')
const message = ref('')
const messageType = ref('')

// 统计数据
const stats = ref({ students: 0, exams: 0, enrollments: 0, auths: 0 })

// 学生注册表单
const registerForm = ref({ exam_id: '', name: '', age: '', grade: '', class: '', password: '' })
const photoInput = ref(null)

// 考试表单
const examForm = ref({ exam_name: '', exam_description: '', exam_date: '', deadline: '', exam_location: '', published_by: '' })

// 数据列表
const students = ref([])
const exams = ref([])
const enrollments = ref([])
const searchQuery = ref('')
const selectedExamFilter = ref('')
const examOptions = ref([])

// 权限分配
const showAuthModal = ref(false)
const currentStudent = ref(null)
const authExams = ref([])
const selectedExams = ref([])

// 报名处理
const selectAll = ref(false)
const selectedEnrollments = ref([])

// 打印
const showPrintModal = ref(false)
const printTickets = ref([])

const showMessage = (text, type) => {
  message.value = text
  messageType.value = type
  setTimeout(() => {
    message.value = ''
  }, 3000)
}

const formatDeadline = (deadline) => {
  if (!deadline) return '无限制'
  const date = new Date(deadline)
  return date.toLocaleString('zh-CN', { 
    month: '2-digit', 
    day: '2-digit', 
    hour: '2-digit', 
    minute: '2-digit' 
  })
}

const loadStats = async () => {
  try {
    const [studentsData, examsData, enrollmentsData] = await Promise.all([
      request('/api/teacher_student.php'),
      request('/api/teacher_exam.php'),
      request('/api/teacher_enrollment.php')
    ])
    stats.value.students = studentsData.success ? studentsData.data.length : 0
    stats.value.exams = examsData.success ? examsData.data.length : 0
    stats.value.enrollments = enrollmentsData.success ? enrollmentsData.data.length : 0
  } catch (err) {}
}

const registerStudent = async () => {
  const formData = new FormData()
  Object.keys(registerForm.value).forEach(key => {
    if (registerForm.value[key]) {
      formData.append(key, registerForm.value[key])
    }
  })
  if (photoInput.value?.files[0]) {
    formData.append('photo', photoInput.value.files[0])
  }

  try {
    const result = await submitForm('/api/teacher_student.php', formData)

    if (result.success) {
      showMessage('学生注册成功', 'success')
      registerForm.value = { exam_id: '', name: '', age: '', grade: '', class: '', password: '' }
      if (photoInput.value) photoInput.value.value = ''
    } else {
      showMessage(result.message, 'error')
    }
  } catch (err) {
    showMessage('注册失败，请检查网络连接', 'error')
    console.error('Register error:', err)
  }
}

const loadStudents = async () => {
  try {
    const result = await request('/api/teacher_student.php')

    if (!result.success) {
      if (result.message === '请先登录') router.push('/teacher-login')
      return
    }
    students.value = result.data
  } catch (err) {}
}

const searchStudents = async () => {
  try {
    const url = searchQuery.value ? `/api/teacher_student.php?search=${encodeURIComponent(searchQuery.value)}` : '/api/teacher_student.php'
    const result = await request(url)
    if (result.success) students.value = result.data
  } catch (err) {}
}

const openAuthModal = async (student) => {
  currentStudent.value = student
  try {
    const result = await request(`/api/teacher_student_auth.php?student_id=${student.student_id}`)

    if (result.success) {
      authExams.value = result.data
      selectedExams.value = result.data.filter(e => e.has_auth).map(e => e.exam_id)
      showAuthModal.value = true
    }
  } catch (err) {}
}

const saveAuth = async () => {
  try {
    const result = await request('/api/teacher_student_auth.php', {
      method: 'POST',
      body: JSON.stringify({
        student_id: currentStudent.value.student_id,
        exam_ids: selectedExams.value
      })
    })

    if (result.success) {
      showMessage('权限分配成功', 'success')
      showAuthModal.value = false
    } else {
      showMessage(result.message, 'error')
    }
  } catch (err) {
    showMessage('保存失败，请检查网络连接', 'error')
    console.error('Save auth error:', err)
  }
}

const createExam = async () => {
  try {
    const result = await request('/api/teacher_exam.php', {
      method: 'POST',
      body: JSON.stringify(examForm.value)
    })

    if (result.success) {
      showMessage('考试创建成功', 'success')
      examForm.value = { exam_name: '', exam_description: '', exam_date: '', deadline: '', exam_location: '', published_by: '' }
      loadExams()
    } else {
      showMessage(result.message, 'error')
    }
  } catch (err) {
    showMessage('创建失败，请检查网络连接', 'error')
    console.error('Create exam error:', err)
  }
}

const loadExams = async () => {
  try {
    const result = await request('/api/teacher_exam.php')
    if (result.success) {
      exams.value = result.data
      // 更新考试选项（用于筛选）
      examOptions.value = result.data.map(e => ({ exam_id: e.exam_id, exam_name: e.exam_name }))
    }
  } catch (err) {}
}

const deleteExam = async (examId) => {
  if (!confirm('确定要删除该考试吗？')) return

  try {
    const result = await request('/api/teacher_exam.php', {
      method: 'DELETE',
      body: JSON.stringify({ exam_id: examId })
    })

    if (result.success) {
      showMessage('删除成功', 'success')
      loadExams()
    } else {
      showMessage(result.message, 'error')
    }
  } catch (err) {
    showMessage('删除失败，请检查网络连接', 'error')
    console.error('Delete exam error:', err)
  }
}

const loadEnrollments = async () => {
  try {
    const url = selectedExamFilter.value 
      ? `/api/teacher_enrollment.php?exam_id=${selectedExamFilter.value}`
      : '/api/teacher_enrollment.php'
    const result = await request(url)
    if (result.success) enrollments.value = result.data
  } catch (err) {}
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedEnrollments.value = enrollments.value.map(e => e.enrollment_id)
  } else {
    selectedEnrollments.value = []
  }
}

const printSelected = async () => {
  if (selectedEnrollments.value.length === 0) {
    showMessage('请先选择要打印的报名记录', 'error')
    return
  }

  try {
    const result = await request('/api/teacher_enrollment.php', {
      method: 'POST',
      body: JSON.stringify({ enrollment_ids: selectedEnrollments.value })
    })

    if (result.success) {
      printTickets.value = result.data
      showPrintModal.value = true
    }
  } catch (err) {
    showMessage('获取准考证信息失败，请检查网络连接', 'error')
    console.error('Print error:', err)
  }
}

const doPrint = () => {
  const printContent = document.getElementById('printPreview').innerHTML
  const win = window.open('', '_blank')
  win.document.write(`
    <html>
    <head>
      <title>打印准考证</title>
      <style>
        @page { size: A4; margin: 0; }
        body { margin: 0; padding: 20mm; font-family: "Noto Sans SC", sans-serif; }
        .ticket-a4-page { 
          width: 210mm; 
          min-height: 297mm; 
          padding: 20mm; 
          box-sizing: border-box;
          page-break-after: always;
          background: #FDF8F5;
        }
        .ticket-a4-page:last-child { page-break-after: auto; }
        .ticket-header-a4 { text-align: center; margin-bottom: 30px; }
        .ticket-title { font-size: 32px; font-weight: bold; color: #1A1A1A; margin-bottom: 10px; }
        .ticket-subtitle { font-size: 20px; color: #4A4A4A; margin-bottom: 20px; }
        .ticket-divider { height: 2px; background: #C9A961; margin: 0 auto; width: 80%; }
        .ticket-body-a4 { display: flex; gap: 40px; margin-top: 40px; }
        .ticket-photo-section { flex-shrink: 0; }
        .ticket-photo-large { width: 120px; height: 160px; object-fit: cover; border-radius: 8px; border: 2px solid #E8D5C4; }
        .ticket-photo-placeholder { width: 120px; height: 160px; background: #F5EBE4; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9A9A9A; }
        .ticket-info-section { flex: 1; }
        .ticket-info-row { display: flex; gap: 40px; margin-bottom: 30px; }
        .ticket-info-block { flex: 1; display: flex; flex-direction: column; gap: 8px; padding: 15px; background: #F5EBE4; border-radius: 8px; }
        .ticket-label-a4 { font-size: 14px; color: #9A9A9A; text-transform: uppercase; letter-spacing: 1px; }
        .ticket-value-a4 { font-size: 18px; color: #1A1A1A; font-weight: 500; }
      </style>
    </head>
    <body>${printContent}</body>
    </html>
  `)
  win.document.close()
  win.print()
}

const logout = async () => {
  try {
    await request('/api/logout.php')
  } catch (err) {}
  router.push('/teacher-login')
}

// 监听分区变化
watch(currentSection, (newSection) => {
  if (newSection === 'student-manage') loadStudents()
  if (newSection === 'exam-manage') loadExams()
  if (newSection === 'enrollment-process') loadEnrollments()
})

onMounted(() => {
  loadStats()
})
</script>

<style scoped>
.status-expired {
  background: rgba(199, 91, 91, 0.15);
  color: var(--error-red);
}

.ticket-a4 {
  max-width: 900px;
  width: 95%;
}

.ticket-a4-page {
  background: var(--sand-pink-50);
  padding: 40px;
  margin-bottom: 30px;
  border-radius: 16px;
}

.ticket-header-a4 {
  text-align: center;
  margin-bottom: 30px;
}

.ticket-title {
  font-family: var(--font-display);
  font-size: 32px;
  color: var(--charcoal-900);
  margin-bottom: 8px;
}

.ticket-subtitle {
  font-size: 18px;
  color: var(--charcoal-600);
  margin-bottom: 20px;
}

.ticket-divider {
  height: 2px;
  background: var(--brass-500);
  width: 60%;
  margin: 0 auto;
}

.ticket-body-a4 {
  display: flex;
  gap: 40px;
  margin-top: 40px;
}

.ticket-photo-section {
  flex-shrink: 0;
}

.ticket-photo-large {
  width: 120px;
  height: 160px;
  object-fit: cover;
  border-radius: 12px;
  border: 3px solid var(--sand-pink-200);
}

.ticket-photo-placeholder {
  width: 120px;
  height: 160px;
  background: var(--sand-pink-100);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--stone-400);
  font-size: 14px;
}

.ticket-info-section {
  flex: 1;
}

.ticket-info-row {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}

.ticket-info-block {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 16px;
  background: var(--sand-pink-100);
  border-radius: 12px;
}

.ticket-label-a4 {
  font-size: 12px;
  color: var(--charcoal-400);
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.ticket-value-a4 {
  font-size: 18px;
  color: var(--charcoal-900);
  font-weight: 500;
}

textarea.form-input {
  resize: vertical;
  min-height: 80px;
}
</style>
