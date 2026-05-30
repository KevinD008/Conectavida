<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['usuario'])) {
    die("Acceso denegado. Debes iniciar sesión primero.");
}

$mensaje = "";

if (isset($_POST['hash_now'])) {
    $email = $_SESSION['usuario'];
    
    // Obtener la contraseña actual (en texto plano)
    $res = mysqli_query($conn, "SELECT password FROM admin WHERE email = '$email'");
    $fila = mysqli_fetch_assoc($res);
    $current_pass = $fila['password'];

    // Solo hashear si no parece ya un hash (los hashes de PHP suelen empezar por $)
    if (substr($current_pass, 0, 1) !== '$') {
        $hashed_pass = password_hash($current_pass, PASSWORD_DEFAULT);
        
        $sql = "UPDATE admin SET password = ? WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $hashed_pass, $email);
        
        if (mysqli_stmt_execute($stmt)) {
            $mensaje = "<p style='color: green;'>¡Éxito! Tu contraseña ha sido encriptada con éxito.</p>";
        } else {
            $mensaje = "<p style='color: red;'>Error al actualizar la contraseña.</p>";
        }
    } else {
        $mensaje = "<p style='color: orange;'>Tu contraseña ya parece estar encriptada.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguridad - Encriptar Contraseña</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
        .card { background: white; padding: 2rem; border-radius: 12px; shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 400px; text-align: center; }
        button { background: #3953bd; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        button:hover { background: #2c3e8c; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Mejora de Seguridad</h2>
        <p>Este asistente encriptará tu contraseña actual en la base de datos utilizando <b>bcrypt</b> para cumplir con los estándares modernos de seguridad.</p>
        <?php echo $mensaje; ?>
        <form method="POST">
            <button type="submit" name="hash_now">Encriptar Mi Contraseña Ahora</button>
        </form>
        <br>
        <a href="dashboard.php" style="color: #666; text-decoration: none; font-size: 0.9rem;">Volver al Dashboard</a>
    </div>
</body>
</html>
