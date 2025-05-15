# SETUP.md

Instrucciones para configurar el entorno de desarrollo del proyecto Tseyor.org 2025.

## Requisitos
- PHP >= 8.1
- Composer
- Node.js >= 18
- npm o yarn
- Docker (opcional para servicios como MailHog)

## Pasos
1. Clona el repositorio
2. Instala dependencias backend: `composer install`
3. Instala dependencias frontend: `npm install`
4. Copia `.env.example` a `.env` y configura variables
5. Ejecuta migraciones: `php artisan migrate`
6. Inicia el servidor de desarrollo: `php artisan serve` y `npm run dev`
7. (Opcional) Para testar envío de mails, inicia servicios de pruebas: `mailhog.cmd`

## Notas
- Consulta ARCHITECTURE.md para entender la estructura
- Usa TODO.md para ver tareas pendientes
