<template>
    <div>
        <!-- Interfaz para cambiar los datos generales de la cuenta -->
        <div class="flex-fill px-4 pt-4" v-if="tab_actual === 'general'">
            <h3 class="mb-4">Configuracion de cuenta</h3>
            <form action="">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="" class="form-control" v-model="this.nombre" placeholder="Ingrese su nombre" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo</label>
                    <input type="email" name="correo" id="" class="form-control" v-model="this.correo" placeholder="Ingrese su correo">
                </div>
                <div class="form-group">
                    <input type="button" value="Guardar datos" class="btn btn-primary">
                </div>
            </form>
        </div>

        <!-- Interfaz para cambiar la contraseña -->
        <div class="flex-fill px-4 pt-4" v-if="tab_actual === 'clave'">
            <h3 class="mb-4">Cambio de contraseña</h3>
            <form action="">
                <div class="form-group">
                    <label for="nombre">Contraseña actual</label>
                    <input type="password" name="contra_actual" id="" class="form-control">
                </div>
                <div class="form-group">
                    <label for="nombre">Nueva contraseña</label>
                    <input type="password" name="contra_nueva" id="" class="form-control">
                </div>
                <div class="form-group">
                    <label for="nombre">Repetir nueva contraseña</label>
                    <input type="password" name="contra_nueva_repetida" id="" class="form-control">
                </div>
                <div class="form-group">
                    <input type="button" value="Cambiar contraseña" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    mounted() {
        this.obtenerDatosUsuario();
    },
    data() {
        return {
            tab_actual: this.obtenerTab(),
            nombre: '',
            correo: ''
        }
    },
    methods: {
        obtenerTab() {
            let GET_tab = this.$route.query.tab; // «Tab» es el parametro GET. Obtenemos su valor.

            if (GET_tab === 'general') {
                this.tab_actual = 'general';
            }

            if (GET_tab === 'clave') {
                this.tab_actual = 'clave';
            }

            return this.tab_actual;
        },
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
        }
    },
    watch: {
        $route() {
            this.obtenerTab();
        }
    }
}
</script>

<style scoped>
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

    p {
        font-family: 'Roboto', 'Arial', sans-serif;
        font-size: 1.3rem;
    }

    .sombra-card {
        box-shadow: 0px 4px 0px 0px rgb(182, 182, 182);
    }
</style>