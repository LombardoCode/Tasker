// Express
const express = require('express');
const app = express();

// MySQL
const mysql = require('mysql');
var conexion = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '1234',
    database: 'Express'
})
conexion.connect(function(error) {
    if (!!error) {
        console.log("Error");
    } else {
        console.log("Conectado");
    }
})

// Puerto
let puerto = process.env.PORT || 3000;

// Escucha del puerto
app.listen(puerto, () => console.log(`Estamos escuchando el puerto ${puerto}.`));



// Routes
app.get('/', (req, res) => {
    //res.send('Hola mundo desde Express!');

    /*
    conexion.query('SELECT * FROM Usuarios', function(error, rows, fields) {
        if (!!error) {
            console.log("Hubo un error en el query");
        } else {
            console.log(rows[0]['Nombre']);
        }
    })
    */
})
