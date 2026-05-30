# ConectaVida 🛡️
Sistema de gestión de seguridad ciudadana con agente de Inteligencia Artificial.

## Descripción
ConectaVida es una plataforma web que permite registrar y gestionar datos de ciudadanos
vinculados a eventos de seguridad. Integra dos agentes de IA: uno administrativo para
consultar la base de datos en lenguaje natural, y uno de atención al público para orientar
a personas en situaciones de emergencia.

---

## Arquitectura del Sistema

```
Usuario
   │
   ▼
Frontend (HTML · PHP · React)
   │
   ▼
Backend API (FastAPI Python · PHP CRUD)
   │              │
   ▼              ▼
Gemini AI ◄──► MySQL (seguridad)
```

| Capa | Tecnología | Descripción |
|------|-----------|-------------|
| Frontend | HTML5, PHP, React (Vite+TSX) | Interfaces de usuario y chat con IA |
| Backend API | Python, FastAPI, Uvicorn | API RESTful, lógica de negocio |
| Base de Datos | MySQL | Persistencia de registros ciudadanos |
| Módulo IA | Google Gemini API | Tool Calling y Structured Output |
| Backend PHP | PHP 8, MySQLi | CRUD y panel de administración |

---

## Diccionario de Datos

**Tabla: `seguridad_registros`**

| Campo | Tipo | Restricción | Descripción |
|-------|------|-------------|-------------|
| id | INT | PK, AUTO_INCREMENT | Identificador único |
| nombre | VARCHAR(100) | NOT NULL | Nombre del ciudadano |
| apellidos | VARCHAR(150) | NOT NULL | Apellidos |
| tipo_documento | ENUM | NOT NULL | cc, ti, tm, ce |
| numero_documento | VARCHAR(20) | NOT NULL | Número de documento |
| email | VARCHAR(150) | NOT NULL | Correo electrónico |
| direccion | VARCHAR(255) | NOT NULL | Dirección de residencia |
| genero | ENUM | NOT NULL | masculino / femenino / otro / prefiero_no_decir |
| descripcion | TEXT | NULLABLE | Descripción del incidente |
| telefono | VARCHAR(20) | NULLABLE | Número de contacto |

---

## Instrucciones de Ejecución

### Prerrequisitos
- Python 3.12+
- Node.js 18+ y npm
- MySQL Server
- XAMPP o WAMP
- Clave de API de Google Gemini

### 1. Clonar el repositorio
```bash
git clone https://github.com/KevinD008/Conectavida.git
cd Conectavida
```

### 2. Configurar variables de entorno
Copia el archivo de ejemplo y agrega tu clave:
```bash
cp .env.example .env
```
Edita `.env` con tus datos:
```
GEMINI_API_KEY=tu_clave_aqui
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=seguridad
```

### 3. Configurar la Base de Datos
Ejecuta este script en MySQL:
```sql
CREATE DATABASE IF NOT EXISTS seguridad;
USE seguridad;

CREATE TABLE seguridad_registros (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  nombre           VARCHAR(100) NOT NULL,
  apellidos        VARCHAR(150) NOT NULL,
  tipo_documento   ENUM('cc','ti','tm','ce') NOT NULL,
  numero_documento VARCHAR(20)  NOT NULL,
  email            VARCHAR(150) NOT NULL,
  direccion        VARCHAR(255) NOT NULL,
  genero           ENUM('masculino','femenino','otro','prefiero_no_decir') NOT NULL,
  descripcion      TEXT,
  telefono         VARCHAR(20)
);
```

### 4. Instalar dependencias Python
```bash
pip install fastapi uvicorn mysql-connector-python google-generativeai
```

### 5. Iniciar Agente Administrativo (puerto 8000)
```bash
uvicorn ia:app --reload --host 127.0.0.1 --port 8000
```

### 6. Iniciar Agente de Emergencias (puerto 8001)
```bash
python iaemergencia.py
```

### 7. Iniciar Frontend React
```bash
cd conectainicio
npm install
npm run dev
```

### 8. Acceder a la aplicación

| Módulo | URL |
|--------|-----|
| Registro ciudadanos | Abrir `index.html` desde XAMPP |
| Chat IA Admin | http://127.0.0.1:8000 |
| Chat Emergencias | http://127.0.0.1:8001 |
| Landing React | http://localhost:5173 |

---

## Módulo de Inteligencia Artificial

### Agente Administrativo (`ia.py`)
- Recibe preguntas en lenguaje natural
- **Tool Calling:** la función `generar_sql()` genera dinámicamente consultas SQL SELECT usando Gemini
- **Structured Output:** retorna siempre JSON con campos `sql_generado` y `respuesta`
- Validación `es_sql_seguro()` bloquea DELETE, UPDATE, INSERT y DROP

**Ejemplo de respuesta:**
```json
{
  "sql_generado": "SELECT COUNT(*) FROM seguridad_registros;",
  "respuesta": [{"COUNT(*)": 42}]
}
```

### Agente de Emergencias (`iaemergencia.py`)
Clasifica incidentes en 6 categorías y orienta al ciudadano:

| Categoría | Situaciones |
|-----------|-------------|
| Policía Nacional | Robos, violencia, amenazas |
| Bomberos | Incendios, fugas de gas, rescates |
| Defensa Civil | Desastres naturales, inundaciones |
| Red Médica | Accidentes, heridos, emergencias médicas |
| Apoyo Comunitario | Necesidades básicas, ayuda humanitaria |
| Línea de Escucha | Crisis emocional, ansiedad |

---

## Seguridad
- Las credenciales se manejan mediante variables de entorno (`.env`)
- El archivo `.env` está en `.gitignore` y **nunca** se sube al repositorio
- El agente IA solo permite consultas `SELECT` (sin DELETE, UPDATE, INSERT, DROP)
- El backend PHP usa sentencias preparadas MySQLi en todas las operaciones CRUD

---

## Control de Roles

| Rol | Permisos |
|-----|----------|
| admin | Acceso completo: consultar, agregar, modificar, eliminar, panel admin |
| editor | Consultar, agregar, modificar |
| viewer | Solo consultar |
| pending | Sin acceso, en espera de aprobación |

---

## Tecnologías

![Python](https://img.shields.io/badge/Python-3.12-3776ab?style=flat&logo=python&logoColor=white)
![FastAPI](https://img.shields.io/badge/FastAPI-0.100+-009688?style=flat&logo=fastapi&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479a1?style=flat&logo=mysql&logoColor=white)
![React](https://img.shields.io/badge/React-Vite+TSX-61dafb?style=flat&logo=react&logoColor=black)
![PHP](https://img.shields.io/badge/PHP-8.0-777bb4?style=flat&logo=php&logoColor=white)
![Gemini](https://img.shields.io/badge/Google-Gemini_AI-4285f4?style=flat&logo=google&logoColor=white)
