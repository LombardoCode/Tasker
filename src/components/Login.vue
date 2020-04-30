<template>
	<div id="fondo">
		<div class="d-flex justify-content-center pt-5" id="overlay-oscuro">
			<div id="login-panel" class="px-4 pb-3">
				<h3 class="text-center pt-5 pb-4">Ingresar al sistema</h3>
				<div class="alert alert-danger" v-if="Object.keys(this.errores).length !== 0">
					<ul class="m-0 pl-2" v-for="error in this.errores" :key="error">
						<li>{{error}}</li>
					</ul>
				</div>
				<form>
					<div class="form-group">
						<label for="correo">Correo electrónico</label>
						<input type="email" class="form-control" name="correo" id="correo"
							placeholder="Ingrese su correo electrónico" v-model="correo">
					</div>
					<div class="form-group">
						<label for="contra">Contraseña</label>
						<input type="password" class="form-control" name="contra" id="contra"
							placeholder="Ingrese su contraseña" v-model="contra">
					</div>
					<div id="form-group">
						<input type="button" value="Ingresar" class="btn btn-lg btn-block btn-primary"
							v-on:click="loguearUsuario()">
					</div>
				</form>
				<p class="mt-3">Registrate hoy mismo haciendo clic 
					<router-link to="/registrarse">aqui</router-link>.
				</p>
			</div>
		</div>
	</div>
</template>

<script>
	import axios from 'axios';
	export default {
		name: 'Login',
		data() {
			return {
				correo: '',
				contra: '',
				errores: {}
			}
		},
		mounted: function () {
			this.obtenerDatosUsuario();
		},
		methods: {
			obtenerDatosUsuario() {
				// Creamos unos parametros
				let parametros = new FormData();
				parametros.append("request", "obtenerUsuario")
				axios.post('/api/funciones.php', parametros)
					.then(res => {
						console.log(res);
						// Si obtenemos datos de un uusario con sesión iniciada...
						if (res.data.status === 'OK') {
							// Redirigimos al usuario al Dashboard
							this.$router.push({
								name: 'Tareas'
							});
						}
					})
					.catch(err => {
						console.log(err);
					})
			},
			loguearUsuario() {
				// Almacenamos los datos del usuario
				let parametros = new FormData();
				parametros.append("request", "loginUsuario");
				parametros.append("correo", this.correo);
				parametros.append("contra", this.contra);

				// Los validamos si son correctos
				axios.post('/api/funciones.php', parametros)
					.then(res => {
						// Si los datos son correctos...
						if (res.data.status === 'OK') {
							console.log("Los datos son correctos");
							console.log(res);
							// Redirigimos al usuario al Dashboard
							this.$router.push({
								name: 'Tareas'
							});
						} else {
							if (res.data.status === 'FAIL') {
								console.log(res);
								// Verificamos si res.data contiene la propiedad 'errores' para después mandarla al UI
								if (Object.prototype.hasOwnProperty.call(res.data, 'errores')) {
									// Obtenemos los errores
									this.errores = res.data.errores;
								}
							}
						}
					})
					.catch(err => {
						console.log(err);
					})
			}
		}
	}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
	#fondo {
		width: 100%;
		height: 100vh;
		background-image: url("https://c1.wallpaperflare.com/preview/372/239/789/paper-office-background-blog-blogger-browsing.jpg");
		background-position: center;
		background-repeat: no-repeat;
		background-size: cover;
	}

	#overlay-oscuro {
		width: 100%;
		height: 100%;
		background: linear-gradient(to bottom, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.9));
	}

	#login-panel {
		width: 500px;
		height: max-content;
		background: #FFFFFF;
		border-radius: 10px;
	}
</style>