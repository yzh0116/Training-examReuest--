<template>
  <div class="login-page">
    <div class="card-luxury login-card">
      <div class="login-header">
        <div class="login-logo" style="background: var(--copper-400)">密</div>
        <h1 class="text-h1" style="margin-bottom: 0.5rem">修改密码</h1>
        <p class="text-body">首次登录需要修改密码</p>
      </div>

      <div v-if="message" :class="['message', `message-${messageType}`]">
        {{ message }}
      </div>

      <form @submit.prevent="handleSubmit">
        <div class="form-group">
          <label class="form-label">原密码</label>
          <input
            v-model="form.old_password"
            type="password"
            class="form-input"
            placeholder="请输入原密码"
          />
          <p class="text-small" style="margin-top: 0.25rem">首次登录可任意填写</p>
        </div>

        <div class="form-group">
          <label class="form-label">新密码</label>
          <input
            v-model="form.new_password"
            type="password"
            class="form-input"
            placeholder="请输入新密码（至少6位）"
            required
          />
        </div>

        <div class="form-group">
          <label class="form-label">确认新密码</label>
          <input
            v-model="form.confirm_password"
            type="password"
            class="form-input"
            placeholder="请再次输入新密码"
            required
          />
        </div>

        <button type="submit" class="btn-magnetic" style="width: 100%; margin-top: 1rem">
          确认修改
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../utils/request.js'

const router = useRouter()
const form = ref({
  old_password: '',
  new_password: '',
  confirm_password: ''
})
const message = ref('')
const messageType = ref('')

const showMessage = (text, type) => {
  message.value = text
  messageType.value = type
  setTimeout(() => {
    message.value = ''
  }, 3000)
}

const handleSubmit = async () => {
  if (form.value.new_password !== form.value.confirm_password) {
    showMessage('两次输入的密码不一致', 'error')
    return
  }

  if (form.value.new_password.length < 6) {
    showMessage('密码长度至少6位', 'error')
    return
  }

  try {
    const result = await request('/api/student_change_password.php', {
      method: 'POST',
      body: JSON.stringify({
        old_password: form.value.old_password,
        new_password: form.value.new_password
      })
    })

    if (result.success) {
      showMessage('密码修改成功，即将跳转', 'success')
      setTimeout(() => router.push('/student-dashboard'), 1000)
    } else {
      showMessage(result.message || '修改失败', 'error')
    }
  } catch (err) {
    showMessage('网络错误，请检查网络连接或联系管理员', 'error')
    console.error('Change password error:', err)
  }
}
</script>
