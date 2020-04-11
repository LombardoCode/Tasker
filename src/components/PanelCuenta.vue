<template>
    <div>
        <h4 class="text-white text-center">{{this.nombre}}</h4>
        <button class="btn btn-danger btn-block" v-on:click="cerrar_sesion()">Cerrar sesión</button>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'PanelCuenta',
    props: {
        nombre: String
    },
    methods: {
        cerrar_sesion() {
            axios.get('/api/cerrar_sesion.php')
                .then(res => {
                    console.log(res);
                    // Si los datos son correctos...
                    if (res.data.status === 'OK') {
                        // Redireccionamos al usuario al login
                        this.$router.push({name: 'Login'});
                    }
                })
                .catch(err => {
                    console.log(err);
                })
        }
    }
}
</script>

<style>

</style>