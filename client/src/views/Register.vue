<template>
  <div class="register-container">
    <div class="register-card">
      <div class="logo-section">
        <img src="/logo2.jpg" alt="Logo" class="logo" />
        <h1 class="app-title">Create Account</h1>
        <p class="app-subtitle">Join us today</p>
      </div>

      <form @submit.prevent="handleRegister" class="register-form">
        <div class="form-group">
          <label for="name" class="form-label">Full Name</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            class="form-input"
            :class="{ 'error': errors.name }"
            placeholder="Enter your full name"
            required
          />
          <span v-if="errors.name" class="error-message">{{ errors.name[0] }}</span>
        </div>

        <div class="form-group">
          <label for="email" class="form-label">Email Address</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="form-input"
            :class="{ 'error': errors.email }"
            placeholder="Enter your email"
            required
          />
          <span v-if="errors.email" class="error-message">{{ errors.email[0] }}</span>
        </div>

        <div class="form-group">
          <label for="role" class="form-label">Role</label>
          <select
            id="role"
            v-model="form.role"
            class="form-input"
            :class="{ 'error': errors.role }"
            required
          >
            <option value="">Select a role</option>
            <option value="user">User</option>
            <option value="moderator">Moderator</option>
            <option value="manager">Manager</option>
            <option value="admin">Administrator</option>
          </select>
          <span v-if="errors.role" class="error-message">{{ errors.role[0] }}</span>
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <div class="password-input-container">
            <input
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="form-input"
              :class="{ 'error': errors.password }"
              placeholder="Enter your password"
              required
            />
            <button
              type="button"
              @click="togglePassword"
              class="password-toggle"
            >
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
          <span v-if="errors.password" class="error-message">{{ errors.password[0] }}</span>
        </div>

        <div class="form-group">
          <label for="password_confirmation" class="form-label">Confirm Password</label>
          <div class="password-input-container">
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              :type="showConfirmPassword ? 'text' : 'password'"
              class="form-input"
              :class="{ 'error': errors.password_confirmation }"
              placeholder="Confirm your password"
              required
            />
            <button
              type="button"
              @click="toggleConfirmPassword"
              class="password-toggle"
            >
              <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
          <span v-if="errors.password_confirmation" class="error-message">{{ errors.password_confirmation[0] }}</span>
        </div>

        <button
          type="submit"
          class="register-button"
          :disabled="loading"
        >
          <span v-if="loading" class="spinner"></span>
          {{ loading ? 'Creating Account...' : 'Create Account' }}
        </button>

        <div v-if="errorMessage" class="alert alert-error">
          {{ errorMessage }}
        </div>

        <div v-if="successMessage" class="alert alert-success">
          {{ successMessage }}
        </div>
      </form>

      <!-- Login Link -->
      <div class="login-link">
        <p>Already have an account? <router-link to="/login" class="link">Sign in here</router-link></p>
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
const showConfirmPassword = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'user'
})

const errors = ref({})

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const toggleConfirmPassword = () => {
  showConfirmPassword.value = !showConfirmPassword.value
}

const handleRegister = async () => {
  loading.value = true
  errorMessage.value = ''
  errors.value = {}

  try {
    await authStore.register(form)
    successMessage.value = 'Account created successfully! Redirecting...'
    setTimeout(() => router.push('/dashboard'), 500)
  } catch (error) {
    errors.value = error.response?.data?.errors || {}
    errorMessage.value = error.response?.data?.message || 'Registration failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.register-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.register-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 40px;
  width: 100%;
  max-width: 450px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.logo-section {
  text-align: center;
  margin-bottom: 30px;
}

.logo {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 20px;
  border: 4px solid #667eea;
  box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
}

.app-title {
  font-size: 28px;
  font-weight: 700;
  color: #2d3748;
  margin: 0 0 8px 0;
}

.app-subtitle {
  color: #718096;
  font-size: 16px;
  margin: 0;
}

.register-form {
  margin-bottom: 30px;
}

.form-group {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #2d3748;
  font-size: 14px;
}

.form-input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 16px;
  transition: all 0.3s ease;
  background: #f7fafc;
}

.form-input:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input.error {
  border-color: #e53e3e;
  background: #fed7d7;
}

.password-input-container {
  position: relative;
}

.password-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #718096;
  cursor: pointer;
  padding: 4px;
}

.password-toggle:hover {
  color: #667eea;
}

.register-button {
  width: 100%;
  padding: 14px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.register-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.register-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.spinner {
  width: 20px;
  height: 20px;
  border: 2px solid transparent;
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.alert {
  padding: 12px 16px;
  border-radius: 8px;
  margin-top: 15px;
  font-size: 14px;
}

.alert-error {
  background: #fed7d7;
  color: #c53030;
  border: 1px solid #feb2b2;
}

.alert-success {
  background: #c6f6d5;
  color: #22543d;
  border: 1px solid #9ae6b4;
}

.error-message {
  color: #e53e3e;
  font-size: 12px;
  margin-top: 4px;
  display: block;
}

.login-link {
  text-align: center;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
}

.login-link p {
  color: #718096;
  margin: 0;
}

.link {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
}

.link:hover {
  text-decoration: underline;
}

@media (max-width: 480px) {
  .register-card {
    padding: 30px 20px;
    margin: 10px;
  }
}
</style>