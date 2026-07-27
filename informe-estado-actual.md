# Informe de Estado Actual — Web TSEYOR

> **Fecha:** 15 de julio de 2026
> **Propósito:** Documentar el estado actual del sitio web, páginas principales, menú de navegación, cambios realizados y pendientes.

---

## 1. Stack Tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 12 (PHP) |
| Frontend | Vue 3 + Inertia.js SPA |
| Estilos | Tailwind CSS + DaisyUI |
| Estado | Pinia |
| Iconos | Phosphor Duotone + Lucide |
| Fuentes | Font Display personalizada |

---

## 2. Estructura de Páginas Principales

### 2.1 Portada (`/`)

| Archivo | Estado |
|---------|--------|
| `Pages/Portada.vue` | Original |
| `Pages/Portada2.vue` | Alternativa |
| **`Pages/Portada3.vue`** | **Actual (702 líneas)** |
| `Pages/_Portada_v1_original.vue` | Backup |
| `Pages/_Portada2_v2_refactorizada.vue` | Backup |

**Controller:** `PaginasController@portada3` (nuevo, lee imágenes reales de `/almacen/medios/portada/usuarios` con shuffle + lista de audios)

**Secciones de Portada3:**
1. Hero con imagen de fondo y superposición con título "MUNDO ARMÓNICO TSEYOR"
2. Stats cards: +40 años, libros, comunicados, ONG
3. "El mensaje desde las estrellas" — wisdom section con imagen de Guías Estelares
4. Secciones narrativas de sabiduría
5. Reproductor de audio para meditación diaria
6. Eventos próximos
7. CTA final

### 2.2 Filosofía (`/filosofia`)

**Archivo:** `Pages/Filosofia.vue` (470 líneas) — **refactorizado como Filosofia2.vue**

**Narrativa progresiva (nuevo orden):**
1. **Entry** — Introducción: "Una visión del despertar humano y cósmico"
2. **¿Por qué ahora?** — Rayo Sincronizador, salto cuántico, especialización, doble interdimensionalidad, cadena de ayuda interdimensional (Guías Estelares como ayuda no unidireccional)
3. **Pilares** — Síntesis general con índice sticky lateral + 4 bloques narrativos:
   - El momento: mensaje desde las estrellas para un cambio de era
   - Naturaleza del ser humano
   - El camino de autodescubrimiento
   - Hacia las Sociedades Armónicas
4. **Grid Descubre** — Tarjetas de temas
5. **Comprobación** — Sección "Un camino de interiorización y comprobación" (cards con fondo bg-base-100)
6. **CTA final** — Llamada a la acción comunitaria

**Subpáginas:**
- `/filosofia/temas` — `Pages/Filosofia/Temas.vue`
- `/guias` — Guías Estelares
- `/origenes-de-tseyor` — Orígenes de TSEYOR
- Temas clave: `/el-rayo-sincronizador`, `/las-sociedades-armonicas`, `/especializacion`, `/retroalimentacion`, `/espejos`

### 2.3 Quiénes Somos (`/quienes-somos`)

**Archivo:** `Pages/QuienesSomos/index.vue` (251 líneas)

**Secciones:**
1. Hero: "¿Quiénes Somos?" con descriptores (personas diversas, sin dogmas, en búsqueda, comunidad abierta, ayuda humanitaria)
2. Chac-Mool Puente — el canalizador
3. Blog: Vida de la comunidad (entradas recientes)
4. ¿Dónde estamos? — Mapa + CTA centros
5. Preguntas frecuentes (acordeón)
6. CTA final — Inscríbete al curso gratuito

### 2.4 ONG (`/ong`)

**Archivo:** `Pages/Ong/Index.vue` (245 líneas)

**Secciones:**
1. Presentación con logo ONG
2. Misión — ámbitos de acción
3. Proyectos activos
4. Voluntariado
5. Hazte voluntario (requiere curso holístico)

**Botones de acción:**
- "Donar ahora" → `/donar`
- "Buscar un centro" → `/centros`
- "Quiero colaborar" → enlace de contacto
- "Hazte voluntario" → `/inscripcion`
- "Documentación de la ONG" → `/libros/la-ong-mundo-armonico-tseyor`

### 2.5 Cursos (`/cursos`)

**Archivo:** `Pages/Cursos/Index.vue` (109 líneas)

**Secciones:**
1. TextImage: Curso Holístico Tseyor (con botón "Inscríbete")
2. Video promocional (YouTube)
3. FeatureColumns: Exploración Cósmica, Desarrollo personal, Extrapolación mental, Cuántica y Conciencia
4. TextImage: Filosofía Cósmico-Crística
5. Libro del Curso (3D) con descarga
6. Sección futuros cursos (oculta con `v-if="false"`)

**Subpáginas:**
- `/inscripcion` — Nueva inscripción
- `/cursos` (Index2.vue existe como alternativa)

### 2.6 Otras páginas relevantes

| Ruta | Página | Estado |
|------|--------|--------|
| `/biblioteca` | Biblioteca | Estable |
| `/novedades` | Novedades | Estable |
| `/descubre` | Descubre | Estable |
| `/contactar` | Contactar | Estable |
| `/sello` | Sello | Estable |
| `/donde-estamos` | Contactos | Estable |
| `/asociacion` | Asociación TSEYOR | CMS |
| `/utg` | Universidad TSEYOR de Granada | Estable |
| `/ong/muular` | Muular | Estable |
| `/donar` | Página de donaciones | CMS (ruta dinámica) |
| `/mis-primeros-pasos` | Guía inicio | CMS (ruta dinámica) |

---

## 3. Menú de Navegación

**Archivo:** `resources/js/navigation.js` (480 líneas)
**Store:** Pinia `useNavStore` en `Stores/nav.js`

### 3.1 Tabs principales (8)

| # | Tab | Ruta | Submenú |
|---|-----|------|---------|
| 1 | **Inicio** | `/` | Sin submenú (solo sidebar) |
| 2 | **Curso** | `/cursos` | El curso + Consulta |
| 3 | **Biblioteca** | `/biblioteca` | Biblioteca, Comunidad, Documentos, Media |
| 4 | **Filosofía** | `/filosofia` | Filosofía + Temas clave |
| 5 | **Quiénes somos** | `/quienes-somos` | Presentación + Lugares Tseyor |
| 6 | **Novedades** | `/novedades` | Novedades + Redes sociales |
| 7 | **Blog** | `/blog` | Comentado (sin submenú activo) |
| 8 | **Miembros** | — | Equipos, Documentación, Herramientas |

### 3.2 Componentes de navegación

| Componente | Archivo | Propósito |
|-----------|---------|-----------|
| `NavBar.vue` | `Components/NavBar.vue` | Barra superior con logo, tabs, search, user menu |
| `NavTabs.vue` | `Components/NavTabs.vue` | Tabs con hover/underscore animation |
| `NavAside.vue` | `Components/NavAside.vue` | Sidebar móvil con accordion |
| `NavSubmenu.vue` | `Components/NavSubmenu.vue` | Submenú hover dropdown |
| `NavSubmenuGroup.vue` | `Components/NavSubmenuGroup.vue` | Grupo de items en submenú |
| `NavLink.vue` | `Components/NavLink.vue` | Link estilizado |
| `GlobalSearch.vue` | `Components/GlobalSearch.vue` | Búsqueda global |

### 3.3 Layout principal

**Archivo:** `Layouts/AppLayout.vue` (231 líneas)

Componentes globales:
- `<Tools />` — herramientas globales (lazy)
- `<ToolTextSearch />` — búsqueda de texto (lazy)
- `<AudioVideoPlayer />` — reproductor global (no en Portada3)
- `<Announcement />` — banner de anuncios
- `<NavAside />` — sidebar móvil
- `<NavBar />` — navegación principal
- `<AppFooter />` — footer (oculto en `/archivos` y `/emails`)

---

## 4. Cambios Realizados (Histórico)

### 4.1 Portada
- ✅ Creada `Portada3.vue` como versión depurada
- ✅ Creado controller `portada3()` con imágenes reales desde `/almacen/medios/portada/usuarios`
- ✅ Shuffle aleatorio de imágenes por request
- ✅ Lista de audios para meditación diaria
- ✅ AudioVideoPlayer oculto en Portada3 (layout)

### 4.2 Filosofía
- ✅ Reordenación completa a narrativa progresiva (de menos a más)
- ✅ Nueva sección "¿Por qué ahora?" con:
  - Rayo Sincronizador
  - Salto cuántico
  - Especialización
  - Doble interdimensionalidad
  - Cadena de ayuda interdimensional
  - Despertar del ser humano atlante
- ✅ Grid Descubre con tarjetas reorganizadas
- ✅ Sección "Un camino de interiorización y comprobación" con fondo bg-base-100
- ✅ Secciones renombradas: "El cambio de era", "Quiénes somos realmente", "El camino de autodescubrimiento", "Hacia las Sociedades Armónicas"
- ✅ Back button de filosofía vacío (sin enlace a Quiénes Somos)

### 4.3 ONG
- ✅ Añadido enlace "Donar ahora" → `/donar`
- ✅ Cambio de CTA a "Quiero colaborar"
- ✅ Añadida sección "Hazte voluntario" que enlaza al curso holístico

---

## 5. Riesgos y Gaps Identificados

### 5.1 Riesgos técnicos
| Riesgo | Descripción | Severidad |
|--------|-------------|-----------|
| Sin pruebas automatizadas | No se detectaron tests para las páginas Vue | Alta |
| Sin TypeScript | Todo el frontend es JS plano | Media |
| Portada3 sin test | 702 líneas sin verificación | Alta |
| Filosofia.vue muy grande | 470 líneas en un solo componente | Media |
| Código legacy conviviendo | Existen Portada, Portada2, Portada3 activas | Baja |
| Dependencia de imágenes locales | Las imágenes se sirven desde el sistema de archivos, no CDN | Media |
| Sin lazy loading consistente | No todas las imágenes usan `loading="lazy"` | Baja |

### 5.2 Gaps de contenido
| Gap | Descripción |
|-----|-------------|
| Página /donar | Existe como ruta dinámica pero no tiene página Vue dedicada |
| Blog como tab | El tab Blog tiene el submenú comentado, no funcional |
| Sección "Próximos cursos" oculta | `v-if="false"` en Cursos/Index.vue, contenido muerto |
| Mis primeros pasos | Ruta CMS sin página Vue dedicada |
| Sin sitemap visible | No se encontró generación de sitemap |
| Sin meta tags dinámicos por página | No se verificó SEO por página individual |

### 5.3 Gaps de navegación
| Gap | Descripción |
|-----|-------------|
| Blog tab sin submenú | El submenú está comentado, el tab redirige pero sin dropdown |
| Publicaciones deshabilitado | `disabled: true` en el menú |
| TSEYOR Canva deshabilitado | `disabled: true` en herramientas |
| Sin breadcrumbs | No hay navegación jerárquica visible |

---

## 6. Enlaces Externos

| Recurso | URL |
|---------|-----|
| Facebook | `http://facebook.com/tseyor` |
| X (Twitter) | `http://twitter.com/tseyor` |
| YouTube | `http://youtube.com/@tseyor` |
| Puzle TSEYOR | `https://puzle.tseyor.org/` |
| Muular Electrónico | `/muular-electronico` (proxy interno) |
| TSEYOR Canva | `/tseyor-canva` (proxy interno, deshabilitado) |

---

## 7. Secciones del Sitio (CRUD)

El sitio cuenta con **25+ secciones CRUD** con listado y detalle:

Noticias, Comunicados, Blog/Entradas, Libros, Audios, Videos, Guías, Términos (Glosario), Preguntas, Cursos, Radio, Lugares, Eventos, Salas, Centros, Contactos, Meditaciones, Psicografías, Tutoriales, Normativas, Usuarios, Publicaciones, Informes, Galerías, Boletines, Equipos, Experiencias

Cada sección tiene `Index.vue` (listado) y `{recurso}.vue` (detalle) en `resources/js/Pages/{seccion}/`.

---

## 8. Próximos Pasos Pendientes

- [ ] Análisis completo con skills impeccable + critique
- [ ] Portfolio.html (página de portfolio pendiente)
- [ ] Evaluar TypeScript migration
- [ ] Implementar tests para páginas principales
- [ ] Reactivar secciones ocultas (Blog submenu, Publicaciones, TSEYOR Canva)
- [ ] Optimizar imágenes (CDN, lazy loading)
- [ ] SEO: meta tags dinámicos, sitemap
