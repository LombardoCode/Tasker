<?php
    // Importamos PHPMailer
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    require './PHPMailer/Exception.php';
    require './PHPMailer/PHPMailer.php';
    require './PHPMailer/SMTP.php';

    
    // Si tenemos conexión a la base de datos
    $conexion = validarConexion();

    // Verificamos si hay conexión
    if ($conexion) {
        // Verificamos qué tipo de request se quiere hacer
        $request = '';
        if (isset($_POST['request'])) {
            // Obtenemos el request
            $request = $_POST['request'];
            
            // Si el usuario quiere loguearse...
            if ($request == 'loginUsuario') {
                // Creamos una variable de errores
                $errores = new ArrayObject();

                // Verificamos si tenemos el usuario y contraseña
                if (isset($_POST['correo']) && (isset($_POST['contra']))) {
                    // Obtenemos el correo que ingresó el usuario
                    $correo = $_POST['correo'];

                    // Eliminamos los espacios del correo
                    $correo = trim($correo);

                    // Convertimos el correo en minúsculas
                    $correo = strtolower($correo);

                    // Sanitizamos el correo (eliminamos caracteres especiales)
                    $correo = filter_var($correo, FILTER_SANITIZE_EMAIL);

                    // Obtenemos la contraseña que ingresó el usuario
                    $contra = $_POST['contra'];

                    // Encriptamos la contraseña (esto porque en la base de datos está encriptada)
                    // Necesitamos la contraseña encriptada para compararla con la de la base de datos
                    $contra = md5($contra);

                    // Sanitizamos el correo (eliminamos caracteres especiales)
                    $correo = filter_var($correo, FILTER_SANITIZE_EMAIL);

                    // Verificamos si el usuario ha ingresado algo dentro del campo de correo electrónico
                    if (strlen($correo) == 0) {
                        $errores -> append("Por favor, ingrese su correo en el campo correspondiente.");
                    } else {
                        // Validamos si el email tiene el formato correcto
                        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                            $errores -> append("Por favor, ingrese una correo válido.");
                        }
                    }

                    // Verificamos si el usuario ha ingresado algo dentro del campo de contraseña
                    if (strlen($contra) == 0) {
                        $errores -> append("Por favor, ingrese su contraseña en el campo correspondiente.");
                    }

                    // Si no hay errores...
                    if (($errores -> count()) == 0) {
                        // Logueamos al usuario
                        loginUsuario($correo, $contra);
                    } else { // Si hay errores...
                        // Creamos una respuesta
                        $respuesta = array(
                            'status' => 'FAIL',
                            'errores' => $errores
                        );

                        // Mandamos la respuesta ha nuestro AJAX
                        echo json_encode($respuesta);
                    }
                }
            }

            // Si el usuario abre el link de verificación de su cuenta (mandada por correo email)...
            if ($request == 'activarCuenta') {
                // Obtenemos la clave de validación de su cuenta
                if ($_POST['clave_de_validacion']) {
                    $clave_de_validacion = $_POST['clave_de_validacion'];

                    // Primero verificamos si la cuenta ya ha sido confirmada previamente
                    $instruccion = "SELECT cuenta_conf FROM Usuarios WHERE clave_conf = :clave_de_validacion";
                    $query = $conexion -> prepare($instruccion);
                    $query -> bindParam(':clave_de_validacion', $clave_de_validacion);
                    if ($query -> execute()) {
                        if ($query -> rowCount() > 0) {
                            $resultados = $query -> fetch();
                        
                            // Si la cuenta ya ha sido confirmada antes...
                            if ($resultados['cuenta_conf'] == 1) {
                                // Creamos una respuesta
                                $respuesta = array(
                                    'status' => 'FAIL',
                                    'status_cuenta' => 'previamenteActivada',
                                    'mensaje' => 'Esta cuenta ya ha sido confirmada anteriormente.'
                                );

                                // Mandamos la respuesta a nuestra petición AJAX
                                echo json_encode($respuesta);
                            } else { // Si la cuenta no ha sido confirmada
                                $instruccion = "UPDATE Usuarios SET cuenta_conf = 1 WHERE clave_conf = :clave_de_validacion";
                                $query = $conexion -> prepare($instruccion);
                                $query -> bindParam(':clave_de_validacion', $clave_de_validacion);
                                
                                // Si la consulta fué realizada existosamente...
                                if ($query -> execute()) {
                                    // Si han hecho cambios en la base de datos...
                                    if ($query -> rowCount() > 0) {
                                        // Creamos una respuesta afirmativa
                                        $resultados = $query -> fetch();

                                        $respuesta = array(
                                            'status' => 'OK',
                                            'status_cuenta' => 'activada',
                                            'mensaje' => '¡La cuenta se ha verificado satisfactoriamente!'
                                        );

                                        echo json_encode($respuesta);
                                    } else {
                                        // No se ha modificado nada
                                        $respuesta = array(
                                            'status' => 'FAIL'
                                        );
                
                                        echo json_encode($respuesta);
                                    }
                                } else {
                                    $respuesta = array(
                                        'status' => 'FAIL'
                                    );

                                    echo json_encode($respuesta);
                                }
                            }
                        } else { // No se encuentra ninguna cuenta con la clave del parametro GET
                            // Creamos una respuesta
                            $respuesta = array(
                                'status' => 'FAIL',
                                'status_cuenta' => 'noEncontrada',
                                'mensaje' => 'Hubo un error'
                            );

                            echo json_encode($respuesta);
                        }
                    }
                }
            }

            // Obtenemos los datos del usuario
            if ($request == 'obtenerDatosUsuario') {
                // Iniciamos una sesión
                session_start();
                
                // Si tenemos una sesión abierta y tenemos un ID dentro de $_SESSION...
                if ((session_status() == PHP_SESSION_ACTIVE) && (isset($_SESSION['id']))) {
                    // Hacemos una instrucción SQL para obtener los datos del usuario
                    $instruccion = "SELECT * FROM Usuarios WHERE ID = :id";
                    $query = $conexion -> prepare($instruccion);
                    $query -> bindParam(':id', $_SESSION['id']);
                    $query -> execute();
                    $resultados = $query -> fetch();

                    // Creamos un JSON donde estén todos los datos del usuario logueado
                    $respuesta = array(
                        'id' => $resultados['ID'],
                        'correo' => $resultados['Correo'],
                        'nombre' => $resultados['Nombre'],
                        'status' => 'OK'
                    );

                    // Mandamos el JSON a nuestra petición AJAX
                    echo json_encode($respuesta);
                } else { // No hay sesión
                    $respuesta = array(
                        'status' => 'FAIL'
                    );

                    // Mndamos el JSON a nuestra petición AJAX
                    echo json_encode($respuesta);
                }
            }

            // Si el usuario quiere cerrar sesión...
            if ($request == 'cerrarSesion') {
                cerrarSesion();
            }

            // Si el usuario quiere registrarse
            if ($request == 'crearCuenta') {
                // Creamos una variable de respuesta
                $respuesta = array();

                $errores = new ArrayObject();

                // Si su nombre está dentro de $_POST...
                if (isset($_POST['nombre'])) {
                    // Obtenemos su nombre
                    $nombre = $_POST['nombre'];

                    // Eliminamos los espacios del nombre
                    $nombre = trim($nombre);

                    // Sanitizamos el nombre (eliminamos etiquetas)
                    $nombre = filter_var($nombre, FILTER_SANITIZE_STRING);

                    // Verificamos si el usuario ha ingresado algo dentro del campo de nombre
                    if (strlen($nombre) == 0) {
                        $errores -> append("Por favor, ingrese su nombre en el campo correspondiente.");
                    }
                }

                // Si su correo está dentro de $_POST...
                if (isset($_POST['correo'])) {
                    // Obtenemos el correo
                    $correo = $_POST['correo'];

                    // Eliminamos los espacios del correo
                    $correo = trim($correo);

                    // Convertimos el correo en minúsculas
                    $correo = strtolower($correo);

                    // Sanitizamos el correo (eliminamos caracteres especiales)
                    $correo = filter_var($correo, FILTER_SANITIZE_EMAIL);
                }

                // Si su contraseña está dentro de $_POST...
                if (isset($_POST['contra'])) {
                    // Obtenemos la contraseña
                    $contra = $_POST['contra'];

                    // Encriptamos la contraseña
                    $contra = md5($contra);
                }

                // Si la repetición de su contraseña está dentro de $_POST...
                if (isset($_POST['contra_repetida'])) {
                    // Obtenemos la contraseña repetida
                    $contra_repetida = $_POST['contra_repetida'];

                    // Encriptamos la contraseña repetida
                    $contra_repetida = md5($contra_repetida);
                }

                // Verificamos si el usuario ha ingresado algo dentro del campo de correo electrónico
                if (strlen($correo) == 0) {
                    $errores -> append("Por favor, ingrese un correo en el campo correspondiente.");
                } else {
                    // Validamos si el email tiene el formato correcto
                    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                        $errores -> append("Por favor, ingrese una correo válido.");
                    }
                }

                // Verificamos si el usuario ha ingresado algo dentro del campo de contraseña
                if (strlen($contra) == 0) {
                    $errores -> append("Por favor, ingrese una contraseña en el campo correspondiente.");
                }

                // Verificamos si las dos contraseñas son iguales
                if ($contra != $contra_repetida) {
                    // Registramos el error
                    $errores -> append("Las contraseñas no coinciden.");
                }

                // Verificamos si el correo ingresado por el usuario ya existe en la base de datos
                $instruccion = "SELECT * FROM Usuarios WHERE Correo = :correo";
                $query = $conexion -> prepare($instruccion);
                $query -> bindParam(':correo', $correo);

                // Si la consulta se realizó existosamente...
                if ($query -> execute()) {
                    // Si encontramos resultados de una cuenta existente...
                    if ($query -> rowCount() > 0) {
                        // Significa que ya hay una cuenta existente
                        $errores -> append("Ya existe una cuenta con esa dirección de correo electrónico");
                    }
                }

                // Generamos una clave de verificación
                $clave_de_validacion = md5(time());
                
                // Si no existen errores...
                if (($errores -> count()) == 0) {
                    // Hacemos el query para registrar al usuario
                    $instruccion = "INSERT INTO Usuarios (Correo, Contra, Nombre, clave_conf) VALUES (:correo, :contra, :nombre, :clave_de_validacion)";
                    $query = $conexion -> prepare($instruccion);
                    $query -> bindParam(':correo', $correo);
                    $query -> bindParam(':contra', $contra);
                    $query -> bindParam(':nombre', $nombre);
                    $query -> bindParam(':clave_de_validacion', $clave_de_validacion);


                    // Si la consulta se realizó...
                    if ($query -> execute()) {
                        // Creamos una respuesta
                        $respuesta = array(
                            'status' => 'OK',
                            'correo' => $correo,
                            'clave_de_validacion' => $clave_de_validacion
                        );

                        // Mandamos la respuesta hacia nuestra petición AJAX
                        echo json_encode($respuesta);
                    } else { // Si la consulta no se realizó...
                        // Creamos una respuesta
                        $respuesta = array(
                            'status' => 'FAIL'
                        );

                        // Mandamos la respuesta hacia nuestra petición AJAX
                        echo json_encode($respuesta);
                    }
                } else {
                    // Creamos una respuesta
                    $respuesta = array(
                        'status' => 'FAIL',
                        'errores' => $errores
                    );

                    // Mandamos la respuesta hacia nuestra petición AJAX
                    echo json_encode($respuesta);
                }
            }

            // Si el usuario quiere crear una tarea...
            if ($request == 'crearTarea') {
                if (isset($_POST['tarea'])) {
                    // Guardamos la tarea sacada de $_POST
                    $tarea = $_POST['tarea'];

                    // Abrimos una sesión
                    session_start();

                    // Si tenemos una sesión activa...
                    if (session_status() == PHP_SESSION_ACTIVE) {
                        // Si tenemos el ID dentro de $_SESSION...
                        if (isset($_SESSION['id'])) {
                            // Obtenemos el ID
                            $id = $_SESSION['id'];
                        }

                        // Hacemos un query (SQL)
                        $instruccion = "INSERT INTO Tareas VALUES(null, :id, :tarea, FALSE)";
                        $query = $conexion -> prepare($instruccion);
                        $query -> bindParam(':id', $id);
                        $query -> bindParam(':tarea', $tarea);
                        $query -> execute();
                        $resultados = $query -> fetchAll();

                        // Creamos una respuesta
                        $respuesta = array(
                            'status' => 'OK',
                            'resultados' => array(
                                'ID' => $conexion -> lastInsertId(),
                                'ID_Usuario' => $_SESSION['id'],
                                'Tarea' => $tarea,
                                'Realizada' => FALSE
                            )
                        );

                        // Mandamos la respuesta por JSON a nuestra petición AJAX
                        echo json_encode($respuesta);
                    }
                } else {
                    echo '[PHP]: No hay tareas para mi.';
                }
            }

            if ($request == 'obtenerFotoPerfil') {
                // Iniciamos una sesión
                session_start();

                // Si tenemos una sesión abierta...
                if (session_status() == PHP_SESSION_ACTIVE) {
                    // Si tenemos el ID del usuario logueado...
                    if (isset($_SESSION['id'])) {
                        // Capturamos el ID del usuario
                        $id = $_SESSION['id'];
                    }
                }

                // Creamos una consulta
                $instruccion = "SELECT foto_perfil FROM Usuarios WHERE ID = :id";
                $query = $conexion -> prepare($instruccion);
                $query -> bindParam(':id', $id);

                if ($query -> execute()) {
                    // Obtenemos el nombre del archivo
                    $resultado = $query -> fetch();
                    $nombre_foto = $resultado['foto_perfil'];

                    // Creamos una respuesta
                    $respuesta = array(
                        'status' => 'OK',
                        'foto_perfil' => $nombre_foto
                    );

                    echo json_encode($respuesta);
                } else {
                    echo 'No resultado';
                }
            }

            // Si el usuario quiere desmarcar o marcar la realización de una tarea...
            if ($request == 'modificarRealizacion') {
                // Si tenemos el id de la tarea en $_POST...
                if (isset($_POST['id_tarea'])) {
                    // Obtenemos el ID de la tarea
                    $id_tarea = $_POST['id_tarea'];
                }

                // Si tenemos el estado de la tarea en $_POST...
                if (isset($_POST['status_tarea'])) {
                    // Obtenemos el estado actual de la tarea
                    $status_tarea = $_POST['status_tarea'];
                }

                // Hacemos el query para actualizar
                $instruccion = "UPDATE Tareas SET Realizada = $status_tarea WHERE ID = $id_tarea";
                $query = $conexion -> prepare($instruccion);

                // Si la consulta fué exitosa...
                if ($query -> execute()) {
                    // Creamos una respuesta
                    $respuesta = array(
                        'status' => 'OK'
                    );
                    
                    // Mandamos la respuesta a nuestra petición AJAX
                    echo json_encode($respuesta);
                }
            }

            // Si se quieren mostrar las tareas...
            if ($request == 'mostrarTareas') {
                // Abrimos una sesión
                session_start();

                // Si tenemos una sesión activa...
                if (session_status() == PHP_SESSION_ACTIVE) {
                    // Si tenemos el ID dentro de $_SESSION...
                    if (isset($_SESSION['id'])) {
                        // Obtenemos el ID
                        $id = $_SESSION['id'];
                    }

                    // Hacemos un query (SQL)
                    $instruccion = "SELECT t.* FROM Tareas t JOIN Usuarios u ON t.ID_Usuario = u.ID WHERE u.ID = :id";
                    $query = $conexion -> prepare($instruccion);
                    $query -> bindParam(':id', $id);
                    $query -> execute();
                    $resultados = $query -> fetchAll();

                    // Creamos una respuesta para nuestra petición AJAX
                    $respuesta = array(
                        'status' => 'OK',
                        'resultados' => $resultados
                    );

                    // Mandamos la respuesta por JSON a nuestra petición AJAX
                    echo json_encode($respuesta);
                }
            }

            // Si se quiere actualizar una tarea...
            if ($request == "actualizarTarea") {
                // Iniciamos una sesión
                session_start();

                // Si tenemos una sesión abierta...
                if (session_status() == PHP_SESSION_ACTIVE) {
                    // Si tenemos el ID del usuario logueado...
                    if (isset($_SESSION['id'])) {
                        // Capturamos el ID del usuario
                        $id_usuario = $_SESSION['id'];
                    }

                    // Si tenemos el ID de la tarea...
                    if (isset($_POST['id_tarea'])) {
                        // Capturamos el ID de la tarea a editar
                        $id_tarea = $_POST['id_tarea'];
                    }

                    // Si tenemos la tarea nueva...
                    if (isset($_POST['tarea_nueva'])) {
                        $tarea_nueva = $_POST['tarea_nueva'];
                    }

                    $id_usuario = intval($id_usuario);
                    $id_tarea = intval($id_tarea);

                    // Hacemos un query (SQL) para actualizar la tarea
                    $instruccion = "UPDATE Tareas SET Tarea = :tarea_nueva WHERE ID = :id_tarea AND ID_Usuario = :id_usuario";
                    $query = $conexion -> prepare($instruccion);
                    $query -> bindParam(':tarea_nueva', $tarea_nueva);
                    $query -> bindParam(':id_tarea', $id_tarea);
                    $query -> bindParam(':id_usuario', $id_usuario);

                    // Si la consulta se realizó
                    if ($query -> execute()) {
                        // Creamos una respuesta
                        $respuesta = array(
                            'status' => 'OK'
                        );

                        // Mandamos la respuesta a nuestra petición AJAX
                        echo json_encode($respuesta);
                    } else {
                        $respuesta = array(
                            'status' => 'FAIL'
                        );

                        // Mandamos la respuesta a nuestra petición AJAX
                        echo json_encode($respuesta);
                    }
                }
            }

            // Si se quiere eliminar una tarea...
            if ($request == "eliminarTarea") {
                // Iniciamos una sesión
                session_start();

                // Si tenemos una sesión abierta...
                if (session_status() == PHP_SESSION_ACTIVE) {
                    // Si tenemos el ID del usuario logueado...
                    if (isset($_SESSION['id'])) {
                        // Capturamos el ID del usuario
                        $id_usuario = $_SESSION['id'];
                    }

                    // Si tenemos el ID de la tarea...
                    if (isset($_POST['id_tarea'])) {
                        // Capturamos el ID de la tarea a editar
                        $id_tarea = $_POST['id_tarea'];
                    }

                    $id_usuario = intval($id_usuario);
                    $id_tarea = intval($id_tarea);

                    // Hacemos un query (SQL) para actualizar la tarea
                    $instruccion = "DELETE FROM Tareas WHERE ID = $id_tarea AND ID_Usuario = $id_usuario";
                    $query = $conexion -> prepare($instruccion);

                    // Si la consulta se realizó
                    if ($query -> execute()) {
                        // Creamos una respuesta
                        $respuesta = array(
                            'status' => 'OK'
                        );

                        // Mandamos la respuesta a nuestra petición AJAX
                        echo json_encode($respuesta);
                    } else {
                        $respuesta = array(
                            'status' => 'FAIL'
                        );

                        // Mandamos la respuesta a nuestra petición AJAX
                        echo json_encode($respuesta);
                    }
                }
            }

            if ($request == "actualizarCuenta") {
                // Obtenemos el sub-request
                if (isset($_POST['subrequest'])) {
                    $subrequest = $_POST['subrequest'];

                    // Si el usuario quiere actualizar sus datos generales...
                    if ($subrequest == "actualizarDatosGenerales") {
                        // Creamos una variable de errores
                        $errores = new ArrayObject();

                        // Creamos una variable para el nombre de la foto a subir a la base de datos y un indicador para saber si se subió correctamente
                        $foto_cargada = false;
                        $foto_nuevo_nombre = null;
                        $foto_subida = false;

                        // Iniciamos una sesión
                        session_start();
                
                        // Si tenemos una sesión abierta y tenemos un ID dentro de $_SESSION...
                        if ((session_status() == PHP_SESSION_ACTIVE) && (isset($_SESSION['id']))) {
                            // Obtenemos el ID
                            $id = $_SESSION['id'];
                        }

                        // Verificamos si tenemos el nombre
                        if (isset($_POST['nombre'])) {
                            // Obtenemos el nombre
                            $nombre = $_POST['nombre'];
                            
                            // Eliminamos los espacios del nombre
                            $nombre = trim($nombre);

                            // Sanitizamos el nombre (eliminamos etiquetas)
                            $nombre = filter_var($nombre, FILTER_SANITIZE_STRING);

                            // Verificamos si el usuario ha ingresado algo dentro del campo de nombre
                            if (strlen($nombre) == 0) {
                                $errores -> append("Por favor, ingrese su nombre en el campo correspondiente.");
                            }
                        }

                        // Verificamos si el usuario subió un archivo dentro del input file del formulario de fotos
                        if (isset($_FILES['foto_perfil'])) {
                            // Obtenemos la foto y la información de la misma
                            $foto         = $_FILES['foto_perfil'];
                            $foto_nombre  = $foto['name'];
                            $foto_tmp     = $foto['tmp_name'];
                            $foto_tamanio = $foto['size'];
                            $foto_error   = $foto['error'];
                            $foto_tipo    = $foto['type'];

                            // Obtenemos la extensión de la foto
                            $foto_ext = explode('.', $foto_nombre);
                            $foto_ext_veridica = strtolower(end($foto_ext));

                            // Establecemos unos formatos permitidos
                            $formatos_foto_permitidos = array('jpg', 'jpeg', 'png');

                            // Verificamos si la foto subida por el usuario contiene el formato permitido
                            if (in_array($foto_ext_veridica, $formatos_foto_permitidos)) {
                                // Verificamos si no obtuvimos un error al subir la foto
                                if ($foto_error == 0) {
                                    // Si el usuario sube una foto inferior a 20,971,520 bytes (20 MB)
                                    if ($foto_tamanio <= 20971520) {
                                        $foto_nuevo_nombre = uniqid('', true) . '.' . $foto_ext_veridica;
                                        $foto_destino = '../img/fotos_de_perfil/' . $foto_nuevo_nombre;

                                        // Movemos la foto a nuestra carpeta de fotos
                                        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $foto_destino)) {
                                            // Indicamos que la foto se ha subido correctamente
                                            $foto_subida = true;
                                        } else {
                                            $foto_subida = false;
                                        }
                                    } else {
                                        // La foto es demasiado grande
                                        $errores -> append('La foto debe de pesar menos de 20 MB.');
                                    }
                                } else {
                                    // Se produjo un error al subir la foto
                                    $errores -> append('Se produjo un error al subir la foto.');
                                }
                            } else {
                                // No puedes subir archivos de este tipo
                                $errores -> append('Solo se admiten archivos de fotos .jpg, .jpeg, .png');
                            }
                        }

                        // Si no tenemos errores...
                        if (($errores -> count()) == 0) {
                            // Dependiendo si el usuario subió o no una foto de perfil será el query que ejecutaremos a la base de datos
                            if (!$foto_subida) {
                                $instruccion = "UPDATE Usuarios SET Nombre = :nombre WHERE ID = :id";
                            } else {
                                $instruccion = "UPDATE Usuarios SET Nombre = :nombre, foto_perfil = :foto_perfil WHERE ID = :id";
                            }

                            // Preparamos la consulta
                            $query = $conexion -> prepare($instruccion);
                            $query -> bindParam(':nombre', $nombre);
                            $query -> bindParam(':id', $id);

                            if ($foto_subida) {
                                $query -> bindParam(':foto_perfil', $foto_nuevo_nombre);
                            }
                            
                            // Ejecutamos la consulta
                            if ($query -> execute()) {
                                // Creamos una respuesta
                                $respuesta = array(
                                    'status' => 'OK',
                                    'resultados' => array(
                                        'nombre' => $nombre
                                    )
                                );

                                // Mandamos la respuesta a nuestro AJAX
                                echo json_encode($respuesta);
                            } else {
                                // Creamos una respuesta
                                $respuesta = array(
                                    'status' => 'FAIL'
                                );

                                // Mandamos la respuesta a nuestro AJAX
                                echo json_encode($respuesta);
                            }
                        } else {
                            // Creamos una respuesta
                            $respuesta = array(
                                'status' => 'FAIL',
                                'errores' => $errores
                            );

                            // Mandamos la respuesta a nuestro AJAX
                            echo json_encode($respuesta);
                        }
                    }

                    // Si el usuario quiere actualizar su correo...
                    if ($subrequest == 'enviarCambioCorreo') {
                        // Creamos una variable de error
                        $error = new ArrayObject();

                        // Si tenemos un correo guardado en $_POST...
                        if (isset($_POST['correo'])) {
                            $correo = $_POST['correo'];
                        }

                        // Verificamos si el nuevo correo que eligió el usuario ya existe en la base de datos
                        $instruccion = "SELECT * FROM Usuarios WHERE Correo = :correo";
                        $query = $conexion -> prepare($instruccion);
                        $query -> bindParam(':correo', $correo);

                        
                        if ($query -> execute()) {
                            // Si ese correo existe en la base de datos...
                            if ($query -> rowCount() > 0) {
                                // Obtenemos los datos de la consulta
                                $resultados = $query -> fetch();

                                // Iniciamos una sesión
                                session_start();
                        
                                // Si tenemos una sesión abierta y tenemos un ID dentro de $_SESSION...
                                if ((session_status() == PHP_SESSION_ACTIVE) && (isset($_SESSION['id']))) {
                                    // Obtenemos el ID
                                    $id = $_SESSION['id'];
                                }

                                // Si el ID obtenido de la consulta es el mismo del ID de la sesión actual...
                                if ($resultados['ID'] == $id) {
                                    // El usuario está intentando cambiar su correo por el mismo correo que tiene actualmente
                                    $error -> append("El correo al que usted intenta cambiar ya estaba ligado a su cuenta");
                                } else {
                                    // El usuario está intentando está intentando cambiar de correo por el de otro usuario que si está registrado en el sitio
                                    $error -> append("El correo que ingresó le pertenece a otro usuario. Por favor, ingrese un correo que no esté vinculado a Tasker.");
                                }

                                // Creamos una respuesta
                                $respuesta = array(
                                    'status' => 'FAIL',
                                    'correoExistente' => true,
                                    'error' => $error
                                );

                                // Maandamos la respuesta al AJAX
                                echo json_encode($respuesta);
                            } else {
                                // Creamos una respuesta
                                $respuesta = array(
                                    'status' => 'OK',
                                    'correoExistente' => false
                                );

                                // Mandamos la respuesta al AJAX
                                echo json_encode($respuesta);
                            }
                        }
                        
                    }

                    // Si el usuario ha colocado el código de verificación correcto se le cambiará el correo electrónico
                    if ($subrequest == 'actualizarCorreo') {
                        // Iniciamos una sesión
                        session_start();
                
                        // Si tenemos una sesión abierta y tenemos un ID dentro de $_SESSION...
                        if ((session_status() == PHP_SESSION_ACTIVE) && (isset($_SESSION['id']))) {
                            // Obtenemos el ID
                            $id = $_SESSION['id'];
                        }

                        // Obtenemos el correo electrónico nuevo del usuario
                        if (isset($_POST['correo'])) {
                            $correo = $_POST['correo'];
                        }

                        // Obtenemos el código que ingresó el usuario
                        if (isset($_POST['claveDeVerificacion'])) {
                            $clave_de_verificacion = $_POST['claveDeVerificacion'];
                            $clave_de_verificacion = md5($clave_de_verificacion);
                        }

                        // Verificamos que el código que ingresó el usuario es el que realmente está en la base de datos
                        $instruccion = "SELECT * FROM Usuarios WHERE ID = :id AND clave_conf = :clave_de_verificacion";
                        $query = $conexion -> prepare($instruccion);
                        $query -> bindParam(':id', $id);
                        $query -> bindParam(':clave_de_verificacion', $clave_de_verificacion);

                        if ($query -> execute()) {
                            if ($query -> rowCount() > 0) {
                                $instruccion = "UPDATE Usuarios SET Correo = :correo WHERE ID = :id";
                                $query = $conexion -> prepare($instruccion);
                                $query -> bindParam(':correo', $correo);
                                $query -> bindParam(':id', $id);

                                if ($query -> execute()) {
                                    $respuesta = array(
                                        'status' => 'OK'
                                    );
    
                                    echo json_encode($respuesta);
                                } else {
                                    $respuesta = array(
                                        'status' => 'FAIL'
                                    );
    
                                    echo json_encode($respuesta);
                                }
                            } else {
                                $respuesta = array(
                                    'status' => 'FAIL'
                                );

                                echo json_encode($respuesta);
                            }
                        }
                    }

                    // Si el usuario quiere actualizar su contraseña...
                    if ($subrequest == 'actualizarContra') {
                        // Creamos una variable de errores
                        $errores = new ArrayObject();

                        // Iniciamos una sesión
                        session_start();
                
                        // Si tenemos una sesión abierta y tenemos un ID dentro de $_SESSION...
                        if ((session_status() == PHP_SESSION_ACTIVE) && (isset($_SESSION['id']))) {
                            // Obtenemos el ID
                            $id = $_SESSION['id'];
                        }

                        // Obtenemos la contraseña actual, la nueva contraseña y la nueva contraseña repetida
                        if (isset($_POST['contra_actual'])) {
                            $contra_actual = $_POST['contra_actual'];

                            if (strlen($contra_actual) == 0) {
                                $errores -> append("Por favor, ingrese su contraseña en el campo correspondiente.");
                            } else {
                                // Encriptamos la contraseña
                                $contra_actual = md5($contra_actual);
                            }
                        }

                        if (isset($_POST['contra_nueva'])) {
                            $contra_nueva = $_POST['contra_nueva'];
                        }

                        if (isset($_POST['contra_nueva_repetida'])) {
                            $contra_nueva_repetida = $_POST['contra_nueva_repetida'];
                        }

                        if (strlen($contra_nueva) == 0 && strlen($contra_nueva_repetida) == 0) {
                            $errores -> append("Por favor, rellene los campos para la nueva contraseña.");
                        }

                        if ((strlen($contra_nueva) != 0 && strlen($contra_nueva_repetida) == 0) || (strlen($contra_nueva) == 0 && strlen($contra_nueva_repetida) != 0)) {
                            $errores -> append("Debe rellenar ambos campos para la nueva contraseña.");
                        }

                        if ((strlen($contra_nueva) > 0) && strlen($contra_nueva_repetida) > 0) {
                            if ($contra_nueva != $contra_nueva_repetida) {
                                $errores -> append("Las nuevas contraseñas no coinciden.");
                            }
                        }


                        // Hacemos una consulta a la base de datos para recuperar la contraseña actual del usuario y compararla para ver si es realmente la verdadera contraseña del usuario
                        $instruccion = "SELECT Contra FROM Usuarios WHERE ID = :id";
                        $query = $conexion -> prepare($instruccion);
                        $query -> bindParam(':id', $id);

                        if ($query -> execute()) {
                            $resultados = $query -> fetch();

                            // Verificamos si la contraseña que dió el usuario coincide con la de la base de datos
                            if (strlen($contra_actual) != 0) {
                                if ($resultados['Contra'] != $contra_actual) {
                                    $errores -> append("La contraseña es incorrecta.");
                                }
                            }

                            // Si no hay errores...
                            if (($errores -> count()) == 0) {
                                // Encriptamos la contraseña
                                $contra_nueva = md5($contra_nueva);

                                $instruccion = "UPDATE Usuarios SET Contra = :contra WHERE ID = :id";
                                $query = $conexion -> prepare($instruccion);
                                $query -> bindParam(':contra', $contra_nueva);
                                $query -> bindParam(':id', $id);

                                if ($query -> execute()) {
                                    $respuesta = array(
                                        'status' => 'OK'
                                    );

                                    echo json_encode($respuesta);
                                }
                            } else {
                                $respuesta = array(
                                    'status' => 'FAIL',
                                    'errores' => $errores
                                );

                                echo json_encode($respuesta);
                            }
                        }
                    }
                }
            }

            // Si el usuario quiere eliminar su cuenta
            if ($request == "verificarContra") {
                // Creamos una variable de errores
                $errores = new ArrayObject();

                // Iniciamos una sesión
                session_start();
                
                // Si tenemos una sesión abierta y tenemos un ID dentro de $_SESSION...
                if ((session_status() == PHP_SESSION_ACTIVE) && (isset($_SESSION['id']))) {
                    // Obtenemos el ID
                    $id = $_SESSION['id'];
                }

                // Obtenemos la contraseña del usuario
                if ($_POST['contra']) {
                    $contra = $_POST['contra'];

                    // Encriptamos la contraseña ya que la base de datos contiene las contraseñas encriptadas
                    $contra = md5($contra);
                }

                // Hacemos una consulta para verificar si la contraseña ingresada por el usuario es la que realmente está en la base de datos
                $instruccion = "SELECT * FROM Usuarios WHERE ID = :id AND Contra = :contra";
                $query = $conexion -> prepare($instruccion);
                $query -> bindParam(':id', $id);
                $query -> bindParam(':contra', $contra);

                // Ejecutamos la consulta
                if ($query -> execute()) {
                    // Si se han obtenido los resultados...
                    if ($query -> rowCount() > 0) {
                        // Creamos una respuesta
                        $respuesta = array(
                            'status' => 'OK'
                        );

                        // Enviamos la respuesta a nuestro AJAX
                        echo json_encode($respuesta);
                    } else {
                        // Significa que la contraseña no es la misma y que el usuario se ha equivocado
                        $errores -> append("La credencial es incorrecta. Por favor, verifíquela.");

                        // Creamos una respuesta
                        $respuesta = array(
                            'status' => 'FAIL',
                            'errores' => $errores
                        );

                        // Respondemos a la petición AJAX
                        echo json_encode($respuesta);
                    }
                }
            }

            if ($request == 'eliminarCuenta') {
                // Iniciamos una sesión
                session_start();
                
                // Si tenemos una sesión abierta y tenemos un ID dentro de $_SESSION...
                if ((session_status() == PHP_SESSION_ACTIVE) && (isset($_SESSION['id']))) {
                    // Obtenemos el ID
                    $id = $_SESSION['id'];
                }
                
                // Procedemos a eliminar las tareas del usuario
                $instruccion = "DELETE FROM Tareas WHERE ID_Usuario = :id";
                $query = $conexion -> prepare($instruccion);
                $query -> bindParam(':id', $id);

                // Ejecutamos la consulta (eliminamos las tareas del usuario)
                if ($query -> execute()) {
                    /* Ahora , eliminamos el usuario */
                    $instruccion = "DELETE FROM Usuarios WHERE ID = :id";
                    $query = $conexion -> prepare($instruccion);
                    $query -> bindParam('id', $id);

                    // Ejecutamos la consulta (eliminamos el usuario)
                    if ($query -> execute()) {
                        // Cerramos la sesión del usuario
                        cerrarSesion();
                    }
                }
            }

            // Envio de correos
            if ($request == "enviarCorreo") {
                // Obtenemos el tipo de correo
                if (isset($_POST['tipoCorreo'])) {
                    $tipo_correo = $_POST['tipoCorreo'];

                    // Verificamos el tipo de correo a mandar al usuario
                    if ($tipo_correo == "verificarCuenta") {
                        // Obtenemos el correo del usuario
                        if (isset($_POST['correo'])) {
                            $correo = $_POST['correo'];

                            // Le enviamos el correo al usuario
                            enviarCorreo($tipo_correo, $correo);
                        }
                    }
                    
                    // Si el usuario quiere cambiar el correo...
                    if ($tipo_correo == "cambiarCorreo") {
                        // Obtenemos el correo del usuario
                        if (isset($_POST['correo'])) {
                            $correo = $_POST['correo'];

                            // Le enviamos el correo al usuario
                            enviarCorreo($tipo_correo, $correo);
                        }
                    }
                }
            }
        }
    }

    function loginUsuario($correo, $contra) {
        // Llamamos a la conexión
        global $conexion;

        // Creamos una variable de errores
        $errores = new ArrayObject();

        // Realizamos el query para buscar al usuario
        $instruccion = "SELECT * FROM Usuarios WHERE Correo = :correo AND Contra = :contra";
        $query = $conexion -> prepare($instruccion);
        $query -> bindParam(':correo', $correo);
        $query -> bindParam(':contra', $contra);
        $query -> execute();
        $resultados = $query -> fetch();

        // Si se obtuvieron resultados de la cuenta...
        if ($query -> rowCount() > 0) {
            // Verificamos si las credenciales ingresadas pertenecen a una cuenta verificada
            if ($resultados['cuenta_conf'] == 0) {
                $errores -> append("Esta cuenta no ha sido verificada, por favor, revise su buzón para activar esta cuenta.");

                // Creamos una respuesta
                $respuesta = array(
                    'status' => 'FAIL',
                    'errores' => $errores
                );

                // Mandamos la respuesta a nuestra petición AJAX
                echo json_encode($respuesta);
            } else { // Si la cuenta ya ha sido confirmada anteriormente...
                // Mandamos la respuesta de que los datos son correctos
                $respuesta = array(
                    'id' => $resultados['ID'],
                    'correo' => $resultados['Correo'],
                    'contra' => $resultados['Contra'],
                    'status' => 'OK'
                );

                // Iniciamos una sesión
                session_start();

                // Si tenemos una sesión abierta...
                if (session_status() == PHP_SESSION_ACTIVE) {
                    // Guardamos el ID del usuario a loguear
                    $_SESSION['id'] = $resultados['ID'];
                }

                // Mandamos la respuesta en forma de JSON
                echo json_encode($respuesta);
            }
        } else {
            // Significa que las credenciales son incorrectas
            $errores -> append("Las credenciales ingresadas con incorrectas.");

            // Mandamos la respuesta de que los datos son incorrectos
            $respuesta = array(
                'status' => 'FAIL',
                'errores' => $errores
            );

            // Mandamos la respuesta en forma de JSON
            echo json_encode($respuesta);
        }
    }


    /**
     *  FUNCIONES
     */

     // Cerramos la sesión del usuario
     function cerrarSesion() {
         // Abrimos una sesión
         session_start();

         // Si tenemos una sesión abierta...
         if (session_status() != PHP_SESSION_NONE) {
             // Si tenemos datos en sesión...
             if (isset($_SESSION)) {
                 // Limpiamos los datos de sesión
                 $_SESSION = [];

                 // Destruimos la sesión
                 session_destroy();

                 // Creamos una respuesta para nuestra petición AJAX
                 $respuesta = array(
                     'status' => 'OK'
                 );

                 // Mandamos la respuesta a nuestro AJAX
                 echo json_encode($respuesta);
             }
         }
     }

    // Obtener la conexión a la base de datos
    function validarConexion() {
        try {
            // Obtenemos la base de datos
            $conexion = new PDO('mysql:host=localhost;dbname=Tasker', 'root', '1234');
            return $conexion;
        } catch(PDOException $PDOException) {
            // Imprimimos el error
            echo 'Error: ' . $PDOException->getMessage();
        }
    }

     // Envio de correos
    function enviarCorreo($tipo_correo, $correo) {
        // Llamamos a la conexión
        global $conexion;

        // Definimos el host
        $nombre_del_server = "";
        if ($_SERVER['SERVER_NAME'] == 'localhost') {
            $nombre_del_server = "localhost:8080";
        } else {
            $nombre_del_server = $_SERVER['SERVER_NAME'];
        }
        
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     // Habilita la encriptación TLS
            
            // Incluimos el archivo de configuración de PHP Mailer
            include './PHPMailer/configPHPMailer.php';
            
            // Recipientes
            $mail->setFrom('TaskerAppSoftware@gmail.com', 'Tasker');
            $mail->addAddress($correo);                               // Correo destinatario
    
            // Contenido
            $mail->isHTML(true);                                      // Permitir HTML en el correo
            
            /* Dependiendo del tipo de correo será lo que recibirá el usuario en su bandeja */
            // Verificación de la cuenta del usuario
            if ($tipo_correo == "verificarCuenta") {
                // Obtenemos la clave de validacion del usuario
                if (isset($_POST['clave_de_validacion'])) {
                    $clave_de_validacion = $_POST['clave_de_validacion'];
                }

                $mail->Subject = 'Verifica tu nueva cuenta de Tasker';
                $mail->Body    = '
                    <h1>¡Bienvenido a Tasker!</h1>
                    <p>Por favor, verifique su cuenta haciendo click <a href="http://' . $nombre_del_server . '/verificar?codigo=' . $clave_de_validacion . '">aqui</a></p>
                ';
            }

            if ($tipo_correo == 'cambiarCorreo') {
                // Generamos el código de verificación del 100,000 al 999,999
                $codigo_de_verificacion = rand(100000, 999999);

                // Iniciamos una sesión
                session_start();
                
                // Si tenemos una sesión abierta y tenemos un ID dentro de $_SESSION...
                if ((session_status() == PHP_SESSION_ACTIVE) && (isset($_SESSION['id']))) {
                    // Obtenemos el ID
                    $id = $_SESSION['id'];
                }

                $clave_encriptada = md5($codigo_de_verificacion);

                // Guardamos en la base de datos el código de verificación
                $instruccion = "UPDATE Usuarios SET clave_conf = :clave WHERE ID = :id";
                $query = $conexion -> prepare($instruccion);
                $query -> bindParam(':clave', $clave_encriptada);
                $query -> bindParam(':id', $id);

                if ($query -> execute()) {
                    $respuesta = array(
                        'status' => 'OK'
                    );

                    echo json_encode($respuesta);
                }

                $mail->Subject = 'Cambio de correo de Tasker';
                $mail->Body    = '
                    <h1>Solicitud de cambio de correo!</h1>
                    <p>El código de verificación es:</p>
                    <span style="background-color: #c7c7c7; border-radius: 6px; padding: 8px 10px; font-size: 1.2rem">' . $codigo_de_verificacion . '</span>
                    <p>Atentamente, el equipo de Tasker.</p>
                ';
            }
    
            // Enviamso el correo
            $mail->send();
        } catch (Exception $e) {
            echo "El mensaje no se pudo enviar. Mensaje de error: {$mail->ErrorInfo}";
        };      
    }
?>