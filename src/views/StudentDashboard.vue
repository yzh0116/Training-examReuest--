<template>
  <div>
    <nav class="navbar">
      <div class="navbar-content">
        <router-link to="/student-dashboard" class="navbar-brand">考试管理系统</router-link>
        <ul class="navbar-nav">
          <li><router-link to="/student-dashboard" class="active">考试列表</router-link></li>
          <li><router-link to="/student-profile">个人中心</router-link></li>
          <li><a @click="logout">退出</a></li>
        </ul>
      </div>
    </nav>

    <div class="container" style="padding: 3rem 2rem">
      <div style="margin-bottom: 2rem">
        <span class="text-label">AVAILABLE EXAMS</span>
        <h1 class="text-hero" style="margin-top: 0.5rem">我的考试</h1>
        <p class="text-body">点击考试卡片查看详情或报名</p>
      </div>

      <div v-if="message" :class="['message', `message-${messageType}`]">
        {{ message }}
      </div>

      <div class="card-grid">
        <div v-if="loading" style="text-align: center; padding: 3rem; color: var(--stone-400); grid-column: 1 / -1">
          <div class="loading"></div>
          <p style="margin-top: 1rem">加载中...</p>
        </div>

        <div v-else-if="exams.length === 0" style="text-align: center; padding: 3rem; color: var(--stone-400); grid-column: 1 / -1">
          暂无可用考试
        </div>

        <div
          v-for="exam in exams"
          :key="exam.exam_id"
          class="exam-card"
          @click="showExamDetail(exam)"
        >
          <div style="display: flex; justify-content: space-between; align-items: flex-start">
            <div>
              <h3 class="exam-card-title">{{ exam.exam_name }}</h3>
              <p class="exam-card-meta" v-if="exam.published_by">发布教师：{{ exam.published_by }}</p>
              <p class="exam-card-meta">
                {{ exam.exam_date ? '考试日期：' + exam.exam_date : '日期待定' }}
              </p>
              <p v-if="exam.deadline" class="exam-card-meta" :style="{ color: exam.is_expired ? 'var(--error-red)' : '' }">
                报名截止：{{ formatDeadline(exam.deadline) }}
              </p>
            </div>
            <span :class="['exam-card-status', getStatusClass(exam)]">
              {{ getStatusText(exam) }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- 考试详情弹窗 -->
    <div :class="['modal-overlay', { active: showModal }]" @click.self="closeModal">
      <div class="modal-content" style="max-width: 600px; max-height: 80vh; overflow-y: auto;">
        <div v-if="selectedExam">
          <h2 class="text-h1" style="margin-bottom: 1.5rem">{{ selectedExam.exam_name }}</h2>
          
          <div class="detail-section">
            <h4 class="detail-label">考试简介</h4>
            <p class="detail-value">{{ selectedExam.exam_description || '暂无简介' }}</p>
          </div>
          
          <div class="detail-grid">
            <div class="detail-item">
              <span class="detail-label">发布教师</span>
              <span class="detail-value">{{ selectedExam.published_by || '-' }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">考试日期</span>
              <span class="detail-value">{{ selectedExam.exam_date || '待定' }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">考试地点</span>
              <span class="detail-value">{{ selectedExam.exam_location || '待定' }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">报名截止</span>
              <span class="detail-value" :style="{ color: selectedExam.is_expired ? 'var(--error-red)' : '' }">
                {{ selectedExam.deadline ? formatDeadline(selectedExam.deadline) : '无限制' }}
              </span>
            </div>
          </div>

          <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button 
              v-if="!selectedExam.is_enrolled && !selectedExam.is_expired" 
              class="btn-magnetic btn-brass" 
              style="flex: 1"
              @click="enrollExam(selectedExam)"
            >
              立即报名
            </button>
            <button 
              v-else-if="selectedExam.is_enrolled" 
              class="btn-magnetic" 
              style="flex: 1; background: var(--success-green);"
              disabled
            >
              已报名
            </button>
            <button 
              v-else-if="selectedExam.is_expired" 
              class="btn-magnetic btn-secondary" 
              style="flex: 1"
              disabled
            >
              报名已截止
            </button>
            <button class="btn-magnetic btn-secondary" @click="closeModal">关闭</button>
          </div>
        </div>
      </div>
    </div>

    <!-- 报名确认弹窗 -->
    <div :class="['modal-overlay', { active: showConfirmModal }]" @click.self="closeConfirmModal">
      <div class="modal-content" style="max-width: 400px;">
        <h3 class="text-h2" style="margin-bottom: 1rem">确认报名</h3>
        <p class="text-body" style="margin-bottom: 1.5rem">
          确定要报名「{{ confirmExam?.exam_name }}」吗？
        </p>
        <div style="display: flex; gap: 1rem;">
          <button class="btn-magnetic btn-brass" style="flex: 1" @click="confirmEnroll">确认</button>
          <button class="btn-magnetic btn-secondary" style="flex: 1" @click="closeConfirmModal">取消</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../utils/request.js'

const router = useRouter()
const exams = ref([])
const loading = ref(true)
const message = ref('')
const messageType = ref('')
const showModal = ref(false)
const showConfirmModal = ref(false)
const selectedExam = ref(null)
const confirmExam = ref(null)

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

const getStatusClass = (exam) => {
  if (exam.is_enrolled) return 'status-enrolled'
  if (exam.is_expired) return 'status-expired'
  return 'status-pending'
}

const getStatusText = (exam) => {
  if (exam.is_enrolled) return '已报名'
  if (exam.is_expired) return '已截止'
  return '未报名'
}

const loadExams = async () => {
  try {
    const result = await request('/api/student_exams.php')

    if (!result.success) {
      if (result.message === '请先登录') {
        router.push('/')
        return
      }
      showMessage(result.message, 'error')
      return
    }

    exams.value = result.data
  } catch (err) {
    showMessage('加载失败，请检查网络连接或联系管理员', 'error')
    console.error('Load exams error:', err)
  } finally {
    loading.value = false
  }
}

const showExamDetail = (exam) => {
  selectedExam.value = exam
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedExam.value = null
}

const enrollExam = (exam) => {
  confirmExam.value = exam
  showConfirmModal.value = true
}

const closeConfirmModal = () => {
  showConfirmModal.value = false
  confirmExam.value = null
}

const confirmEnroll = async () => {
  if (!confirmExam.value) return
  
  try {
    const result = await request('/api/student_enroll.php', {
      method: 'POST',
      body: JSON.stringify({ exam_id: confirmExam.value.exam_id })
    })

    if (result.success) {
      showMessage('报名成功', 'success')
      closeConfirmModal()
      closeModal()
      loadExams()
    } else {
      showMessage(result.message || '报名失败', 'error')
    }
  } catch (err) {
    showMessage('网络错误，请检查网络连接或联系管理员', 'error')
    console.error('Enroll error:', err)
  }
}

const logout = async () => {
  try {
    await request('/api/logout.php')
  } catch (err) {}
  router.push('/')
}

onMounted(loadExams)
</script>

<style scoped>
.detail-section {
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: var(--sand-pink-100);
  border-radius: 12px;
}

.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.detail-label {
  font-size: 0.75rem;
  color: var(--charcoal-400);
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.detail-value {
  font-size: 0.9375rem;
  color: var(--charcoal-900);
  font-weight: 500;
}

.status-expired {
  background: rgba(199, 91, 91, 0.15);
  color: var(--error-red);
}
</style>
