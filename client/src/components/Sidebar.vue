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
      <template v-for="item in menuItems" :key="item.path || item.label">
        <!-- Menu item with submenu -->
        <div v-if="item.submenu" class="nav-group">
          <button 
            @click="toggleSubmenu(item.label)"
            class="nav-item nav-parent"
            :class="{ 'active': openSubmenus.includes(item.label) }"
          >
            <i :class="item.icon"></i>
            <span v-if="!isCollapsed" class="nav-text">{{ item.label }}</span>
            <i v-if="!isCollapsed" class="fas fa-chevron-down submenu-arrow" :class="{ 'rotated': openSubmenus.includes(item.label) }"></i>
          </button>
          
          <div v-if="!isCollapsed && openSubmenus.includes(item.label)" class="submenu">
            <router-link 
              v-for="subitem in item.submenu" 
              :key="subitem.path"
              :to="subitem.path"
              class="submenu-item"
            >
              <i :class="subitem.icon"></i>
              <span>{{ subitem.label }}</span>
            </router-link>
          </div>
        </div>

        <!-- Regular menu item -->
        <router-link 
          v-else
          :to="item.path"
          class="nav-item"
          :class="{ 'primary': item.primary }"
        >
          <i :class="item.icon"></i>
          <span v-if="!isCollapsed" class="nav-text">{{ item.label }}</span>
          <span v-if="item.badge && !isCollapsed" class="badge">{{ item.badge }}</span>
        </router-link>
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
const openSubmenus = ref(['Products', 'Inventory', 'Reports'])

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
      { path: '/sales/new', icon: 'fas fa-shopping-cart', label: 'New Sale', primary: true },
      { path: '/sales/my-sales', icon: 'fas fa-list', label: 'My Sales' },
      { path: '/products', icon: 'fas fa-box', label: 'Products' },
      { path: '/profile', icon: 'fas fa-user', label: 'My Profile' }
    )
  } else if (authStore.isManager || authStore.isAdmin) {
    items.push(
      { path: '/manager/dashboard', icon: 'fas fa-home', label: 'Dashboard' },
      { path: '/sales/pending', icon: 'fas fa-clock', label: 'Pending Approvals', badge: 8 },
      { path: '/sales/all', icon: 'fas fa-receipt', label: 'All Sales' },
      {
        icon: 'fas fa-box',
        label: 'Products',
        submenu: [
          { path: '/products', icon: 'fas fa-list', label: 'View Products' },
          { path: '/products/add', icon: 'fas fa-plus', label: 'Add Product' },
          { path: '/products/categories', icon: 'fas fa-tags', label: 'Categories' }
        ]
      },
      {
        icon: 'fas fa-warehouse',
        label: 'Inventory',
        submenu: [
          { path: '/inventory/stock-levels', icon: 'fas fa-boxes', label: 'Stock Levels' },
          { path: '/inventory/add-stock', icon: 'fas fa-plus-circle', label: 'Add Stock' },
          { path: '/inventory/history', icon: 'fas fa-history', label: 'Stock History' }
        ]
      },
      {
        icon: 'fas fa-chart-bar',
        label: 'Reports',
        submenu: [
          { path: '/reports/daily', icon: 'fas fa-calendar-day', label: 'Daily Report' },
          { path: '/reports/monthly', icon: 'fas fa-calendar-alt', label: 'Monthly Report' },
          { path: '/reports/stock', icon: 'fas fa-box-open', label: 'Stock Report' },
          { path: '/reports/export', icon: 'fas fa-file-export', label: 'Export PDF/Excel' }
        ]
      },
      { path: '/profile', icon: 'fas fa-user', label: 'Profile' }
    )
  }
  
  return items
})

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value
  if (isCollapsed.value) {
    openSubmenus.value = []
  } else {
    openSubmenus.value = ['Products', 'Inventory', 'Reports']
  }
}

const toggleSubmenu = (label) => {
  if (isCollapsed.value) return
  
  const index = openSubmenus.value.indexOf(label)
  if (index > -1) {
    openSubmenus.value.splice(index, 1)
  } else {
    openSubmenus.value.push(label)
  }
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
  overflow-y: auto;
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
  flex-shrink: 0;
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

.nav-group {
  margin-bottom: 4px;
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
  cursor: pointer;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
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

.nav-parent {
  font-weight: 500;
}

.nav-parent.active {
  background: #f9fafb;
  color: #1e3a8a;
}

.nav-item i {
  font-size: 18px;
  width: 20px;
  text-align: center;
  flex-shrink: 0;
}

.nav-text {
  flex: 1;
  font-size: 15px;
}

.submenu-arrow {
  font-size: 12px;
  transition: transform 0.2s;
  margin-left: auto;
}

.submenu-arrow.rotated {
  transform: rotate(180deg);
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

.submenu {
  padding-left: 12px;
  margin-top: 4px;
  margin-bottom: 8px;
}

.submenu-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  margin-bottom: 2px;
  border-radius: 6px;
  color: #6b7280;
  text-decoration: none;
  font-size: 14px;
  transition: all 0.2s;
}

.submenu-item:hover {
  background: #f3f4f6;
  color: #1e3a8a;
}

.submenu-item.router-link-active {
  background: #eff6ff;
  color: #1e3a8a;
  font-weight: 500;
}

.submenu-item i {
  font-size: 14px;
  width: 16px;
  text-align: center;
}

.sidebar-footer {
  padding: 16px;
  border-top: 1px solid #e5e7eb;
  flex-shrink: 0;
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
