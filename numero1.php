<?php
// Configuración de la API Key de Google Maps
$apiKey = "AIzaSyCZQsbrj4JdE1ahw26iQSr6P01wU9Wcj3s";

// Verificamos si se enviaron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $document_type = $_POST['document_type'] ?? '';
    $document_number = $_POST['document_number'] ?? '';
    $email = $_POST['email'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $genero = $_POST['genero'] ?? '';

    // Validación básica (se puede expandir)
    if (empty($nombre) || empty($apellido) || empty($document_type) || 
        empty($document_number) || empty($email) || empty($direccion) || empty($genero)) {
        die("Error: Todos los campos son requeridos");
    }

    try {
        // Conectar a la base de datos (usando MySQLi en lugar de PDO para mantener consistencia con tu código)
        $conexion = mysqli_connect("localhost", "root", "", "seguridad")
            or die("Problemas en la conexión: " . mysqli_connect_error());

        // 1. Almacenar los datos del usuario en la tabla seguridad_registros
        $sql = "INSERT INTO seguridad_registros (nombre, apellidos, tipo_documento, numero_documento, email, direccion, genero)
                VALUES ('$nombre', '$apellido', '$document_type', '$document_number', '$email', '$direccion', '$genero')";

        // Ejecutar la consulta de inserción
        if (!mysqli_query($conexion, $sql)) {
            die("Error al guardar los datos: " . mysqli_error($conexion));
        }
        
        // 2. Buscar el CAI más cercano
        // Consulta para obtener todos los CAIs de la base de datos
        $resultado_cais = mysqli_query($conexion, "SELECT * FROM cais");
        
        if (!$resultado_cais) {
            die("Error en la consulta de CAIs: " . mysqli_error($conexion));
        }
        
        // Convertir el resultado a un array para procesarlo
        $cais = [];
        while ($fila = mysqli_fetch_assoc($resultado_cais)) {
            $cais[] = $fila;
        }
        
        // Si no hay CAIs en la base de datos
        if (count($cais) == 0) {
            die("No se encontraron CAIs en la base de datos");
        }

        // Primero intentamos el método anterior de coincidencia por texto
        $direccion_lower = strtolower($direccion);
        $cai_cercano = null;
        $cai_encontrado = false;
        $metodo_busqueda = "texto"; // Por defecto usamos búsqueda por texto
        
        // Buscar coincidencias de sector o ubicación en la dirección
        foreach ($cais as $cai) {
            $sector = strtolower($cai['sector'] ?? '');
            $ubicacion = strtolower($cai['ubicacion'] ?? '');
            
            // Buscamos coincidencias en cualquier parte del texto
            if (strpos($direccion_lower, $sector) !== false || 
                strpos($direccion_lower, $ubicacion) !== false ||
                strpos($direccion_lower, strtolower('KENNEDY')) !== false) {
                
                $cai_cercano = $cai;
                $cai_encontrado = true;
                break; // Tomamos el primer CAI que coincida
            }
        }
        
        // Si no encontramos coincidencias exactas, usamos la API de Google Maps para calcular distancias
        if (!$cai_encontrado && count($cais) > 0) {
            $metodo_busqueda = "mapa"; // Cambiamos a búsqueda por mapa
            
            // 1. Geocodificar la dirección del usuario para obtener lat/lng
            $direccion_usuario_encoded = urlencode($direccion);
            $url_geocode = "https://maps.googleapis.com/maps/api/geocode/json?address={$direccion_usuario_encoded}&key={$apiKey}";
            
            $response_geocode = file_get_contents($url_geocode);
            $geocode_data = json_decode($response_geocode, true);
            
            if ($geocode_data['status'] == 'OK') {
                $lat_usuario = $geocode_data['results'][0]['geometry']['location']['lat'];
                $lng_usuario = $geocode_data['results'][0]['geometry']['location']['lng'];
                
                // Calculamos la distancia a cada CAI
                $distancia_minima = PHP_FLOAT_MAX;
                
                foreach ($cais as $cai) {
                    // Verificamos si el CAI tiene una dirección para geocodificar
                    if (!empty($cai['direccion'])) {
                        $direccion_cai_encoded = urlencode($cai['direccion']);
                        $url_geocode_cai = "https://maps.googleapis.com/maps/api/geocode/json?address={$direccion_cai_encoded}&key={$apiKey}";
                        
                        $response_geocode_cai = file_get_contents($url_geocode_cai);
                        $geocode_data_cai = json_decode($response_geocode_cai, true);
                        
                        if ($geocode_data_cai['status'] == 'OK') {
                            $lat_cai = $geocode_data_cai['results'][0]['geometry']['location']['lat'];
                            $lng_cai = $geocode_data_cai['results'][0]['geometry']['location']['lng'];
                            
                            // Calculamos la distancia usando la fórmula de Haversine
                            $distancia = calcularDistanciaHaversine($lat_usuario, $lng_usuario, $lat_cai, $lng_cai);
                            
                            // Guardamos el CAI más cercano
                            if ($distancia < $distancia_minima) {
                                $distancia_minima = $distancia;
                                $cai_cercano = $cai;
                                $cai_cercano['distancia'] = number_format($distancia, 2); // Guardamos la distancia en km
                                $cai_encontrado = true;
                            }
                        }
                    }
                }
            }
            
            // Si por alguna razón falla la API de Google Maps, volvemos al primer CAI como fallback
            if ($cai_cercano === null && count($cais) > 0) {
                $cai_cercano = $cais[0];
                $metodo_busqueda = "fallback";
            }
        }
        
        // Cerrar la conexión a la base de datos
        mysqli_close($conexion);
        
    } catch(Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

// Función para calcular la distancia entre dos puntos geográficos usando la fórmula de Haversine
function calcularDistanciaHaversine($lat1, $lon1, $lat2, $lon2) {
    $radio_tierra = 6371; // Radio de la Tierra en km
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distancia = $radio_tierra * $c; // Distancia en kilómetros
    
    return $distancia;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - CAI más cercano</title>
    <style>
        /* Estilos similares al formulario original para mantener consistencia */
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --background-color: #f4f6f7;
            --text-color: #34495e;
            --input-background: #fff;
            --input-border: #bdc3c7;
            --error-color: #e74c3c;
            --button-hover: #27ae60;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .result-container {
            background-color: var(--input-background);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px var(--shadow-color);
            max-width: 600px;
            width: 100%;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1, h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 2.2em;
            text-shadow: 1px 1px 2px var(--shadow-color);
        }

        h2 {
            font-size: 1.8em;
            margin-top: 30px;
        }

        .success-message {
            background-color: rgba(46, 204, 113, 0.1);
            border-left: 4px solid var(--secondary-color);
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
            text-align: center;
            font-weight: bold;
            color: var(--secondary-color);
        }
        
        .warning-message {
            background-color: rgba(241, 196, 15, 0.1);
            border-left: 4px solid #f1c40f;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
            text-align: center;
            font-weight: bold;
            color: #f39c12;
        }

        .cai-info, .user-info {
            background-color: rgba(52, 152, 219, 0.1);
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .cai-info p, .user-info p {
            margin: 8px 0;
            line-height: 1.6;
        }

        .cai-title {
            font-weight: bold;
            color: var(--primary-color);
            font-size: 1.2em;
        }

        .back-button {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            text-align: center;
            transition: background-color 0.3s, transform 0.1s ease-in-out;
            margin-top: 20px;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: var(--button-hover);
            transform: translateY(-2px);
        }

        .data-divider {
            border-top: 1px solid #e1e8ed;
            margin: 30px 0;
        }

        .data-item {
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .label {
            font-weight: bold;
            color: var(--primary-color);
        }

        .value {
            color: var(--text-color);
            margin-left: 5px;
        }

        @media (max-width: 600px) {
            .result-container {
                padding: 20px;
            }

            h1 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="result-container">
        <?php if (isset($nombre) && isset($cai_cercano)): ?>
            <div class="success-message">
                <p>¡Registro completado exitosamente!</p>
            </div>
            
            <h1>Datos Recibidos</h1>
            <div class="user-info">
                <div class="data-item">
                    <span class="label">Nombre:</span>
                    <span class="value"><?php echo htmlspecialchars($nombre); ?></span>
                </div>
                <div class="data-item">
                    <span class="label">Apellido:</span>
                    <span class="value"><?php echo htmlspecialchars($apellido); ?></span>
                </div>
                <div class="data-item">
                    <span class="label">Tipo de Documento:</span>
                    <span class="value"><?php echo htmlspecialchars($document_type); ?></span>
                </div>
                <div class="data-item">
                    <span class="label">Número de Documento:</span>
                    <span class="value"><?php echo htmlspecialchars($document_number); ?></span>
                </div>
                <div class="data-item">
                    <span class="label">Email:</span>
                    <span class="value"><?php echo htmlspecialchars($email); ?></span>
                </div>
                <div class="data-item">
                    <span class="label">Dirección:</span>
                    <span class="value"><?php echo htmlspecialchars($direccion); ?></span>
                </div>
                <div class="data-item">
                    <span class="label">Género:</span>
                    <span class="value"><?php echo htmlspecialchars($genero); ?></span>
                </div>
            </div>
            
            <div class="data-divider"></div>
            
            <h2>Centro de Atención Inmediata (CAI) más cercano</h2>
            
            <?php if (!$cai_encontrado): ?>
            <div class="warning-message">
                <p>No se encontró un CAI específico para la dirección ingresada. Mostrando el CAI más cercano disponible.</p>
            </div>
            <?php elseif ($metodo_busqueda == "mapa"): ?>
            <div class="success-message">
                <p>Se encontró el CAI físicamente más cercano a su ubicación utilizando el mapa.</p>
                <p>Distancia aproximada: <?php echo $cai_cercano['distancia'] ?? ''; ?> km</p>
            </div>
            <?php endif; ?>
            
            <div class="cai-info">
                <p class="cai-title">CAI <?php echo htmlspecialchars($cai_cercano['nombre_lugar'] ?? 'No disponible'); ?></p>
                <p><strong>Sector:</strong> <?php echo htmlspecialchars($cai_cercano['sector'] ?? 'No disponible'); ?></p>
                <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($cai_cercano['ubicacion'] ?? 'No disponible'); ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($cai_cercano['direccion'] ?? 'No disponible'); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($cai_cercano['tel_cai'] ?? 'No disponible'); ?></p>
                <p><strong>Teléfono Cuadrante:</strong> <?php echo htmlspecialchars($cai_cercano['tel_cuadrante'] ?? 'No disponible'); ?></p>
                <p><strong>Código:</strong> <?php echo htmlspecialchars($cai_cercano['codigo'] ?? 'No disponible'); ?></p>
            </div>
            
            <!-- Mapa de Google Maps que muestra la ubicación del CAI -->
            <?php if ($metodo_busqueda == "mapa" && isset($lat_usuario) && isset($lng_usuario) && isset($lat_cai) && isset($lng_cai)): ?>
            <div id="map" style="width: 100%; height: 400px; margin-top: 20px; border-radius: 8px;"></div>
            <script>
                function initMap() {
                    // Ubicación del usuario
                    var userLocation = {
                        lat: <?php echo $lat_usuario; ?>, 
                        lng: <?php echo $lng_usuario; ?>
                    };
                    
                    // Ubicación del CAI
                    var caiLocation = {
                        lat: <?php echo $lat_cai; ?>, 
                        lng: <?php echo $lng_cai; ?>
                    };
                    
                    // Crear el mapa centrado en un punto intermedio
                    var map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 13,
                        center: {
                            lat: (userLocation.lat + caiLocation.lat) / 2,
                            lng: (userLocation.lng + caiLocation.lng) / 2
                        }
                    });
                    
                    // Marcador para la ubicación del usuario
                    var userMarker = new google.maps.Marker({
                        position: userLocation,
                        map: map,
                        title: 'Su ubicación',
                        icon: {
                            url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                        }
                    });
                    
                    // Marcador para la ubicación del CAI
                    var caiMarker = new google.maps.Marker({
                        position: caiLocation,
                        map: map,
                        title: 'CAI <?php echo htmlspecialchars($cai_cercano['nombre_lugar'] ?? 'Cercano'); ?>'
                    });
                    
                    // Dibujar una línea entre los dos puntos
                    var path = new google.maps.Polyline({
                        path: [userLocation, caiLocation],
                        geodesic: true,
                        strokeColor: '#FF0000',
                        strokeOpacity: 1.0,
                        strokeWeight: 2
                    });
                    
                    path.setMap(map);
                }
            </script>
            <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=<?php echo $apiKey; ?>&callback=initMap">
            </script>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="error-message">
                <p>No se pudo procesar la solicitud o encontrar un CAI cercano. Por favor intente nuevamente.</p>
            </div>
        <?php endif; ?>
        
        <a href="javascript:history.back()" class="back-button">Volver al formulario</a>
    </div>
</body>
</html>