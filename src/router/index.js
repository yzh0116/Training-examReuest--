import { createRouter, createWebHistory } from 'vue-router'
import StudentLogin from '../views/StudentLogin.vue'
import TeacherLogin from '../views/TeacherLogin.vue'
import ChangePassword from '../views/ChangePassword.vue'
import StudentDashboard from '../views/StudentDashboard.vue'
import StudentProfile from '../views/StudentProfile.vue'
import TeacherDashboard from '../views/TeacherDashboard.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'StudentLogin',
      component: StudentLogin
    },
    {
      path: '/teacher-login',
      name: 'TeacherLogin',
      component: TeacherLogin
    },
    {
      path: '/change-password',
      name: 'ChangePassword',
      component: ChangePassword
    },
    {
      path: '/student-dashboard',
      name: 'StudentDashboard',
      component: StudentDashboard
    },
    {
      path: '/student-profile',
      name: 'StudentProfile',
      component: StudentProfile
    },
    {
      path: '/teacher-dashboard',
      name: 'TeacherDashboard',
      component: TeacherDashboard
    }
  ]
})

export default router
