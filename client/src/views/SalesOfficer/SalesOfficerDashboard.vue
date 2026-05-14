<template>
  <div class="dashboard-layout">
    <Sidebar />
    
    <main class="main-content">
      <div class="dashboard-header">
        <div>
          <h1 class="page-title">Dashboard</h1>
          <p class="page-subtitle">Welcome back, {{ user?.name }}!</p>
        </div>
        <div class="header-actions">
          <button class="btn-primary" @click="goToNewSale">
            <i class="fas fa-plus"></i>
            New Sale
          </button>
        </div>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon blue">
            <i class="fas fa-shopping-cart"></i>
          </div>
          <div class="stat-content">
            <p class="stat-label">Today's Sales</p>
            <h3 class="stat-value">{{ todayStats.totalSales }}</h3>
            <p class="stat-change positive">+{{ todayStats.salesChange }}% from yesterday</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon green">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <div class="stat-content">
            <p class="stat-label">Today's Revenue</p>
            <h3 class="stat-value">{{ formatCurrency(todayStats.totalRevenue) }}</h3>
            <p class="stat-change positive">+{{ todayStats.revenueChange }}% from yesterday</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon orange">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-content">
            <p class="stat-label">Pending Approvals</p>
            <h3 class="stat-value">{{ todayStats.pendingSales }}</h3>
            <p class="stat-info">Waiting for manager approval</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon purple">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-content">
            <p class="stat-label">Completed Today</p>
            <h3 class="stat-value">{{ todayStats.completedSales }}</h3>
            <p class="stat-info">Successfully processed</p>
          </div>
        </div>
      </div>

      <div class="dashboard-grid">
        <div class="card recent-sales">
          <div class="card-header">
            <h2 class="card-title">
              <i class="fas fa-list"></i>
              My Recent Sales
            </h2>
            <router-link to="/sales/my-sales" class="link">View All</router-link>
          </div>
          <div class="card-body">
            <div v-if="loading" class="loading">
              <i class="fas fa-spinner fa-spin"></i>
              Loading...
            </div>
            <div v-else-if="recentSales.length === 0" class="empty-state">
              <i class="fas fa-inbox"></i>
              <p>No sales yet today</p>
              <button class="btn-primary" @click="goToNewSale">Create Your First Sale</button>
            </div>
            <div v-else class="sales-list">
              <div v-for="sale in recentSales" :key="sale.id" class="sale-item">
                <div class="sale-info">
                  <p class="sale-number">{{ sale.sale_number }}</p>
                  <p class="sale-date">{{ formatDate(sale.sale_date) }}</p>
                </div>
                <div class="sale-amount">
                  <p class="amount">{{ formatCurrency(sale.total_amount) }}</p>
                </div>
                <div class="sale-status">
                  <span class="status-badge" :class="sale.status">
                    <i :class="getStatusIcon(sale.status)"></i>
                    {{ getStatusLabel(sale.status) }}
                  </span>
                </div>
                <button class="btn-icon" @click="viewSale(sale.id)">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="card pending-approvals">
          <div class="card-header">
            <h2 class="card-title">
              <i class="fas fa-hourglass-half"></i>
              Pending Approvals
            </h2>
          </div>
          <div class="card-body">
            <div v-if="pendingApprovals.length === 0" class="empty-state small">
              <i class="fas fa-check-circle"></i>
              <p>No pending approvals</p>
            </div>
            <div v-else class="pending-list">
              <div v-for="sale in pendingApprovals" :key="sale.id" class="pending-item">
                <div class="pending-info">
                  <p class="pending-number">{{ sale.sale_number }}</p>
                  <p class="pending-amount">{{ formatCurrency(sale.total_amount) }}</p>
                </div>
                <div class="pending-status">
                  <span class="waiting-badge">
                    <i class="fas fa-clock"></i>
                    Waiting
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card low-stock-alerts">
        <div class="card-header">
          <h2 class="card-title">
            <i class="fas fa-exclamation-triangle"></i>
            Low Stock Alerts
          </h2>
          <router-link to="/products" class="link">View All Products</router-link>
        </div>
        <div class="card-body">
          <div v-if="lowStockProducts.length === 0" class="empty-state small">
            <i class="fas fa-check-circle"></i>
            <p>All products are well stocked</p>
          </div>
          <div v-else class="stock-grid">
            <div v-for="product in lowStockProducts" :key="product.id" class="stock-item">
              <div class="stock-icon">
                <i class="fas fa-box"></i>
              </div>
              <div class="stock-info">
                <p class="stock-name">{{ product.name }}</p>
                <p class="stock-sku">SKU: {{ product.sku }}</p>
              </div>
              <div class="stock-quantity">
                <span class="quantity-badge" :class="getStockClass(product.current_stock)">
                  {{ product.current_stock }} left
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import axios from '../../axios'
import Sidebar from '../../components/Sidebar.vue'

const router = useRouter()
const authStore = useAuthStore()
const loading = ref(true)
const error = ref(null)

const user = computed(() => authStore.user)

const todayStats = ref({
  totalSales: 0,
  salesChange: 0,
  totalRevenue: 0,
  revenueChange: 0,
  pendingSales: 0,
  completedSales: 0
})

const recentSales = ref([])
const lowStockProducts = ref([])

const pendingApprovals = computed(() => {
  return recentSales.value.filter(sale => sale.status === 'pending')
})

onMounted(async () => {
  await fetchDashboardData()
})

const fetchDashboardData = async () => {
  loading.value = true
  error.value = null
  
  try {
    const [statsRes, salesRes, stockRes] = await Promise.all([
      axios.get('/api/dashboard/stats'),
      axios.get('/api/sales/my-sales?limit=5'),
      axios.get('/api/products/low-stock')
    ])
    
    if (statsRes.data.success) {
      todayStats.value = statsRes.data.data
    }
    
    if (salesRes.data.success) {
      recentSales.value = salesRes.data.data
    }
    
    if (stockRes.data.success) {
      lowStockProducts.value = stockRes.data.data
    }
  } catch (err) {
    console.error('Error fetching dashboard data:', err)
    error.value = 'Failed to load dashboard data'
    
    todayStats.value = {
      totalSales: 0,
      salesChange: 0,
      totalRevenue: 0,
      revenueChange: 0,
      pendingSales: 0,
      completedSales: 0
    }
    recentSales.value = []
    lowStockProducts.value = []
  } finally {
    loading.value = false
  }
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-ET', {
    style: 'currency',
    currency: 'ETB',
    minimumFractionDigits: 2
  }).format(amount).replace('ETB', 'Br')
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const getStatusLabel = (status) => {
  const labels = {
    completed: 'Completed',
    pending: 'Pending',
    approved: 'Approved',
    rejected: 'Rejected'
  }
  return labels[status] || status
}

const getStatusIcon = (status) => {
  const icons = {
    completed: 'fas fa-check-circle',
    pending: 'fas fa-clock',
    approved: 'fas fa-check-circle',
    rejected: 'fas fa-times-circle'
  }
  return icons[status] || 'fas fa-circle'
}

const getStockClass = (stock) => {
  if (stock === 0) return 'out-of-stock'
  if (stock <= 5) return 'critical'
  if (stock <= 10) return 'low'
  return 'normal'
}

const goToNewSale = () => {
  alert('New Sale page coming soon! Your backend friend needs to set up the products API first.')
}

const viewSale = (id) => {
  alert(`View sale details for ID: ${id} - Page coming soon!`)
}
</script>

<style scoped>
.dashboard-layout {
  display: flex;
  min-height: 100vh;
  background: #f9fafb;
}

.main-content {
  flex: 1;
  margin-left: 260px;
  padding: 24px;
  transition: margin-left 0.3s ease;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-title {
  font-size: 28px;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.page-subtitle {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.btn-primary {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: #1e3a8a;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary:hover {
  background: #1e40af;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 24px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  gap: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.2s;
}

.stat-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: white;
}

.stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #1e40af); }
.stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

.stat-content {
  flex: 1;
}

.stat-label {
  font-size: 13px;
  color: #6b7280;
  margin: 0 0 4px 0;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.stat-change {
  font-size: 12px;
  margin: 0;
}

.stat-change.positive {
  color: #10b981;
}

.stat-info {
  font-size: 12px;
  color: #6b7280;
  margin: 0;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
  margin-bottom: 24px;
}

.card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.card-header {
  padding: 20px;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-title {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.link {
  color: #1e3a8a;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
}

.link:hover {
  text-decoration: underline;
}

.card-body {
  padding: 20px;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #6b7280;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #6b7280;
}

.empty-state.small {
  padding: 40px 20px;
}

.empty-state i {
  font-size: 48px;
  color: #d1d5db;
  margin-bottom: 16px;
}

.empty-state p {
  margin: 0 0 16px 0;
  font-size: 14px;
}

.sales-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.sale-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background: #f9fafb;
  border-radius: 8px;
  transition: all 0.2s;
}

.sale-item:hover {
  background: #f3f4f6;
}

.sale-info {
  flex: 1;
}

.sale-number {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.sale-date {
  font-size: 12px;
  color: #6b7280;
  margin: 0;
}

.sale-amount .amount {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
}

.status-badge.completed {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.pending {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.approved {
  background: #dbeafe;
  color: #1e40af;
}

.status-badge.rejected {
  background: #fee2e2;
  color: #991b1b;
}

.btn-icon {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-icon:hover {
  background: #f9fafb;
  color: #1e3a8a;
  border-color: #1e3a8a;
}

.pending-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.pending-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  background: #fef3c7;
  border-radius: 8px;
  border-left: 4px solid #f59e0b;
}

.pending-number {
  font-size: 13px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.pending-amount {
  font-size: 14px;
  font-weight: 600;
  color: #92400e;
  margin: 0;
}

.waiting-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  background: #fbbf24;
  color: #78350f;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
}

.stock-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.stock-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f9fafb;
  border-radius: 8px;
}

.stock-icon {
  width: 40px;
  height: 40px;
  background: #fee2e2;
  color: #dc2626;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.stock-info {
  flex: 1;
}

.stock-name {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.stock-sku {
  font-size: 12px;
  color: #6b7280;
  margin: 0;
}

.quantity-badge {
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
}

.quantity-badge.critical {
  background: #fee2e2;
  color: #991b1b;
}

.quantity-badge.low {
  background: #fef3c7;
  color: #92400e;
}

@media (max-width: 1024px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
    padding: 16px;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .dashboard-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
}
</style>
