# Guion — Video Demostrativo

Duración estimada: **5-7 minutos**. Graba tu pantalla (OBS Studio, Xbox Game Bar `Win+G`, o cualquier grabador de pantalla) mientras sigues estos pasos y los narras en voz alta.

**Antes de grabar**: asegúrate de que MySQL esté corriendo y el servidor PHP levantado (`php -S localhost:8000 -t public public/index.php`), con datos de prueba cargados.

---

### 0:00 – 0:30 — Introducción
> "Este es el Sistema Hospitalario Inteligente, desarrollado en PHP puro, MySQL y JavaScript/jQuery con AJAX, sin frameworks. Cubre 10 módulos: Pacientes, Consultas, Hospitalización, Laboratorio, Farmacia, Radiología, Facturación, Emergencias, Agenda médica e Inventario."

Muestra la pantalla de login.

### 0:30 – 1:00 — Login y seguridad
- Inicia sesión con `admin@hospital.com` / `password123`.
- Menciona: "Las contraseñas están hasheadas con bcrypt, hay protección CSRF, y bloqueo automático tras 5 intentos fallidos."

### 1:00 – 1:30 — Navegación por rol
- Muestra la barra lateral con todos los módulos (vista admin).
- Cierra sesión, entra como `farmacia@hospital.com` / `password123`.
- "Nota cómo el menú ahora solo muestra los módulos de farmacia — el resto del sistema le queda completamente bloqueado a este rol."
- Intenta escribir la URL de otro módulo directamente para mostrar el mensaje "No tienes acceso".
- Vuelve a entrar como admin.

### 1:30 – 2:30 — CRUD de Pacientes (módulo de referencia)
- Ve a **Pacientes**.
- Usa el buscador para filtrar por un nombre.
- Crea un paciente nuevo (llena el formulario del modal).
- Edítalo.
- Elimínalo.
- "Todo esto ocurre sin recargar la página — es AJAX puro con jQuery."

### 2:30 – 3:30 — Flujo de negocio real: Hospitalización
- Ve a **Hospitalización**.
- Muestra la tabla de camas (algunas ocupadas, algunas libres).
- Registra un nuevo ingreso en una cama libre.
- "Nota que la cama pasó a 'ocupada' automáticamente — esto lo hace un trigger de MySQL, no el código PHP."
- Da de alta ese ingreso y muestra que la cama vuelve a estar libre.

### 3:30 – 4:15 — Flujo de negocio real: Farmacia
- Ve a **Farmacia**.
- Muestra el stock de un medicamento.
- Abre una receta existente, dispensa un ítem.
- "El sistema usa un procedimiento almacenado que valida el stock disponible antes de descontarlo — si no alcanza, rechaza la operación."

### 4:15 – 5:00 — Dashboard y reportes
- Ve a **Dashboard**.
- Muestra las gráficas (ocupación de camas, stock bajo, emergencias por prioridad).
- Descarga un reporte PDF (por ejemplo, Pacientes) y un reporte Excel (Facturas).
- Ábrelos brevemente para mostrar que son archivos reales.

### 5:00 – 5:45 — Administración
- Ve a **Usuarios** (solo visible para admin).
- Muestra la lista de usuarios con sus roles.
- Ve a **Auditoría** y muestra el historial de las acciones que acabas de hacer en el video (crear/editar/eliminar el paciente, el ingreso, la dispensación).
- "Cada acción queda registrada con el usuario, la fecha y el detalle exacto del cambio."

### 5:45 – 6:15 — Cierre
> "Este proyecto cumple con los requisitos del enunciado: base de datos normalizada con más de 55 tablas, relaciones 1 a 1, 1 a muchos y muchos a muchos, triggers, procedimientos almacenados, funciones, seguridad completa, AJAX en todos los CRUD, y reportes en PDF y Excel — todo sin usar ningún framework. Gracias."

---

## Consejos para grabar

- Habla despacio y claro; es mejor recortar silencios en edición que hablar muy rápido.
- Si te equivocas, pausa, respira y retoma la frase — puedes cortar el error después.
- Usa una resolución de pantalla no muy alta (1366×768 o 1920×1080 con zoom del navegador al 100-110%) para que el texto se lea bien en el video.
- Cierra pestañas/ventanas que no necesites antes de grabar.
