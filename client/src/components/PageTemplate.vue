<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="main-content">
      <div class="page-header">
        <h1 class="page-title">{{ title }}</h1>
        <p class="page-subtitle">{{ subtitle }}</p>
      </div>
      
      <div class="coming-soon">
        <i :class="icon"></i>
        <h2>{{ title }}</h2>
        <p>{{ message || 'This page is under construction. Backend API needed.' }}</p>
      </div>
    </main>
  </div>
</template>

<script setup>
import Sidebar from './Sidebar.vue'

defineProps({
  title: String,
  subtitle: String,
  icon: String,
  message: String
})
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
}

.page-header {
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

.coming-soon {
  background: white;
  border-radius: 12px;
  padding: 80px 40px;
  text-align: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.coming-soon i {
  font-size: 64px;
  color: #1e3a8a;
  margin-bottom: 20px;
}

.coming-soon h2 {
  font-size: 24px;
  color: #1f2937;
  margin: 0 0 12px 0;
}

.coming-soon p {
  font-size: 16px;
  color: #6b7280;
  margin: 0;
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
  }
}
</style>
