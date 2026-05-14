import { defineStore } from 'pinia'
import axios from '../axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    permissions: null,
    isAuthenticated: false,
    loading: false
  }),

  getters: {
    isAdmin: (state) => state.user?.role === 'admin',
    isManager: (state) => state.user?.role === 'manager',
    isSalesOfficer: (state) => state.user?.role === 'sales_officer',
    
    hasPermission: (state) => (permission) => {
      return state.permissions?.[permission] || false
    }
  },

  actions: {
    async login(credentials) {
      this.loading = true
      try {
        const response = await axios.post('/api/auth/login', credentials)
        
        if (response.data.success) {
          this.user = response.data.user
          this.permissions = response.data.permissions
          this.isAuthenticated = true
        }
        
        return response.data
      } catch (error) {
        this.clearAuth()
        throw error
      } finally {
        this.loading = false
      }
    },

    async register(userData) {
      this.loading = true
      try {
        const response = await axios.post('/api/auth/register', userData)
        
        if (response.data.success) {
          this.user = response.data.user
          this.permissions = response.data.permissions
          this.isAuthenticated = true
        }
        
        return response.data
      } catch (error) {
        this.clearAuth()
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      this.loading = true
      try {
        await axios.post('/api/auth/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.clearAuth()
        this.loading = false
      }
    },

    async checkAuth() {
      try {
        const response = await axios.get('/api/auth/me')
        
        if (response.data.success) {
          this.user = response.data.user
          this.permissions = response.data.permissions
          this.isAuthenticated = true
        } else {
          this.clearAuth()
        }
      } catch (error) {
        this.clearAuth()
      }
    },

    clearAuth() {
      this.user = null
      this.permissions = null
      this.isAuthenticated = false
    }
  }
})