import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import SalesOfficerDashboard from '../views/SalesOfficer/SalesOfficerDashboard.vue'
import ManagerDashboard from '../views/Manager/ManagerDashboard.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    },
    {
      path: '/login',
      name: 'login',
      component: Login
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: SalesOfficerDashboard,
      meta: { requiresAuth: true },
      beforeEnter: (to, from) => {
        const authStore = useAuthStore()
        if (authStore.isManager || authStore.isAdmin) {
          return { name: 'manager-dashboard' }
        }
      }
    },
    {
      path: '/manager/dashboard',
      name: 'manager-dashboard',
      component: ManagerDashboard,
      meta: { requiresAuth: true, requiresManager: true }
    },
    {
      path: '/sales/new',
      name: 'new-sale',
      component: () => import('../views/SalesOfficer/NewSale.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/sales/my-sales',
      name: 'my-sales',
      component: () => import('../views/SalesOfficer/MySales.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/sales/pending',
      name: 'pending-approvals',
      component: () => import('../views/Manager/PendingApprovals.vue'),
      meta: { requiresAuth: true, requiresManager: true }
    },
    {
      path: '/sales/all',
      name: 'all-sales',
      component: () => import('../views/Manager/AllSales.vue'),
      meta: { requiresAuth: true, requiresManager: true }
    },
    {
      path: '/products',
      name: 'products',
      component: () => import('../views/Products/ProductList.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/products/add',
      name: 'add-product',
      component: () => import('../views/Products/AddProduct.vue'),
      meta: { requiresAuth: true, requiresManager: true }
    },
    {
      path: '/products/categories',
      name: 'categories',
      component: () => import('../views/Products/Categories.vue'),
      meta: { requiresAuth: true, requiresManager: true }
    },
    {
      path: '/inventory/stock-levels',
      name: 'stock-levels',
      component: () => import('../views/Inventory/StockLevels.vue'),
      meta: { requiresAuth: true, requiresManager: true }
    },
    {
      path: '/inventory/add-stock',
      name: 'add-stock',
      component: () => import('../views/Inventory/AddStock.vue'),
      meta: { requiresAuth: true, requiresManager: true }
    },
    {
      path: '/inventory/history',
      name: 'stock-history',
      component: () => import('../views/Inventory/StockHistory.vue'),
      meta: { requiresAuth: true, requiresManager: true }
    },
    {
      path: '/reports/daily',
      name: 'daily-report',
      component: () => import('../views/Reports/DailyReport.vue'),
      meta: { requiresAuth: true, requiresManager: true }
    },
    {
      path: '/reports/monthly',
      name: 'monthly-report',
      component: () => import('../views/Reports/MonthlyReport.vue'),
      meta: { requiresAuth: true, requiresManager: true }
    },
    {
      path: '/reports/stock',
      name: 'stock-report',
      component: () => import('../views/Reports/StockReport.vue'),
      meta: { requiresAuth: true, requiresManager: true }
    },
    {
      path: '/reports/export',
      name: 'export-reports',
      component: () => import('../views/Reports/ExportReports.vue'),
      meta: { requiresAuth: true, requiresManager: true }
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/Profile.vue'),
      meta: { requiresAuth: true }
    }
  ],
})

router.beforeEach(async (to, from) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      await authStore.checkAuth()
    }

    if (!authStore.isAuthenticated) {
      return '/login'
    }
  }
})

export default router
