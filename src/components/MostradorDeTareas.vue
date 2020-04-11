<template>
    <div>
        <h3>Lista de tareas</h3>
        <div v-if="this.tareas.length > 0">
            <table class="table w-100 tabla sombra-tabla my-3">
                <thead class="bg-primary tabla-thead">
                    <tr class="text-white">
                        <td class="py-2 px-3">Tarea</td>
                        <td class="py-2 px-3">¿Realizada?</td>
                        <td class="py-2 px-3">Opciones</td>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr v-for="tarea in tareas" v-bind:key="tarea.id">
                        <td>{{tarea.Tarea}}</td>
                        <td v-if="tarea.Realizada == 1">
                            <input type="checkbox" name="" id="" checked>
                        </td>
                        <td v-else>
                            <input type="checkbox" name="" id="">
                        </td>
                        <td>
                            <div class="d-flex">
                                <input type="button" value="Editar" class="btn btn-warning mr-1">
                                <input type="button" value="Eliminar" class="btn btn-danger ml-1">
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="alert alert-warning">
            Actualmente no dispone de tareas. ¡Cree una!
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'MostradorDeTareas',
    data() {
        return {
            tareas: {}
        }
    },
    props: {
        tareaMandada: Object
    },
    mounted: function() {
        this.mostrarTareas();
    },
    methods: {
        mostrarTareas() {
            // Creamos unos parametros
            let parametros = new FormData();
            parametros.append("request", "mostrarTareas");

            // Hacemos una petición AJAX
            axios.post('/api/funciones.php', parametros)
                .then(res => {
                    console.log(res);
                    // Si los datos fueron recibidos correctamente...
                    if (res.data.status === 'OK') {
                        // Obtenemos las tareas
                        let tareas = res.data.resultados;

                        // Almacenamos las tareas
                        this.tareas = tareas;

                        console.log(this.tareas);
                    }
                })
                .catch(err => {
                    console.log(err);
                })
        }
    },
    watch: {
        tareaMandada() {
            this.tareas.push(this.tareaMandada);
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

    .tabla {
        width: 100%;
        height: auto;
        border-radius: 10px;
        overflow: hidden;
    }

    .tabla-thead {
        font-family: 'Roboto', 'Arial', sans-serif;
        font-size: 1.3rem;
    }

    .tabla-thead td {
        margin: 0 !important;
    }

    .sombra-tabla {
        box-shadow: 0px 4px 0px 0px rgb(182, 182, 182);
    }
</style>