<?php
// Habilitar reporte de errores de mysqli
mysqli_report(MYSQLI_REPORT_OFF); 

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'seguridad';

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>
