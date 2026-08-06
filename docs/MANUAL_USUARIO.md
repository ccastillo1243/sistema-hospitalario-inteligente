# Manual de Usuario — Sistema Hospitalario Inteligente

## 1. Introducción

Este sistema permite gestionar la operación diaria de un hospital: pacientes, personal médico, citas, hospitalización, laboratorio, farmacia, radiología, facturación, emergencias e inventario. Cada persona ve únicamente los módulos que le corresponden según su rol.

## 2. Ingresar al sistema

1. Abre `http://localhost:8000/login.php` (o la URL que te haya dado tu administrador).
2. Escribe tu correo y contraseña.
3. Haz clic en **Ingresar**.

### ¿Olvidaste tu contraseña?

1. En la pantalla de login, haz clic en **¿Olvidaste tu contraseña?**
2. Escribe tu correo y haz clic en **Enviar enlace**.
3. Copia el token que aparece en pantalla (en esta versión de demostración el token se muestra directamente, no se envía por correo).
4. Escribe tu nueva contraseña y confirma.

### Cuenta bloqueada

Si escribes tu contraseña incorrecta **5 veces seguidas**, tu cuenta se bloquea automáticamente por 15 minutos como medida de seguridad. Puedes esperar, o pedirle a un administrador que te desbloquee desde el módulo **Usuarios**.

## 3. La pantalla principal

Al ingresar verás:

- **Barra lateral izquierda**: los módulos a los que tienes acceso (solo se muestran los tuyos).
- **Barra superior**: título de la página, campana de notificaciones, acceso a tu perfil, y tu rol.
- **Botón "Cerrar sesión"**: al final de la barra lateral.

En pantallas pequeñas (celular/tablet), la barra lateral se oculta y aparece un botón de menú (☰) para abrirla.

## 4. Roles y qué puede hacer cada uno

| Rol | Módulos a los que tiene acceso |
|---|---|
| **Administrador** | Todos los módulos, incluyendo Usuarios y Auditoría |
| **Médico** | Pacientes, Personal, Agenda médica, Hospitalización, Laboratorio, Farmacia, Radiología, Emergencias |
| **Enfermería** | Pacientes, Personal, Hospitalización, Laboratorio, Farmacia, Inventario, Agenda médica, Emergencias |
| **Recepción** | Pacientes, Personal, Agenda médica, Hospitalización, Facturación, Emergencias |
| **Farmacia** | Pacientes, Personal, Farmacia, Inventario |
| **Laboratorio** | Pacientes, Personal, Laboratorio |
| **Radiología** | Pacientes, Personal, Radiología |
| **Facturación** | Pacientes, Personal, Facturación |

Si intentas entrar a un módulo que no te corresponde (por ejemplo, escribiendo la dirección directamente en el navegador), verás un aviso **"No tienes acceso a este módulo"** en vez de una pantalla vacía o rota.

## 5. Cómo usar cualquier módulo (patrón general)

La mayoría de los módulos funcionan igual:

1. **Buscar**: escribe en el campo de búsqueda para filtrar la tabla al instante.
2. **Crear**: botón **"+ Nuevo..."** arriba a la derecha — abre una ventana emergente (modal) con un formulario.
3. **Editar**: ícono de lápiz (✎) en la fila del registro.
4. **Eliminar**: ícono de basura (🗑) — pide confirmación antes de borrar.
5. Los cambios se guardan **sin recargar la página** (todo funciona por AJAX).

Los estados (pendiente, completado, cancelado, etc.) se muestran con una etiqueta de color:
- 🟢 Verde = completado/activo/pagado
- 🟡 Ámbar = pendiente/en espera
- 🔵 Azul = programado/en proceso
- 🔴 Rojo = ocupado/cancelado/bloqueado

## 6. Módulos

### Pacientes
Registro de pacientes con expediente, datos de contacto y documento de identidad.

### Personal
Dos listas: **Médicos** (con su especialidad) y **Enfermería**.

### Agenda médica
Gestión de citas: paciente, médico, tipo de cita, fecha/hora y estado.

### Hospitalización
- Tabla de **camas** con su estado (libre/ocupada).
- **Ingresos activos**: registrar un nuevo ingreso (elige una cama libre), y dar de alta cuando el paciente se retira. La cama se libera automáticamente al dar el alta.

### Laboratorio
Flujo en 3 pasos: crear una **orden** → registrar una **muestra** de esa orden → registrar el **resultado** de esa muestra. La orden pasa a "completada" automáticamente cuando todos sus parámetros tienen resultado.

### Farmacia
- **Stock de medicamentos** (solo lectura, calculado de los lotes vigentes).
- **Recetas**: crear una receta, agregarle ítems (medicamentos), y **dispensar** cada ítem — el sistema valida que haya stock suficiente antes de descontarlo.

### Inventario
Artículos de almacenes generales (no farmacia). Cada artículo tiene una existencia que se ajusta con **movimientos** (entrada/salida/ajuste).

### Radiología
Flujo en 3 pasos similar a Laboratorio: orden → estudio → informe.

### Facturación
Crear una factura seleccionando servicios (hasta 2 por factura en el formulario), y registrar pagos — la factura pasa a "parcial" o "pagada" según el monto acumulado.

### Emergencias
Los casos se muestran **ordenados por prioridad de triage** (el más urgente primero, sin importar cuándo llegó). Registrar un caso nuevo y luego "Atender" cuando un médico lo atiende.

### Dashboard
Indicadores generales (pacientes, camas ocupadas, citas de hoy, etc.), gráficas de ocupación/stock/emergencias, y enlaces de descarga de reportes (solo se muestran los reportes a los que tu rol tiene acceso).

### Usuarios *(solo administrador)*
Ver todos los usuarios del sistema, crear cuentas nuevas, asignar roles, activar/desactivar, cambiar contraseña, **desbloquear** cuentas bloqueadas, y eliminar (no puedes eliminar tu propia cuenta).

### Auditoría *(solo administrador)*
Historial de todas las acciones (crear/editar/eliminar) hechas en el sistema, con filtros por tabla, tipo de acción y búsqueda por usuario.

### Mi perfil
Accesible desde el ícono junto a la campana — edita tu nombre, apellido, correo, y cambia tu contraseña (pide tu contraseña actual).

## 7. Notificaciones

El ícono de campana en la barra superior muestra un número si tienes notificaciones sin leer. Haz clic para ver el listado; haz clic en una notificación para marcarla como leída, o usa "Marcar todo leído".

## 8. Cerrar sesión

Haz clic en **"Cerrar sesión"** al final de la barra lateral. Esto invalida tu sesión de forma segura.

## 9. Preguntas frecuentes

**¿Por qué no veo un módulo que antes sí veía?**
Tu rol determina qué módulos ves. Si cambiaron tu rol, la próxima vez que inicies sesión verás los módulos correspondientes al nuevo rol.

**¿Por qué me dice "Token CSRF inválido"?**
Tu sesión pudo haber expirado o se abrió en otra pestaña. Cierra sesión y vuelve a entrar.

**¿Puedo usar el sistema desde el celular?**
Sí, el diseño se adapta a pantallas pequeñas (la barra lateral se convierte en un menú).
