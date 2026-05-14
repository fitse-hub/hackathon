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
    isModerator: (state) => state.user?.role === 'moderator',
    isUser: (state) => state.user?.role === 'user',
    
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
          
          // Store in localStorage for persistence
          localStorage.setItem('user', JSON.stringify(this.user))
          localStorage.setItem('permissions', JSON.stringify(this.permissions))
          localStorage.setItem('isAuthenticated', 'true')
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
          
          // Store in localStorage for persistence
          localStorage.setItem('user', JSON.stringify(this.user))
          localStorage.setItem('permissions', JSON.stringify(this.permissions))
          localStorage.setItem('isAuthenticated', 'true')
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
      // Check localStorage first
      const storedUser = localStorage.getItem('user')
      const storedPermissions = localStorage.getItem('permissions')
      const storedAuth = localStorage.getItem('isAuthenticated')
      
      if (storedUser && storedPermissions && storedAuth === 'true') {
        this.user = JSON.parse(storedUser)
        this.permissions = JSON.parse(storedPermissions)
        this.isAuthenticated = true
      }

      // Verify with server
      try {
        const response = await axios.get('/api/auth/me')
        
        if (response.data.success) {
          this.user = response.data.user
          this.permissions = response.data.permissions
          this.isAuthenticated = true
          
          // Update localStorage
          localStorage.setItem('user', JSON.stringify(this.user))
          localStorage.setItem('permissions', JSON.stringify(this.permissions))
          localStorage.setItem('isAuthenticated', 'true')
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
      
      // Clear localStorage
      localStorage.removeItem('user')
      localStorage.removeItem('permissions')
      localStorage.removeItem('isAuthenticated')
    },

    async getRoles() {
      try {
        const response = await axios.get('/api/auth/roles')
        return response.data.roles
      } catch (error) {
        console.error('Failed to fetch roles:', error)
        return {}
      }
    }
  }
})