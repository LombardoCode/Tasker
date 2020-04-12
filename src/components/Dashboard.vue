<template>
    <div>
        <div id="contenido" class="d-flex">
            <div id="panel_izquierdo" class="px-4">
                <PanelCuenta :nombre="this.nombre"/>
            </div>
            <div id="panel_derecho" class="flex-fill px-4 pt-4">
                <CreadorDeTareas @datosTarea="tareaRecibida"/>
                <MostradorDeTareas :tareaMandada="this.datosTarea"/>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import PanelCuenta from './PanelCuenta'
import CreadorDeTareas from './CreadorDeTareas'
import MostradorDeTareas from './MostradorDeTareas'

export default {
    name: 'Dashboard',
    components: {
        PanelCuenta,
        CreadorDeTareas,
        MostradorDeTareas
    },
    data() {
        return {
            id: 0,
            correo: '',
            nombre: '',
            datosTarea: {}
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
                    //console.log(res);
                    // Guardamos los datos del usuario
                    this.id = parseInt(res.data.id);
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
        tareaRecibida(tarea) {
            // Asignamos la tarea que se va a mandar al componente de «MostradorDeTareas.vue»
            this.datosTarea = tarea;
        }
    }
}
</script>

<style scoped>
    #panel_izquierdo {
        min-width: 300px;
        height: 100vh;
        background-color: #1b1b1b;
    }

    #panel_derecho {
        background-color: rgb(230, 230, 230);
    }
</style>