# ARCHITECTURE.md

Documentación sobre la arquitectura del proyecto Tseyor.org 2025.

## Estructura general
- Backend: Laravel
- Frontend: Inertia.js + Vue 3 (Composition API)
- Sistema de colas: Redis + Laravel Queue Workers
- Almacenamiento: Storage local y S3
- Autenticación: Jetstream (sin Sanctum)

## Principios
- Modularidad y separación de responsabilidades
- Uso intensivo de componentes reutilizables en Vue
- Integración de autoimportación de componentes y métodos

## Estructura de carpetas relevante
- `app/` (lógica de backend)
- `resources/js/` (frontend Vue)
- `routes/` (rutas Laravel)
- `public/` (archivos públicos)
- `storage/` (logs, archivos temporales, etc.)

## Notas
- Ver detalles de tareas y mejoras en TODO.md y ROADMAP.md
