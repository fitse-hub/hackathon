import { createApp } from 'vue'
import { createPinia } from 'pinia'
import axios from './axios'

import App from './App.vue'
import router from './router'

const app = createApp(App)

// Make axios available globally
app.config.globalProperties.$axios = axios

app.use(createPinia())
app.use(router)

app.mount('#app')

