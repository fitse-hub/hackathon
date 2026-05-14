<template>
  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="logo">
        <img src="/logo2.jpg" alt="QMT" class="logo-img" />
      </div>
      <p class="menu-label">MENU</p>
    </div>

    <nav class="sidebar-nav">
      <template v-for="item in menuItems" :key="item.path || item.label">
        <div v-if="item.submenu" class="nav-group">
          <button 
            @click="toggleSubmenu(item.label)"
            class="nav-item nav-parent"
            :class="{ 'active': openSubmenus.includes(item.label) }"
          >
            <i :class="item.icon"></i>
            <span class="nav-text">{{ item.label }}</span>
            <i class="fas fa-chevron-down submenu-arrow" :class="{ 'rotated': openSubmenus.includes(item.label) }"></i>
          </button>
          
          <div v-if="openSubmenus.includes(item.label)" class="submenu">
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

        <router-link 
          v-else
          :to="item.path"
          class="nav-item"
          :class="{ 'primary': item.primary }"
        >
          <i :class="item.icon"></i>
          <span class="nav-text">{{ item.label }}</span>
          <span v-if="item.badge" class="badge">{{ item.badge }}</span>
        </router-link>
      </template>
    </nav>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const openSubmenus = ref(['Products', 'Inventory', 'Reports'])

const menuItems = computed(() => {
  const items = []
  
  if (authStore.isSalesOfficer) {
    items.push(
      { path: '/dashboard', icon: 'fas fa-th-large', label: 'Dashboard' },
      { path: '/sales/new', icon: 'fas fa-shopping-cart', label: 'New Sale', primary: true },
      { path: '/sales/my-sales', icon: 'fas fa-file-invoice', label: 'My Sales' },
      { path: '/products', icon: 'fas fa-box', label: 'Products' },
      { path: '/profile', icon: 'fas fa-user', label: 'My Profile' }
    )
  } else if (authStore.isManager || authStore.isAdmin) {
    items.push(
      { path: '/manager/dashboard', icon: 'fas fa-th-large', label: 'Dashboard' },
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

const toggleSubmenu = (label) => {
  const index = openSubmenus.value.indexOf(label)
  if (index > -1) {
    openSubmenus.value.splice(index, 1)
  } else {
    openSubmenus.value.push(label)
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap');

* {
  font-family: 'Outfit', sans-serif;
}

.sidebar {
  width: 240px;
  height: 100vh;
  background: #ffffff;
  border-right: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 0;
  z-index: 100;
  overflow-y: auto;
}

.sidebar-header {
  padding: 24px 20px 20px;
  border-bottom: 1px solid #f3f4f6;
}

.logo {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.logo-img {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
}

.menu-label {
  font-size: 11px;
  font-weight: 600;
  color: #9ca3af;
  letter-spacing: 0.5px;
  margin: 0;
  padding-left: 16px;
}

.sidebar-nav {
  flex: 1;
  padding: 16px 12px;
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
  color: #6b7280;
  text-decoration: none;
  transition: all 0.2s;
  position: relative;
  cursor: pointer;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  font-size: 14px;
}

.nav-item:hover {
  background: #f9fafb;
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
  font-weight: 500;
}

.nav-item.primary:hover {
  background: #1e40af;
  color: white;
}

.nav-parent {
  font-weight: 500;
  color: #4b5563;
}

.nav-parent.active {
  background: #f9fafb;
  color: #1e3a8a;
}

.nav-item i {
  font-size: 16px;
  width: 18px;
  text-align: center;
  flex-shrink: 0;
}

.nav-text {
  flex: 1;
  font-size: 14px;
}

.submenu-arrow {
  font-size: 10px;
  transition: transform 0.2s;
  margin-left: auto;
}

.submenu-arrow.rotated {
  transform: rotate(180deg);
}

.badge {
  background: #ef4444;
  color: white;
  font-size: 10px;
  font-weight: 600;
  padding: 2px 7px;
  border-radius: 10px;
  min-width: 18px;
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
  font-size: 13px;
  transition: all 0.2s;
}

.submenu-item:hover {
  background: #f9fafb;
  color: #1e3a8a;
}

.submenu-item.router-link-active {
  background: #eff6ff;
  color: #1e3a8a;
  font-weight: 500;
}

.submenu-item i {
  font-size: 13px;
  width: 14px;
  text-align: center;
}

@media (max-width: 768px) {
  .sidebar {
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }
  
  .sidebar.mobile-open {
    transform: translateX(0);
  }
}
</style>
