<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: Ingresoadmin.php");
    exit();
}

// Obtener métricas dinámicas
$res_voluntarios = mysqli_query($conn, "SELECT COUNT(*) as total FROM seguridad_registros");
$fila_voluntarios = mysqli_fetch_assoc($res_voluntarios);
$total_voluntarios = $fila_voluntarios['total'];

$res_cais = mysqli_query($conn, "SELECT COUNT(*) as total FROM cais");
$total_cais = 0;
if ($res_cais) {
    $fila_cais = mysqli_fetch_assoc($res_cais);
    $total_cais = $fila_cais['total'];
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="../include/css/Style.css">


    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>ConectaVida - Panel de Administración</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary": "#3953bd",
                        "secondary": "#754aa1",
                        "background": "#f9f9ff",
                        "surface": "#ffffff",
                        "on-surface": "#161c27",
                        "on-surface-variant": "#444653",
                        "outline-variant": "#c5c5d5",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                        "primary-fixed": "#dde1ff",
                        "primary-fixed-dim": "#b9c3ff",
                        "secondary-fixed": "#f0dbff",
                        "tertiary-fixed": "#ffddb9"
                    },
                    "borderRadius": {
                        "lg": "0.5rem",
                        "xl": "0.75rem"
                    },
                    "spacing": {
                        "base": "8px",
                        "md": "16px",
                        "lg": "24px",
                        "xl": "32px",
                        "gutter": "24px",
                        "margin-desktop": "32px",
                        "container-max": "1280px"
                    },
                    "fontFamily": {
                        "body-md": ["Inter"],
                        "headline-md": ["Inter"],
                        "headline-lg": ["Inter"]
                    }
                }
            }
        }
    </script>
    <style>
        .brand-gradient {
            background: linear-gradient(135deg, #3953bd 0%, #754aa1 100%);
        }

        .brand-gradient-hover:hover {
            filter: brightness(110%);
        }

        .soft-sentinel {
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(226, 232, 240, 0.5);
        }
    </style>
</head>

<body class="bg-background text-on-surface font-body-md overflow-x-hidden">
    <!-- IA- CHAT -->
   <section class="red">

    <div class="chat-container">

        <span class="chat-text">
            Habla con IA
        </span>

        <a href="../chat.html">
            <img
                width="48"
                height="48"
                src="https://img.icons8.com/fluency/48/chat--v3.png"
                alt=""
            >
        </a>

    </div>

</section>

    <!-- TopAppBar -->
    <header class="bg-surface docked full-width top-0 sticky z-50 border-b border-outline-variant shadow-sm">
        <div class="flex justify-between items-center px-margin-desktop h-16 w-full max-w-container-max mx-auto">
            <div class="flex items-center gap-xl">
                <span class="text-2xl font-bold text-primary">ConectaVida</span>
                <nav class="hidden md:flex items-center gap-lg">
                    <a class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors" href="#">Emergencias</a>
                    <a class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors" href="voluntarios.php">Voluntarios</a>
                    <a class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors" href="#">Recursos</a>
                </nav>
            </div>
            <div class="flex items-center gap-md">
                <button class="brand-gradient text-white px-6 py-2 rounded-lg text-sm font-bold brand-gradient-hover active:scale-95 transition-all">Pedir Ayuda</button>
                <div class="flex gap-sm">
                    <span class="material-symbols-outlined text-on-surface-variant cursor-pointer p-2 hover:bg-background rounded-full">notifications</span>
                    <div class="relative group">
                        <span class="material-symbols-outlined text-on-surface-variant cursor-pointer p-2 hover:bg-background rounded-full">account_circle</span>
                        <div class="absolute right-0 pt-2 w-48 hidden group-hover:block z-50">
                            <div class="bg-surface border border-outline-variant rounded-lg shadow-lg overflow-hidden">
                                <div class="p-4 border-b border-outline-variant">
                                    <p class="font-bold text-sm"><?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
                                    <p class="text-xs text-on-surface-variant"><?php echo htmlspecialchars($_SESSION['usuario']); ?></p>
                                </div>
                                <a href="logout.php" class="flex items-center gap-2 p-4 hover:bg-background transition-colors text-error">
                                    <span class="material-symbols-outlined text-sm">logout</span>
                                    <span class="text-sm font-medium">Cerrar Sesión</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex min-h-[calc(100vh-64px)] w-full max-w-container-max mx-auto">
        <!-- Sidebar Navigation -->
        <aside class="hidden md:flex flex-col w-64 bg-surface border-r border-outline-variant py-8 px-4 gap-4">
            <div class="px-4 mb-4">
                <p class="text-xs uppercase text-on-surface-variant tracking-wider font-bold">Menú Principal</p>
            </div>
            <a class="flex items-center gap-4 p-4 rounded-lg brand-gradient text-white shadow-md" href="dashboard.php">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-medium">Dashboard</span>
            </a>
            <a class="flex items-center gap-4 p-4 rounded-lg text-on-surface-variant hover:bg-background transition-colors" href="voluntarios.php">
                <span class="material-symbols-outlined">groups</span>
                <span class="font-medium">Voluntarios</span>
            </a>
            <a class="flex items-center gap-4 p-4 rounded-lg text-on-surface-variant hover:bg-background transition-colors" href="#">
                <span class="material-symbols-outlined">analytics</span>
                <span class="font-medium">Reportes</span>
            </a>
            <a class="flex items-center gap-4 p-4 rounded-lg text-on-surface-variant hover:bg-background transition-colors mt-auto" href="#">
                <span class="material-symbols-outlined">settings</span>
                <span class="font-medium">Configuración</span>
            </a>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 p-8 overflow-y-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-on-surface mb-2">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h1>
                <p class="text-on-surface-variant">Monitoreo en tiempo real de la red comunitaria de ConectaVida.</p>
            </div>

            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-surface border border-outline-variant p-6 rounded-xl flex flex-col gap-2 hover:border-primary transition-colors cursor-default">
                    <div class="flex justify-between items-center">
                        <span class="material-symbols-outlined text-error p-2 bg-error-container rounded-lg">emergency</span>
                        <span class="text-error text-xs font-bold">+3 hoy</span>
                    </div>
                    <span class="text-3xl font-bold">14</span>
                    <span class="text-sm text-on-surface-variant">Emergencias Activas</span>
                </div>
                <div class="bg-surface border border-outline-variant p-6 rounded-xl flex flex-col gap-2 hover:border-primary transition-colors cursor-default">
                    <div class="flex justify-between items-center">
                        <span class="material-symbols-outlined text-primary p-2 bg-primary-fixed rounded-lg">volunteer_activism</span>
                        <span class="text-primary text-xs font-bold">+<?php echo $total_voluntarios; ?></span>
                    </div>
                    <span class="text-3xl font-bold"><?php echo number_format($total_voluntarios); ?></span>
                    <span class="text-sm text-on-surface-variant">Voluntarios Registrados</span>
                </div>
                <div class="bg-surface border border-outline-variant p-6 rounded-xl flex flex-col gap-2 hover:border-primary transition-colors cursor-default">
                    <div class="flex justify-between items-center">
                        <span class="material-symbols-outlined text-secondary p-2 bg-secondary-fixed rounded-lg">location_on</span>
                    </div>
                    <span class="text-3xl font-bold"><?php echo $total_cais; ?></span>
                    <span class="text-sm text-on-surface-variant">Centros (CAIs)</span>
                </div>
                <div class="bg-surface border border-outline-variant p-6 rounded-xl flex flex-col gap-2 hover:border-primary transition-colors cursor-default">
                    <div class="flex justify-between items-center">
                        <span class="material-symbols-outlined text-amber-600 p-2 bg-amber-100 rounded-lg">bolt</span>
                        <span class="text-amber-600 text-xs font-bold">Óptimo</span>
                    </div>
                    <span class="text-3xl font-bold">99.8%</span>
                    <span class="text-sm text-on-surface-variant">Uptime del Servicio</span>
                </div>
            </div>

            <!-- Activity Feed and Map (Static for now) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
                <div class="lg:col-span-5 bg-surface border border-outline-variant rounded-xl p-6 h-full">
                    <h3 class="text-xl font-bold mb-6">Actividad Reciente</h3>
                    <div class="flex flex-col gap-4">
                        <div class="flex gap-4 p-4 rounded-lg hover:bg-background transition-colors border-l-4 border-error">
                            <div class="w-12 h-12 bg-error-container rounded-full flex items-center justify-center text-error">
                                <span class="material-symbols-outlined">medical_services</span>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-sm">Alerta Médica: Zona Norte</p>
                                <p class="text-xs text-on-surface-variant">Solicitud de primeros auxilios enviada.</p>
                                <span class="text-xs text-on-surface-variant opacity-60">Hace 2 minutos</span>
                            </div>
                        </div>
                        <div class="flex gap-4 p-4 rounded-lg hover:bg-background transition-colors border-l-4 border-primary">
                            <div class="w-12 h-12 bg-primary-fixed rounded-full flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-sm">Nuevo Voluntario</p>
                                <p class="text-xs text-on-surface-variant">Un nuevo miembro se ha unido a la red.</p>
                                <span class="text-xs text-on-surface-variant opacity-60">Hace 15 minutos</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-7 bg-surface border border-outline-variant rounded-xl overflow-hidden relative h-[400px]">
                    <div class="absolute top-4 left-4 z-10 glass-card p-4 rounded-lg shadow-sm">
                        <h3 class="text-sm font-bold mb-1">Mapa de Cobertura</h3>
                        <div class="flex gap-2 items-center">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs text-on-surface-variant">Sistema Operativo</span>
                        </div>
                    </div>
                    <div class="w-full h-full bg-slate-100 flex items-center justify-center text-on-surface-variant italic">
                        Visualización de Mapa Interactiva (Cargando...)
                    </div>
                </div>
            </div>
        </main>
    </div>

    <footer class="bg-primary text-white border-t border-primary-container">
        <div class="w-full py-8 px-8 flex flex-col items-center gap-4">
            <span class="text-xl font-bold">ConectaVida</span>
            <p class="text-sm opacity-70">© 2026 ConectaVida. Sistema de Respuesta Comunitaria de Precisión.</p>
        </div>
    </footer>
</body>

</html>