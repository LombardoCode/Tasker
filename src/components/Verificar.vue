<template>
    <div>
        <h1>Verificación de la cuenta</h1>
        <div v-if="(this.mensaje !== null) && (this.estado === 'activada')" class="alert alert-success">
            {{this.mensaje}}
        </div>
        <div v-if="(this.mensaje !== null) && (this.estado === 'previamenteActivada')" class="alert alert-warning">
            {{this.mensaje}}
        </div>
        <div v-if="(this.mensaje !== null) && (this.estado === 'noEncontrada')" class="alert alert-danger">
            {{this.mensaje}}
        </div>
        
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'verificar',
    data() {
        return {
            GET_codigo: '',
            mensaje: null,
            estado: null
        }
    },
    mounted() {
        // Obtenemos el código de verificación del URL
        let URL_GETS = this.$route.query;
        if (Object.prototype.hasOwnProperty.call(URL_GETS, 'codigo')) {
            // El parametro GET «codigo» es el código de verificación de la cuenta del usuario. Obtenemos su valor.
            this.GET_codigo = this.$route.query.codigo;

            // Creamos unos parametros
            let parametros = new FormData();
            parametros.append("request", "activarCuenta");
            parametros.append("clave_de_validacion", this.GET_codigo);

            // Hacemos una petición AJAX para validar al usuario
            axios.post("/api/funciones.php", parametros)
                .then(res => {
                    // Obtenemos el mensaje obtenido desde el backend (si es que hay)
                    if (Object.prototype.hasOwnProperty.call(res.data, 'mensaje')) {
                        this.mensaje = res.data.mensaje;
                    }

                    // Obtenemos el estado de la cuenta (si es que hay)
                    if (Object.prototype.hasOwnProperty.call(res.data, 'status_cuenta')) {
                        this.estado = res.data.status_cuenta;
                    }

                    console.log(res);
                })
                .catch(err => {
                    console.log(err);
                })
        } else {
            this.error = "Se produjo un error.";
        }
    }
}
</script>

<style>

</style>