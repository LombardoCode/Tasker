<template>
    <div id="componente">
        <!-- Interfaz para cambiar los datos generales de la cuenta -->
        <div class="flex-fill px-4 pt-4">
            <h3 class="mb-4">Configuración de cuenta</h3>

            <!-- Pestañas de configuración -->            
            <div class="accordion">
                <!-- Cambiar información general de cuenta -->
                <div class="card">
                    <div class="card-header">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#InformacionCuenta">
                            <i class="fas fa-user mr-2"></i>
                            Información de cuenta
                        </button>
                    </div>
                    <div id="InformacionCuenta" class="collapse show">
                        <div class="card-body">
                            <div class="alert alert-danger" v-if="Object.keys(this.erroresDatosCuenta).length !== 0">
                                <ul class="m-0 pl-2" v-for="error in this.erroresDatosCuenta" :key="error">
                                    <li>{{error}}</li>
                                </ul>
                            </div>
                            <form enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" name="nombre" id="" class="form-control" v-model="nombre">
                                </div>
                                <div class="form-group">
                                    <label for="foto_perfil">Foto de perfil</label>
                                    <input type="file" class="form-control-file" id="" name="foto_perfil" @change="procesarImagen">
                                    <div id="visualizador-foto" class="mt-2" v-if="this.foto_perfil_blob">
                                        <img :src="this.foto_perfil_blob" alt="">
                                    </div>
                                </div>
                                <div class="form-group d-flex justify-content-end">
                                    <input type="button" value="Guardar cambios" class="btn btn-primary" v-on:click="actualizarDatos()">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Cambiar correo -->
                <div class="card">
                    <div class="card-header">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#CambiarCorreo">
                            <i class="fas fa-envelope mr-2"></i>
                            Cambiar correo
                        </button>
                    </div>
                    <div id="CambiarCorreo" class="collapse">
                        <div class="card-body">
                            <div v-if="verificadorContra === false">
                                <div v-if="correoYaExiste === true">
                                    <div class="alert alert-danger" v-for="error in this.error" :key="error">
                                        {{error}}
                                    </div>
                                </div>
                                <form>
                                    <div class="form-group">
                                        <label for="correo">Correo</label>
                                        <input type="email" name="correo" id="" class="form-control" v-model="correo">
                                    </div>
                                    <div class="form-group d-flex justify-content-end">
                                        <input type="button" value="Guardar cambios" class="btn btn-primary" v-on:click="habilitarVerificacionContra()">
                                    </div>
                                </form>
                            </div>
                            <div v-else>
                                <p>Hemos enviado un código de verificación al correo {{this.correo}}. Por favor, ingrese el código.</p>
                                <form>
                                    <div class="form-group d-flex">
                                        <input type="text" name="" id="" class="form-control" v-model="claveDeVerificacion">
                                    </div>
                                    <div class="form-group d-flex justify-content-end">
                                        <input type="button" value="Guardar cambios" class="btn btn-primary" v-on:click="actualizarCorreo()">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Cambiar contraseña -->
                <div class="card">
                    <div class="card-header">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#CambiarContra">
                            <i class="fas fa-key mr-2"></i>
                            Cambiar contraseña
                        </button>
                    </div>
                    <div id="CambiarContra" class="collapse">
                        <div class="card-body">
                            <div class="alert alert-danger" v-if="Object.keys(this.erroresContra).length !== 0">
                                <ul class="m-0 pl-2" v-for="error in this.erroresContra" :key="error">
                                    <li>{{error}}</li>
                                </ul>
                            </div>
                            <form>
                                <div class="form-group">
                                    <label for="contra_actual">Contraseña actual</label>
                                    <input type="password" name="contra_actual" id="" class="form-control" v-model="contra_actual">
                                </div>
                                <div class="form-group">
                                    <label for="nueva_contra">Contraseña nueva</label>
                                    <input type="password" name="nueva_contra" id="" class="form-control" v-model="contra_nueva">
                                </div>
                                <div class="form-group">
                                    <label for="nueva_contra_repetida">Repetir nueva contraseña</label>
                                    <input type="password" name="nueva_contra_repetida" id="" class="form-control" v-model="contra_nueva_repetida">
                                </div>
                                <div class="form-group d-flex justify-content-end">
                                    <input type="button" value="Guardar contraseña" class="btn btn-primary" v-on:click="actualizarContra()">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Eliminar la cuenta -->
                <div class="card">
                    <div class="card-header">
                        <button class="btn btn-link text-danger" data-toggle="collapse" data-target="#EliminarCuenta">
                            <i class="fas fa-minus-circle"></i>
                            Eliminar cuenta
                        </button>
                    </div>
                    <div id="EliminarCuenta" class="collapse">
                        <div class="card-body">
                            <div class="alert alert-danger" v-if="Object.keys(this.erroresEliminacionCuenta).length !== 0">
                                <ul class="m-0 pl-2" v-for="error in this.erroresEliminacionCuenta" :key="error">
                                    <li>{{error}}</li>
                                </ul>
                            </div>
                            <p>Para eliminar por completo su cuenta escriba su contraseña:</p>
                            <form>
                                <div class="form-group">
                                    <input type="password" name="" id="" class="form-control" placeholder="Ingrese su contraseña..." v-model="contraEliminacionCuenta">
                                </div>
                                <div class="form-group d-flex justify-content-end">
                                    <input type="button" value="Eliminar cuenta" class="btn btn-danger" v-on:click="verificarContra()">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para eliminar la cuenta del usuario -->
            <div class="modal fade" id="modalEliminarCuenta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
                <!-- Modal para editar tareas -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Eliminación de cuenta</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>¿Realmente quiere eliminar su cuenta de Tasker? Al hacerlo, ya no podrá acceder a su cuenta y todos sus datos serán eliminados una vez que elimine su cuenta. <span class="font-weight-bold">Este paso no se puede revertir.</span></p>
                            
                        </div>
                        <div class="modal-footer">
                            <form>
                                <div class="form-group">
                                    <input type="button" value="Cancelar" class="btn btn-secondary mr-2" data-dismiss="modal" aria-label="Close">
                                    <input type="button" value="Lo entiendo y quiero eliminar mi cuenta" class="btn btn-danger" data-dismiss="modal" v-on:click="eliminarCuenta()">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import iziToast from 'izitoast'
import $ from 'jquery'

export default {
    mounted() {
        this.obtenerDatosUsuario();
    },
    data() {
        return {
            nombre: '',
            foto_perfil: null,
            foto_perfil_blob: null,
            correo: '',
            erroresDatosCuenta: {},
            verificadorContra: false,
            claveDeVerificacion: null,
            correoYaExiste: false,
            error: {},
            erroresContra: {},
            contra_actual: '',
            contra_nueva: '',
            contra_nueva_repetida: '',
            contraEliminacionCuenta: '',
            erroresEliminacionCuenta: {}
        }
    },
    methods: {
        obtenerDatosUsuario() {
            // Creamos unos parametros
            let parametros = new FormData();
            parametros.append("request", "obtenerDatosUsuario");

            // Creamos una petición AJAX
            axios.post("/api/funciones.php", parametros)
                .then(res => {
                    // Obtenemos los datos
                    let datos = res.data;

                    // Asignamos los datos
                    this.nombre = datos.nombre;
                    this.correo = datos.correo;
                })
                .catch(err => {
                    console.log(err);
                })
        },
        procesarImagen(e) {
            this.foto_perfil = e.target.files[0];
            this.foto_perfil_blob = URL.createObjectURL(this.foto_perfil);
            console.log(this.foto_perfil);
            console.log(this.foto_perfil_blob);
        },
        actualizarDatos() {
            // Creamos unos parametros
            let parametros = new FormData();
            parametros.append("request", "actualizarCuenta");
            parametros.append("subrequest", "actualizarDatosGenerales");
            parametros.append("nombre", this.nombre);
            parametros.append("foto_perfil", this.foto_perfil)

            // Creamos una petición AJAX
            axios.post('/api/funciones.php', parametros)
                .then(res => {
                    console.log(res);
                    if (res.data.status === 'OK') {
                        // Mostramos por notificación que los datos fueron guardados exitósamente
                        iziToast.success({
                            title: '¡Excelente!',
                            message: 'Los datos se han guardado satisfactoriamente.',
                            position: 'topRight',
                        });

                        // Limpiamos los errores
                        this.erroresDatosCuenta = {};

                        // Emitimos unas señales para mandar información al componente padre
                        this.$emit('labelNombre', res.data.resultados.nombre);
                        this.$emit('fotoPerfilBlob', this.foto_perfil_blob);
                    } else {
                        if (res.data.status === 'FAIL') {
                            // Verificamos si dentro de la respuesta tenemos una llave de errores
                            if (Object.prototype.hasOwnProperty.call(res.data, 'errores')) {
                                // Obtenemos los errores
                                this.erroresDatosCuenta = res.data.errores;
                            }
                        }
                    }
                })
                .catch(err => {
                    console.log(err);
                })
        },
        habilitarVerificacionContra() {
            // Creamos unos parametros
            let parametros = new FormData();
            parametros.append('request', 'actualizarCuenta');
            parametros.append('subrequest', 'enviarCambioCorreo');
            parametros.append('correo', this.correo);

            // Creamos una petición AJAX
            axios.post('/api/funciones.php', parametros)
                .then(res => {
                    if (res.data.status === 'OK') {
                        // Le ponemos el verificador al usuario
                        this.verificadorContra = true;

                        // Le deshabilitamos el error al usuario ("Ya existe un correo similar en nuestra base de datos...")
                        this.correoYaExiste = false;

                        // Creamos unos parametros
                        let parametros = new FormData();
                        parametros.append('request', 'enviarCorreo');
                        parametros.append('tipoCorreo', 'cambiarCorreo');
                        parametros.append('correo', this.correo);

                        // Creamos una petición AJAX para enviar el correo electrónico con el código
                        axios.post('/api/funciones.php', parametros)
                            .then(res => {
                                console.log(res);
                            })
                            .catch(err => {
                                console.log(err);
                            })
                    } else {
                        if (res.data.status === 'FAIL') {
                            // Verificamos si el correo existe
                            if (res.data.correoExistente === true) {
                                // Notificamos que el correo ya existe
                                this.correoYaExiste = true;

                                // Obtenemos los errores
                                this.error = res.data.error;
                            }
                        }
                    }
                })
                .catch(err => {
                    console.log(err);
                })
        },
        actualizarCorreo() {
            // Creamos unos parámetros
            let parametros = new FormData();
            parametros.append('request', 'actualizarCuenta');
            parametros.append('subrequest', 'actualizarCorreo');
            parametros.append('correo', this.correo);
            parametros.append('claveDeVerificacion', this.claveDeVerificacion);

            // Creamos una petición AJAX
            axios.post('/api/funciones.php', parametros)
                .then(res => {
                    console.log(res);
                    if (res.data.status === 'OK') {
                        this.verificadorContra = false;

                        iziToast.success({
                            title: '¡Excelente!',
                            message: 'Se ha guardado el correo satisfactoriamente.',
                            position: 'topRight',
                        });
                    } else {
                        iziToast.error({
                            title: '¡Error!',
                            message: 'El código ingresado es incorrecto. Por favor, verifíquelo.',
                            position: 'topRight',
                        });
                    }
                })
                .catch(err => {
                    console.log(err);
                })
        },
        actualizarContra() {
            // Creamos unos parametros
            let parametros = new FormData();
            parametros.append('request', 'actualizarCuenta');
            parametros.append('subrequest', 'actualizarContra');
            parametros.append('contra_actual', this.contra_actual);
            parametros.append('contra_nueva', this.contra_nueva);
            parametros.append('contra_nueva_repetida', this.contra_nueva_repetida);

            // Hacemos una petición AJAX
            axios.post('/api/funciones.php', parametros)
                .then(res => {
                    console.log(res);
                    if (res.data.status === 'OK') {
                        this.erroresContra = {};

                        // Notificamos al usuario que la contraseña ha sido modificada
                        iziToast.success({
                            title: '¡Excelente!',
                            message: 'Se ha guardado la contraseña satisfactoriamente.',
                            position: 'topRight',
                        });

                        // Reseteamos los campos
                        this.contra_actual = '';
                        this.contra_nueva = '';
                        this.contra_nueva_repetida = '';
                    } else {
                        // Obtenemos los errores para mostrarlos al usuario
                        this.erroresContra = res.data.errores;
                    }
                })
                .catch(err => {
                    console.log(err);
                })
        },
        verificarContra() {
            // Creamos unos parametros
            let parametros = new FormData();
            parametros.append("request", "verificarContra");
            parametros.append("contra", this.contraEliminacionCuenta);

            // Creamos una petición AJAX
            axios.post('/api/funciones.php', parametros)
                .then(res => {
                    // Si recibimos una respuesta positiva
                    if (res.data.status === 'OK') {
                        // Eliminamos los errores
                        this.erroresEliminacionCuenta = {};

                        // Le mostramos al usuario el modal para eliminar su cuenta
                        $('#modalEliminarCuenta').modal();
                    } else {
                        if (res.data.status === 'FAIL') {
                            // Obtenemos los errores y se los mostramos al usuario
                            this.erroresEliminacionCuenta = res.data.errores;
                        }
                    }
                })
                .catch(err => {
                    console.log(err);
                })
        },
        eliminarCuenta() {
            // Creamos unos parametros
            let parametros = new FormData();
            parametros.append("request", "eliminarCuenta");

            // Creamos una petición AJAX
            axios.post('/api/funciones.php', parametros)
                .then(res => {
                    // Si la respuesta es positiva...
                    if (res.data.status === 'OK') {
                        // Redireccionamos al usuario al login en 1/2 segundo
                        setTimeout(() => {
                            this.$router.push({name: 'Login'});
                        }, 500);
                    }
                })
                .catch(err => {
                    console.log(err);
                })
        }
    }
}
</script>

<style scoped>
    #componente {
        max-width: 1100px;
    }

    h3 {
        font-family: 'Roboto', 'Arial', sans-serif;
        font-weight: 300;
        font-size: 2rem;
    }

    .card-panel {
        width: 100%;
        height: auto;
        border-radius: 10px;
        overflow: hidden;
    }

    .titulo-card {
        width: 100%;
        height: auto;
    }

    .sombra-card {
        box-shadow: 0px 4px 0px 0px rgb(182, 182, 182);
    }

    button {
        text-decoration: none !important;
    }

    #visualizador-foto {
        width: 200px;
    }

    #visualizador-foto img {
        max-width: 100%;
    }
</style>