// API 基础配置
// 开发环境使用相对路径，生产环境请修改为实际的后端地址

const isDev = import.meta.env.DEV

// 生产环境配置 - 修改为您的实际域名
export const API_BASE_URL = isDev ? '' : '网站地址'

// 如果前端和后端在同一域名下，保持为空字符串
// 如果后端在不同域名，例如：export const API_BASE_URL = '网站地址'
