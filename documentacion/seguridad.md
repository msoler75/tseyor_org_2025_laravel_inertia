# Seguridad

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
- Middleware de protección: ver hoja_de_ruta.md y TODO.md

---

## Implementación de recomendaciones

- **Rate limiting:** Utilizar middleware para limitar el número de solicitudes que un usuario puede hacer en un período de tiempo determinado.
- **Validación y saneamiento de entradas de usuario:** Utilizar funciones de validación y saneamiento para asegurarse de que las entradas de usuario son válidas y no contienen código malicioso.
- **Protección contra ataques CSRF y XSS:** Utilizar tokens CSRF y escapar las salidas para protegerse contra ataques CSRF y XSS.

## Herramientas de análisis de seguridad

Se pueden utilizar herramientas de análisis de seguridad para identificar vulnerabilidades en la aplicación. Algunas herramientas populares son:

- OWASP ZAP
- SonarQube

## Tareas Pendientes

*   Instalar `Laravel-Abuse-IP`
*   Integración de middleware de seguridad avanzada
*   Comprobar posibles hackeos de invitación a equipo

### [Ver índice de documentación](./index.md)
