<template>
    <div>
        <div id="contenido" class="d-flex">
            <PanelCuenta :nombre="this.nombre"/>
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import PanelCuenta from './PanelCuenta'

export default {
    name: 'Dashboard',
    components: {
        PanelCuenta
    },
    data() {
        return {
            id: 0,
            correo: '',
            nombre: '',
        }
    },
    mounted: function() {
        // Obtenemos los datos del usuario
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
                    // Si los datos son correctos...
                    if (res.data.status === 'OK') {
                        // Guardamos los datos del usuario
                        this.id = parseInt(res.data.id);
                        this.correo = res.data.correo;
                        this.nombre = res.data.nombre;
                    } else {
                        // Si los datos no son correctos...
                        if (res.data.status === 'FAIL') {
                            // ...significa que no se identificó al usuario y se redireccionará
                            // a la página de login
                            this.$router.push({ name: 'Login' })
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

<style scoped>
    #panel_derecho {
        background-color: rgb(230, 230, 230);
    }
</style>