<template>
  <aside class="sidebar" :class="{ 'collapsed': isCollapsed }">
    <div class="sidebar-header">
      <div class="logo">
        <img src="/logo2.jpg" alt="Qelem Meda" class="logo-img" />
        <span v-if="!isCollapsed" class="logo-text">QMT</span>
      </div>
      <button @click="toggleSidebar" class="toggle-btn">
        <i :class="isCollapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"></i>
      </button>
    </div>

    <nav class="sidebar-nav">
      <template v-for="item in menuItems" :key="item.path">
        <router-link 
          v-if="item.path !== '#'"
          :to="item.path"
          class="nav-item"
          :class="{ 'primary': item.primary }"
        >
          <i :class="item.icon"></i>
          <span v-if="!isCollapsed" class="nav-text">{{ item.label }}</span>
          <span v-if="item.badge && !isCollapsed" class="badge">{{ item.badge }}</span>
        </router-link>
        
        <a 
          v-else
          href="#"
          @click.prevent="item.onClick"
          class="nav-item"
          :class="{ 'primary': item.primary }"
        >
          <i :class="item.icon"></i>
          <span v-if="!isCollapsed" class="nav-text">{{ item.label }}</span>
          <span v-if="item.badge && !isCollapsed" class="badge">{{ item.badge }}</span>
        </a>
      </template>
    </nav>

    <div class="sidebar-footer">
      <div class="user-info" v-if="!isCollapsed">
        <div class="user-avatar">
          <i class="fas fa-user-circle"></i>
        </div>
        <div class="user-details">
          <p class="user-name">{{ user?.name }}</p>
          <p class="user-role">{{ roleLabel }}</p>
        </div>
      </div>
      <button @click="handleLogout" class="logout-btn" :title="isCollapsed ? 'Logout' : ''">
        <i class="fas fa-sign-out-alt"></i>
        <span v-if="!isCollapsed">Logout</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const isCollapsed = ref(false)

const user = computed(() => authStore.user)

const roleLabel = computed(() => {
  const role = authStore.user?.role
  if (role === 'sales_officer') return 'Sales Officer'
  if (role === 'manager') return 'Manager'
  if (role === 'admin') return 'Administrator'
  return role
})

const menuItems = computed(() => {
  const items = []
  
  if (authStore.isSalesOfficer) {
    items.push(
      { path: '/dashboard', icon: 'fas fa-home', label: 'Dashboard' },
      { path: '#', icon: 'fas fa-shopping-cart', label: 'New Sale', primary: true, onClick: () => alert('Coming soon! Backend API needed first.') },
      { path: '#', icon: 'fas fa-list', label: 'My Sales', onClick: () => alert('Coming soon!') },
      { path: '#', icon: 'fas fa-box', label: 'Products', onClick: () => alert('Coming soon!') },
      { path: '#', icon: 'fas fa-user', label: 'My Profile', onClick: () => alert('Coming soon!') }
    )
  } else if (authStore.isManager || authStore.isAdmin) {
    items.push(
      { path: '/dashboard', icon: 'fas fa-home', label: 'Dashboard' },
      { path: '#', icon: 'fas fa-clock', label: 'Pending Approvals', badge: 8, onClick: () => alert('Coming soon!') },
      { path: '#', icon: 'fas fa-receipt', label: 'All Sales', onClick: () => alert('Coming soon!') },
      { path: '#', icon: 'fas fa-box', label: 'Products', onClick: () => alert('Coming soon!') },
      { path: '#', icon: 'fas fa-warehouse', label: 'Inventory', onClick: () => alert('Coming soon!') },
      { path: '#', icon: 'fas fa-chart-bar', label: 'Reports', onClick: () => alert('Coming soon!') },
      { path: '#', icon: 'fas fa-user', label: 'Profile', onClick: () => alert('Coming soon!') }
    )
  }
  
  return items
})

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.sidebar {
  width: 260px;
  height: 100vh;
  background: #ffffff;
  border-right: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease;
  position: fixed;
  left: 0;
  top: 0;
  z-index: 100;
}

.sidebar.collapsed {
  width: 70px;
}

.sidebar-header {
  padding: 20px;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-img {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  object-fit: cover;
}

.logo-text {
  font-size: 20px;
  font-weight: 700;
  color: #1e3a8a;
}

.toggle-btn {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s;
}

.toggle-btn:hover {
  background: #f3f4f6;
  color: #1e3a8a;
}

.sidebar-nav {
  flex: 1;
  padding: 20px 12px;
  overflow-y: auto;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  margin-bottom: 4px;
  border-radius: 8px;
  color: #4b5563;
  text-decoration: none;
  transition: all 0.2s;
  position: relative;
}

.nav-item:hover {
  background: #f3f4f6;
  color: #1e3a8a;
}

.nav-item.router-link-active {
  background: #eff6ff;
  color: #1e3a8a;
  font-weight: 600;
}

.nav-item.primary {
  background: #1e3a8a;
  color: white;
  font-weight: 600;
}

.nav-item.primary:hover {
  background: #1e40af;
  color: white;
}

.nav-item i {
  font-size: 18px;
  width: 20px;
  text-align: center;
}

.nav-text {
  flex: 1;
  font-size: 15px;
}

.badge {
  background: #ef4444;
  color: white;
  font-size: 11px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 12px;
  min-width: 20px;
  text-align: center;
}

.sidebar-footer {
  padding: 16px;
  border-top: 1px solid #e5e7eb;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f9fafb;
  border-radius: 8px;
  margin-bottom: 12px;
}

.user-avatar i {
  font-size: 36px;
  color: #1e3a8a;
}

.user-details {
  flex: 1;
  min-width: 0;
}

.user-name {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  font-size: 12px;
  color: #6b7280;
  margin: 0;
}

.logout-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 10px;
  background: #fee2e2;
  color: #dc2626;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.logout-btn:hover {
  background: #fecaca;
}

.collapsed .sidebar-nav {
  padding: 20px 8px;
}

.collapsed .nav-item {
  justify-content: center;
  padding: 12px;
}

.collapsed .user-info {
  justify-content: center;
  padding: 8px;
}

.collapsed .logout-btn {
  padding: 10px;
}

@media (max-width: 768px) {
  .sidebar {
    width: 70px;
  }
  
  .sidebar.collapsed {
    width: 0;
    border: none;
  }
}
</style>
