<template>
  <div class="dashboard-layout">
    <Sidebar />
    
    <main class="main-content">
      <div class="dashboard-header">
        <div>
          <h1 class="page-title">Manager Dashboard</h1>
          <p class="page-subtitle">Welcome back, {{ user?.name }}!</p>
        </div>
        <div class="header-actions">
          <button class="btn-secondary" @click="refreshData">
            <i class="fas fa-sync-alt"></i>
            Refresh
          </button>
        </div>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon blue">
            <i class="fas fa-shopping-cart"></i>
          </div>
          <div class="stat-content">
            <p class="stat-label">Total Sales Today</p>
            <h3 class="stat-value">{{ todayStats.totalSales }}</h3>
            <p class="stat-change positive">+{{ todayStats.salesChange}}% from yesterday</p>
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

        <div class="stat-card alert">
          <div class="stat-icon orange">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-content">
            <p class="stat-label">Pending Approvals</p>
            <h3 class="stat-value">{{ todayStats.pendingSales }}</h3>
            <p class="stat-info urgent">Requires your attention!</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon purple">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-content">
            <p class="stat-label">Active Officers</p>
            <h3 class="stat-value">{{ todayStats.activeOfficers }}</h3>
            <p class="stat-info">Working today</p>
          </div>
        </div>
      </div>

      <div class="alert-banner" v-if="todayStats.pendingSales > 0">
        <i class="fas fa-exclamation-triangle"></i>
        <span>You have <strong>{{ todayStats.pendingSales }} sales</strong> waiting for approval</span>
        <button class="btn-alert" @click="goToPendingApprovals">Review Now</button>
      </div>

      <div class="dashboard-grid">
        <div class="card pending-approvals-card">
          <div class="card-header">
            <h2 class="card-title">
              <i class="fas fa-hourglass-half"></i>
              Pending Approvals
            </h2>
            <button class="link" @click="goToPendingApprovals">View All</button>
          </div>
          <div class="card-body">
            <div v-if="loading" class="loading">
              <i class="fas fa-spinner fa-spin"></i>
              Loading...
            </div>
            <div v-else-if="pendingApprovals.length === 0" class="empty-state small">
              <i class="fas fa-check-circle"></i>
              <p>No pending approvals</p>
            </div>
            <div v-else class="approvals-list">
              <div v-for="sale in pendingApprovals" :key="sale.id" class="approval-item">
                <div class="approval-header">
                  <div class="approval-info">
                    <p class="approval-number">{{ sale.sale_number }}</p>
                    <p class="approval-officer">By: {{ sale.prepared_by?.name }}</p>
                  </div>
                  <div class="approval-amount">
                    <p class="amount">{{ formatCurrency(sale.total_amount) }}</p>
                    <p class="approval-date">{{ formatDate(sale.created_at) }}</p>
                  </div>
                </div>
                <div class="approval-actions">
                  <button class="btn-approve" @click="approveSale(sale.id)">
                    <i class="fas fa-check"></i>
                    Approve
                  </button>
                  <button class="btn-reject" @click="rejectSale(sale.id)">
                    <i class="fas fa-times"></i>
                    Reject
                  </button>
                  <button class="btn-view" @click="viewSale(sale.id)">
                    <i class="fas fa-eye"></i>
                    Details
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card sales-officers-card">
          <div class="card-header">
            <h2 class="card-title">
              <i class="fas fa-users"></i>
              Sales Officers Performance
            </h2>
          </div>
          <div class="card-body">
            <div class="officers-list">
              <div v-for="officer in salesOfficers" :key="officer.id" class="officer-item">
                <div class="officer-avatar">
                  <i class="fas fa-user-circle"></i>
                </div>
                <div class="officer-info">
                  <p class="officer-name">{{ officer.name }}</p>
                  <p class="officer-stats">{{ officer.sales_count }} sales</p>
                </div>
                <div class="officer-revenue">
                  <p class="revenue">{{ formatCurrency(officer.revenue) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="dashboard-grid-2">
        <div class="card low-stock-card">
          <div class="card-header">
            <h2 class="card-title">
              <i class="fas fa-exclamation-triangle"></i>
              Low Stock Alerts
            </h2>
            <span class="badge-count">{{ lowStockProducts.length }}</span>
          </div>
          <div class="card-body">
            <div v-if="lowStockProducts.length === 0" class="empty-state small">
              <i class="fas fa-check-circle"></i>
              <p>All products well stocked</p>
            </div>
            <div v-else class="stock-list">
              <div v-for="product in lowStockProducts" :key="product.id" class="stock-item">
                <div class="stock-icon">
                  <i class="fas fa-box"></i>
                </div>
                <div class="stock-info">
                  <p class="stock-name">{{ product.name }}</p>
                  <p class="stock-sku">SKU: {{ product.sku }}</p>
                </div>
                <div class="stock-quantity">
                  <span class="quantity-badge critical">
                    {{ product.current_stock }} left
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card quick-actions-card">
          <div class="card-header">
            <h2 class="card-title">
              <i class="fas fa-bolt"></i>
              Quick Actions
            </h2>
          </div>
          <div class="card-body">
            <div class="actions-grid">
              <button class="action-card" @click="alert('Coming soon!')">
                <i class="fas fa-plus-circle"></i>
                <span>Add Product</span>
              </button>
              <button class="action-card" @click="alert('Coming soon!')">
                <i class="fas fa-box-open"></i>
                <span>Add Stock</span>
              </button>
              <button class="action-card" @click="alert('Coming soon!')">
                <i class="fas fa-file-pdf"></i>
                <span>Generate Report</span>
              </button>
              <button class="action-card" @click="alert('Coming soon!')">
                <i class="fas fa-chart-line"></i>
                <span>View Analytics</span>
              </button>
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

const user = computed(() => authStore.user)

const todayStats = ref({
  totalSales: 0,
  salesChange: 0,
  totalRevenue: 0,
  revenueChange: 0,
  pendingSales: 0,
  activeOfficers: 0
})

const pendingApprovals = ref([])
const salesOfficers = ref([])
const lowStockProducts = ref([])

onMounted(async () => {
  await fetchDashboardData()
})

const fetchDashboardData = async () => {
  loading.value = true
  
  try {
    const [statsRes, pendingRes, officersRes, stockRes] = await Promise.all([
      axios.get('/api/dashboard/manager-stats'),
      axios.get('/api/sales/pending?limit=5'),
      axios.get('/api/dashboard/officers-performance'),
      axios.get('/api/products/low-stock')
    ])
    
    if (statsRes.data.success) {
      todayStats.value = statsRes.data.data
    }
    
    if (pendingRes.data.success) {
      pendingApprovals.value = pendingRes.data.data
    }
    
    if (officersRes.data.success) {
      salesOfficers.value = officersRes.data.data
    }
    
    if (stockRes.data.success) {
      lowStockProducts.value = stockRes.data.data
    }
  } catch (err) {
    console.error('Error fetching dashboard data:', err)
    
    todayStats.value = {
      totalSales: 0,
      salesChange: 0,
      totalRevenue: 0,
      revenueChange: 0,
      pendingSales: 0,
      activeOfficers: 0
    }
  } finally {
    loading.value = false
  }
}

const refreshData = () => {
  fetchDashboardData()
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
    hour: '2-digit',
    minute: '2-digit'
  })
}

const goToPendingApprovals = () => {
  alert('Pending Approvals page coming soon!')
}

const approveSale = (id) => {
  alert(`Approve sale ${id} - Coming soon!`)
}

const rejectSale = (id) => {
  alert(`Reject sale ${id} - Coming soon!`)
}

const viewSale = (id) => {
  alert(`View sale details ${id} - Coming soon!`)
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

.btn-secondary {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: white;
  color: #1e3a8a;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #1e3a8a;
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

.stat-card.alert {
  border: 2px solid #f59e0b;
  background: #fffbeb;
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
  flex-shrink: 0;
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

.stat-info.urgent {
  color: #dc2626;
  font-weight: 600;
}

.alert-banner {
  background: linear-gradient(135deg, #fef3c7, #fde68a);
  border: 2px solid #f59e0b;
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.alert-banner i {
  font-size: 24px;
  color: #d97706;
}

.alert-banner span {
  flex: 1;
  color: #78350f;
  font-size: 15px;
}

.btn-alert {
  padding: 10px 20px;
  background: #f59e0b;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-alert:hover {
  background: #d97706;
  transform: translateY(-1px);
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
  margin-bottom: 24px;
}

.dashboard-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
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

.badge-count {
  background: #fee2e2;
  color: #dc2626;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.link {
  color: #1e3a8a;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  background: none;
  border: none;
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
  margin: 0;
  font-size: 14px;
}

.approvals-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.approval-item {
  padding: 16px;
  background: #fef3c7;
  border-radius: 8px;
  border-left: 4px solid #f59e0b;
}

.approval-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
}

.approval-number {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.approval-officer {
  font-size: 12px;
  color: #6b7280;
  margin: 0;
}

.approval-amount .amount {
  font-size: 18px;
  font-weight: 700;
  color: #92400e;
  margin: 0 0 4px 0;
  text-align: right;
}

.approval-date {
  font-size: 11px;
  color: #6b7280;
  margin: 0;
  text-align: right;
}

.approval-actions {
  display: flex;
  gap: 8px;
}

.btn-approve,
.btn-reject,
.btn-view {
  flex: 1;
  padding: 8px 12px;
  border: none;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.btn-approve {
  background: #10b981;
  color: white;
}

.btn-approve:hover {
  background: #059669;
}

.btn-reject {
  background: #ef4444;
  color: white;
}

.btn-reject:hover {
  background: #dc2626;
}

.btn-view {
  background: #3b82f6;
  color: white;
}

.btn-view:hover {
  background: #2563eb;
}

.officers-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.officer-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f9fafb;
  border-radius: 8px;
}

.officer-avatar i {
  font-size: 40px;
  color: #1e3a8a;
}

.officer-info {
  flex: 1;
}

.officer-name {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.officer-stats {
  font-size: 12px;
  color: #6b7280;
  margin: 0;
}

.officer-revenue .revenue {
  font-size: 16px;
  font-weight: 700;
  color: #10b981;
  margin: 0;
}

.stock-list {
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

.actions-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.action-card {
  padding: 20px;
  background: #f9fafb;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.action-card:hover {
  background: #eff6ff;
  border-color: #1e3a8a;
  transform: translateY(-2px);
}

.action-card i {
  font-size: 32px;
  color: #1e3a8a;
}

.action-card span {
  font-size: 13px;
  font-weight: 600;
  color: #1f2937;
}

@media (max-width: 1024px) {
  .dashboard-grid,
  .dashboard-grid-2 {
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
