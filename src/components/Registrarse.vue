<template>
	<div id="fondo">
		<div class="d-flex justify-content-center pt-5" id="overlay-oscuro">
			<div id="login-panel" class="px-4 pb-3" v-if="this.registrado === false">
				<h3 class="text-center pt-5 pb-4">Registrate</h3>
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
							placeholder="Ingrese su contraseña" v-model="contra" required>
					</div>
					<div id="form-group">
						<input type="button" value="Registrarse" class="btn btn-lg btn-block btn-success"
							v-on:click="registrarUsuario()">
					</div>
				</form>
			</div>
            <div id="login-panel" class="px-4 pb-3" v-if="this.registrado === true">
				<h3 class="text-center pt-5 pb-4">Registrado</h3>
                <p class="mb-5">¡Ha sido registrado en el sistema! Será redireccionado al login en breve...</p>

                <p class="mt-5">...O si no puedes hacer click 
                    <router-link to="/">aqui</router-link>
                    para ser redireccionado para iniciar sesión.
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

                // Hacemos una petición AJAX para registrar al usuario
                axios.post('/api/funciones.php', parametros)
                    .then(res => {
                        console.log(res);
                        // Si la consulta se realizó...
                        if (res.data.status === 'OK') {
                            // Indicamos que se ha registrado el usuario
                            this.registrado = true
                        }
                    })
                    .catch(err => {
                        console.log(err);
                    })
            }
        },
        watch: {
            registrado() {
                setTimeout(() => {
                    this.$router.push({ name: 'Login' });
                }, 6000);
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