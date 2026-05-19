# CarSense — Tu asistente automotriz inteligente

Aplicación web de diagnóstico automotriz con IA. Arquitectura multi-página PHP + MySQL con vanilla JS para interactividad.

## Stack

- **Backend:** PHP 8.x (PDO, sesiones nativas)
- **Base de datos:** MySQL (XAMPP)
- **Frontend:** HTML/CSS/JS vanilla (sin frameworks)
- **IA:** Groq API (modelo LLaMA, acceso por `js/grog.js`)

## Requisitos

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP 8.x)
- Navegador moderno

## Configuración local

### 1. Clonar en htdocs

Coloca (o clona) este repositorio en `C:\xampp\htdocs\`:

```
C:\xampp\htdocs\
├── api/
├── css/
├── includes/
├── js/
├── index.php
├── login.php
...
```

### 2. Iniciar XAMPP

Abre **XAMPP Control Panel** y pon en marcha:
- **Apache**
- **MySQL**

### 3. Crear la base de datos

1. Abre [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Crea una base de datos llamada `carsense`
3. Importa el archivo SQL del proyecto (si existe `carsense.sql`)  
   o ejecuta `importar_datos.php` en el navegador para poblar las tablas:
   ```
   http://localhost/importar_datos.php
   ```

### 4. Verificar la conexión a BD

Revisa `api/config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'carsense');
define('DB_USER', 'root');
define('DB_PASS', '');      // vacía en XAMPP por defecto
```

### 5. Configurar la API de Groq (opcional)

Para activar el asistente de IA en la página de resultados, edita `js/config.js`:

```js
export const GROQ_API_KEY = 'tu_clave_aqui';
export const GROQ_MODEL   = 'llama3-8b-8192';
```

Obtén tu clave gratis en [console.groq.com](https://console.groq.com).

### 6. Abrir la aplicación

Visita [http://localhost](http://localhost) en tu navegador.  
Usa la cuenta demo para explorar sin registrarte:

| Campo | Valor |
|-------|-------|
| Email | `bruce@carsense.app` |
| Contraseña | `demo1234` |

## Estructura del proyecto

```
htdocs/
├── api/
│   ├── config/database.php      # Conexión PDO
│   ├── endpoints/               # REST API (create/get/update/delete)
│   └── models/                  # Modelos (User, Vehicle, System, …)
├── css/styles.css               # Estilos globales (no modificar)
├── includes/
│   ├── auth_check.php           # Guard de sesión PHP
│   ├── header.php               # HTML head + nav + breadcrumbs
│   ├── footer.php               # Mobile nav + cierre HTML
│   ├── icons.php                # Función icon() en PHP
│   └── images.php               # URLs de imágenes Unsplash
├── js/
│   ├── config.js                # Clave Groq y modelo
│   ├── grog.js                # Cliente Groq AI
│   └── icons.js                 # Función icon() en JS (módulo ES)
├── index.php                    # Inicio
├── login.php                    # Inicio de sesión
├── registro.php                 # Registro (2 pasos)
├── olvide-contrasena.php        # Recuperación de contraseña
├── sistemas.php                 # Lista de sistemas
├── sistema.php                  # Detalle de sistema (?id=slug)
├── componente.php               # Detalle de componente (?id=slug&sistema=slug)
├── diagnostico.php              # Buscador de diagnósticos
├── resultado.php                # Resultado + chat IA (?id=slug)
├── perfil.php                   # Perfil de usuario (auth requerida)
└── logout.php                   # Cierre de sesión
```

## Notas de desarrollo

- Las páginas de autenticación (`login.php`, `registro.php`, `olvide-contrasena.php`) tienen layout propio sin `header.php`/`footer.php`.
- `perfil.php` requiere sesión activa; redirige a `login.php` si no hay sesión.
- Las demás páginas son públicas pero muestran contenido diferente según si hay sesión.
- Los datos de sistemas, componentes y diagnósticos se cargan de MySQL en tiempo de servidor.
- Las operaciones CRUD de vehículos usan `fetch` a los endpoints de `api/endpoints/`.
