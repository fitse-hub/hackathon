<template>
  <div class="home-page">
    <!-- Header -->
    <header class="header">
      <div class="header-content">
        <h1 class="system-title">Qelemeda Payroll System</h1>
        <router-link to="/login" class="login-btn-header">Login</router-link>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-container">
        <!-- Left Content -->
        <div class="hero-content">
          <h1 class="hero-title">
            Modernized Payroll System for Seamless <span class="highlight">Process.</span>
          </h1>
          <p class="hero-subtitle">
            Platform with digital solution that is robust, secure and scalable
          </p>
          <button class="explore-btn" @click="exploreDashboard">
            Explore Dashboard
          </button>
        </div>

        <!-- Right Illustration -->
        <div class="hero-illustration">
          <img src="/hero-illustration.png" alt="Payroll System Illustration" class="hero-image" />
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const exploreDashboard = () => {
  // Check if user is authenticated
  const isAuthenticated = localStorage.getItem('isAuthenticated')
  
  if (isAuthenticated === 'true') {
    router.push('/dashboard')
  } else {
    router.push('/login')
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

.home-page {
  min-height: 100vh;
  background-color: #f8f9fa;
  font-family: 'Outfit', sans-serif;
}

/* Header - Same as Login Page */
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
  text-decoration: none;
  display: inline-block;
}

.login-btn-header:hover {
  background-color: #f59e0b;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

/* Hero Section */
.hero-section {
  min-height: calc(vh - 64px);
  display: flex;
  align-items: center;
  padding: 60px 40px;
  background-color: #f8f9fa;
}

.hero-container {
  max-width: 1400px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 80px;
  align-items: center;
  width: 100%;
}

/* Left Content */
.hero-content {
  max-width: 600px;
  margin-left: 20px;
}

.hero-title {
  font-size: 45px;
  font-weight: 700;
  color: #1a1a1a;
  line-height: 1.2;
  margin-bottom: 24px;
  font-family: 'Outfit', sans-serif;
}

.hero-title .highlight {
  color: #1e3a8a;
}

.hero-subtitle {
  font-size: 18px;
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 36px;
  font-family: 'Outfit', sans-serif;
}

.explore-btn {
  background-color: #1e3a8a;
  color: white;
  border: none;
  padding: 16px 36px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: 'Outfit', sans-serif;
}

.explore-btn:hover {
  background-color: #f59e0b;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
}

.explore-btn:active {
  transform: translateY(0);
}

/* Right Illustration */
.hero-illustration {
  display: flex;
  justify-content: center;
  align-items: center;
}

.hero-image {
  width: 100%;
  max-width: 600px;
  height: auto;
  object-fit: contain;
  filter: drop-shadow(0 10px 40px rgba(0, 0, 0, 0.1));
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-20px);
  }
}

/* Responsive Design */
@media (max-width: 1024px) {
  .hero-container {
    gap: 60px;
  }
  
  .hero-title {
    font-size: 48px;
  }
  
  .hero-image {
    max-width: 500px;
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
  
  .hero-section {
    padding: 40px 24px;
    min-height: auto;
  }
  
  .hero-container {
    grid-template-columns: 1fr;
    gap: 40px;
  }
  
  .hero-content {
    text-align: center;
  }
  
  .hero-title {
    font-size: 40px;
  }
  
  .hero-subtitle {
    font-size: 16px;
  }
  
  .hero-image {
    max-width: 450px;
  }
  
  .illustration-icon {
    font-size: 60px;
  }
  
  .illustration-text {
    font-size: 18px;
  }
}

@media (max-width: 480px) {
  .header-content {
    padding: 12px 16px;
  }
  
  .system-title {
    font-size: 16px;
  }
  
  .hero-title {
    font-size: 32px;
  }
  
  .hero-subtitle {
    font-size: 15px;
  }
  
  .explore-btn {
    width: 100%;
    padding: 14px 28px;
  }
  
  .hero-image {
    max-width: 350px;
  }
}
</style>
