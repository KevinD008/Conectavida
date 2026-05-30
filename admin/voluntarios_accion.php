<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: Ingresoadmin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $tipo_documento = $_POST['tipo_documento'] ?? '';
    $numero_documento = $_POST['numero_documento'] ?? '';
    $email = $_POST['email'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $genero = $_POST['genero'] ?? '';

    switch ($accion) {
        case 'agregar':
            $sql = "INSERT INTO seguridad_registros (nombre, apellidos, tipo_documento, numero_documento, email, direccion, genero) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssss", $nombre, $apellidos, $tipo_documento, $numero_documento, $email, $direccion, $genero);
            mysqli_stmt_execute($stmt);
            break;

        case 'modificar':
            $sql = "UPDATE seguridad_registros SET nombre=?, apellidos=?, tipo_documento=?, numero_documento=?, email=?, direccion=?, genero=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssssi", $nombre, $apellidos, $tipo_documento, $numero_documento, $email, $direccion, $genero, $id);
            mysqli_stmt_execute($stmt);
            break;

        case 'eliminar':
            $sql = "DELETE FROM seguridad_registros WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            break;
    }

    header("Location: voluntarios.php");
    exit();
}
?>
