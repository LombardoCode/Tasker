<template>
    <div id="componente">
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
                    <tr v-for="tarea in tareas" v-bind:key="tarea.ID">
                        <td>{{tarea.Tarea}}</td>
                        <td v-if="tarea.Realizada == true">
                            <input type="checkbox" name="" id="" checked v-on:click="modificarRealizacion(tarea, $event)">
                        </td>
                        <td v-else-if="tarea.Realizada == false">
                            <input type="checkbox" name="" id="" v-on:click="modificarRealizacion(tarea, $event)">
                        </td>
                        <td>
                            <div class="d-flex">
                                <input type="button" value="Editar" class="btn btn-warning mr-1" data-toggle="modal" data-target="#modalTarea" v-on:click="editarTarea(tarea)">
                                <input type="button" value="Eliminar" class="btn btn-danger ml-1" data-toggle="modal" data-target="#modalTarea" v-on:click="eliminarTarea(tarea)">
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Modal :id="idModal" @tareaNueva="actualizarTareaInput" @tareaActualizar="actualizarTareaNombre" @tareaEliminar="eliminarTareaLocal" :tarea="tareaModal" :accion="accion"/>
        </div>
        <div v-else class="alert alert-warning">
            Actualmente no dispone de tareas. ¡Cree una!
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import Modal from './Modal'

export default {
    name: 'MostradorDeTareas',
    components: {
        Modal
    },
    data() {
        return {
            tareas: {},
            accion: '',
            tareaModal: '',
            idModal: 0,
        }
    },
    props: {
        // Obtenemos la tarea que se creó con el módulo de creación de tareas
        tareaMandada: Object
    },
    mounted: function() {
        // Obtenemos todas las tareas de la base de datos
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
                    //console.log(res);
                    // Si los datos fueron recibidos correctamente...
                    if (res.data.status === 'OK') {
                        // Obtenemos las tareas
                        let tareas = res.data.resultados;

                        // Almacenamos las tareas
                        this.tareas = tareas;
                    }
                })
                .catch(err => {
                    console.log(err);
                })
        },
        modificarRealizacion(tarea, event) {
            // Obtenemos el estado actual del checkbox
            let estado_tarea = event.target.checked;

            // Creamos unos parametros
            let parametros = new FormData();
            parametros.append("request", "modificarRealizacion");
            parametros.append("id_tarea", tarea.ID);
            parametros.append("status_tarea", estado_tarea)

            // Creamos una petición AJAX
            axios.post('/api/funciones.php', parametros)
                .then(res => {
                    console.log(res);
                })
                .catch(err => {
                    console.log(err);
                })
        },
        editarTarea(tarea) {
            // Al hacer clic en editar en el botón de cada tarea
            // se obtendrá el ID y la tarea actual de la fila 
            // deseada para ser impresa por el componente «Modal.vue»
            // y almacendada dentro de los props su componente
            this.idModal = parseInt(tarea.ID);
            this.tareaModal = tarea.Tarea;

            // Le damos a entender al Modal que se autoconfigure para ser usado
            // exclusivamente para editar tareas
            this.accion = "editar";
        },
        eliminarTarea(tarea) {
            // Al hacer clic en editar en el botón de cada tarea
            // se obtendrá el ID y la tarea actual de la fila
            // deseada para ser eliminada posteriormente por el
            // componente «Modal.vue»
            this.idModal = parseInt(tarea.ID);
            this.tareaModal = tarea.Tarea;

            // Le damos a entender al Modal que se autoconfigure para ser usado
            // exclusivamente para eliminar tareas
            this.accion = "eliminar";
        },
        actualizarTareaInput(tarea) {
            // Actualizamos el nuevo valor del prop de «Modal.vue»
            this.tareaModal = tarea;

            // Eliminamos los espacios al inicio y fin de la tarea
            this.tareaModal = this.tareaModal.trim();

            // Eliminamos los espacios extra que pueden haber entre palabras
            this.tareaModal = this.tareaModal.replace(/\s+/g, " ");
        },
        actualizarTareaNombre(datos) {
            // Hacemos un for para encontrar el ID que sea igual al ID de la tarea
            // recibida por parte del componente «Modal.vue»
            for (let tarea in this.tareas) {
                if (this.tareas[tarea]["ID"] == datos.ID) {
                    // Actualizamos el texto de la tarea editada
                    this.tareas[tarea]["Tarea"] = datos.Tarea;
                }
            }
        },
        eliminarTareaLocal(datos) {
            // Hacemos un for para encontrar el ID que sea igual al ID de la tarea
            // recibida por parte del componente «Modal.vue»
            let index;
            for (let tarea in this.tareas) {
                if (this.tareas[tarea]["ID"] == datos.ID) {
                    // Encontramos el index de nuestro array que coincide con el
                    // id de las tareas y el id de los datos obtenidos de «Modal.vue»
                    index = tarea
                }
            }

            // Eliminamos el array de la lista de tareas de manera local
            this.tareas.splice(index, 1);
        }
    },
    watch: {
        // Cuando el prop «tareaMandada» sufra un cambio se ejecutará este método
        tareaMandada() {
            // Adjuntamos a la lista de tareas nuestra nueva tarea mandada que ha
            // sido creada con el módulo de creación de tareas
            this.tareas.push(this.tareaMandada);
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