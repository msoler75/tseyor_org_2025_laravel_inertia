# 🌐 tseyor.org – Plataforma Web de Mundo Armónico Tseyor

¡Bienvenido/a! Este repositorio contiene el código fuente de la web oficial de la ONG Mundo Armónico Tseyor: [https://tseyor.org](https://tseyor.org) ✨

## 🤝 ¿Qué es este proyecto?

Este proyecto es el corazón digital de la organización, donde se publican noticias, comunicados, eventos, recursos y mucho más. Está pensado para ser mantenido y evolucionado por cualquier miembro técnico de la ONG, facilitando la continuidad y la colaboración.

- **ONG:** Mundo Armónico Tseyor
- **Propósito:** Portal web, gestión de contenidos, buscador global, automatización de publicaciones y recursos.
- **Tecnologías principales:** Laravel (backend), Inertia.js (puente), Vue 3 (frontend), MySQL (base de datos)
- **Frontend:** Vue 3, Tailwind CSS 4, DaisyUI 5, editor TipTap (rich text)

## 🚀 Stack tecnológico

- **Laravel**: Framework PHP robusto para el backend y la API.
- **Inertia.js**: Conecta el backend con el frontend sin APIs REST tradicionales.
- **Vue 3**: Framework progresivo para la interfaz de usuario.
- **TNTSearch/Scout**: Buscador interno eficiente.
- **Ziggy**: Rutas de Laravel disponibles en JavaScript.

## 📚 Documentación técnica

Toda la información técnica, comandos, optimizaciones, buenas prácticas y detalles de desarrollo están centralizados en la carpeta [`documentacion/`](./documentacion/index.md).

## 🛠️ Guía rápida para nuevos colaboradores

1. **Clona el repositorio y copia `.env.example` a `.env`**
2. Instala dependencias:
   - `composer install`
   - `npm install`
3. Genera la clave de la app: `php artisan key:generate`
4. Configura la base de datos en `.env` y ejecuta migraciones:
   - `php artisan migrate --seed`
5. Compila los assets:
   - `npm run dev` (desarrollo) o `npm run build` (producción)
6. ¡Listo! Accede a la web en tu entorno local.

> **Consejo:** Consulta siempre la [documentación técnica](./documentacion/index.md) para entender la arquitectura, tareas pendientes y convenciones del proyecto.

## 📁 Estructura del proyecto (resumen)

- `/app` – Lógica de negocio, modelos, controladores, servicios
- `/resources` – Vistas, componentes Vue, assets
- `/routes` – Definición de rutas web y API
- `/database` – Migraciones, seeders, factories
- `/public` – Archivos públicos y assets compilados
- `/config` – Configuración de paquetes y servicios

## 📊 Estadísticas del proyecto (a fecha 15/05/2025)

- **Modelos:** 49
- **Controladores:** 88 (incluyendo los de administración CRUD)

## 📝 Buenas prácticas y contribución

- Usa ramas descriptivas y pull requests claros.
- Documenta cualquier cambio relevante en este README.

## 🔗 Enlaces útiles

- [Documentación Laravel](https://laravel.com/docs)
- [Documentación Inertia.js](https://inertiajs.com/)
- [Documentación Vue 3](https://vuejs.org/)
- [TNTSearch](https://tntsearch.dev/)
- [Ziggy](https://github.com/tighten/ziggy)

---

> Si tienes dudas, contacta con el equipo técnico o revisa la documentación interna. ¡Gracias por contribuir a la web de Tseyor! 🌱

---
