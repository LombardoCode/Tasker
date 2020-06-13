<template>
    <div>
        <div class="d-lg-none">
            <nav class="navbar navbar-expand-lg navbar-dark navbarFondo">
                <a class="navbar-brand" href="#">Tasker</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"><router-link class="nav-link" to="/tareas"><i class="fas fa-clipboard-list mr-2"></i>Tareas / Recordatorios</router-link></li>
                        <li class="nav-item"><router-link class="nav-link" to="/ajustes"><i class="fas fa-clipboard-list mr-2"></i>Configuración de cuenta</router-link></li>
                        <button class="btn btn-danger btn-block mt-4" v-on:click="cerrar_sesion()">Cerrar sesión</button>
                    </ul>
                </div>
            </nav>
        </div>


        <div class="d-none d-lg-block">
            <div id="panel_izquierdo" class="px-3 pt-4 d-flex flex-column align-items-center">
                <div id="marco_foto" class="rounded rounded-circle">
                    <img v-if="this.foto_perfil_nombre === null" src="@/assets/foto_default.png" alt="" class="rounded rounded-circle">
                    <img v-if="this.foto_perfil_nombre !== null" :src="`@/../img/fotos_de_perfil/${this.foto_perfil_nombre}`" alt="" class="rounded rounded-circle">
                    <img v-if="this.foto_perfil_blob !== null" :src="this.foto_perfil_blob" alt="" class="rounded rounded-circle">
                </div>
                <h4 class="text-white text-center mt-3">{{this.nombre}}</h4>
                <div id="opciones-cuenta" class="mt-3 w-100">
                    <li class="py-2"><router-link to="/tareas"><i class="fas fa-clipboard-list mr-2"></i>Tareas / Recordatorios</router-link></li>

                    <li class="py-2 d-flex align-items-center"><router-link to="/ajustes" class="nav-link link-collapse px-0"><i class="fas fa-cog mr-2"></i>Configuración de cuenta</router-link></li>

                    <button class="btn btn-danger btn-block mt-4" v-on:click="cerrar_sesion()">Cerrar sesión</button>
                </div>
            </div>
        </div>
        
    </div>
    
</template>

<script>
import axios from 'axios'

export default {
    name: 'PanelCuenta',
    props: {
        nombre: String,
        foto_perfil_blob: String
    },
    data() {
        return {
            accion_cuenta: '',
            foto_perfil_nombre: null,
            foto_perfi_blob: ''
        }
    },
    mounted() {
        this.obtenerFotoPerfil();
    },
    methods: {
        cerrar_sesion() {
            // Creamos unos parametros
            let parametros = new FormData();
            parametros.append("request", "cerrarSesion");

            axios.post('/api/funciones.php', parametros)
                .then(res => {
                    console.log(res);
                    // Si los datos son correctos...
                    if (res.data.status === 'OK') {
                        // Redireccionamos al usuario al login
                        this.$router.push({name: 'Login'});
                    }
                })
                .catch(err => {
                    console.log(err);
                })
        },
        obtenerAccion(event) {
            // Obtenemos el valor del atributo
            console.log(event);
        },
        obtenerFotoPerfil() {
            // Creamos unos parametros
            let parametros = new FormData();
            parametros.append('request', "obtenerFotoPerfil");

            axios.post('/api/funciones.php', parametros)
                .then(res => {
                    console.log(res);
                    if (res.data.status === 'OK') {
                        this.foto_perfil_nombre = res.data.foto_perfil;
                    }
                })
                .catch(err => {
                    console.log(err);
                })
        }
    },
    watch: {
        foto_perfil_blob: function(foto) {
            // Hacemos que el nombre de la foto de perfil sea nulo para que Vue le haga caso a la foto con el blob incorporado
            this.foto_perfil_nombre = null;

            // Le indicamos la nueva foto de perfil
            this.foto_perfil_blob = foto;
        }
    }
}
</script>

<style scoped>
    #panel_izquierdo {
        width: 320px;
        height: 100vh;
        background-color: #1b1b1b;
    }

    #marco_foto {
        position: relative;
        width: 220px;
        height: 220px;
        overflow: hidden;
    }
    
    img {
        scale: 102%;
        position: absolute;
        overflow: hidden;
        object-fit: cover;
        width: 100%;
        height: 100%;
    }

    a, .nav-link {
        color: #FFFFFF !important;
    }

    .navbarFondo {
        background-color: #1b1b1b;
    }

    .link-collapse {
        cursor: pointer;
    }
</style>