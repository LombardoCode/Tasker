<template>
    <div>
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
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
                                <textarea name="" id="" cols="4" rows="" class="form-control" v-model="actualizarTareaInput"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success" data-dismiss="modal" v-on:click="actualizarTarea()">Actualizar tarea</button>
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
            tarea: String
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

                // Hacemos una petición AJAX para actualizar la tarea en la base de datos
                axios.post('/api/funciones.php', parametros)
                    .then(res => {
                        console.log(res);
                    })
                    .catch(err => {
                        console.log(err);
                    })
                
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
        },
        computed: {
            actualizarTareaInput: {
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