import axios from 'axios'

const axiosInstance = axios.create({
  baseURL: 'http://localhost:8000',
  timeout: 10000,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

axiosInstance.interceptors.request.use(
  (config) => config,
  (error) => Promise.reject(error)
)

axiosInstance.interceptors.response.use(
  (response) => response,
  (error) => Promise.reject(error)
)

export default axiosInstance
