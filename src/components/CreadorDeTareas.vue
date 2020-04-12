<template>
    <div>
        <h3>Creador de tareas / recordatorios</h3>
        <div class="card-panel bg-white sombra-card mt-3 mb-5">
            <div class="titulo-card py-2 px-3 bg-primary">
                <p class="m-0 text-white">Ingrese una tarea a realizar</p>
            </div>
            <div class="px-3 py-3">
                <form>
                    <div class="form-group m-0">
                        <textarea class="form-control" name="tarea" id="tarea" cols="30" rows="2" placeholder="Ingrese una tarea a recordar..." v-model="tarea"></textarea>
                    </div>
                    <div class="form-group m-0 d-flex justify-content-end">
                        <input type="button" value="Crear tarea" class="btn btn-success mt-3 px-4" v-on:click="crearTarea()">
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'CreadorDeTareas',
    data() {
        return {
            tarea: ''
        }
    },
    methods: {
        crearTarea() {
            // Obtenemos la tarea
            let tarea = this.tarea

            // Creamos unos parametros
            let parametros = new FormData();
            parametros.append("request", "crearTarea");
            parametros.append("tarea", tarea)

            // Creamos un AJAX
            axios.post('/api/funciones.php', parametros)
                .then(res => {
                    //console.log(res);
                    // Si los datos fueron insertados correctamente...
                    if (res.data.status === 'OK') {
                        // Mandamos la información al componente padre (Dashboard.vue)
                        this.$emit('datosTarea', res.data.resultados);

                        // Limpiamos el input de la tarea
                        this.tarea = '';
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
    @import url("https://fonts.googleapis.com/css?family=Roboto:100,100italic,300,300italic,regular,italic,500,500italic,700,700italic,900,900italic");

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