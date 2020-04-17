import Vue from 'vue'
import App from './App.vue'
import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.css'
import VueRouter from 'vue-router'
import Login from './components/Login.vue'
import Registrarse from './components/Registrarse.vue'
import Dashboard from './components/Dashboard.vue'

Vue.config.productionTip = false
Vue.use(VueRouter)

const Router = new VueRouter({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'Login',
      component: Login
    },
    {
      path: '/registrarse',
      name: 'Registrarse',
      component: Registrarse
    },
    {
      path: '/dashboard',
      name: 'Dashboard',
      component: Dashboard
    }
  ]
});

new Vue({
  render: h => h(App),
  router: Router
}).$mount('#app')
