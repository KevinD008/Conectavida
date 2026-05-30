import uvicorn
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import HTMLResponse
import mysql.connector
import google.generativeai as genai


app = FastAPI()

# 🔥 CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

# 🔹 GEMINI
genai.configure(api_key="GEMINI_API_KEY")
model = genai.GenerativeModel("gemini-3-flash-preview")

# 🔹 MYSQL
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="seguridad"
)
cursor = db.cursor()

# 🔹 SERVIR HTML
@app.get("/", response_class=HTMLResponse)
def pagina():
    with open("chat.html", "r", encoding="utf-8") as f:
        return f.read()

# 🔥 GENERAR SQL CON IA
def generar_sql(pregunta):
    prompt = f"""
    Eres un asistente inteligente para un administrador de sistema.

    Tu tarea es interpretar preguntas en lenguaje natural y convertirlas en SQL.
      Responde como si hablaras con una persona.
    No uses JSON.

    Tabla: seguridad_registros

    Columnas disponibles:
    - id
    - nombre
    - apellidos
    - tipo_documento
    - numero_documento
    - email
    - direccion
    - genero
    - telefono
    - descripcion

    OBJETIVO:
    Convertir preguntas del administrador en consultas SQL correctas.

    REGLAS:
    - SOLO generar consultas SELECT
    - NO usar DELETE, UPDATE, INSERT
    - usar COUNT(*) para conteos
    - usar GROUP BY para detectar duplicados
    - usar WHERE para filtros
    - usar LIKE para búsquedas por nombre
    - si mencionan "cédula", usar numero_documento
    - si piden datos completos, incluir nombre, apellidos, email, direccion
    - devolver SOLO el SQL, sin explicación

    EJEMPLOS:

    Pregunta: ¿Cuántas personas hay registradas?
    SQL: SELECT COUNT(*) FROM seguridad_registros;

    Pregunta: ¿Hay correos repetidos?
    SQL: SELECT email, COUNT(*) FROM seguridad_registros GROUP BY email HAVING COUNT(*) > 1;

    Pregunta: Dame los datos de la persona con cédula 123
    SQL: SELECT nombre, apellidos, email, direccion FROM seguridad_registros WHERE numero_documento = 123;

    Pregunta: Usuarios con nombre Kevin
    SQL: SELECT * FROM seguridad_registros WHERE nombre LIKE '%Kevin%';

    Ahora convierte esta pregunta:
    {pregunta}
    """

    respuesta = model.generate_content(prompt)
    return respuesta.text.strip()

# 🔥 VALIDAR SQL (MUY IMPORTANTE)
def es_sql_seguro(sql):
    sql = sql.lower()
    return sql.startswith("select") and not any(
        palabra in sql for palabra in ["delete", "update", "insert", "drop"]
    )

# 🔥 EJECUTAR SQL
def ejecutar_sql(sql):
    cursor.execute(sql)
    columnas = [col[0] for col in cursor.description]
    datos = cursor.fetchall()

    resultado = []
    for fila in datos:
        resultado.append(dict(zip(columnas, fila)))

    return resultado

# 🔥 CHAT INTELIGENTE
@app.get("/chat")
def chat(mensaje: str):
    try:
        # saludo
        if mensaje.lower() in ["hola", "hi"]:
            return {"respuesta": "Hola 👋 ¿Qué quieres consultar?"}

        sql = generar_sql(mensaje)

        # validar seguridad
        if not es_sql_seguro(sql):
            return {"respuesta": "Consulta no permitida"}

        resultado = ejecutar_sql(sql)

        if not resultado:
            return {"respuesta": "No se encontraron datos"}

        return {
            "sql_generado": sql,
            "respuesta": resultado
        }

    except Exception as e:
        return {"error": str(e)}

if __name__ == "__main__":
    uvicorn.run(app, host="127.0.0.1", port=8000)
