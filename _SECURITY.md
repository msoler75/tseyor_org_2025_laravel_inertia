# SECURITY.md

Políticas y recomendaciones de seguridad para Tseyor.org 2025.

## Recomendaciones generales
- Mantener dependencias actualizadas
- Usar HTTPS en producción
- Revisar y limitar permisos de archivos y carpetas
- No exponer información sensible en logs ni errores
- Usar variables de entorno para credenciales

## Prácticas específicas
- Revisar autenticación y autorización en rutas y controladores
- Validar y sanear entradas de usuario
- Revisar configuración de CORS y cookies
- Limitar intentos de login y proteger endpoints sensibles

## Recursos
- Middleware de protección: ver ROADMAP.md y TODO.md
