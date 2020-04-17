<template>
    <div>
        <div class="modal fade" id="modalTarea" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <!-- Modal para editar tareas -->
            <div class="modal-dialog" role="document" v-if="this.accion === 'editar'">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edición de tarea</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <textarea name="" id="" cols="4" rows="" class="form-control" v-model="obtenerTareaInput"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success" data-dismiss="modal" v-on:click="actualizarTarea()">Actualizar tarea</button>
                    </div>
                </div>
            </div>
            <!-- Modal para eliminar tareas -->
            <div class="modal-dialog" role="document" v-if="this.accion === 'eliminar'">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Eliminación de tarea</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Realmente quiere eliminar la siguiente tarea?:</p>
                        <form>
                            <div class="form-group">
                                <input type="text" name="" id="" class="form-control" v-model="obtenerTareaInput" disabled>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" v-on:click="eliminarTarea()">Eliminar tarea</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        name: 'Modal',
        props: {
            id: Number,
            tarea: String,
            accion: String
        },
        data() {
            return {
                tareita: 'chin'
            }
        },
        methods: {
            actualizarTarea() {
                // Creamos unos parametros
                let parametros = new FormData();
                parametros.append("request", "actualizarTarea");
                parametros.append("id_tarea", this.id);
                parametros.append("tarea_nueva", this.tarea);
                console.log(this.accion);


                // Hacemos una petición AJAX para actualizar la tarea en la base de datos
                axios.post('/api/funciones.php', parametros)
                    .then(res => {
                        console.log(res);
                        // Si la consulta fué realizada exitosamente...
                        if (res.data.status === 'OK') {
                            // Preparamos los datos a enviar
                            let datos = {
                                ID: this.id,
                                Tarea: this.tarea
                            }

                            // Emitimos una señal al componente padre «MostradorDeTareas.vue»
                            // y mandamos los datos para que se actualice el texto de la tarea
                            // que se editó
                            this.$emit('tareaActualizar', datos);
                        }
                    })
                    .catch(err => {
                        console.log(err);
                    })
            },
            eliminarTarea() {
                // Creamos unos parametros
                let parametros = new FormData();
                parametros.append("request", "eliminarTarea");
                parametros.append("id_tarea", this.id);

                // Creamos una petición AJAX para eliminar la tarea deseada
                axios.post('/api/funciones.php', parametros)
                    .then(res => {
                        console.log(res);
                        // Si la consulta fué realizada exitosamente...
                        if (res.data.status === 'OK') {
                            // Preparamos los datos a enviar
                            let datos = {
                                ID: this.id,
                            }

                            // Emitimos una señal al componente padre «MostradorDeTareas.vue»
                            // y mandamos los datos para que se actualice el texto de la tarea
                            // que se editó
                            this.$emit('tareaEliminar', datos);
                        }
                    })
                    .catch(err => {
                        console.log(err);
                    })
            }
        },
        computed: {
            obtenerTareaInput: {
                get: function() {
                    return this.tarea;
                },
                set: function(nuevaTarea) {
                    this.$emit('tareaNueva', nuevaTarea);
                }
            }
        }
    }
</script>

<style>

</style>