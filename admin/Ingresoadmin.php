<?php
session_start();
require_once __DIR__ . '/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Usar sentencias preparadas para mayor seguridad
    $sql = "SELECT * FROM admin WHERE email=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($fila = mysqli_fetch_assoc($resultado)) {
        // Verificar contraseña (soporta hash o texto plano para transición inicial)
        if (password_verify($password, $fila['password']) || $password === $fila['password']) {
            $_SESSION['usuario'] = $fila['email'];
            $_SESSION['nombre'] = isset($fila['nombre']) ? $fila['nombre'] : 'Admin';

            echo "<script>
                    alert('Bienvenido');
                    window.location='dashboard.php';
                  </script>";
            exit();
        } else {
            $error = "Correo o contraseña incorrectos";
        }
    } else {
        $error = "Correo o contraseña incorrectos";
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-primary": "#ffffff",
                        "inverse-on-surface": "#ecf0ff",
                        "inverse-primary": "#b9c3ff",
                        "secondary-fixed": "#f0dbff",
                        "surface-container-highest": "#dde2f3",
                        "outline-variant": "#c5c5d5",
                        "on-primary-container": "#fffbff",
                        "on-secondary-fixed": "#2c0051",
                        "on-background": "#161c27",
                        "error-container": "#ffdad6",
                        "background": "#f9f9ff",
                        "secondary": "#754aa1",
                        "secondary-container": "#ce9ffd",
                        "on-error": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "on-secondary-container": "#5a3086",
                        "primary-fixed": "#dde1ff",
                        "secondary-fixed-dim": "#dcb8ff",
                        "primary": "#3953bd",
                        "on-secondary-fixed-variant": "#5c3187",
                        "surface-variant": "#dde2f3",
                        "tertiary-container": "#a56600",
                        "on-secondary": "#ffffff",
                        "on-primary-fixed-variant": "#1f3ba6",
                        "inverse-surface": "#2a303d",
                        "on-surface": "#161c27",
                        "on-tertiary-container": "#fffbff",
                        "on-surface-variant": "#444653",
                        "surface-tint": "#3c55bf",
                        "surface-dim": "#d4daea",
                        "primary-fixed-dim": "#b9c3ff",
                        "error": "#ba1a1a",
                        "on-tertiary-fixed": "#2b1700",
                        "surface-container-high": "#e3e8f9",
                        "surface": "#f9f9ff",
                        "on-tertiary-fixed-variant": "#663e00",
                        "tertiary-fixed-dim": "#ffb964",
                        "surface-bright": "#f9f9ff",
                        "primary-container": "#546cd7",
                        "on-error-container": "#93000a",
                        "tertiary-fixed": "#ffddb9",
                        "surface-container-low": "#f1f3ff",
                        "outline": "#757684",
                        "tertiary": "#835100",
                        "surface-container": "#e8eeff",
                        "on-tertiary": "#ffffff",
                        "on-primary-fixed": "#001356"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "xs": "4px",
                        "sm": "12px",
                        "base": "8px",
                        "xl": "32px",
                        "md": "16px",
                        "gutter": "24px",
                        "container-max": "1280px",
                        "lg": "24px"
                    },
                    "fontFamily": {
                        "headline-xl-mobile": ["Inter"],
                        "body-md": ["Inter"],
                        "label-sm": ["Inter"],
                        "headline-lg": ["Inter"],
                        "body-lg": ["Inter"],
                        "label-md": ["Inter"],
                        "headline-xl": ["Inter"],
                        "headline-md": ["Inter"]
                    },
                    "fontSize": {
                        "headline-xl-mobile": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "label-sm": ["12px", { "lineHeight": "16px", "fontWeight": "600" }],
                        "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }],
                        "label-md": ["14px", { "lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "500" }],
                        "headline-xl": ["48px", { "lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "headline-md": ["24px", { "lineHeight": "32px", "fontWeight": "600" }]
                    }
                },
            },
        }
    </script>
    <style>
        .brand-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .soft-sentinel-shadow {
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
        }

        .high-depth-shadow {
            box-shadow: 0px 12px 32px rgba(26, 32, 44, 0.1);
        }
    </style>
</head>

<body class="bg-surface font-body-md text-on-surface antialiased min-h-screen flex flex-col">
    <!-- Content Canvas -->
    <main class="flex-grow flex items-center justify-center brand-gradient p-md md:p-xl relative overflow-hidden">
        <!-- Decorative abstract background elements for high-end feel -->
        <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
            <div class="absolute -top-1/4 -left-1/4 w-1/2 h-1/2 bg-on-primary rounded-full blur-3xl"></div>
            <div class="absolute -bottom-1/4 -right-1/4 w-1/2 h-1/2 bg-secondary rounded-full blur-3xl"></div>
        </div>
        <!-- Login Card -->
        <div
            class="w-full max-w-[440px] bg-surface-container-lowest rounded-xl high-depth-shadow p-xl z-10 border border-outline-variant/30">
            <!-- Branding Header -->
            <div class="flex flex-col items-center mb-xl">
              
                <h1 class="font-headline-md text-headline-md text-primary tracking-tight mb-xs">ConectaVida</h1 >
                <p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Access Portal
                </p>
            </div>
            <!-- Login Form -->
            <?php if (!empty($error)): ?>
                <div class="bg-error-container text-on-error-container p-md rounded-lg mb-md text-center font-label-md">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form action="#" class="space-y-lg" method="POST">
                <!-- Email Field -->
                <div class="flex flex-col gap-base">
                    <label class="font-label-md text-label-md text-on-surface-variant" for="email">Correo
                        Electrónico</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-md flex items-center pointer-events-none">
                            <span
                                class="material-symbols-outlined text-outline text-md group-focus-within:text-primary transition-colors"
                                data-icon="alternate_email">alternate_email</span>
                        </div>
                        <input
                            class="w-full pl-11 pr-md py-md bg-surface-container-low border border-outline-variant rounded-lg font-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                            id="email" name="email" placeholder="nombre@conectavida.org" required="" type="email" />
                    </div>
                </div>
                <!-- Password Field -->
                <div class="flex flex-col gap-base">
                    <div class="flex justify-between items-center">
                        <label class="font-label-md text-label-md text-on-surface-variant"
                            for="password">Contraseña</label>
                        <a class="font-label-sm text-label-sm text-primary hover:underline transition-all"
                            href="#">¿Olvidaste tu contraseña?</a>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-md flex items-center pointer-events-none">
                            <span
                                class="material-symbols-outlined text-outline text-md group-focus-within:text-primary transition-colors"
                                data-icon="lock_open">lock_open</span>
                        </div>
                        <input
                            class="w-full pl-11 pr-md py-md bg-surface-container-low border border-outline-variant rounded-lg font-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                            id="password" name="password" placeholder="••••••••" required="" type="password" />
                        <div
                            class="absolute inset-y-0 right-0 pr-md flex items-center cursor-pointer text-outline hover:text-on-surface-variant">
                            <span class="material-symbols-outlined text-md" data-icon="visibility">visibility</span>
                        </div>
                    </div>
                </div>
                <!-- Remember Me Toggle -->
                <div class="flex items-center gap-md">
                    <div class="relative flex items-center h-5">
                        <input
                            class="h-5 w-5 rounded border-outline-variant text-primary focus:ring-primary/20 focus:ring-offset-0 transition-all cursor-pointer"
                            id="remember" name="remember" type="checkbox" />
                    </div>
                    <label class="font-label-md text-label-md text-on-surface-variant cursor-pointer select-none"
                        for="remember">Mantenerme conectado</label>
                </div>
                <!-- Submit Button -->
                <button
                    class="w-full brand-gradient text-on-primary font-label-md text-label-md py-md rounded-lg font-bold shadow-md hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-base"
                    type="submit">
                    <span>Iniciar Sesión</span>
                    <span class="material-symbols-outlined text-[18px]" data-icon="login">login</span>
                </button>
            </form>
            <!-- Support Footer -->
            <div class="mt-xl pt-lg border-t border-outline-variant/30 text-center">
                <p class="font-body-md text-on-surface-variant text-[13px]">
                    ¿Tienes problemas para acceder? <br /> Contacta al
                    <a class="text-primary font-semibold hover:underline" href="#">Soporte Técnico de ConectaVida</a>
                </p>
            </div>
        </div>
        <!-- Security Badge -->
        <div
            class="absolute bottom-xl left-1/2 -translate-x-1/2 flex items-center gap-xs text-on-primary/60 font-label-sm text-label-sm">
            <span class="material-symbols-outlined text-[14px]" data-icon="verified_user">verified_user</span>
            <span>Conexión Encriptada SSL de 256 bits</span>
        </div>
    </main>
    <!-- Footer - Rendered based on Shared Components JSON -->
    <footer
        class="w-full py-12 px-margin-desktop flex flex-col items-center gap-8 bg-primary dark:bg-primary-container border-t border-primary-container dark:border-primary">
        <div class="font-headline-md text-headline-md text-on-primary dark:text-on-primary-container font-bold">
            ConectaVida</div>
        <nav class="flex flex-wrap justify-center gap-xl">
            <a class="font-label-sm text-label-sm text-on-primary/80 dark:text-on-primary-container/80 hover:text-on-primary hover:underline decoration-on-primary-container transition-opacity cursor-pointer"
                href="#">Privacidad</a>
            <a class="font-label-sm text-label-sm text-on-primary/80 dark:text-on-primary-container/80 hover:text-on-primary hover:underline decoration-on-primary-container transition-opacity cursor-pointer"
                href="#">Términos de Servicio</a>
            <a class="font-label-sm text-label-sm text-on-primary/80 dark:text-on-primary-container/80 hover:text-on-primary hover:underline decoration-on-primary-container transition-opacity cursor-pointer"
                href="#">Contacto Emergente</a>
            <a class="font-label-sm text-label-sm text-on-primary/80 dark:text-on-primary-container/80 hover:text-on-primary hover:underline decoration-on-primary-container transition-opacity cursor-pointer"
                href="#">Red de Centros</a>
            <a class="font-label-sm text-label-sm text-on-primary/80 dark:text-on-primary-container/80 hover:text-on-primary hover:underline decoration-on-primary-container transition-opacity cursor-pointer"
                href="#">Documentación</a>
        </nav>
        <p class="font-body-md text-body-md text-on-primary dark:text-on-primary-container opacity-90 text-center">
            © 2026 ConectaVida. Sistema de Respuesta Comunitaria de Precisión.
        </p>
    </footer>
</body>

</html>