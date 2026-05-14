<template>
  <div class="dashboard">
    <header class="dashboard-header">
      <div class="header-content">
        <div class="logo-section">
          <img src="/logo2.jpg" alt="Logo" class="logo" />
          <h1>Dashboard</h1>
        </div>
        <div class="user-section">
          <div class="user-info">
            <span class="user-name">{{ user?.name }}</span>
            <span class="user-role" :class="user?.role">{{ user?.role }}</span>
          </div>
          <button @click="handleLogout" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Logout
          </button>
        </div>
      </div>
    </header>

    <main class="dashboard-main">
      <div class="welcome-section">
        <h2>Welcome back, {{ user?.name }}!</h2>
        <p>You are logged in as <strong>{{ user?.role }}</strong></p>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-content">
            <h3>Total Users</h3>
            <p class="stat-number">1,234</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-chart-line"></i>
          </div>
          <div class="stat-content">
            <h3>Analytics</h3>
            <p class="stat-number">89%</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-tasks"></i>
          </div>
          <div class="stat-content">
            <h3>Tasks</h3>
            <p class="stat-number">42</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-bell"></i>
          </div>
          <div class="stat-content">
            <h3>Notifications</h3>
            <p class="stat-number">7</p>
          </div>
        </div>
      </div>

      <div class="permissions-section">
        <h3>Your Permissions</h3>
        <div class="permissions-grid">
          <div 
            v-for="(value, permission) in permissions" 
            :key="permission"
            class="permission-item"
            :class="{ 'granted': value, 'denied': !value }"
          >
            <i :class="value ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
            <span>{{ formatPermission(permission) }}</span>
          </div>
        </div>
      </div>

      <div class="actions-section">
        <h3>Quick Actions</h3>
        <div class="actions-grid">
          <button 
            v-if="permissions?.can_manage_users" 
            class="action-btn primary"
          >
            <i class="fas fa-users-cog"></i>
            Manage Users
          </button>
          
          <button 
            v-if="permissions?.can_manage_content" 
            class="action-btn secondary"
          >
            <i class="fas fa-edit"></i>
            Manage Content
          </button>
          
          <button 
            v-if="permissions?.can_view_analytics" 
            class="action-btn info"
          >
            <i class="fas fa-chart-bar"></i>
            View Analytics
          </button>
          
          <button 
            v-if="permissions?.can_manage_settings" 
            class="action-btn warning"
          >
            <i class="fas fa-cog"></i>
            System Settings
          </button>
          
          <button 
            v-if="permissions?.can_moderate" 
            class="action-btn success"
          >
            <i class="fas fa-shield-alt"></i>
            Moderation
          </button>
          
          <button class="action-btn default">
            <i class="fas fa-user-edit"></i>
            Edit Profile
          </button>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const user = computed(() => authStore.user)
const permissions = computed(() => authStore.permissions)

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

const formatPermission = (permission) => {
  return permission
    .replace('can_', '')
    .replace(/_/g, ' ')
    .replace(/\b\w/g, l => l.toUpperCase())
}
</script>

<style scoped>
.dashboard {
  min-height: 100vh;
  background: #f7fafc;
}

.dashboard-header {
  background: white;
  border-bottom: 1px solid #e2e8f0;
  padding: 1rem 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
}

.logo-section h1 {
  color: #2d3748;
  margin: 0;
  font-size: 24px;
}

.user-section {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.user-name {
  font-weight: 600;
  color: #2d3748;
}

.user-role {
  font-size: 12px;
  padding: 2px 8px;
  border-radius: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.user-role.admin {
  background: #fed7d7;
  color: #c53030;
}

.user-role.manager {
  background: #bee3f8;
  color: #2b6cb0;
}

.user-role.moderator {
  background: #d6f5d6;
  color: #22543d;
}

.user-role.user {
  background: #e2e8f0;
  color: #4a5568;
}

.logout-btn {
  background: #e53e3e;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  background: #c53030;
  transform: translateY(-1px);
}

.dashboard-main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.welcome-section {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.welcome-section h2 {
  color: #2d3748;
  margin: 0 0 0.5rem 0;
}

.welcome-section p {
  color: #718096;
  margin: 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 24px;
}

.stat-content h3 {
  margin: 0 0 0.5rem 0;
  color: #4a5568;
  font-size: 14px;
  font-weight: 500;
}

.stat-number {
  margin: 0;
  font-size: 24px;
  font-weight: 700;
  color: #2d3748;
}

.permissions-section,
.actions-section {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.permissions-section h3,
.actions-section h3 {
  margin: 0 0 1.5rem 0;
  color: #2d3748;
}

.permissions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.permission-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 500;
}

.permission-item.granted {
  background: #c6f6d5;
  color: #22543d;
}

.permission-item.denied {
  background: #fed7d7;
  color: #c53030;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.action-btn {
  padding: 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  transition: all 0.3s ease;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.action-btn.primary {
  background: #667eea;
  color: white;
}

.action-btn.secondary {
  background: #718096;
  color: white;
}

.action-btn.info {
  background: #3182ce;
  color: white;
}

.action-btn.warning {
  background: #d69e2e;
  color: white;
}

.action-btn.success {
  background: #38a169;
  color: white;
}

.action-btn.default {
  background: #e2e8f0;
  color: #4a5568;
}

@media (max-width: 768px) {
  .dashboard-header {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    gap: 1rem;
  }
  
  .dashboard-main {
    padding: 1rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .permissions-grid,
  .actions-grid {
    grid-template-columns: 1fr;
  }
}
</style>