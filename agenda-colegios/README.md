📚 Agenda de Colegios
💡 Idea del Proyecto

La aplicación Agenda de Colegios es una plataforma web diseñada para gestionar talleres escolares en distintos colegios. Permite:

Crear, editar y eliminar eventos/talleres.

Asignar talleristas a eventos.

Generar reportes de actividades.

Gestionar colegios y usuarios según permisos.

El objetivo es centralizar la planificación y supervisión de talleres, optimizando la organización y comunicación entre colegios y talleristas.

👥 Usuarios y Roles
| Rol | Descripción | Acceso principal |
| -------------- | ------------------------------------------------------------- | ------------------------------------------------------------ |
| **Admin** | Usuario con control total sobre la plataforma. | Gestión de usuarios, colegios, talleres, eventos y reportes. |
| **Supervisor** | Usuario encargado de supervisar y coordinar actividades. | Gestión de colegios, talleres, eventos y reportes. |
| **Tallerista** | Usuario encargado de dictar talleres y registrar actividades. | Gestión de colegios, talleres y eventos asignados. |

⚙️ Funcionalidades por Rol
1️⃣ Admin

CRUD de Usuarios

CRUD de Colegios

CRUD de Talleres / Eventos

Asignación de Talleristas a eventos

CRUD de Reportes (visualización y exportación)

2️⃣ Supervisor

CRUD de Colegios

CRUD de Talleres / Eventos

Asignación de Talleristas a eventos

CRUD de Reportes (visualización y exportación)

3️⃣ Tallerista

CRUD de Colegios

CRUD de Talleres / Eventos asignados

Consultar agenda de talleres

🛠️ Tecnologías Utilizadas

Backend: PHP, Laravel

Frontend: Blade + Bootstrap + FullCalendar

Base de datos: MySQL

Autenticación y roles: Middleware personalizado CheckRole

Gestión de dependencias: Composer
