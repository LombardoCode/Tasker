<template>
    <div id="componente">
        <h3>Creador de tareas / recordatorios</h3>
        <div class="card-panel bg-white sombra-card mt-3 mb-5">
            <div class="titulo-card py-2 px-3 bg-primary">
                <p class="m-0 text-white">Ingrese una tarea a realizar</p>
            </div>
            <div class="px-3 py-3">
                <div class="alert alert-danger" v-if="Object.keys(this.errores).length !== 0">
                    <ul class="m-0 pl-2" v-for="error in this.errores" :key="error">
                        <li>{{error}}</li>
                    </ul>
                </div>
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
            tarea: '',
            errores: []
        }
    },
    methods: {
        crearTarea() {
            // Obtenemos la tarea
            let tarea = this.tarea

            // Eliminamos los espacios al inicio y fin de la tarea
            tarea = tarea.trim();

            // Eliminamos los espacios extra que pueden haber entre palabras
            tarea = tarea.replace(/\s+/g, " ");

            // Verificamos si el usuario escribió algo en el campo de las tarea
            if (tarea.length === 0) {
                // Limpiamos los errores
                this.errores = [];

                // Agregamos el error
                this.errores.push('Ingrese una tarea válida en el campo correspondiente.');
            } else {
                // Limpiamos los errores
                this.errores = [];

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
}
</script>

<style scoped>
    @import url("https://fonts.googleapis.com/css?family=Roboto:100,100italic,300,300italic,regular,italic,500,500italic,700,700italic,900,900italic");

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

    p {
        font-family: 'Roboto', 'Arial', sans-serif;
        font-size: 1.3rem;
    }

    .sombra-card {
        box-shadow: 0px 4px 0px 0px rgb(182, 182, 182);
    }
</style>