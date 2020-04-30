<template>
	<div id="fondo">
		<div class="d-flex justify-content-center pt-5" id="overlay-oscuro">
			<div id="login-panel" class="px-4 pb-3" v-if="this.registrado === false">
				<h3 class="text-center pt-5 pb-4">Registrate</h3>
				<div class="alert alert-danger" v-if="Object.keys(this.errores).length !== 0">
					<ul class="m-0 pl-2" v-for="error in this.errores" :key="error">
						<li>{{error}}</li>
					</ul>
				</div>
                <p class="mb-4">¡Llena los siguientes datos para registrar tu cuenta hoy mismo!</p>
				<form>
                    <div class="form-group">
						<label for="correo">Nombre</label>
						<input type="text" class="form-control" name="nombre" id="nombre"
							placeholder="Ingrese su nombre" v-model="nombre" required>
					</div>
					<div class="form-group">
						<label for="correo">Correo electrónico</label>
						<input type="email" class="form-control" name="correo" id="correo"
							placeholder="Ingrese su correo electrónico" v-model="correo" required>
					</div>
					<div class="form-group">
						<label for="contra">Contraseña</label>
						<input type="password" class="form-control" name="contra" id="contra"
							placeholder="Ingrese su nueva contraseña" v-model="contra" required>
					</div>
					<div class="form-group">
						<label for="contra">Repetir contraseña</label>
						<input type="password" class="form-control" name="contra" id="contra"
							placeholder="Ingrese de nuevo su contraseña" v-model="contra_repetida" required>
					</div>
					<div id="form-group">
						<input type="button" value="Registrarse" class="btn btn-lg btn-block btn-success"
							v-on:click="registrarUsuario()">
					</div>
				</form>
			</div>
            <div id="login-panel" class="px-4 pb-3" v-if="this.registrado === true">
				<h3 class="text-center pt-5 pb-4">Verifique su buzón de email</h3>
                <p class="mb-5">En breve recibirá por correo electrónico una liga para verificar su nueva cuenta.</p>

                <p class="mt-5">Haga clic
                    <router-link to="/">aquí</router-link>
                    para ser redireccionado al login.
                </p>
			</div>
		</div>
	</div>
</template>

<script>
    import axios from 'axios'

	export default {
		name: 'Login',
		data() {
			return {
				nombre: '',
                correo: '',
                contra: '',
				contra_repetida: '',
				errores: {},
                registrado: false
			}
		},
        methods: {
            registrarUsuario() {
                // Creamos unos parametros
                let parametros = new FormData();
                parametros.append("request", "crearCuenta");
                parametros.append("nombre", this.nombre);
                parametros.append("correo", this.correo);
                parametros.append("contra", this.contra);
                parametros.append("contra_repetida", this.contra_repetida);

                // Hacemos una petición AJAX para registrar al usuario
                axios.post('/api/funciones.php', parametros)
                    .then(res => {
						// Verificamos si res.data contiene la propiedad 'errores' para después mandarla al UI
						if (Object.prototype.hasOwnProperty.call(res.data, 'errores')) {
							// Obtenemos los errores
							this.errores = res.data.errores;
						}

                        // Si la consulta se realizó...
                        if (res.data.status === 'OK') {
                            // Indicamos que se ha registrado el usuario
							this.registrado = true;

							// Obtenemos datos de la petición AJAX anterior
							let correo = res.data.correo;
							let clave_de_validacion = res.data.clave_de_validacion;

							/* Enviamos una petición para enviar el correo electrónico */
							// Creamos unos parametros
							let parametros = new FormData();
							parametros.append("request", "enviarCorreo");
							parametros.append("tipoCorreo", "verificarCuenta");
							parametros.append("correo", correo);
							parametros.append("clave_de_validacion", clave_de_validacion);

							// Hacemos una petición AJAX para mandar el correo electrónico
							axios.post('/api/funciones.php', parametros)
								.then(res => {
									console.log(res);
								})
								.catch(err => {
									console.log(err);
								})
                        } else {
							if (res.data.status === 'FAIL') {
								this.errores = res.data.errores;
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