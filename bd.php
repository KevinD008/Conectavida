<?php
// Conexión a la base de datos
$host = 'localhost'; // Dirección del servidor
$user = 'root'; // Usuario de la base de datos
$dbname = 'seguridad'; // Nombre de la base de datos
$conn = mysqli_connect($host, $user, "", $dbname) or die("Problemas en conexión: " . mysqli_connect_error()); // Conexión a la base de datos, muestra error si falla

// Verifica si la conexión se realizó correctamente
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error); // Muestra error de conexión
}

// Obtención de datos del formulario mediante POST
$id = isset($_POST['id']) ? $_POST['id'] : null;
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
$apellidos = isset($_POST['apellidos']) ? $_POST['apellidos'] : null;
$tipo_documento = isset($_POST['tipo_documento']) ? $_POST['tipo_documento'] : null;
$numero_documento = isset($_POST['numero_documento']) ? $_POST['numero_documento'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$direccion = isset($_POST['direccion']) ? $_POST['direccion'] : null;
$genero = isset($_POST['genero']) ? $_POST['genero'] : null;
$accion = isset($_POST['accion']) ? $_POST['accion'] : null;

// Estilos para la visualización de mensajes y tablas
echo "<style>
    body {
        font-family: Arial, sans-serif; /* Tipografía */
        background-color: #f4f6f7; /* Color de fondo */
        margin: 0; /* Elimina márgenes */
        padding: 20px; /* Padding interno */
        color: #34495e; /* Color del texto */
    }
    h2 {
        color: #3498db; /* Color del encabezado */
    }
    .message {
        padding: 15px; /* Padding interno para mensajes */
        margin-bottom: 20px; /* Espacio inferior */
        border-radius: 5px; /* Bordes redondeados */
    }
    .success {
        background-color: #2ecc71; /* Color de fondo para mensajes de éxito */
        color: white; /* Color del texto */
    }
    .error {
        background-color: #e74c3c; /* Color de fondo para mensajes de error */
        color: white; /* Color del texto */
    }
    table {
        width: 100%; /* Ancho completo para tablas */
        border-collapse: collapse; /* Colapsar bordes */
        margin-bottom: 20px; /* Espacio inferior */
    }
    table, th, td {
        border: 1px solid #bdc3c7; /* Bordes de la tabla */
    }
    th, td {
        padding: 10px; /* Padding interno para celdas */
        text-align: left; /* Alineación del texto */
    }
    th {
        background-color: #3498db; /* Color de fondo de encabezados */
        color: white; /* Color del texto de encabezados */
    }
    td {
        background-color: #ecf0f1; /* Color de fondo de celdas */
    }
</style>";

// Estructura de control para determinar la acción a realizar
switch($accion) {
    case 'agregar':
        // Agregar un nuevo registro a la base de datos
        $sql = "INSERT INTO seguridad_registros (nombre, apellidos, tipo_documento, numero_documento, email, direccion, genero) VALUES (?, ?, ?, ?, ?, ?, ?)"; // Consulta SQL para insertar
        $stmt = $conn->prepare($sql); // Prepara la consulta
        $stmt->bind_param("sssssss", $nombre, $apellidos, $tipo_documento, $numero_documento, $email, $direccion, $genero); // Vínculo de parámetros

        // Ejecución de la consulta
        if ($stmt->execute()) {
            echo "<div class='message success'>Registro agregado con éxito.</div>"; // Mensaje de éxito
        } else {
            echo "<div class='message error'>Error al agregar el registro: " . $stmt->error . "</div>"; // Mensaje de error
        }
        break;

    case 'modificar':
        // Modificar un registro existente en la base de datos
        $sql = "UPDATE seguridad_registros SET nombre=?, apellidos=?, tipo_documento=?, numero_documento=?, email=?, direccion=?, genero=? WHERE id=?"; // Consulta SQL para actualizar
        $stmt = $conn->prepare($sql); // Prepara la consulta
        $stmt->bind_param("sssssssi", $nombre, $apellidos, $tipo_documento, $numero_documento, $email, $direccion, $genero, $id); // Vínculo de parámetros
        
        // Ejecución de la consulta
        if ($stmt->execute()) {
            echo "<div class='message success'>Registro modificado con éxito.</div>"; // Mensaje de éxito
        } else {
            echo "<div class='message error'>Error al modificar el registro: " . $stmt->error . "</div>"; // Mensaje de error
        }
        break;

    case 'eliminar':
        // Eliminar un registro de la base de datos
        $sql = "DELETE FROM seguridad_registros WHERE id=?"; // Consulta SQL para eliminar
        $stmt = $conn->prepare($sql); // Prepara la consulta
        $stmt->bind_param("i", $id); // Vínculo de parámetros
        
        // Ejecución de la consulta
        if ($stmt->execute()) {
            echo "<div class='message success'>Registro eliminado con éxito.</div>"; // Mensaje de éxito
        } else {
            echo "<div class='message error'>Error al eliminar el registro: " . $conn->error . "</div>"; // Mensaje de error
        }
        break;

    case 'consultar_por_id':
        // Consultar un registro específico por ID
        if ($id !== null) {
            $sql = "SELECT * FROM seguridad_registros WHERE id = ?"; // Consulta SQL para seleccionar por ID
            $stmt = $conn->prepare($sql); // Prepara la consulta
            $stmt->bind_param("i", $id); // Vínculo de parámetros
            $stmt->execute(); // Ejecución de la consulta
            $result = $stmt->get_result(); // Obtención del resultado

            // Verifica si se encontró algún registro
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc(); // Obtiene los datos de la fila
                echo "<h2>Detalles del registro con ID: $id</h2>"; // Encabezado de detalles
                echo "<table>"; // Inicio de la tabla
                echo "<tr><th>Columna</th><th>Valor</th></tr>"; // Encabezado de la tabla
                foreach ($row as $columna => $valor) { // Recorre las columnas y valores
                    echo "<tr>";
                    echo "<td><strong>$columna</strong></td>"; // Muestra el nombre de la columna
                    echo "<td>$valor</td>"; // Muestra el valor correspondiente
                    echo "</tr>";
                }
                echo "</table>"; // Fin de la tabla
            } else {
                echo "<div class='message error'>No se encontró ningún registro con el ID: " . $id . "</div>"; // Mensaje de error si no se encuentra el ID
            }
        } else {
            echo "<div class='message error'>Error: No se proporcionó un ID válido.</div>"; // Mensaje de error si el ID es nulo
        }
        break;

    default:
        echo "<div class='message error'>Acción no válida.</div>"; // Mensaje de error si la acción no es válida
        break;
}

// Cierra la declaración si está definida
if (isset($stmt)) {
    $stmt->close(); // Cierre de la declaración preparada
}
$conn->close(); // Cierre de la conexión a la base de datos
?> 