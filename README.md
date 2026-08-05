# Sistema Hospitalario Inteligente

Proyecto 2 — Grupo 4. Sistema de gestión hospitalaria con 10 módulos (Pacientes, Consultas, Hospitalización, Laboratorio, Farmacia, Radiología, Facturación, Emergencias, Agenda médica, Inventario), construido con **PHP puro + MySQL + HTML/CSS/JavaScript/jQuery/AJAX**, sin frameworks.

## Requisitos

- **XAMPP** (incluye PHP 8.x, MySQL/MariaDB y phpMyAdmin) — https://www.apachefriends.org/
- **Composer** (para instalar TCPDF y PhpSpreadsheet) — https://getcomposer.org/
- Un navegador moderno (Chrome, Edge, Firefox)

No hace falta Node.js ni ningún framework: todo el frontend es HTML/CSS/JS servido directamente por PHP.

## 1. Clonar el repositorio

```bash
git clone <URL-de-este-repo>
cd "Sistema Hospitalario"
```

## 2. Instalar dependencias PHP (Composer)

Desde la raíz del proyecto:

```bash
composer install
```

Esto descarga `vendor/` con **TCPDF** (reportes PDF) y **PhpSpreadsheet** (reportes Excel). Si no tienes `composer` en el PATH, puedes usar el que trae XAMPP: `C:\xampp\php\php.exe composer.phar install` (descargando antes `composer.phar` desde https://getcomposer.org/composer-stable.phar).

> **Extensiones PHP requeridas**: `gd`, `zip`, `mysqli`/`pdo_mysql` (ya vienen en XAMPP, pero `gd` y `zip` suelen estar comentadas en `php.ini`). Abre `C:\xampp\php\php.ini` y quita el `;` de estas líneas:
> ```
> extension=gd
> extension=zip
> ```
> Reinicia el servidor PHP después de este cambio.

## 3. Crear y poblar la base de datos

1. Inicia MySQL desde el panel de control de XAMPP (o `C:\xampp\mysql_start.bat`).
2. Crea la base de datos vacía:
   ```sql
   CREATE DATABASE sistema_hospitalario CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
   (puedes hacerlo desde phpMyAdmin o con `mysql -u root`).
3. Importa los scripts **en este orden exacto**, siempre indicando el charset utf8mb4 (si no, los acentos se corrompen):
   ```bash
   mysql -u root --default-character-set=utf8mb4 sistema_hospitalario < database/schema.sql
   mysql -u root --default-character-set=utf8mb4 sistema_hospitalario < database/views.sql
   mysql -u root --default-character-set=utf8mb4 sistema_hospitalario < database/triggers.sql
   mysql -u root --default-character-set=utf8mb4 sistema_hospitalario < database/procedures.sql
   mysql -u root --default-character-set=utf8mb4 sistema_hospitalario < database/seed.sql
   mysql -u root --default-character-set=utf8mb4 sistema_hospitalario < database/migration_02_password_reset.sql
   mysql -u root --default-character-set=utf8mb4 sistema_hospitalario < database/seed_test_data.sql
   ```
   Esto crea las 67 tablas, 5 vistas, 4 triggers, 2 procedimientos almacenados, 2 funciones, y datos de prueba en todas las tablas.

### Configurar la conexión a la base de datos

Edita `src/config/config.php` si tu usuario/contraseña de MySQL no son los de XAMPP por defecto (`root` sin contraseña):

```php
'db' => [
    'host' => '127.0.0.1',
    'port' => '3306',
    'name' => 'sistema_hospitalario',
    'user' => 'root',
    'pass' => '',
    ...
],
```

## 4. Levantar el servidor

**Opción A — servidor embebido de PHP (recomendada, evita problemas de configuración de Apache):**

```bash
php -S localhost:8000 -t public public/index.php
```

(o con la ruta completa de XAMPP: `C:\xampp\php\php.exe -S localhost:8000 -t public public/index.php`)

Abre **http://localhost:8000/login.php**

**Opción B — Apache de XAMPP (requiere Virtual Host, NO simplemente copiar a htdocs):**

⚠️ **No accedas al proyecto como** `http://localhost/sistema-hospitalario-inteligente/public/index.php` **con la carpeta suelta dentro de `htdocs`.** Aunque arregles el error "Forbidden", el sistema seguirá roto: el login y todos los módulos usan rutas absolutas (`/auth/login`, `/patients`, etc.) que asumen que el sitio vive en la raíz del dominio (`http://localhost/`), no en una subcarpeta.

La forma correcta con Apache es un **Virtual Host** cuyo `DocumentRoot` apunte directo a la carpeta `public/` del proyecto:

1. Edita `C:\xampp\apache\conf\extra\httpd-vhosts.conf` y agrega:
   ```apache
   <VirtualHost *:80>
       ServerName hospital.local
       DocumentRoot "C:/xampp/htdocs/sistema-hospitalario-inteligente/public"
       <Directory "C:/xampp/htdocs/sistema-hospitalario-inteligente/public">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```
2. Agrega esta línea a `C:\Windows\System32\drivers\etc\hosts` (como administrador): `127.0.0.1 hospital.local`
3. Reinicia Apache desde el panel de XAMPP.
4. Abre **http://hospital.local/login.php**

Si aun así ves **"Forbidden"**, es porque el `.htaccess` de la raíz del proyecto (que bloquea el acceso directo al código fuente por seguridad) se está heredando — confirma que el `DocumentRoot` apunta a `public/` y no a la carpeta raíz del proyecto.

## 5. Iniciar sesión

Todos los usuarios de prueba usan la contraseña **`password123`**:

| Rol | Email |
|---|---|
| Administrador | admin@hospital.com |
| Médico | medico1@hospital.com / medico2@hospital.com |
| Enfermería | enfermera1@hospital.com / enfermera2@hospital.com |
| Farmacia | farmacia@hospital.com |
| Laboratorio | laboratorio@hospital.com |
| Radiología | radiologia@hospital.com |
| Facturación | facturacion@hospital.com |
| Recepción | recepcion@hospital.com |

## Estructura del proyecto

```
database/          Scripts SQL (schema, vistas, triggers, procedimientos, seeds)
public/            Document root — páginas PHP, CSS, JS (jQuery/AJAX)
src/
  config/          Configuración (BD, app)
  core/            Router, Model base, Controller base, Auth, CSRF, Íconos
  middleware/      Auth y Roles
  controllers/     Un controlador por módulo/entidad
  models/          Un modelo por tabla principal
  reports/         Generadores de reportes PDF/Excel
  views/partials/  Layout compartido (sidebar + topbar)
PROGRESS.md         Bitácora completa de desarrollo (qué se hizo, errores y cómo se resolvieron)
```

## Notas de seguridad (proyecto académico)

- Las credenciales de MySQL en `src/config/config.php` son las de una instalación local de XAMPP (usuario `root` sin contraseña) — **no usar en producción**.
- El bloqueo de cuenta por intentos fallidos y la recuperación de contraseña son funcionales; el envío real de correo no está implementado (el token se muestra directamente en pantalla, con una nota que lo indica).

## Documentación de desarrollo

`PROGRESS.md` contiene la bitácora fase por fase de todo el desarrollo: qué se construyó, qué errores reales se encontraron y cómo se solucionaron.
