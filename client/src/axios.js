import axios from 'axios'

// Create axios instance with base configuration
const axiosInstance = axios.create({
  baseURL: 'http://localhost:8000',
  timeout: 10000,
  withCredentials: true, // Enable cookies/sessions
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Request interceptor
axiosInstance.interceptors.request.use(
  (config) => {
    // Add any request modifications here
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor
axiosInstance.interceptors.response.use(
  (response) => {
    // Add any response processing here
    return response
  },
  (error) => {
    // Handle errors globally if needed
    return Promise.reject(error)
  }
)

export default axiosInstance
