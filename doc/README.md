# Carpeta de Documentación - HIS Sistema Hospitalario

Índice de documentación técnica generada para el proyecto y sus ramas de desarrollo:

- [docker-mysql-schema/](./docker-mysql-schema/README.md): Documentación exhaustiva de la rama `feature/docker-mysql-schema`. Incluye diagrama Entidad-Relación (ERD), diccionario de datos de todas las tablas (`medical_specialties`, `doctors`, `patients`, `appointments`), estrategia de indexación, arquitectura de contenedores Docker con MySQL 8.0 y datos semilla mínimos con trazabilidad de citas.
- [api-rest-citas/](./api-rest-citas/README.md): Documentación de la API RESTful de la rama `feature/api-rest-citas`. Catálogo de endpoints para citas (crear, listar, actualizar, reprogramar, cancelar, cambiar estado) y lectura de médicos, pacientes y especialidades con payloads de petición/respuesta en JSON.
- [validacion-conflictos-estados/](./validacion-conflictos-estados/README.md): Documentación de la rama `feature/validacion-conflictos-estados`. Detección de doble reserva en servidor para médicos y pacientes, algoritmo de traslapes temporales, concurrencia segura con bloqueo pesimista (`lockForUpdate`), máquina de estados estricta y pruebas unitarias.
- [fullcalendar-ui/](./fullcalendar-ui/README.md): Documentación de la interfaz gráfica interactiva con FullCalendar v6 de la rama `feature/fullcalendar-ui`. Consumo de API REST, agendamiento al hacer clic, modal de detalle clínico y reprogramación con Drag & Drop y reversión automática ante conflictos.
