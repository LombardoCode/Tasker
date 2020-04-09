<template>
    <div>
        <div id="contenido" class="d-flex">
            <div id="panel_izquierdo" class="px-4">
                <button class="btn btn-danger btn-block" v-on:click="cerrar_sesion()">Cerrar sesión</button>
            </div>
            <div id="panel_derecho">
                <h1>Bienvenido de vuelta, {{this.nombre}}.</h1>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'Dashboard',
    data() {
        return {
            id: '',
            correo: '',
            nombre: ''
        }
    },
    mounted: function() {
        this.obtenerDatosUsuario();
    },
    methods: {
        obtenerDatosUsuario() {
            // Creamos unos parametros
            let parametros = new FormData();
            parametros.append("request", "obtenerDatosUsuario");

            // Obtenemos los datos del usuario
            axios.post('/api/funciones.php', parametros)
                .then(res => {
                    console.log(res);
                    // Guardamos los datos del usuario
                    this.id = res.data.id;
                    this.correo = res.data.correo;
                    this.nombre = res.data.nombre;

                    // Si los datos son correctos...
                    if (res.data.status === 'OK') {
                        console.log("Usuario " + this.nombre + " satisfactoriamente logueado.");
                    } else {
                        // Si los datos no son correctos...
                        if (res.data.status === 'FAIL') {
                            // ...significa que no se identificó al usuario y se redireccionará
                            // a la página de login
                            console.log("El usuario no ha sido identificado.");
                            this.$router.push({ name: 'Login' })
                        }
                    }
                })
                .catch(err => {
                    console.log(err);
                })
        },
        cerrar_sesion() {
            axios.get('/api/cerrar_sesion.php')
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
        }
    }
}
</script>

<style>
    #panel_izquierdo {
        width: 300px;
        height: 100vh;
        background-color: #1b1b1b;
    }
</style>