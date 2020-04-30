import Vue from 'vue'
import App from './App.vue'
import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.css'
import VueRouter from 'vue-router'
import Login from './components/Login.vue'
import Registrarse from './components/Registrarse.vue'
import Dashboard from './components/Dashboard.vue'
import Tareas from './components/Tareas.vue'
import ConfigCuenta from './components/ConfigCuenta.vue'
import Verificar from './components/Verificar.vue'
import '@fortawesome/fontawesome-free/css/all.css'

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
			path: '/verificar',
			name: 'verificar',
			component: Verificar
		},
		{
			path: '/dashboard',
			name: 'Dashboard',
			component: Dashboard,
			children: [
				{
					path: '/tareas',
					name: 'Tareas',
					component: Tareas
				},
				{
					path: '/ajustes',
					name: 'ConfigCuenta',
					component: ConfigCuenta
				}
			]
		}
	]
});

new Vue({
	render: h => h(App),
	router: Router
}).$mount('#app')


