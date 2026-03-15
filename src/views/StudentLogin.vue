<template>
  <div class="login-page">
    <div class="card-luxury login-card">
      <div class="login-header">
        <div class="login-logo">考</div>
        <h1 class="text-h1" style="margin-bottom: 0.5rem;">学生登录</h1>
        <p class="text-body">欢迎回到考试管理系统</p>
      </div>

      <div v-if="message" :class="['message', `message-${messageType}`]">
        {{ message }}
      </div>

      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label class="form-label">准考证号</label>
          <input
            v-model="form.exam_id"
            type="text"
            class="form-input"
            placeholder="请输入准考证号"
            required
          />
        </div>

        <div class="form-group">
          <label class="form-label">密码</label>
          <input
            v-model="form.password"
            type="password"
            class="form-input"
            placeholder="请输入密码"
            required
          />
        </div>

        <div class="form-group">
          <label class="form-label">验证码</label>
          <div class="captcha-wrapper">
            <input
              v-model="form.captcha"
              type="text"
              class="form-input"
              placeholder="请输入验证码"
              style="flex: 1"
              required
            />
            <div style="position: relative;">
              <img
                v-if="!captchaError"
                :src="captchaUrl"
                alt="验证码"
                class="captcha-image"
                @click="refreshCaptcha"
                @error="handleCaptchaError"
              />
              <div
                v-else
                class="captcha-image"
                style="display: flex; align-items: center; justify-content: center; background: var(--sand-pink-100); font-size: 0.75rem; color: var(--charcoal-600); cursor: pointer;"
                @click="refreshCaptcha"
              >
                点击刷新
              </div>
            </div>
          </div>
          <p v-if="captchaError" class="text-small" style="color: var(--error-red); margin-top: 0.5rem;">
            验证码加载失败，请检查网络或联系管理员
          </p>
        </div>

        <button type="submit" class="btn-magnetic" style="width: 100%; margin-top: 1rem">
          登录
        </button>
      </form>

      <div style="text-align: center; margin-top: 1.5rem">
        <router-link to="/teacher-login" style="color: var(--brass-500); text-decoration: none; font-size: 0.875rem">
          教师入口 →
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../utils/request.js'
import { API_BASE_URL } from '../config.js'

const router = useRouter()
const form = ref({
  exam_id: '',
  password: '',
  captcha: ''
})
const message = ref('')
const messageType = ref('')
const captchaTimestamp = ref(Date.now())
const captchaError = ref(false)
const useTextCaptcha = ref(false)

const captchaUrl = computed(() => {
  const baseUrl = useTextCaptcha.value ? '/api/captcha_text.php' : '/api/captcha.php'
  return `${API_BASE_URL}${baseUrl}?t=${captchaTimestamp.value}`
})

const refreshCaptcha = () => {
  captchaTimestamp.value = Date.now()
  captchaError.value = false
}

const handleCaptchaError = () => {
  if (!useTextCaptcha.value) {
    // 第一次失败，尝试使用 SVG 验证码
    useTextCaptcha.value = true
    refreshCaptcha()
  } else {
    // SVG 也失败，显示错误状态
    captchaError.value = true
  }
}

const showMessage = (text, type) => {
  message.value = text
  messageType.value = type
  setTimeout(() => {
    message.value = ''
  }, 3000)
}

const handleLogin = async () => {
  try {
    const result = await request('/api/student_login.php', {
      method: 'POST',
      body: JSON.stringify(form.value)
    })

    if (result.success) {
      showMessage('登录成功', 'success')
      if (result.data.is_first_login == 1) {
        setTimeout(() => router.push('/change-password'), 500)
      } else {
        setTimeout(() => router.push('/student-dashboard'), 500)
      }
    } else {
      showMessage(result.message || '登录失败', 'error')
      refreshCaptcha()
    }
  } catch (err) {
    showMessage('网络错误，请检查网络连接或联系管理员', 'error')
    console.error('Login error:', err)
  }
}
</script>
