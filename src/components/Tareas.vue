<template>
    <div>
        <div id="panel_derecho" class="px-4 pt-4">
            <CreadorDeTareas @datosTarea="tareaRecibida"/>
            <MostradorDeTareas :tareaMandada="this.datosTarea"/>
        </div>
    </div>
</template>

<script>
import MostradorDeTareas from './MostradorDeTareas'
import CreadorDeTareas from './CreadorDeTareas'

export default {
    name: 'VisualizadorRouter',
    components: {
        MostradorDeTareas,
        CreadorDeTareas
    },
    data() {
        return {
            datosTarea: {}
        }
    },
    methods: {
        tareaRecibida(tarea) {
            // Asignamos la tarea que se va a mandar al componente de «MostradorDeTareas.vue»
            this.datosTarea = tarea;
        }
    }
}
</script>

<style>
    #panel_derecho {
        width: 100%;
    }
</style>