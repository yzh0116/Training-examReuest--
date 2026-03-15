<template>
  <div>
    <nav class="navbar">
      <div class="navbar-content">
        <router-link to="/student-dashboard" class="navbar-brand">考试管理系统</router-link>
        <ul class="navbar-nav">
          <li><router-link to="/student-dashboard">考试列表</router-link></li>
          <li><router-link to="/student-profile" class="active">个人中心</router-link></li>
          <li><a @click="logout">退出</a></li>
        </ul>
      </div>
    </nav>

    <div class="container" style="padding: 3rem 2rem">
      <div style="margin-bottom: 2rem">
        <span class="text-label">PROFILE</span>
        <h1 class="text-hero" style="margin-top: 0.5rem">个人中心</h1>
      </div>

      <div v-if="message" :class="['message', `message-${messageType}`]">
        {{ message }}
      </div>

      <div class="card-luxury" style="max-width: 600px">
        <div style="display: flex; gap: 2rem; margin-bottom: 2rem">
          <img
            v-if="profile.photo"
            :src="profile.photo"
            alt="头像"
            class="avatar avatar-large"
          />
          <div
            v-else
            style="width: 120px; height: 120px; background: var(--sand-pink-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--charcoal-600)"
          >
            无照片
          </div>
          <div>
            <h2 class="text-h2">{{ profile.name || '加载中...' }}</h2>
            <p class="text-body">准考证号：{{ profile.exam_id || '-' }}</p>
          </div>
        </div>

        <form @submit.prevent="saveProfile">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem">
            <div class="form-group">
              <label class="form-label">姓名</label>
              <input v-model="form.name" type="text" class="form-input" required />
            </div>
            <div class="form-group">
              <label class="form-label">年龄</label>
              <input v-model="form.age" type="number" class="form-input" />
            </div>
            <div class="form-group">
              <label class="form-label">年级</label>
              <input v-model="form.grade" type="text" class="form-input" />
            </div>
            <div class="form-group">
              <label class="form-label">班级</label>
              <input v-model="form.class" type="text" class="form-input" />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">准考证号（不可修改）</label>
            <input
              v-model="profile.exam_id"
              type="text"
              class="form-input"
              disabled
              style="background: var(--sand-pink-100)"
            />
          </div>

          <div style="display: flex; gap: 1rem; margin-top: 1.5rem">
            <button type="submit" class="btn-magnetic" style="flex: 1">保存修改</button>
            <button type="button" class="btn-magnetic btn-secondary" @click="$router.push('/change-password')">
              修改密码
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '../utils/request.js'

const router = useRouter()
const profile = ref({})
const form = ref({
  name: '',
  age: '',
  grade: '',
  class: ''
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

const loadProfile = async () => {
  try {
    const result = await request('/api/student_profile.php')

    if (!result.success) {
      if (result.message === '请先登录') {
        router.push('/')
        return
      }
      showMessage(result.message, 'error')
      return
    }

    profile.value = result.data
    form.value = {
      name: result.data.name,
      age: result.data.age || '',
      grade: result.data.grade || '',
      class: result.data.class || ''
    }
  } catch (err) {
    showMessage('加载失败，请检查网络连接或联系管理员', 'error')
    console.error('Load profile error:', err)
  }
}

const saveProfile = async () => {
  try {
    const result = await request('/api/student_profile.php', {
      method: 'PUT',
      body: JSON.stringify({
        name: form.value.name,
        age: form.value.age || null,
        grade: form.value.grade,
        class: form.value.class
      })
    })

    if (result.success) {
      showMessage('保存成功', 'success')
      profile.value.name = form.value.name
    } else {
      showMessage(result.message || '保存失败', 'error')
    }
  } catch (err) {
    showMessage('网络错误，请检查网络连接或联系管理员', 'error')
    console.error('Save profile error:', err)
  }
}

const logout = async () => {
  try {
    await request('/api/logout.php')
  } catch (err) {}
  router.push('/')
}

onMounted(loadProfile)
</script>
