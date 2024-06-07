import { createApp } from 'vue'
import App from './App.vue'
import { createRouter, createWebHistory } from 'vue-router'
import './index.css'
import store from './store'
import MainPage from './views/MainPage.vue'
import AuthForm from './views/userAuthForm.vue'
import registerPage from './views/registerPage.vue'

const router = createRouter({
  routes: [
    {
      path: '/',
      component: MainPage
    },
    {
      path: '/auth',
      component: AuthForm
    },
    {
      path: '/registerPage',
      component: registerPage
    }
  ],
  history: createWebHistory()
})

const app = createApp(App)

app.use(router)
app.mount('#app')
app.use(store)
