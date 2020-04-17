<template>
	<div id="fondo">
		<div class="d-flex justify-content-center pt-5" id="overlay-oscuro">
			<div id="login-panel" class="px-4 pb-3">
				<h3 class="text-center pt-5 pb-4">Ingresar al sistema</h3>
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
				contra: ''
			}
		},
		mounted: function () {
			this.obtenerDatosUsuario();
		},
		methods: {
			obtenerDatosUsuario() {
				// Creamos unos parametros
				let parametros = new FormData();
				parametros.append("request", "obtenerDatosUsuario")
				axios.post('/api/funciones.php', parametros)
					.then(res => {
						console.log(res);
						// Si obtenemos datos de un uusario con sesión iniciada...
						if (res.data.status === 'OK') {
							// Redirigimos al usuario al Dashboard
							this.$router.push({
								name: 'Dashboard'
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
				parametros.append("request", "validarUsuario");
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
								name: 'Dashboard'
							});
						} else {
							if (res.data.status === 'FAIL') {
								console.log("Los datos son INCORRECTOS");
								console.log(res);
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