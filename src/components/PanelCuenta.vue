<template>
    <div id="panel_izquierdo" class="px-3 d-flex align-items-center flex-column pt-4">
        <img :src="require('@/assets/foto_default.png')" alt="" class="rounded rounded-circle w-75 mb-3">
        <h4 class="text-white text-center">{{this.nombre}}</h4>
        <div id="opciones-cuenta" class="mt-2 w-100">
            <li class="py-2"><router-link to="/tareas"><i class="fas fa-clipboard-list mr-2"></i>Tareas / Recordatorios</router-link></li>

            <li class="py-2 d-flex align-items-center"><a class="nav-link link-collapse px-0" data-toggle="collapse" data-target="#configurar-contra"><i class="fas fa-cog mr-2"></i>Configuración de cuenta</a><i class="fas fa-caret-down text-white ml-2"></i></li>

            <div class="collapse" id="configurar-contra">
                <router-link to="/ajustes?tab=general" class="nav-link" v-on:click.native="obtenerAccion($event)">Datos personales</router-link>
                <router-link to="/ajustes?tab=clave" class="nav-link">Cambiar contraseña</router-link>
                <a class="nav-link">123</a>
            </div>

            <button class="btn btn-danger btn-block mt-4" v-on:click="cerrar_sesion()">Cerrar sesión</button>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'PanelCuenta',
    props: {
        nombre: String
    },
    data() {
        return {
            accion_cuenta: ''
        }
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
            //let accion = event.target.attributes.accion.value;
            console.log(event);
        }
    }
}
</script>

<style scoped>
    #panel_izquierdo {
        max-width: 300px;
        height: 100vh;
        background-color: #1b1b1b;
    }
    
    img {
        max-width: 100%;
    }

    a, .nav-link {
        color: #FFFFFF !important;
    }

    .link-collapse {
        cursor: pointer;
    }
</style>