<template>
  <div class="login-page">
    <!-- Header -->
    <header class="header">
      <div class="header-content">
        <h1 class="system-title">QMT Inventory and Sales</h1>
        <button class="login-btn-header">Login</button>
      </div>
    </header>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Back to Home Link -->
      <div class="back-link">
        <router-link to="/" class="back-to-home">
          <i class="fas fa-chevron-left"></i>
          Back to Home
        </router-link>
      </div>

      <!-- Login Section -->
      <div class="login-section">
        <!-- Form Container -->
        <div class="form-container">
          <h2 class="form-title">Sign In</h2>
          <p class="form-subtitle">Enter your email and password to sign in!</p>

          <form @submit.prevent="handleLogin" class="login-form">
            <!-- Email Field -->
            <div class="form-group">
              <label for="email" class="form-label">Email*</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="info@gmail.com"
                class="form-input"
                :class="{ 'error': errors.email }"
                required
              />
              <span v-if="errors.email" class="error-text">{{ errors.email[0] }}</span>
            </div>

            <!-- Password Field -->
            <div class="form-group">
              <label for="password" class="form-label">Password*</label>
              <div class="password-wrapper">
                <input
                  id="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Enter your password"
                  class="form-input password-input"
                  :class="{ 'error': errors.password }"
                  required
                />
                <button
                  type="button"
                  @click="togglePassword"
                  class="password-toggle"
                  tabindex="-1"
                >
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              <span v-if="errors.password" class="error-text">{{ errors.password[0] }}</span>
            </div>

            <!-- Error Message -->
            <div v-if="errorMessage" class="error-message">
              {{ errorMessage }}
            </div>

            <!-- Sign In Button -->
            <button
              type="submit"
              class="signin-button"
              :disabled="loading"
            >
              <span v-if="loading" class="loading-spinner"></span>
              <span v-else>Sign In</span>
            </button>

            <!-- Success Message -->
            <div v-if="successMessage" class="success-message">
              {{ successMessage }}
            </div>
          </form>
        </div>

        <!-- Logo Section -->
        <div class="logo-section">
          <div class="logo-container">
            <div class="logo-wrapper">
              <img src="/logo2.jpg" alt="Qelem Meda Technologies Logo" class="company-logo" />
            </div>
            <div class="company-info">
              <p class="amharic-text">ቀለም ሜዳ ቴክኖሎጂስ</p>
              <p class="english-text">Qelem Meda Technologies</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const showPassword = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const form = reactive({
  email: '',
  password: '',
  remember: false
})

const errors = ref({})

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const handleLogin = async () => {
  loading.value = true
  errorMessage.value = ''
  errors.value = {}

  try {
    await authStore.login(form)
    successMessage.value = 'Login successful! Redirecting...'
    setTimeout(() => router.push('/dashboard'), 500)
  } catch (error) {
    errors.value = error.response?.data?.errors || {}
    errorMessage.value = error.response?.data?.message || 'Login failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap');

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.login-page {
  min-height: 100vh;
  background-color: #ffffff;
  font-family: 'Outfit', sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-rendering: optimizeLegibility;
}

/* Header */
.header {
  background-color: #ffffff;
  border-bottom: 1px solid #e5e7eb;
  padding: 0;
}

.header-content {
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 20px;
}

.system-title {
  font-size: 24px;
  font-weight: 800;
  color: #1e3a8a;
  margin: 0;
  font-family: 'Outfit', sans-serif;
}

.login-btn-header {
  background-color: #1e3a8a;
  color: white;
  border: none;
  padding: 12px 32px;
  border-radius: 8px;
  font-weight: 500;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: 'Outfit', sans-serif;
}

.login-btn-header:hover {
  background-color: #f59e0b;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

/* Main Content */
.main-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px 40px;
}

.back-link {
  margin-bottom: 25px;
}

.back-to-home {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  color: #6b7280;
  text-decoration: none;
  font-size: 16px;
  font-weight: 400;
  transition: all 0.3s ease;
  font-family: 'Outfit', sans-serif;
}

.back-to-home:hover {
  color: #1e3a8a;
  transform: translateX(-4px);
}

.back-to-home i {
  font-size: 14px;
}

/* Login Section */
.login-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 120px;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0;
}

/* Form Container */
.form-container {
  max-width: 480px;
  width: 100%;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.form-title {
  font-size: 38px;
  font-weight: 700;
  color: #1e3a8a;
  margin-bottom: 10px;
  font-family: 'Outfit', sans-serif;
  line-height: 1.2;
}

.form-subtitle {
  color: #6b7280;
  margin-bottom: 28px;
  font-size: 14px;
  font-weight: 400;
  font-family: 'Outfit', sans-serif;
  line-height: 1.5;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.form-label {
  font-weight: 500;
  color: #374151;
  font-size: 15px;
  font-family: 'Outfit', sans-serif;
}

.form-input {
  width: 100%;
  padding: 14px 18px;
  border: 2px solid #d1d5db;
  border-radius: 10px;
  font-size: 15px;
  transition: all 0.3s ease;
  background-color: #ffffff;
  font-family: 'Outfit', sans-serif;
  color: #374151;
  height: 52px;
}

.form-input::placeholder {
  color: #9ca3af;
}

.form-input:focus {
  outline: none;
  border-color: #1e3a8a;
  box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1);
}

.form-input:hover {
  border-color: #f59e0b;
}

.form-input.error {
  border-color: #ef4444;
  background-color: #fef2f2;
}

.password-wrapper {
  position: relative;
  width: 100%;
}

.password-input {
  padding-right: 52px;
}

.password-toggle {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 8px;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  border-radius: 6px;
}

.password-toggle:hover {
  color: #f59e0b;
  background-color: rgba(245, 158, 11, 0.1);
}

.error-text {
  color: #ef4444;
  font-size: 14px;
  font-family: 'Outfit', sans-serif;
  margin-top: -4px;
}

.error-message {
  color: #ef4444;
  font-size: 15px;
  text-align: left;
  padding: 12px 16px;
  background-color: #fef2f2;
  border-radius: 8px;
  border: 1px solid #fecaca;
  font-family: 'Outfit', sans-serif;
}

.success-message {
  color: #10b981;
  font-size: 15px;
  text-align: center;
  padding: 12px 16px;
  background-color: #f0fdf4;
  border-radius: 8px;
  border: 1px solid #86efac;
  font-family: 'Outfit', sans-serif;
}

.signin-button {
  background-color: #1e3a8a;
  color: white;
  border: none;
  padding: 14px 28px;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-top: 10px;
  font-family: 'Outfit', sans-serif;
  height: 52px;
}

.signin-button:hover:not(:disabled) {
  background-color: #f59e0b;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
}

.signin-button:active:not(:disabled) {
  transform: translateY(0);
}

.signin-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.loading-spinner {
  width: 20px;
  height: 20px;
  border: 3px solid transparent;
  border-top: 3px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Logo Section */
.logo-section {
  display: flex;
  justify-content: center;
  align-items: center;
}

.logo-container {
  text-align: center;
}

.logo-wrapper {
  margin-bottom: 24px;
  display: flex;
  justify-content: center;
}

.company-logo {
  width: 380px;
  height: auto;
  object-fit: contain;
  transition: transform 0.3s ease;
  image-rendering: -webkit-optimize-contrast;
  image-rendering: crisp-edges;
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
}

.company-logo:hover {
  transform: scale(1.05);
}

.company-info {
  text-align: center;
}

.amharic-text {
  font-size: 20px;
  color: #1e3a8a;
  margin-bottom: 10px;
  font-weight: 600;
  font-family: 'Outfit', sans-serif;
  line-height: 1.4;
}

.english-text {
  font-size: 26px;
  color: #f59e0b;
  font-weight: 700;
  font-family: 'Outfit', sans-serif;
  line-height: 1.3;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .login-section {
    gap: 80px;
  }
  
  .company-logo {
    width: 320px;
    height: auto;
  }
  
  .english-text {
    font-size: 24px;
  }
  
  .amharic-text {
    font-size: 18px;
  }
  
  .form-title {
    font-size: 34px;
  }
  
  .form-title {
    font-size: 40px;
  }
}

@media (max-width: 768px) {
  .header-content {
    padding: 16px 24px;
  }
  
  .system-title {
    font-size: 20px;
  }
  
  .login-btn-header {
    padding: 10px 24px;
    font-size: 14px;
  }
  
  .main-content {
    padding: 15px 24px;
  }
  
  .back-link {
    margin-bottom: 18px;
  }
  
  .login-section {
    grid-template-columns: 1fr;
    gap: 60px;
    text-align: center;
  }
  
  .form-container {
    max-width: 100%;
  }
  
  .form-title {
    font-size: 36px;
  }
  
  .company-logo {
    width: 260px;
    height: auto;
  }
  
  .english-text {
    font-size: 20px;
  }
  
  .amharic-text {
    font-size: 16px;
  }
  
  .form-title {
    font-size: 30px;
  }
}

@media (max-width: 480px) {
  .header-content {
    padding: 12px 16px;
  }
  
  .system-title {
    font-size: 16px;
  }
  
  .main-content {
    padding: 15px 16px;
  }
  
  .form-title {
    font-size: 32px;
  }
  
  .form-input,
  .signin-button {
    height: 52px;
    font-size: 15px;
  }
  
  .company-logo {
    width: 220px;
    height: auto;
  }
  
  .english-text {
    font-size: 18px;
  }
  
  .amharic-text {
    font-size: 15px;
  }
  
  .form-title {
    font-size: 26px;
  }
}
</style>