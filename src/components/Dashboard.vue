<template>
    <div>
        <div v-if="this.widthVentana > 576">
            <div id="contenido" class="d-lg-flex">
                <PanelCuenta :nombre="this.nombre" :foto_perfil_blob="this.foto_perfil_blob"/>
                <router-view class="flex-lg-fill" @labelNombre="actualizarLabelNombre" @fotoPerfilBlob="actualizarFoto"></router-view>
            </div>
        </div>
        <div v-else>
            <div class="overlay d-flex flex-column align-items-center justify-content-center px-5">
                <i class="far fa-frown text-white fa-10x"></i>
                <h4 class="text-white mt-2 display-4">Lo sentimos.</h4>
                <p id="recomendacion" class="text-white mt-2">Necesitas un dispositivo con una resolución más grande para visualizar el contenido. Prueba este sitio desde una tableta o un ordenador de escritorio.</p>
            </div>
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
            foto_perfil_blob: null,
            widthVentana: null
        }
    },
    mounted: function() {
        // Obtenemos los datos del usuario
        this.obtenerDatosUsuario();

        // Obtenemos el tamaño en ancho de la ventana
        this.widthVentana = window.innerWidth;

        // Hacemos un listener para saber en todo momento el tamaño en ancho de la ventana
        window.addEventListener('resize', () => {
            this.widthVentana = window.innerWidth;
        })
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
        },
        actualizarLabelNombre(nuevoNombre) {
            this.nombre = nuevoNombre;
        },
        actualizarFoto(foto) {
            this.foto_perfil_blob = foto;
        }
    }
}
</script>

<style scoped>
    #panel_derecho {
        background-color: rgb(230, 230, 230);
    }

    .overlay {
        width: 100%;
        height: 100vh;
        background-color: #000000;
    }

    #recomendacion {
        font-size: 1.3rem;
    }
</style>