// 封装请求工具
import { API_BASE_URL } from '../config.js'

export async function request(url, options = {}) {
  const fullUrl = `${API_BASE_URL}${url}`
  
  try {
    const response = await fetch(fullUrl, {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        ...options.headers
      }
    })
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    return await response.json()
  } catch (error) {
    console.error('Request failed:', error)
    throw error
  }
}

// 表单提交（用于文件上传）
export async function submitForm(url, formData) {
  const fullUrl = `${API_BASE_URL}${url}`
  
  try {
    const response = await fetch(fullUrl, {
      method: 'POST',
      body: formData
    })
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    return await response.json()
  } catch (error) {
    console.error('Request failed:', error)
    throw error
  }
}
