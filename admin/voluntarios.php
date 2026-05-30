<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: Ingresoadmin.php");
    exit();
}

// Obtener todos los registros de seguridad_registros
$query = "SELECT * FROM seguridad_registros ORDER BY id DESC";
$resultado = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Gestión de Voluntarios - ConectaVida</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
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
                        "error-container": "#ffdad6"
                    }
                }
            }
        }
    </script>
    <style>
        .brand-gradient {
            background: linear-gradient(135deg, #3953bd 0%, #754aa1 100%);
        }
        .soft-sentinel {
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-background text-on-surface font-sans antialiased">
    <!-- TopAppBar -->
    <header class="bg-surface border-b border-outline-variant sticky top-0 z-50">
        <div class="flex justify-between items-center px-8 h-16 max-w-7xl mx-auto">
            <div class="flex items-center gap-8">
                <span class="text-xl font-bold text-primary">ConectaVida</span>
                <nav class="hidden md:flex items-center gap-6">
                    <a class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors" href="dashboard.php">Dashboard</a>
                    <a class="text-sm font-bold text-primary border-b-2 border-primary h-16 flex items-center" href="voluntarios.php">Voluntarios</a>
                </nav>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative group">
                    <span class="material-symbols-outlined text-on-surface-variant cursor-pointer p-2 hover:bg-background rounded-full">account_circle</span>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-on-surface">Gestión de Voluntarios</h1>
                <p class="text-on-surface-variant">Administra la base de datos de voluntarios y usuarios registrados.</p>
            </div>
            <button onclick="openModal('add')" class="brand-gradient text-white px-6 py-2 rounded-lg font-bold shadow-md hover:brightness-110 active:scale-95 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">person_add</span>
                Nuevo Voluntario
            </button>
        </div>

        <!-- Volunteers Table -->
        <div class="bg-surface border border-outline-variant rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-background border-b border-outline-variant">
                            <th class="p-4 font-bold text-xs uppercase text-on-surface-variant">ID</th>
                            <th class="p-4 font-bold text-xs uppercase text-on-surface-variant">Nombre Completo</th>
                            <th class="p-4 font-bold text-xs uppercase text-on-surface-variant">Documento</th>
                            <th class="p-4 font-bold text-xs uppercase text-on-surface-variant">Email</th>
                            <th class="p-4 font-bold text-xs uppercase text-on-surface-variant">Dirección</th>
                            <th class="p-4 font-bold text-xs uppercase text-on-surface-variant">Género</th>
                            <th class="p-4 font-bold text-xs uppercase text-on-surface-variant text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        <?php if (mysqli_num_rows($resultado) > 0): ?>
                            <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                                <tr class="hover:bg-background/50 transition-colors">
                                    <td class="p-4 text-sm"><?php echo $fila['id']; ?></td>
                                    <td class="p-4">
                                        <p class="text-sm font-bold"><?php echo htmlspecialchars($fila['nombre'] . ' ' . $fila['apellidos']); ?></p>
                                    </td>
                                    <td class="p-4 text-sm text-on-surface-variant">
                                        <?php echo htmlspecialchars($fila['tipo_documento'] . ': ' . $fila['numero_documento']); ?>
                                    </td>
                                    <td class="p-4 text-sm"><?php echo htmlspecialchars($fila['email']); ?></td>
                                    <td class="p-4 text-sm text-on-surface-variant"><?php echo htmlspecialchars($fila['direccion']); ?></td>
                                    <td class="p-4 text-sm">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase <?php echo ($fila['genero'] == 'Masculino') ? 'bg-blue-100 text-blue-700' : ($fila['genero'] == 'Femenino' ? 'bg-pink-100 text-pink-700' : 'bg-gray-100 text-gray-700'); ?>">
                                            <?php echo htmlspecialchars($fila['genero']); ?>
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-center gap-2">
                                            <button onclick='editVolunteer(<?php echo json_encode($fila); ?>)' class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Editar">
                                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                            </button>
                                            <button onclick="deleteVolunteer(<?php echo $fila['id']; ?>)" class="p-2 text-error hover:bg-error/10 rounded-lg transition-colors" title="Eliminar">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="p-8 text-center text-on-surface-variant italic">No se encontraron voluntarios registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal for Add/Edit -->
    <div id="volunteerModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[100] backdrop-blur-sm">
        <div class="bg-surface w-full max-w-lg rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="p-6 border-b border-outline-variant flex justify-between items-center">
                <h3 id="modalTitle" class="text-xl font-bold">Agregar Nuevo Voluntario</h3>
                <button onclick="closeModal()" class="text-on-surface-variant hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="volunteerForm" action="voluntarios_accion.php" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="id" id="form_id">
                <input type="hidden" name="accion" id="form_accion" value="agregar">
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase text-on-surface-variant">Nombre</label>
                        <input type="text" name="nombre" id="form_nombre" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary text-sm">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase text-on-surface-variant">Apellidos</label>
                        <input type="text" name="apellidos" id="form_apellidos" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase text-on-surface-variant">Tipo Documento</label>
                        <select name="tipo_documento" id="form_tipo_documento" class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary text-sm">
                            <option value="CC">Cédula de Ciudadanía</option>
                            <option value="TI">Tarjeta de Identidad</option>
                            <option value="CE">Cédula de Extranjería</option>
                            <option value="Pasaporte">Pasaporte</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase text-on-surface-variant">Número</label>
                        <input type="text" name="numero_documento" id="form_numero_documento" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary text-sm">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase text-on-surface-variant">Email</label>
                    <input type="email" name="email" id="form_email" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary text-sm">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase text-on-surface-variant">Dirección</label>
                    <input type="text" name="direccion" id="form_direccion" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary text-sm">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase text-on-surface-variant">Género</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="genero" value="Masculino" checked class="text-primary focus:ring-primary"> Masculino
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="genero" value="Femenino" class="text-primary focus:ring-primary"> Femenino
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="genero" value="Otro" class="text-primary focus:ring-primary"> Otro
                        </label>
                    </div>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-outline-variant rounded-lg text-sm font-bold hover:bg-background transition-colors">Cancelar</button>
                    <button type="submit" class="flex-1 px-4 py-2 brand-gradient text-white rounded-lg text-sm font-bold shadow-md hover:brightness-110 transition-all">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(mode) {
            const modal = document.getElementById('volunteerModal');
            const title = document.getElementById('modalTitle');
            const form = document.getElementById('volunteerForm');
            const accion = document.getElementById('form_accion');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            if (mode === 'add') {
                title.innerText = 'Agregar Nuevo Voluntario';
                form.reset();
                accion.value = 'agregar';
                document.getElementById('form_id').value = '';
            }
        }

        function closeModal() {
            const modal = document.getElementById('volunteerModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function editVolunteer(data) {
            openModal('edit');
            document.getElementById('modalTitle').innerText = 'Editar Voluntario';
            document.getElementById('form_accion').value = 'modificar';
            
            document.getElementById('form_id').value = data.id;
            document.getElementById('form_nombre').value = data.nombre;
            document.getElementById('form_apellidos').value = data.apellidos;
            document.getElementById('form_tipo_documento').value = data.tipo_documento;
            document.getElementById('form_numero_documento').value = data.numero_documento;
            document.getElementById('form_email').value = data.email;
            document.getElementById('form_direccion').value = data.direccion;
            
            const radioButtons = document.getElementsByName('genero');
            for (let rb of radioButtons) {
                if (rb.value === data.genero) {
                    rb.checked = true;
                    break;
                }
            }
        }

        function deleteVolunteer(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'voluntarios_accion.php';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = id;
                
                const accionInput = document.createElement('input');
                accionInput.type = 'hidden';
                accionInput.name = 'accion';
                accionInput.value = 'eliminar';
                
                form.appendChild(idInput);
                form.appendChild(accionInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
